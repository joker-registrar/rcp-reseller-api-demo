<?php

/**
 * Class for logging request status, user defined error messages etc.
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class Log
{
	/**
	 * Log directory.
         * Its value is overridden in the class constructor.
	 *
	 * @var		string
	 * @access	private
         * @see		Log()
	 */
	var $log_dir = "";

	/**
	 * Flag for start/stop of the logging
         * Its value is overridden in the class constructor.
	 *
	 * @var		boolean
	 * @access	private
         * @see		Log()
	 */
	var $run_log = false;

	/**
	 * String that sets the log filename
         * Its value is overridden in the class constructor.
	 *
	 * @var		string
	 * @access	private
         * @see		Log()
	 */
	var $log_filename = "";
	
	/**
	 * Array with all log message types
	 * Its values are overridden in the class constructor.
         * 
	 * @var		array
	 * @access	private
         * @see		Log()
	 */
	var $log_msg = array();
	
	/**
	 * Default log message type
         * Its value is overridden in the class constructor.
	 *
	 * @var		string
	 * @access	private
         * @see		Log()
	 */
	var $default_log_msg = "";

	/**
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
	 * @param	string	$type      type of log message - could be informative, error etc.
	 * @param	string	$data      content of the log message
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