<?php

define( 'MAIL_NL', "\r\n" );
define( 'MAIL_NLNL', MAIL_NL . MAIL_NL );
    /**
     * Mail.php
     * 
     * This files contains the implementation of the WebLab_Mail class.
     *
     * @see WebLab_Mail
     */
    /**
     * Send mails based on WebLab_Template.
     * Joins images into the e-mail for optimal viewing.
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     *
     */
    class WebLab_Mail extends WebLab_Template
    {
    	
    	// TODO: Set resources directory.

    	/**
    	 * Receiver of the e-mail.
    	 * @var String The e-mailaddress of the receiver.
    	 */
        protected $_to;
        
        /**
         * The sender of the e-mail.
         * @var String The e-mailaddress of the sender.
         */
        protected $_from;
        
        /**
         * The subject of the e-mail.
         * @var String The subject of the e-mail.
         */
        protected $_subject;
        
        /**
         * The boundary splitting multiple content types.
         * @var String The boundary used to split content types.
         */
        protected $_boundary;
        
        /**
         * Defines primary content type
         * @var String Defaults to text/html
         */
        protected $_content_type = 'text/html';

        /**
         * Attachment to be added to the body using multipart
         */
        protected $_attachments;

        /**
         * Generates a new Mail object.
         * @param String $template The template path
         * @param String $to The recipients
         * @param String $subject The subject of this e-mail. Defaults to 'Automated mail'
         * @param String $from From who the receiver gets this message. Defaults to 'admin@server'
         * @param String $content_type The content type to set in the headers.
         */
        public function __construct( $template, $to, $subject='Automated mail', $from='admin@server', $content_type='text/html' ){
            parent::__construct($template);

            if( !is_array( $to ) )
                $to = explode( ',', $to );

            $this->_to = $to;
            $this->_subject = $subject;
            $this->_from = $from;
            $this->_content_type = $content_type;
            $this->_attachments = array();
        }

        /**
         * Generates the body of the Mail.
         * @param bool $show Output result of this function to the browser. Defaults to false.
         * @return String The body of the e-mail.
         */
        public function render( $show=false )
        {
            $code = parent::render(false);

            $code = $this->_parseTemplate($code);
            $code = $this->_renderAttachments($code);
            if( $show )
                echo $code;

            return $code;
        }

        /**
         * Render the mail body including attachments
         */
        protected function _renderAttachments($html_code) {
            if (empty($this->_attachments))
                return $html_code;

            $this->_boundary = md5( uniqid( time() ) );

            $result[] = $this->_renderAttachment(array(
                'body' => $html_code,
                'name' => '',
                'content-type' => $this->_content_type,
                'headers' => array()
            ));

            foreach($this->_attachments as $attachment)
                $result[] = $this->_renderAttachment($attachment);

            return implode(PHP_EOL . PHP_EOL, $result) . PHP_EOL .
                '--' . $this->_boundary . '--' . PHP_EOL;
        }

        /**
         * Render a single attachment
         */
        protected function _renderAttachment($attachment) {
            $body = $attachment['body'];
            $name = $attachment['name'];
            $contentType = $attachment['content-type'];
            $headers = $attachment['headers'];

            if (!empty($name))
                $contentType .= '; name="' . $name . '"';

            return '--' . $this->_boundary . PHP_EOL .
                'Content-Type: ' . $contentType . PHP_EOL .
                implode(PHP_EOL, $headers) . PHP_EOL .
                $body . PHP_EOL;
        }

        /**
         * Parses the code generate by the template for images.
         * @param String $html_code The HTML code to parse.
         * @return String The HTML code parsed to include images.
         */
        protected function _parseTemplate( $html_code ){
            $regExp = "/(background|src)=\"((.*)\.(jpeg|png|gif|jpg))\"/i";

            preg_match_all( $regExp, $html_code, $matches, PREG_SET_ORDER );

            $images = array();
            foreach( $matches as $match ){
                $html_code = str_replace( $match[2], 'cid:img' . count( $images ), $html_code);
                $images[count($images)] = $match[2];
            }

            if( empty( $images ) )
            	return $html_code;

            $this->_boundary = md5( uniqid( time() ) );

            $html = '--' . $this->_boundary . PHP_EOL;
            $html .= 'Content-Type: ' . $this->_content_type . ';' .
                PHP_EOL . PHP_EOL . $html_code . PHP_EOL . PHP_EOL ;

            foreach( $images as $key => $image )
            {
                $html .= '--' . $this->_boundary . PHP_EOL;
                $name = basename( $image );
                $img = file_get_contents( $image );
                $img = chunk_split( base64_encode( $img ), 68, PHP_EOL );
                $contentType = explode( '.', $name );
                $contentType = array_pop( $contentType );
                $contentType = 'image/' . $contentType;

                $this->addAttachment($img, $name, $contentType, array(
                    'Content-ID: <img' . $key . '>',
                    'Content-Transfer-Encoding: base64',
                    'Content-Disposition: inline'
                ));
            }

            return $html;
        }

        protected function _generateBoundary() {
            $this->_boundary = md5( uniqid( time() ) );
        }

        public function addAttachment($body, $name, $contentType = 'text/plain', $headers = array()) {
            $this->_attachments[] = array(
                'body' => $body,
                'name' => $name,
                'content-type' => $contentType,
                'headers' => $headers
            );
            return $this;
        }

        /**
         * Transmits the e-mail to all the receivers.
         * @return bool Indicates whether the send operation was successful.
         */
        public function send(){
            if( empty( $this->_from ) ) {
                return false;
            }

            $content = $this->render( false );

        	$headers = 'From:' . $this->_from . MAIL_NL .
                'X-Mailer: WebLab_Mailer' . MAIL_NL .
                'Message-ID: <' . md5( time() ) . '@' . $_SERVER['SERVER_ADDR'] . ">" . MAIL_NL .
                'Date: ' . date('r') . MAIL_NL .
                'Mime-Version: 1.0' . MAIL_NL;

        	if( !empty( $this->_boundary ) ) {
        		$headers .= 'Content-Type: multipart/related; ' .
        			'boundary=' . $this->_boundary . MAIL_NL;
        	} else {
        		$headers .= 'Content-Type: ' . $this->_content_type . MAIL_NL;
        	}

			$error_address = array();
            foreach( $this->_to as $email ){
                if( strpos( $email, '<' ) === false ) {
                    $email = '<' . $email . '>';
                }

            	if( !mail( $email, $this->_subject, $content, trim( $headers, MAIL_NL ) ) )
            		$error_address[] = $email;
            }

            if( count( $error_address ) > 0 )
            	return $error_address;
            	
            return true;
        }

    }