<?php
    /**
    * Query.php
    *
    * This file contains the implementation of the WebLab_Data_MySQLi_Query class.
    * @see WebLab_Data_MySQLi_Query
    */
    /**
     *
     * Implementation of a query, specific to the MySQLi driver.
     *
     * @see WebLab_Data_Query
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Data_MySQLi
     *
     */
	class WebLab_Data_MySQLi_Query extends WebLab_Data_Query {
		
		protected function createResult()
		{
			return new WebLab_Data_MySQLi_Result( $this );
		}
		
        /**
         * Retrieve a builder instance for this query, this method ensures lazy loading.
         *
         * @return WebLab_Data_MySQLi_QueryBuilder A builder to be used for this query instance.
         */
		public function builder()
		{
			if( empty( $this->_builder ) )
				$this->_builder = new WebLab_Data_MySQLi_QueryBuilder();

			return $this->_builder->setQuery( $this );
		}
		
	}
