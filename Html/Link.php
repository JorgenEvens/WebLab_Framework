<?php
	class WebLab_Html_Link extends WebLab_Xml_Tag {
		
		public function __construct( $path, $type='text/css', $relation='stylesheet' ) {
			parent::__construct( 'link', array(
				'href' => $path,
				'type' => $type,
				'rel' => $relation
			));
		}
		
	}