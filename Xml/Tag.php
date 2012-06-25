<?php
	class WebLab_Xml_Tag {
		
		public $name;
		
		public $attributes;
		
		public $content = null;
		
		public $self_closing = true;
		
		public function __construct( $name, $attributes=array(), $content=null, $self_closing=true ) {
			$this->name = $name;
			$this->attributes = $attributes;
			$this->content = $content;
			$this->self_closing = $self_closing;
		}
		
		public function __toString() {
			$tag = '<' . $this->name;
			
			foreach( $this->attributes as $key => $value ) {
				$tag .= ' ' . htmlentities( $key ) . '="' . htmlentities( $value ) . '"';
			}
			
			if( empty( $this->content ) && $this->self_closing ) {
				return $tag . ' />';
			}
			
			return $tag . '>' . $this->content . '</' . $this->name . '>';
		}
		
	}