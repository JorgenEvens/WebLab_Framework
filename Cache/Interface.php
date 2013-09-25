<?php

	interface WebLab_Cache_Interface {

		function exists( $key );

		function get( $key );

		function set( $key, $value=null );

		function delete( $key );

	}