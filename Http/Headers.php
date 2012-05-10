<?php
	class WebLab_Http_Headers {
		
		/**
		 * Contains HTTP response codes.
		 * 
		 * @var array
		 */
		protected static $_response_codes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Moved Temporaril',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-out',
			410 => 'Conflict',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Large',
			415 => 'Unsupported Media Type',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Time-out',
			505 => 'HTTP Version not supported'
		);
		
		/**
		 * Holds an instance of the Headers for the request.
		 * 
		 * @var WebLab_Http_Headers
		 */
		protected static $_request = null;
		
		/*
		 * Get an instance of the current request's headers.
		 *
		 * @return WebLab_Http_Headers
		 */
		public static function getForRequest() {
			if( empty( self::$_request ) ) {
				self::$_request = new self();
			}
			return self::$_request;
		}
		
		/**
		 * Response code that is currently set
		 * 
		 * @var int
		 */
		protected $_response_code = 200;
		
		/**
		 * Stores headers
		 * 
		 * @var array
		 */
		protected $_headers = null;
		
		/**
		 * Set responsecode
		 * 
		 * @param int $code Responsecode to send
		 * @param string $text Message for unknown HTTP status codes.
		 * @return string HTTP header that sets the status code.
		 */
		public function setResponseCode( $code, $text='Unknown Code' ) {
			$codes = self::$_response_codes;
			
			if( isset( $codes[ $code ] ) ) {
				$text = $codes[ $code ];
			}
			
			$protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER[ 'SERVER_PROTOCOL' ] : 'HTTP/1.0';
			$header = $code . ' ' . $text;
			
			$this->setHeader( $protocol . ' ' . $header );
			$this->setHeader( 'Status', $header );
			$this->_response_code = $code;
			
			return $header;
		}
		
		/**
		 * Returns the response code.
		 * 
		 * $return int
		 */
		public function getResponseCode() {
			return $this->_response_code;
		}
		
		/**
		 * Set a header
		 * 
		 * @param string $header
		 * @param string $value
		 */
		public function setHeader( $header, $value=null ) {
			$headers = &$this->getHeaders();
			
			if( $value != null ) {
				$header = $header . ': ' . $value;
			}
			
			header( $header );
			$headers[] = $header;
		}
		
		/**
		 * Get all headers that have been set
		 * 
		 * @return array
		 */
		public function &getHeaders() {
			$headers = &$this->_headers;
			if( empty( $headers ) ) {
				$headers = headers_list();
			}
			return $headers;
		}
		
		/**
		 * Get headers with the specified key
		 * 
		 * @return array
		 */
		public function getHeader( $key ) {
			$headers = $this->getHeaders();
			$response = array();
			$key .= ':';
			
			foreach( $headers as $header ) {
				if( strpos( $header, $key ) === 0 ) {
					$response[] = $header;
				}
			}
			
			return $response;
		}
		
	}