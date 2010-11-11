<?php
    class WebLab_Mail extends WebLab_Template
    {

        protected $_to;
        protected $_from;
        protected $_subject;
        protected $_boundary;

        public function __construct( $template, $to, $subject='Automated mail', $from='admin@server' ){
            parent::__construct($template);

            if( !is_array( $to ) )
                $to = explode( ',', $to );

            $this->_to = $to;
            $this->_subject = $subject;
            $this->_from = $from;
            $this->_boundary = 'Mail_' . md5( uniqid( time() ) );
        }

        public function render( $show=false )
        {
            ob_start();
            include( $this->_dir . '/source/' . $this->_template );
            $code = ob_get_clean();

            $code = $this->_parseTemplate($code);
            if( $show )
            {
                echo $code;
            }
            return $code;
        }

        protected function _parseTemplate( $html_code ){
            $regExp = "/(background|src)=\"(.*)\.(jpeg|png|gif|jpg)\"/i";

            preg_match( $regExp, $html_code, $matches );

            $images = array();
            foreach( $matches as $match ){
                $html_code = str_replace( $match[2], 'cid:img' . count( $images ), $html_code);
                $images[count($images)] = $match[2];
            }
            $html = '--' . $this->_boundary . "\n";
            $html .= 'Content-Type: text/html;' ."\n\n" . $html_code;

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
            $html .= '--' . $this->_boundary . '--' . "\n";
            return $html;
        }

        public function send(){
            if( !empty( $this->_from ) )
                    $headers = 'FROM:' . $this->_from . "\n" .
                        'Content-Type: multipart/related;' .
                        'boundary="' . $this->_boundary . '"' . "\n";
            $content = $this->render();


            foreach( $this->_to as $email ){
                try{
                    mail( $email, $this->_subject, $content, $headers );
                }catch( Exception $ex ){

                }
            }

            return true;
        }

    }