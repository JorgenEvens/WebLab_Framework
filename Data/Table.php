<?php
    class WebLab_Data_Table
    {

        protected $_fields;

        public function __construct( $_fields )
        {
            
        }

        public function addField( $fieldName )
        {
            $this->_fields[] = $fieldName;

            $this->_updateSelect();
        }

        public function removeField( $fieldName )
        {
            for( $i=0; $i<count( $this->_fields );$i++ )
            {
                if( $this->_fields[ i ] == $fieldName )
                {
                    unset( $this->_fields[i] );
                }
            }

            $this->_updateSelect();
        }

        protected function _updateSelect()
        {
            
        }

    }