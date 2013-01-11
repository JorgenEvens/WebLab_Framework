<?php
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
            $regExp = "/(background|src)=\"((.*)\.(jpeg|png|gif|jpg))\"/i";

            preg_match_all( $regExp, $html_code, $matches, PREG_SET_ORDER );

            $images = array();
            foreach( $matches as $match ){
                $html_code = str_replace( $match[2], 'cid:img' . count( $images ), $html_code);
                $images[count($images)] = $match[2];
            }
            
            if( empty( $images ) ) {
            	return $html_code;
            } else {
            	$this->_boundary = md5( uniqid( time() ) );
            	
	            $html = '--' . $this->_boundary . "\r\n";
	            $html .= 'Content-Type: ' . $this->_content_type . ';' .
                    "\r\n\r\n" . $html_code . "\r\n\r\n";
	
	            foreach( $images as $key => $image )
	            {
	                $html .= '--' . $this->_boundary . "\r\n";
	                $img = file_get_contents( $image );
	                $name = basename( $image );
	                $contentType = array_pop( explode( '.', $name ) );
	
	                $html .= 'Content-Type: image/' . $contentType . '; name="' . $name . '"' . "\r\n" .
	                            'Content-ID: <img' . $key . '>' . "\r\n" .
	                            'Content-Transfer-Encoding: base64' . "\r\n" .
	                            'Content-Disposition: inline' . "\r\n\r\n";
	                $html .= chunk_split( base64_encode( $img ), 68, "\r\n" );
	                $html .= "\r\n";
	            }
	            $html = trim( $html, "\r\n" );
	            $html .= "\r\n\r\n" . '--' . $this->_boundary . '--' . "\r\n";
            }
            return $html;
        }

        /**
         * Transmits the e-mail to all the receivers.
         * @return bool Indicates whether the send operation was successful.
         */
        public function send(){
            if( empty( $this->_from ) ) {
                return false;
            }

            $content = $this->render();

        	$headers = 'From:' . $this->_from . "\r\n" .
                'X-Mailer: WebLab_Mailer' . "\r\n" . 
                'Message-ID: <' . md5( time() ) . '@' . $_SERVER['SERVER_ADDR'] . ">\r\n" .
                'Date: ' . date('r') . "\r\n" . 
                'Mime-Version: 1.0' . "\r\n";
                
        	if( !empty( $this->_boundary ) ) {
        		$headers .= 'Content-Type: multipart/related; ' .
        			'boundary=' . $this->_boundary . "\r\n";
        	} else {
        		$headers .= 'Content-Type: ' . $this->_content_type . "\r\n";
        	}

			$error_address = array();
            foreach( $this->_to as $email ){
                if( strpos( $email, '<' ) === false ) {
                    $email = '<' . $email . '>';
                }

            	if( !mail( $email, $this->_subject, $content, $headers ) )
            		$error_address[] = $email;
            }

            if( count( $error_address ) > 0 )
            	return $error_address;
            	
            return true;
        }

    }