<?php

/**
 * Class User is a container for methods which do not fit in the outline
 * of the other classes.
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class User
{
	/**
	 * Represents the uppermost level of the current user position.
         * Its value is usually set in the class constructor.
	 *
	 * @var		string
	 * @access	private
         * @see		User()
	 */
	var $nav_main  = "";
	
	/**
	 * Represents the 2nd level of the current user position.
	 * Its value is set for every function.
         * 
	 * @var		string
	 * @access	private
	 */
	var $nav_submain  = "";

	/**
	 * Array that contains regular expressions and error messages.
         * Its values are overridden in the class constructor.
	 *
         * Note that not all validation is handled with information from
         * this array.
         * 
	 * @var		array
	 * @access	private
         * @see		User(), Tools::is_valid(), Tools::is_valid_contact_hdl()
	 */
	var $err_arr  = array();

	/**
	 * Array that contains configuration data.
         * Its values are overridden in the class constructor.
	 *
	 * @var		array
	 * @access	private
         * @see		User()
	 */
	var $config  = array();
	
	/**
	 * Array containing the possible number of rows per page
         * in result list. Its values are overridden in the class constructor.
	 *
	 * @var		array
	 * @access	private
         * @see		User()
	 */
	var $result_list_rows = array();
	
	/**
	 * Default number of rows per page in result list.
         * Its value is overridden in the class constructor.
	 *
	 * @var		int
	 * @access	private
         * @see		User()
	 */
	var $result_list_def_rows = 20;
	
	/**
	 * Default filename for the exported result list
	 * Its value is overridden in the class constructor.
	 *
         * @var		string
	 * @access	private
         * @see		User()
	 */
	var $result_list_filename = "results";
	
	/**
	 * Default temp directory
	 * Its value is overridden in the class constructor.
         * 
	 * @var		string
	 * @access	private
         * @see		User()
	 */
	var $tmp_dir = "/tmp";
	
	//var $AUTH_ID = "";
	//var $RESPONSE = array();

	/**
	 * Class constructor. No optional parameters.
	 *
	 * usage: User()
	 *
	 * @access	private
	 * @return	void
	 */
	function User()
	{
		global $error_array, $config, $tools, $requests, $request_status, $nav, $messages;
		$this->err_arr = $error_array;
		$this->config = $config;
		$this->tools = $tools;
		$this->requests = $requests;
		$this->request_status = $request_status;
		$this->messages = $messages;
		$this->nav = $nav;
		$this->log = new Log;
		$this->connect = new Connect;		
		$this->nav_main = $this->nav["other"];
		$this->result_list_rows = $config["result_list_rows"];
		$this->result_list_def_rows = $config["result_list_def_rows"];		
		$this->temp_dir = $config["temp_dir"];
	}

	/**
	 * Redirects the function calls after input validation.
         * 
	 * @param	string	$mode
         * @access	public
	 * @return	void
	 */
	function dispatch($mode)
	{
		switch ($mode) {
			case "login":
				$error = $this->validation("login");
				if ($error) {
					$this->login_form();
				} else {
					$this->login();
				}
			break;
		}
	}

	/**
	 * Shows the login form.
	 *
	 * @access    public
	 * @return	void
	 */
	function login_form()
	{
		$this->tools->tpl->parse("BODY", "login_form");
	}

	/**	
	 * Login in the web interface.
         * 
         * on success - go to main screen
         * on failure - back to the login form
	 *
	 * @access	private
	 * @return	void         
         * @see		login_form()
	 */
	function login()
	{
		$fields = array(
				"username"	=> $_SESSION["userdata"]["t_username"],
             			"password"	=> $_SESSION["userdata"]["t_password"]
             			);
		if (	$this->connect->execute_request("login", $fields, $_SESSION["response"], $this->config["no_content"])
			&& $this->connect->set_auth_id($_SESSION["auth-sid"],$_SESSION["response"])) {
			$_SESSION["username"] = $_SESSION["userdata"]["t_username"];
			$this->tools->tpl->set_var("NAV_LINKS","");
			$this->tools->tpl->parse("NAV","navigation");
		} else {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_auth_failed"]["err_msg"]);
			$this->login_form();
		}
	}

	/**
	 * Logs out the user. Terminates the session and goes to the login screen.
	 *
	 * @access    public
	 * @return	void
	 */
	function logout()
	{
             	session_destroy();
		//setcookie( session_name() ,"",0,"/");
		$this->tools->goto();
	}

	/**	
	 * Returns summary of all user requests to the DMAPI server and their status.
         * Take in mind that the request data is extracted once and then saved in the session.
         * Every consequent access to this data is through the session array.
         *
	 * @access	public
	 * @return	void
	 */
	function result_list()
	{
		$this->nav_submain = $this->nav["result_list"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$fields = "";
		if (!isset($_SESSION["userdata"]["request_results"]) || isset($_SESSION["httpvars"]["refresh"])) {
			if (!$this->connect->execute_request("result-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
				$this->tools->tpl->set_block("repository","general_error_box");
				$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);			
			} else {
				$_SESSION["userdata"]["request_results"] = array_reverse($this->tools->parse_text($_SESSION["response"]["response_body"]));
			}
		}
		if (isset($_SESSION["userdata"]["request_results"]) && is_array($_SESSION["userdata"]["request_results"])) {
			//result list page number
			if (!isset($_SESSION["userdata"]["rlp"]) || !is_numeric($_SESSION["userdata"]["rlp"])) {
				$_SESSION["userdata"]["rlp"] = 1;
			}
			$total_num_rows = count($_SESSION["userdata"]["request_results"]);
			//result list number of rows
			if (	!isset($_SESSION["userdata"]["rlnr"]) ||
				!is_numeric($_SESSION["userdata"]["rlnr"]) ||
				!in_array($_SESSION["userdata"]["rlnr"],$this->result_list_rows)) {
				$_SESSION["userdata"]["rlnr"] = $this->result_list_def_rows;
			}
			if ($_SESSION["userdata"]["rlnr"]*$_SESSION["userdata"]["rlp"] > $total_num_rows) {
				$_SESSION["userdata"]["rlp"] = 1;
			}
			$this->tools->tpl->set_block("repository","RESULT_LIST_ROWS","RESULT_LIST_RS");
			$this->tools->tpl->set_block("repository","RESULT_LIST_SEL_ROW","RESULT_LIST_SEL_R");
			foreach ($this->result_list_rows as $val)
			{
				if ($val == $_SESSION["userdata"]["rlnr"]) {
					$this->tools->tpl->set_var("ROWS_OPTION","[".$val."]");
					$this->tools->tpl->parse("RESULT_LIST_ROWS_PER_PAGE","RESULT_LIST_SEL_ROW",true);
				} else {
					$this->tools->tpl->set_var("RLNR",$val);
					$this->tools->tpl->set_var("RLP",$_SESSION["userdata"]["rlp"]);
					$this->tools->tpl->set_var("ROWS_OPTION","[".$val."]");
					$this->tools->tpl->parse("RESULT_LIST_ROWS_PER_PAGE","RESULT_LIST_ROWS",true);
				}				
			}						
			$this->tools->tpl->set_block("repository","RESULT_LIST_PAGES","RESULT_LIST_P");
			$this->tools->tpl->set_block("repository","RESULT_LIST_SEL_PAGES","RESULT_LIST_SEL_P");			
			if ($total_num_rows % $_SESSION["userdata"]["rlnr"] == 0) {
				$total_num_pages = ($total_num_rows/$_SESSION["userdata"]["rlnr"]);				
			} else {				
				$total_num_pages = (int)($total_num_rows/$_SESSION["userdata"]["rlnr"]) + 1;				
			}
			for ($i=1;$i<=$total_num_pages;$i++)
			{		
				if ($i == $_SESSION["userdata"]["rlp"]) {
					$this->tools->tpl->set_var("PAGE_OPTION",$i);					
					$this->tools->tpl->parse("RESULT_LIST_NUM_PAGES","RESULT_LIST_SEL_PAGES",true);					
				} else {
					$this->tools->tpl->set_var("RLNR",$_SESSION["userdata"]["rlnr"]);
					$this->tools->tpl->set_var("RLP",$i);
					$this->tools->tpl->set_var("PAGE_OPTION",$i);
					$this->tools->tpl->parse("RESULT_LIST_NUM_PAGES","RESULT_LIST_PAGES",true);
				}			
			}
			$this->tools->tpl->set_block("result_list","result_row","res_row");			
			$max_idx = ($_SESSION["userdata"]["rlp"] * $_SESSION["userdata"]["rlnr"] > $total_num_rows) ? $total_num_rows : $_SESSION["userdata"]["rlp"] * $_SESSION["userdata"]["rlnr"];
			$min_idx = (($max_idx - $_SESSION["userdata"]["rlnr"]) > 0) ? $max_idx - $_SESSION["userdata"]["rlnr"] : 0;			
			for ($i=$min_idx;$i<$max_idx;$i++)
			{
				$val = $_SESSION["userdata"]["request_results"][$i];
				$year = substr($val["0"],0,4);
				$month = substr($val["0"],4,2);
				$day = substr($val["0"],6,2);
				$hour = substr($val["0"],8,2);
				$min = substr($val["0"],10,2);
				$sec = substr($val["0"],12,2);				 
				$this->tools->tpl->set_var(array(
				"TIMESTAMP"	=> date("m/d/y H:i:s",mktime($hour,$min,$sec,$month,$day,$year)),
				//"SVTRID"	=> $val["1"],
				"PROC_ID"	=> $val["2"],
				"REQUEST_TYPE"	=> (is_array($this->requests[$val["3"]]) ? $this->requests[$val["3"]]["text"] : $this->requests["unknown"]["text"]),
				"REQUEST_OBJECT"=> $val["4"],
				"STATUS"	=> (is_array($this->request_status[$val["5"]]) ? $this->request_status[$val["5"]]["text"] : $this->request_status["unknown"]["text"]),
				"CLTRID"	=> $val["6"],
				));
			    $this->tools->tpl->parse("res_row", "result_row",true);
			}
			$this->tools->tpl->parse("CONTENT", "result_list");
		}
	}

	/**	
	 * Deletes all available user requests from the session array
         *
	 * @access	public
	 * @return	void
         * @see		result_list()
         * @see		result_delete()
	 */
	function empty_result_list()
	{
		if (isset($_SESSION["userdata"]["request_results"]) && is_array($_SESSION["userdata"]["request_results"])) {
			$req_status = true;
			foreach ($_SESSION["userdata"]["request_results"] as $val)
			{
				if (!$this->result_delete($val["1"])) {
					$req_status = false;
				}
			}
			if (!$req_status) {
				$this->tools->show_request_status($this->messages["_request_partial_success"],false,false);
			} else {
				$this->tools->show_request_status($this->messages["_request_successful"],false,false);
			}
		}
		//hack for cleaning the result array in the session
		$_SESSION["httpvars"]["refresh"] = "";
		$this->result_list();	
	}

	/**	
	 * Deletes a record from the result list based on its SvTrId
         *
         * @param	string	$svtrid	server tracking id
	 * @access	public
	 * @return	boolean       
         * @see		empty_result_list()
	 */
	function result_delete($svtrid)
	{
		$fields = array(
			"SvTrId" => $svtrid,			
             		);
		return $this->connect->execute_request("result-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"]);
	}

	/**
	 * Exports the result list into file with user specified filetype
         *
         * @param	string	$filetype	e.g. csv, xsl etc.
	 * @access	public
	 * @return	void
	 */
	function result_export($filetype)
	{
		switch (strtolower(trim($filetype)))
		{
			case "csv":
				$testarr = array(
						array('madonna', 'pop', 'usa'), 
						array('alanis morisette', 'rock', 'canada'), 
						array('falco', 'pop', 'austria'), 
				);
				$docroot = $GLOBALS["HTTP_SERVER_VARS"]["DOCUMENT_ROOT"];
				clearstatcache();
				if (!is_dir($docroot."/".$this->temp_dir)) {					
					mkdir($docroot."/".$this->temp_dir,0775);
				} else {					
					chmod($docroot."/".$this->temp_dir,0775);
				}
				$path = $docroot."/".$this->temp_dir."/";
				$sub_dir = md5($_SESSION["username"].rand(1,99999));				
				if (mkdir($path.$sub_dir, 0775)) {
					$csv = new Bs_CsvUtil;
					//could lead to slow down - dunno how big is the result list array
                                        $text[] = $csv->arrayToCsvString(array("TIMESTAMP","SVTRID","REQUEST TYPE","REQUEST OBJECT","STATUS","PROC ID","CLTRID"));
					foreach ($_SESSION["userdata"]["request_results"] as $val)
					{
						$year = substr($val["0"],0,4);
						$month = substr($val["0"],4,2);
						$day = substr($val["0"],6,2);
						$hour = substr($val["0"],8,2);
						$min = substr($val["0"],10,2);
						$sec = substr($val["0"],12,2);				 
						$row_arr = array(
							date("m/d/y H:i:s",mktime($hour,$min,$sec,$month,$day,$year)),
							$val["1"],							
							(is_array($this->requests[$val["3"]]) ? $this->requests[$val["3"]]["text"] : $this->requests["unknown"]["text"]),
							$val["4"],
							(is_array($this->request_status[$val["5"]]) ? $this->request_status[$val["5"]]["text"] : $this->request_status["unknown"]["text"]),
							$val["6"],
							$val["2"]
						);
						$text[] = $csv->arrayToCsvString($row_arr);					
					}
					$text = implode("\n",$text);
					
					$path_to_file = $path.$sub_dir."/".$this->result_list_filename . ".csv";					
					touch($path_to_file);
					if (is_writable($path_to_file)) {
						if (!$fp = fopen($path_to_file, 'a')) {
							$this->log->req_status("e", "function result_export($filetype): Cannot open file for writing ($path_to_file)");
							exit;
						}
						if (fwrite($fp, $text) === FALSE) {
							$this->log->req_status("e", "function result_export($filetype): Cannot write file ($path_to_file)");							
							exit;
						}
						fclose($fp);
					} else {
						$this->log->req_status("e", "function result_export($filetype): The file $path_to_file is not writable");						
					}                                        
					header("Pragma: ");
					header("Cache-Control: ");
					header('Content-type: application/octet-stream');
					header("Content-Length: " . strlen($text));
					header('Content-Disposition: attachment; filename="'.trim($this->result_list_filename.".csv").'"');
					if (!$fp = fopen($path_to_file, "rb")) {
						$this->log->req_status("e", "function result_export($filetype): Cannot open file for reading($path_to_file)");
						exit;
					}                                        
					fpassthru($fp);
					fclose($fp);
					exit;
                                }
				break;
			
			default:
				$this->result_list();
				break;
		}
	}	

	/**
	 * Shows the user profile
         *
	 * @access	public
	 * @return	void
	 */
	function query_profile()
	{
		$this->nav_submain = $this->nav["query_profile"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
	    $fields = "";
	    if ($this->connect->execute_request("query-profile", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
		$result = $this->tools->parse_text($_SESSION["response"]["response_body"],true);
	    }	    
	    if ($result != $this->config["empty_result"] && is_array($result)) {
		    $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_submit_btn");
		    $this->tools->tpl->set_block("repository","result_table_row");
		    $this->tools->tpl->set_block("repository","result_table");
		    foreach($result as $value)
		    {
			$this->tools->tpl->set_var(array(
				"FIELD1"	=> $value["0"],
				"FIELD2"	=> $value["1"],
				));
			$this->tools->tpl->parse("FORMTABLEROWS", "result_table_row",true);
		    }
		    $this->tools->tpl->parse("CONTENT", "result_table");
	    } else {
		$this->tools->tpl->set_block("repository","general_error_box");
		$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
	        $this->empty_content();
	    }
	}

	/**
	 * Shows tips for using the interface
         *
	 * @access	public
	 * @return	void
	 */
	function tips()
	{
		$this->nav_submain = $this->nav["tips"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->parse("CONTENT", "tips");
	}

	/**
	 * Parses empty content
         *
	 * @access	public
	 * @return	void
	 */
	function empty_content()
	{		
		$this->tools->tpl->set_var("CONTENT", "");
	}

	/**	
	 * Main validation method. Validation rules for every mode
         * 
         * on success - returns true
         * on failure - returns false
	 *
	 * @access	private
	 * @return	boolean      
         * @see		dispatch()
	 */
	function validation($mode)
	{
	    	$this->tools->tpl->set_block("repository","general_error_box");
		$this->tools->tpl->set_block("repository","field_error_box");
		$error = false;
		switch ($mode) {
			case "login":
			    if (!$this->tools->is_valid($this->err_arr["_username"]["regexp"],$_SESSION["httpvars"]["t_username"])) {
			    	$error = true;
			    	$this->tools->field_err("ERROR_INVALID_USERNAME",$this->err_arr["_username"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid($this->err_arr["_password"]["regexp"],$_SESSION["httpvars"]["t_password"])) {
			    	$error = true;
			    	$this->tools->field_err("ERROR_INVALID_PASSWORD",$this->err_arr["_password"]["err_msg"]);
			    }
			    break;			    
		}
		return $error;
	}
}

?>