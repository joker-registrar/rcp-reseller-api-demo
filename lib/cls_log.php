<?php

/*
 * Class for logging request status etc.
 */

class Log
{
	/**
	 * Log directory
	 *
	 * @var		string
	 * @access	private
	 */
	var $log_dir = "";

	/**
	 * Flag for start/stop of the logging
	 *
	 * @var		boolean
	 * @access	private
	 */
	var $run_log = false;

	/**
	 * String that sets the log filename
	 *
	 * @var		string
	 * @access	private
	 */
	var $log_filename = "";
	
	/**
	 * Log message
	 *
	 * @var		array
	 * @access	private
	 */
	var $log_msg = array();
	
	/**
	 * Default log message
	 *
	 * @var		string
	 * @access	private
	 */
	var $default_log_msg = "";

	/******************************************************************************
	 * Class constructor. No optional parameters.
	 *
	 * usage: Log()
	 *
	 * @access	private
	 * @return	void
	 */
	function Log()
	{
		global $config;		
		$this->log_dir = $config["log_dir"];
		$this->run_log = $config["run_log"];
		$this->log_filename = $config["log_filename"];
		$this->log_msg = $config["log_msg"];
		$this->default_log_msg = $config["log_default_msg"];
	}
	
	/**
	 * Records the log events
	 *
         * usage: req_status(string $type, string $data)
	 *
	 * @param	$type      type of log message - could be informative, error etc.
	 * @param	$data      content of the log message
	 * @access	public
	 * @return	void
	 */
	function req_status($type, $data)
	{
	    if ($this->run_log) {
	    	if (!file_exists($this->log_dir)) {
		    mkdir($this->log_dir);
		}		
		if ($this->log_msg[$type] == "") {
		    $type = $this->default_log_msg;
		}
		$fp = fopen($this->log_dir . "/" . $this->log_filename, "a");
    		fwrite($fp, "[" . date("j-m-Y H:i:s") . "]" . "[" . $_SESSION["userdata"]["t_username"] . "]" . "[" . $_SERVER["REMOTE_ADDR"] . "]" . "[" . $this->log_msg[$type] . "] " . $data . "\n");
    		fclose($fp);
    	    }
	}

} //end of class Log

?>