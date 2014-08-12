<?php
	class WebLab_Http_Request extends WebLab_Http_Message {

		public $method = 'GET';

		public $url = '';

		public function __construct( $url, $method='GET', $headers=array(), $body='', $http_version='1.0' ) {
			parent::__construct( $headers, $body, $http_version );
			$this->method = $method;
			$this->url = $url;
		}

		public function parseMessage( $message ) {
			parent::parseMessage( $message );
			$this->parseRequestLine( $message );
		}

		public function parseRequestLine( $message ) {
			$start = 0;
			$end = strpos( $message, "\r\n" );

			if( $end > -1 ) {
				$line = substr( $message, 0, $end-$start );
			} else {
				$line = $message;
			}

			$line = explode( ' ', $line );
			if( count( $line ) != 3 ) return;

			$line[2] = explode( '/', $line[2] );

			$this->method = $line[0];
			$this->url = $line[1];
			$this->http_version = $line[2][1];
		}

		public function perform( $host, $https=false, $port=null ) {
			$url = ( $https ? 'https://' : 'http://' ) . $host;
			if( $port === null ) {
				$port = $https ? 443 : 80;
			} else {
				$url .= ':' . $port;
			}
			$url .= $this->url;

			$stream = $this->_createStream( $url );
			$response = $this->_readResponse( $stream );
			$this->_closeStream( $stream );

			return $response;
		}

		protected function _closeStream( $stream ) {
			fclose( $stream );
		}

		protected function _readResponse( $stream ) {
			$meta = stream_get_meta_data( $stream );
			$body = '';
			while( is_resource( $stream ) && !feof( $stream ) ) {
				$body .= stream_get_contents( $stream );
			}

			$response = new WebLab_Http_Response(500);
			foreach( $meta['wrapper_data'] as $header ) {
				if( strpos( $header, 'HTTP/' ) !== false ) {
					$response->parseStatusLine($header);
					continue;
				}
				$response->addHeader( $header );
			}

			$response->body = $body;

			return $response;
		}

		protected function _createStream( $url ) {
			$stream_ctx = $this->_createContext();

			$stream = fopen( $url, 'r', false, $stream_ctx );

			return $stream;
		}

		protected function _createContext() {
			$stream_ctx = array( 
				'http' => array(
					'method' => strtoupper( $this->method ),
					'header' => array_filter( explode( "\r\n", $this->headersAsString() ) ),
					'content' => $this->body,
					'user_agent' => 'WebLab_Http Extension',
					'protocol_version' => $this->http_version,
					'max_redirects' => 0,
					'ignore_errors' => true
				)
			);
			return stream_context_create( $stream_ctx );
		}

		public function toString() {
			$message = strtoupper( $this->method ) . ' ' . $this->url . ' HTTP/' . $this->http_version . "\r\n";
			$message .= $this->headersAsString() . "\r\n";
			return $message . $this->body;
		}

	}
