<?php
	class WebLab_Http_Response extends WebLab_Http_Message {

		public $status_code = 200;

		public $status_text = '';

		public function __construct( $status_code, $status_text='', $headers=array(), $body='', $http_version='1.0' ) {
			parent::__construct( $headers, $body, $http_version );
			$this->status_code = $status_code;
			$this->status_text = $status_text;
		}

		public function parseMessage( $message ) {
			parent::parseMessage( $message );
			$this->parseStatusLine( $message );
		}

		public function parseStatusLine( $message ) {
			$start = 0;
			$end = strpos( $message, "\r\n" );

			if( $end > -1 ) {
				$line = substr( $message, 0, $end-$start );
			} else {
				$line = $message;
			}

			$line = explode( ' ', $line );
			if( count( $line ) < 3 ) return;

			$version = explode( '/', array_shift( $line ) );

			$this->http_version = $version[1];
			$this->status_code = array_shift( $line );
			$this->status_text = implode( ' ', $line );
		}

	}