<?php
    class WebLab_Data_CriteriaChain
    {

        protected $_criteria = array();

        public function addAnd( $criteria )
        {
            if( !( $criteria instanceof WebLab_Data_Criteria || $criteria instanceof WebLab_Data_CriteriaChain ) )
            {
                throw new Exception( 'Must supply a Criteria or a CriteriaChain' );
            }
            
            $this->_criteria[] = (object) array( criteria => $criteria, operator => 'AND' );

            return $this;
        }

        public function addOr( $criteria )
        {
            if( !( $criteria instanceof WebLab_Data_Criteria || $criteria instanceof WebLab_Data_CriteriaChain ) )
            {
                throw new Exception( 'Must supply a Criteria or a CriteriaChain' );
            }
            
            $this->_criteria[] = (object) array( criteria => $criteria, operator => 'OR' );

            return $this;
        }

        public function get( $adapterSpecs )
        {
            $q = '';

            foreach( $this->_criteria as $criteria )
            {
                $tmp = '';
                
                if( empty( $q ) )
                {
                    $tmp = $criteria->criteria->get( $adapterSpecs ) . ' ';
                }else
                {
                    $tmp = $criteria->operator . ' ' . $criteria->criteria->get( $adapterSpecs ) . ' ';
                }

                if( $criteria instanceof WebLab_Data_CriteriaChain )
                {
                    $q .= '( ' . $tmp . ' )';
                }else
                {
                    $q .= $tmp;
                }
            }

            return $q;
        }

    }