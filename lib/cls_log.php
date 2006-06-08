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
     * @var     string
     * @access  private
     * @see     Log()
     */
    var $log_dir = "";

    /**
     * Octal value setting the permission on the log file
     *
     * @var     string
     * @access  private
     * @see     Log()
     */
    var $log_perm = "";

    /**
     * Flag for start/stop of the logging
     * Its value is overridden in the class constructor.
     *
     * @var     boolean
     * @access  private
     * @see     Log()
     */
    var $run_log = false;

    /**
     * String that sets the log filename
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     Log()
     */
    var $log_filename = "";

    /**
     * Array with all log message types
     * Its values are overridden in the class constructor.
     *
     * @var     array
     * @access  private
     * @see     Log()
     */
    var $log_msg = array();

    /**
     * Default log message type
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     Log()
     */
    var $default_log_msg = "";

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Log()
     *
     * @access  private
     * @return  void
     */
    function Log()
    {
        global $jpc_config;        
        $this->log_dir = $jpc_config["log_dir"];
        $this->run_log = $jpc_config["run_log"];
        $this->log_perm = $jpc_config["log_file_perm"];
        $this->log_filename = $jpc_config["log_filename"].date("Y-m", time()).".log";
        $this->log_msg = $jpc_config["log_msg"];
        $this->default_log_msg = $jpc_config["log_default_msg"];
    }

    /**
     * Records the log events
     *
     * usage: req_status(string $type, string $data)
     *
     * @param   string  $type      type of log message - could be informative, error etc.
     * @param   string  $data      content of the log message
     * @access  public
     * @return  void
     */
    function req_status($type, $data)
    {                
        if ($this->run_log) {
            clearstatcache();            
            if (strtoupper(substr(php_uname("s"), 0, 3)) === 'WIN') {            
                $separator = "\\";
            } else {
                $separator = "/";
            }
            if (!is_dir($this->log_dir)) {
                if (!mkdir($this->log_dir, $this->log_perm)) {
                    die("Log dir error: Cannot create " . $this->log_dir);                    
                }
            } else {
                //if (!chmod($this->log_dir, $this->log_perm)) {
                //    die("Log dir error: Cannot change mod of " . $this->log_dir);                    
                //}
            }                                                            
            if ($this->log_msg[$type] == "") {
                $type = $this->default_log_msg;
            }
            $fp = @fopen($this->log_dir . $separator . $this->log_filename, "a");
            if (!$fp) {
                die("Log file error: Failed to open " . $this->log_dir . $separator . $this->log_filename);
            }
            if (fwrite($fp, "[" . date("j-m-Y H:i:s") . "]" . 
                            "[" . $_SESSION["userdata"]["t_username"] . "]" . 
                            "[" . $_SERVER["REMOTE_ADDR"] . "]" . 
                            "[" . $this->log_msg[$type] . "] " . $data . "\n") === FALSE) {
                die("Log file error: Cannot write to file " . $this->log_filename);
            }
            if (fclose($fp) === FALSE) {
                die("Log file error: Cannot close file " . $this->log_filename);
            }            
        }
    }

} //end of class Log

?>
