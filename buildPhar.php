#!/usr/bin/env php
<?php
	$dir = getcwd();

	$path = explode( DIRECTORY_SEPARATOR, $dir );
	array_pop( $path );
	$path = implode( DIRECTORY_SEPARATOR, $path );

	$archive = new Phar( $dir . DIRECTORY_SEPARATOR . 'WebLab.phar', 0, 'WebLab.phar' );
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );

	$archive->buildFromIterator( $iterator, $path );
	$stub = file_get_contents( $dir . DIRECTORY_SEPARATOR . 'Framework.php' );
	$stub .= '
		try {
			Phar::mapPhar( \'WebLab.phar\' );
		} catch( Exception $ex ) {
			var_dump( $ex );
		}
		set_include_path( \'phar://WebLab.phar\' . PATH_SEPARATOR . get_include_path() );
		__HALT_COMPILER();';

	$archive->setStub( $stub );
	$archive->setSignatureAlgorithm( Phar::SHA1 );

	rename( 'WebLab.phar', '../WebLab.phar' );

	echo "Archive build!\n";

