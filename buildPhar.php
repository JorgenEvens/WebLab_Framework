#!/usr/bin/env php
<?php
	$dir = getcwd();

	$path = explode( DIRECTORY_SEPARATOR, $dir );
	array_pop( $path );
	$path = implode( DIRECTORY_SEPARATOR, $path );

	$archive = new Phar( $dir . DIRECTORY_SEPARATOR . 'WebLab.phar', 0, 'WebLab.phar' );
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );

	$archive->buildFromIterator( $iterator, $path );
	$archive->setStub( $archive->createDefaultStub( 'WebLab/Framework.php' ) );

	echo "Archive build!\n";

