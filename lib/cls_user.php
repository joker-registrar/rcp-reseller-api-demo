<?php

class User
{
	/**
	 * Represents the current position of the user
         * uppermost level
	 *
	 * @var		string
	 * @access	private
	 */
	var $nav_main  = "";
	
	/**
	 * Represents the current position of the user
         * the next lower level from the uppermost one
	 *
	 * @var		string
	 * @access	private
	 */
	var $nav_submain  = "";

	/**
	 * Array that contains error regular expressions and
         * error messages. Note that not all validation is
         * handled with information from it. Take a look at
         * cls_tools.php and the "is_valid" style functions
	 *
	 * @var		array
	 * @access	private
	 */
	var $err_arr  = array();

	/**
	 * Array that contains configuration data
	 *
	 * @var		array
	 * @access	private
	 */
	var $config  = array();

	var $AUTH_ID = "";

	var $RESPONSE = array();

	
	/******************************************************************************
	 * Class constructor. No optional parameters.
	 *
	 * usage: User()
	 *
	 * @access	private
	 * @return	void
	 */
	function User()
	{
		global $error_array, $config, $tools, $requests, $request_status, $nav;
		$this->err_arr = $error_array;
		$this->config = $config;
		$this->tools = $tools;
		$this->requests = $requests;
		$this->request_status = $request_status;
		$this->nav = $nav;		
		$this->connect = new Connect;		
		$this->nav_main = $this->nav["other"];		
	}

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
		    
			case "result_delete":
				$error = $this->validation("result_delete");
				if (!$error) {
					$this->result_delete();
				}
			break;
		}
	}

	function login_form()
	{
		$this->tools->tpl->parse("BODY", "login_form");
	}

	function login()
	{
		$fields = array(
				"username"	=> $_SESSION["userdata"]["t_username"],
             			"password"	=> $_SESSION["userdata"]["t_password"]
             			);
		if ($this->connect->execute_request("login", $fields, $_SESSION["response"], $this->config["no_content"]) && $this->connect->has_auth_id($_SESSION["auth-sid"],$_SESSION["response"])) {
			$_SESSION["username"] = $_SESSION["userdata"]["t_username"];
			$this->tools->tpl->set_var("NAV_LINKS","");
			$this->tools->tpl->parse("NAV","navigation");
		} else {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_auth_failed"]["err_msg"]);
			$this->login_form();
		}
	}

	function logout()
	{
             	session_destroy();
		//setcookie( session_name() ,"",0,"/");
		$this->tools->goto();
	}

	function result_list()
	{
		$this->nav_submain = $this->nav["result_list"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$fields = "";
		if (!$this->connect->execute_request("result-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->tpl->set_block("repository","general_error_box");
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);			
		} else {
		    $result = $this->tools->parse_text($_SESSION["response"]["response_body"]);		
		    if (is_array($result)) {
			$result = array_reverse($result);
			$this->tools->tpl->set_block("result_list","result_row","res_row");
			foreach ($result as $val) {				
				 $year = substr($val["0"],0,4);
				 $month = substr($val["0"],4,2);
				 $day = substr($val["0"],6,2);
				 $hour = substr($val["0"],8,2);
				 $min = substr($val["0"],10,2);
				 $sec = substr($val["0"],12,2);				 
			    $this->tools->tpl->set_var(array(
				"TIMESTAMP"	=> date("m/d/y H:i:s",mktime($hour,$min,$sec,$month,$day,$year)),
				"SVTRID"	=> $val["1"],
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
	}

	function result_delete()
	{
		$fields = array(
			"SvTrId"	=> $_SESSION["userdata"]["t_svtrid"],			
             		);
		if (!$this->connect->execute_request("result-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->empty_content();
		} else {
			$this->result_list();
		}
	}
	
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

	function tips()
	{
		$this->nav_submain = $this->nav["tips"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->parse("CONTENT", "tips");
	}
	
	function empty_content()
	{		
		$this->tools->tpl->set_var("CONTENT", "");
	}
	
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
			
			case "result_delete":
			    if (!$this->tools->is_valid($this->err_arr["_svtrid"]["regexp"],$_SESSION["httpvars"]["t_svtrid"])) {
			    	$error = true;				
				$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_svtrid"]["err_msg"]);
			    }
			    break;
			    
		}
		return $error;
	}
}

?>