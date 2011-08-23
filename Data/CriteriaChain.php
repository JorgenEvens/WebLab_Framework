<?php
	/**
	 * Combines a set of criteria using AND or OR.
	 * 
	 * @author jorgen
	 * @package WebLab
	 * @subpackage WebLab_Data
	 *
	 */
    class WebLab_Data_CriteriaChain {

    	/**
    	 * Holds the criteria added to the chain.
    	 * 
    	 * @var WebLab_Data_Criteria[]
    	 */
        protected $_criteria = array();

        /**
         * Add a criteria to the end of the chain using the AND keyword.
         * 
         * @param WebLab_Data_Criteria $criteria
         * @throws WebLab_Data_Exception If neither a criteria nor a criteriachain is supplied as $criteria.
         * @return WebLab_Data_CriteriaChain
         */
        public function addAnd( $criteria ) {
            if( !( $criteria instanceof WebLab_Data_Criteria || $criteria instanceof WebLab_Data_CriteriaChain ) ) {
                throw new WebLab_Data_Exception( 'Must supply a Criteria or a CriteriaChain' );
            }
            
            $this->_criteria[] = (object) array( 'criteria' => $criteria, 'operator' => 'AND' );

            return $this;
        }

        /**
         * Add a criteria to the end of the chain using the OR keyword.
         * 
         * @param WebLab_Data_Criteria $criteria
         * @throws WebLab_Data_Exception If neither a criteria nor a criteriachain is supplied as $criteria.
         * @return WebLab_Data_CriteriaChain
         */
        public function addOr( $criteria ) {
            if( !( $criteria instanceof WebLab_Data_Criteria || $criteria instanceof WebLab_Data_CriteriaChain ) ) {
                throw new Exception( 'Must supply a Criteria or a CriteriaChain' );
            }
            
            $this->_criteria[] = (object) array( 'criteria' => $criteria, 'operator' => 'OR' );

            return $this;
        }

        /**
         * Convert the chain to it's text representation using the adapter specifications.
         * 
         * @param object $adapterSpecs
         * @return string
         */
        public function get( $adapterSpecs ) {
            $q = '';
			
            foreach( $this->_criteria as $criteria ) {
                $tmp = ' ' . $criteria->criteria->get( $adapterSpecs ) . ' ';

                if( $criteria->criteria instanceof WebLab_Data_CriteriaChain ) {
                    $tmp = ' (' . $tmp . ')';
                }
                
                $q .= ( empty( $q ) ? '' : $criteria->operator ) . $tmp;
            }

            return $q;
        }

    }