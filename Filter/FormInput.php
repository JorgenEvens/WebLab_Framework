<?php
	/**
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Filter
	 *
	 */
	class WebLab_Filter_FormInput extends WebLab_Filter {
		
		public function isChecked( WebLab_Form_Field $value, $testValue ){
			return $value->checked === 'checked';
		}
		
		public function isSelected( WebLab_Form_Field $value, $testValue ){
			return $value->selected === 'selected';
		}
		
		public function required( $testValue, $value ) {
			$value = trim( $value );
			return !empty( $value  );
		}
		
	}
