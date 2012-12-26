<?php
	class WebLab_Http_Message {

		/**
		 * THe headers provided by the HTTP message.
		 *
		 * @var mixed $headers HTTP message headers.
		 */
		public $headers = array();

		/**
		 * The body of the HTTP Message.
		 * 
		 * @var string HTTP message body
		 */
		public $body = '';

		/**
		 * HTTP version
		 *
		 * @var string HTTP Version number
		 */
		public $http_version = '1.0';

		/**
		 * Construct a parsed representation of $message
		 *
		 * @param string $message A raw HTTP message as string.
		 */
		public function __construct( $headers=array(), $body='', $http_version='1.0' ) {
			$this->headers = $headers;
			$this->body = $body;
			$this->http_version = $http_version;
		}

		/**
		 * Parse a raw HTTP message.
		 *
		 * @param string $message The raw HTTP message.
		 */
		public function parseMessage( $message ) {
			if( empty( $message ) ) return;
			$this->extractHeader( $message );
			$this->extractBody( $message );
		}

		/**
		 * Finds headers and parses them.
		 *
		 * @param string $message The message to parse headers from.
		 * @param int $start If start of headers is known, this prevents strpos from being run.
		 */
		public function extractHeader( $message, $start=null ) {
			if( $start === null ) {
				$start = strpos( $message, "\r\n" );
				if( $start === false ) return;
				$start+=2;
			}
			$end = strpos( $message, "\r\n\r\n", $start );

			$headers = substr( $message, $start, $end-$start );
			$this->headers = $this->parseHeaders( $headers );
		}

		/**
		 * Parses a string containing headers
		 *
		 * @param string $headers A string containing headers as specified by the RFC.
		 * @return mixed $data The headers that have been parsed.
		 */
		public function parseHeaders( $headers ) {
			$headers = explode( "\r\n", $headers );
			$this->headers = array();
			foreach( $headers as $header ) {
				$this->addHeader( $header );
			}
			return $this->headers;
		}

		/**
		 * Add a header to the message.
		 *
		 * @param string $header A single header
		 * @param string $value The value for the header with name $header
		 */
		public function addHeader( $header, $value=null ) {
			if( $value === null ) {
				$value = explode( ':', $header );
				$header = trim( array_shift( $value ) );
				$value = trim( implode(':', $value ) );
			}

			$data = &$this->headers;

			if( isset( $data[$header] ) &&  is_array( $data[$header] ) ) {
				$data[$header][] = trim( $value );
			} elseif( !empty( $data[$header] ) ) {
				$data[$header] = array( $data[$header], $value );
			} else {
				$data[$header] = $value;
			}

		}

		/**
		 * Extract the body from a HTTP message.
		 *
		 * @param string $message The message to extract body from.
		 * @param int $start If start of body is known, this prevents strpos from being run.
		 */
		public function extractBody( $message, $start=null ) {
			if( $start === null ) {
				$start = strpos( $message, "\r\n\r\n" )+4;
			}
			if( $start < 0 ) return;

			$this->body = substr( $message, $start );
		}

		public function headersAsString() {
			$headers = '';
			foreach( $this->headers as $key => $value ) {
				if( is_array( $value ) ) {
					foreach( $value as $v ) {
						$headers .= $key . ': ' . $v . " \r\n";
					}
				} else {
					$headers .= $key . ': ' . $value . " \r\n";
				}
			}
			return $headers;
		}

	}