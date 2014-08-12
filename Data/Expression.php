<?php
	/**
	 * Represents a expression column in a statement.
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data
	 *
	 */
    class WebLab_Data_Expression extends WebLab_Data_Field
    {

        protected $_expression;

        public function __construct( $expr, $name=null ) {
            if( isset( $name ) )
                $this->setAlias($name);
            $this->setExpression( $expr );
        }

        public function getFullName() {
            return $this->_alias;
        }

        public function getName() {
            return $this->_alias;
        }

        public function setName($name) {
            return $this->setAlias($name);
        }

        public function getExpression() {
            return $expr;
        }

        public function setExpression( $expr ) {
            $this->_expression = $expr;
        }

        public function __toString() {
            $expr = $this->_expression;
            if( isset( $this->_function ) )
                $expr = $this->_function . '( ' . $expr . ' )';
            return $expr;
        }

    }