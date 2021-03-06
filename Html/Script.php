<?php
	/**
	 * 
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
	 * @subpackage Html
	 *
	 */
	class WebLab_Html_Script extends WebLab_Xml_Tag {
		
		public function __construct( $path, $type='text/javascript' ) {
			parent::__construct( 'script', array(
				'src' => $path,
				'type' => 'text/javascript'
			), '', false );
		}
		
	}