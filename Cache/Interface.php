<?php

	interface WebLab_Cache_Interface {

		function exists( $key );

		function get( $key );

		function set( $key, $value=null, $ttl=0 );

		function delete( $key );

	}