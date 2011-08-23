<?php
    /**
     *
     * Mail wrapper
     *
     * Send mails based on WebLab_Template.
     * Joins images into the e-mail for optimal viewing.
     *
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
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
         * Generates a new Mail object.
         * @param String $template The template path
         * @param String $to The recipients
         * @param String $subject The subject of this e-mail. Defaults to 'Automated mail'
         * @param String $from From who the receiver gets this message. Defaults to 'admin@server'
         */
        public function __construct( $template, $to, $subject='Automated mail', $from='admin@server', $content_type='text/html' ){
            parent::__construct($template);

            if( !is_array( $to ) )
                $to = explode( ',', $to );

            $this->_to = $to;
            $this->_subject = $subject;
            $this->_from = $from;
            $this->_boundary = 'Mail_' . md5( uniqid( time() ) );
            $this->_content_type = $content_type;
        }

        /**
         * Generates the body of the Mail.
         * @param bool $show Output result of this function to the browser. Defaults to false.
         * @return String The body of the e-mail.
         */
        public function render( $show=false )
        {
            $code = parent::render();

            $code = $this->_parseTemplate($code);
            if( $show )
                echo $code;
            
            return $code;
        }

        /**
         * Parses the code generate by the template for images.
         * @param String $html_code The HTML code to parse.
         * @return String The HTML code parsed to include images.
         */
        protected function _parseTemplate( $html_code ){
            $regExp = "/(background|src)=\"(.*)\.(jpeg|png|gif|jpg)\"/i";

            preg_match( $regExp, $html_code, $matches );

            $images = array();
            foreach( $matches as $match ){
                $html_code = str_replace( $match[2], 'cid:img' . count( $images ), $html_code);
                $images[count($images)] = $match[2];
            }
            $html = '--' . $this->_boundary . "\n";
            $html .= 'Content-Type: ' . $this->_content_type . ';' ."\n\n" . $html_code;

            foreach( $images as $key => $image )
            {
                $html .= '--' . $this->_boundary . "\n";
                $img = file_get_contents( $image );
                $name = basename( $image );
                $contentType = array_pop( explode( '.', $name ) );

                $html .= 'Content-Type: image/' . $contentType . '; name="' . $name . '"' . "\n" .
                            'Content-ID: <img' . $key . '>' . "\n" .
                            'Content-Transfer-Encoding: base64' . "\n" .
                            'Content-Disposition: inline' . "\n\n";
                $html .= chunk_split( base64_encode( $img ), 68, "\n" );
                $html .= "\n";
            }
            $html = trim( $html, "\n" );
            $html .= "\n\n" . '--' . $this->_boundary . '--' . "\n";
            return $html;
        }

        /**
         * Transmits the e-mail to all the receivers.
         * @return bool Indicates whether the send operation was successful.
         */
        public function send(){
            if( !empty( $this->_from ) )
                    $headers = 'FROM:' . $this->_from . "\r\n" .
                        'Content-Type: multipart/related;' .
                        'boundary="' . $this->_boundary . '"' . "\r\n";
            $content = $this->render();

			$error_address = array();
            foreach( $this->_to as $email ){
            	if( !mail( $email, $this->_subject, $content, $headers ) )
            		$error_address[] = $email;
            }

            if( count( $error_address ) > 0 )
            	return $error_address;
            	
            return true;
        }

    }