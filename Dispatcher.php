<?php
    /**
     * An interface to which each request dispatcher should comply.
     *
     * @author Jorgen Evens <jorgen@wlab.be>
     * @package WebLab
     * @subpackage Dispatcher
     *
     */
	interface WebLab_Dispatcher {
		
		function execute();
		
	}