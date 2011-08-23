<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Filter
	 *
	 */
	class WebLab_Filter_FormInput extends WebLab_Filter {
		
		public function isChecked( $testValue, WebLab_Form_Field $value ){
			return $value->checked === 'checked';
		}
		
		public function isSelected( $testValue, WebLab_Form_Field $value ){
			return $value->selected === 'selected';
		}
		
	}