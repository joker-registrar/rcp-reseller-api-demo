<?php

/**
 * Domain management related class. Handles visualization and request handling
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class Domain
{
	/**
	 * Represents the uppermost level of the current user position.
         * Its value is usually set in the class constructor.
	 *
	 * @var		string
	 * @access	private
         * @see		Domain()
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
         * @see		Domain(), Tools::is_valid(), Tools::is_valid_contact_hdl()
	 */
	var $err_arr  = array();

	/**
	 * Array that contains configuration data.
         * Its values are overridden in the class constructor.
	 *
	 * @var		array
	 * @access	private
         * @see		Domain()
	 */
	var $config  = array();
	
	/**
	 * Class constructor. No optional parameters.
	 *
	 * usage: Domain()
	 *
	 * @access	private
	 * @return	void
	 */
	function Domain()
	{
		global $error_array, $config, $tools, $messages, $nav;
		$this->config = $config;
		$this->err_arr = $error_array;		
		$this->tools = $tools;
		$this->msg = $messages;
		$this->nav = $nav;
		$this->connect = new Connect;
		$this->nav_main = $this->nav["domain"];		
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

			case "view":
				$error = $this->validation("view");
				if ($error) {					
					$this->view_form();
				} else {
					$this->view();
				}
			break;

			case "register":
				$error = $this->validation("register");
				if ($error) {					
					$this->register_form();
				} else {
					$this->register();
				}
			break;
		
			case "renew":
				$error = $this->validation("renew");
				if ($error) {
					$this->renew_form();
				} else {
					$this->renew();
				}
			break;
		
			case "transfer":
				$error = $this->validation("transfer");
				if ($error) {
					$this->transfer_form();
				} else {
					$this->transfer();
				}
			break;
		
			case "modify":
				$error = $this->validation("modify");
				if ($error) {
					$this->modify_form();
				} else {
					$this->modify();
				}
			break;
		
			case "delete":
				$error = $this->validation("delete");
				if ($error) {					
					$this->delete_form();
				} else {
					$this->delete();
				}
			break;
		
			case "owner_change":
				$error = $this->validation("owner_change");
				if ($error) {
					$this->owner_change_form();
				} else {
					$this->owner_change();
				}
			break;

			case "lock_unlock":
				$error = $this->validation("lock_unlock");
				if ($error) {
					$this->lock_unlock_form();
				} else {
					$this->lock_unlock();
				}
			break;

			case "redemption":
				$error = $this->validation("redemption");
				if ($error) {
					$this->redemption_form();
				} else {
					$this->redemption();
				}
			break;

			case "list_result":
				$this->list_result();
			break;
		}
	}

	/**
	 * Shows domain view form
	 *
	 * @access    public
	 * @return	void
	 */
	function view_form()
	{
		$this->nav_submain = $this->nav["view_info"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->parse("CONTENT","domain_view_form");
	}

	/**
	 * Returns information about a domain.
         * 
         * on success - visualizes domain data
         * on failure - error message
	 *
	 * @access	private
	 * @return	void
         * @see		view_form()
	 */
	function view()
	{
		$this->nav_submain = $this->nav["view_info"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->set_block("repository","result_table_row","result_table_r");
		$this->tools->tpl->set_block("repository","std_result_table","std_result_tbl");
		
		$result = $this->tools->query_object("domain",$_SESSION["userdata"]["t_domain"]);
		if ($result) {			
			foreach ($result as $val)
			{
				$field_value = "";
				$arr = explode(".",$val["0"]);				
				//skip the first element
                                $arr = array_reverse($arr);
				array_pop($arr);
                                $field_name = implode(" ",array_reverse($arr));
				$this->tools->tpl->set_var("FIELD1",$field_name);
				$cnt = count($val);
				for ($i=1;$i<$cnt;$i++)
				{
					$field_value .= $val[$i]." ";
				}				
				$this->tools->tpl->set_var("FIELD2",$field_value);
				$this->tools->tpl->parse("FORMTABLEROWS","result_table_row",true);
			}			
			$this->tools->tpl->parse("CONTENT","std_result_table");
		} else {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
		}
	}
	
	/**
	 * Shows domain registration form
	 *
	 * @access    public
	 * @return	void
	 */
	function register_form()
	{
		$this->nav_submain = $this->nav["registration"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		if (!isset($_SESSION["httpvars"]["c_all_as_owner"])) {
			$this->tools->tpl->set_var("C_ALL_AS_OWNER","");
			unset($_SESSION["userdata"]["c_all_as_owner"]);
			unset($_SESSION["formdata"]["c_all_as_owner"]);		
		}
		if (!isset($_SESSION["formdata"]["r_ns_type"])) {
			$this->tools->tpl->set_var("R_NS_TYPE_DEFAULT","checked");			
		}
		$this->tools->tpl->set_block("repository","reg_period_menu","reg_period_mn");
		$this->tools->tpl->parse("DOMAIN_REG_PERIOD","reg_period_menu");
		$this->tools->tpl->parse("CONTENT","domain_register_form");

	}

	/**
	 * Registers a domain. Asynchronous request - the final status of this request
         * should be checked with result_list()
         * 
         * on success - success status message
         * on failure - back to the domain registration form
	 *
	 * @access	private
	 * @return	void
         * @see		User::result_list()
         * @see		register_form()
	 */
	function register()
	{
		$this->nav_submain = $this->nav["registration"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		switch (strtolower($_SESSION["userdata"]["r_ns_type"]))
		{
			case "default":				
                                foreach ($this->config["ns_joker_default"] as $value)
				{
					$str[] = $value["host"];
				}
				$ns_str = implode(":",$str);
				break;
				
			case "own":				
				foreach ($_SESSION["userdata"] as $key => $value)
				{
					if (preg_match("/^t_ns/i",$key)) {											
						$str[] = $value;
					}
				}
				$ns_str = implode(":",$str);
				break;
		}
		$fields = array(
			"domain"	=> $_SESSION["userdata"]["t_domain"],
			"period"	=> ($this->config["max_reg_period"] > $_SESSION["userdata"]["s_reg_period"]) ? $_SESSION["userdata"]["s_reg_period"]*12 : $this->config["max_reg_period"]*12,
			"status"	=> "production",
			"owner-c"	=> $_SESSION["userdata"]["t_contact_owner"],
			"billing-c"	=> (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_billing"],
			"admin-c"	=> (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_admin"],
			"tech-c"	=> (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_tech"],
			"ns-list"	=> $ns_str
             		);
		if (!$this->connect->execute_request("domain-register", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->register_form();
		} else {
			unset($_SESSION["userdata"]["c_all_as_owner"]);
			unset($_SESSION["formdata"]["c_all_as_owner"]);				
			$this->tools->show_request_status();
		}
	}

	/**
	 * Shows domain renewal form
	 *
	 * @access    public
	 * @return	void
	 */
	function renew_form()
	{
		$this->nav_submain = $this->nav["renewal"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->set_block("repository","reg_period_menu","reg_period_mn");
		$this->tools->tpl->parse("DOMAIN_REG_PERIOD","reg_period_menu");
		$this->tools->tpl->parse("CONTENT", "domain_renew_form");
	}

	/**
	 * Renewal of a domain. Asynchronous request - the final status of this request
         * should be checked with result_list()
         * 
         * on success - success status message
         * on failure - back to the domain renewal form
	 *
	 * @access	private
	 * @return	void
         * @see		User::result_list()
         * @see		register_form()
	 */	
	function renew()
	{
		$this->nav_submain = $this->nav["renewal"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$fields = array(
			"domain"	=> $_SESSION["userdata"]["t_domain"],
			"period"	=> ($this->config["max_reg_period"] > $_SESSION["userdata"]["s_reg_period"]) ? $_SESSION["userdata"]["s_reg_period"]*12 : $this->config["max_reg_period"]*12,
             		);
		if (!$this->connect->execute_request("domain-renew", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->renew_form();
		} else {			
			$this->tools->show_request_status();
		}
	}

	/**
	 * Shows domain transfer form
	 *
	 * @access    public
	 * @return	void
	 */
	function transfer_form()
	{
		$this->nav_submain = $this->nav["transfer"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->parse("CONTENT", "domain_transfer_form");
	}

	/**
	 * Transfer of a domain. Asynchronous request - the final status of this request
         * should be checked with result_list()
         * 
         * on success - success status message
         * on failure - back to the domain transfer form
	 *
	 * @access	private
	 * @return	void
         * @see		User::result_list()
         * @see		transfer_form()
	 */	
	function transfer()
	{
		$this->nav_submain = $this->nav["transfer"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$fields = array(
			"domain"	=> $_SESSION["userdata"]["t_domain"],
			"transfer-auth-id" => $_SESSION["userdata"]["t_auth_id"],
			"billing-c"	=> $_SESSION["userdata"]["t_contact_billing"],
             		);
		if (!$this->connect->execute_request("domain-transfer-in", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->transfer_form();
		} else {
			$this->tools->show_request_status();
		}
	}

	/**
	 * Shows domain transfer form
	 *
	 * @access    public
	 * @return	void
	 */	
	function modify_form()
	{
		$this->nav_submain = $this->nav["modification"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->parse("CONTENT", "domain_modify_form");
	}

	/**
	 * Modification of a domain. Asynchronous request - the final status of this request
         * should be checked with result_list()
         * 
         * on success - success status message
         * on failure - back to the domain modification form
	 *
	 * @access	private
	 * @return	void
         * @see		User::result_list()
         * @see		modify_form()
	 */
	function modify()
	{
		$this->nav_submain = $this->nav["modification"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$fields = array(
			"domain"	=> $_SESSION["userdata"]["t_domain"],			
			"billing-c"	=> $_SESSION["userdata"]["t_contact_billing"],
			"admin-c"	=> $_SESSION["userdata"]["t_contact_admin"],
			"tech-c"	=> $_SESSION["userdata"]["t_contact_tech"],
			"ns-list"	=> $_SESSION["userdata"]["t_nameserver_list"]
             		);
		if (!$this->connect->execute_request("domain-modify", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->modify_form();
		} else {
			$this->tools->show_request_status();
		}
	}

	/**
	 * Shows domain deletion form
	 *
	 * @access    public
	 * @return	void
	 */
	function delete_form()
	{
		$this->nav_submain = $this->nav["deletion"];	
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		if (!isset($_SESSION["httpvars"]["c_force_del"])) {
			$this->tools->tpl->set_var("C_FORCE_DEL","");
			unset($_SESSION["userdata"]["c_force_del"]);
			unset($_SESSION["formdata"]["c_force_del"]);
		}
		$this->tools->tpl->parse("CONTENT","domain_delete_form");
	}

	/**
	 * Deletion of a domain. Asynchronous request - the final status of this request
         * should be checked with result_list()
         * 
         * on success - success status message
         * on failure - back to the domain deletion form
	 *
	 * @access	private
	 * @return	void
         * @see		User::result_list()
         * @see		delete_form()
	 */
	function delete()
	{
		$this->nav_submain = $this->nav["deletion"];			
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$fields = array(
			"domain"	=> $_SESSION["userdata"]["t_domain"],
             		);
		if (isset($_SESSION["userdata"]["c_force_del"]) && strtolower($_SESSION["userdata"]["c_force_del"]) == "y") {
			$fields["force"] = 1;
		}		
		if (!$this->connect->execute_request("domain-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->delete_form();
		} else {			
			unset($_SESSION["userdata"]["c_force_del"]);
			unset($_SESSION["formdata"]["c_force_del"]);
			$this->tools->show_request_status();
		}
	}

	/**
	 * Shows domain owner change form. Still under construction
	 *
	 * @access    public
	 * @return	void
	 */
	function owner_change_form()
	{
		$this->tools->tpl->parse("CONTENT", "domain_owner_change_form");
	}

	/**
	 * Still under construction!!!
	 * Owner change procedure of a domain. Asynchronous request - the final status of this request
         * should be checked with result_list()
         * 
         * on success - success status message
         * on failure - back to the domain owner change form
	 *
	 * @access	private
	 * @return	void
         * @see		User::result_list()
         * @see		owner_change_form()
	 */
	function owner_change()
	{		
		$fields = array(
			"domain"	=> $_SESSION["userdata"]["t_domain"],
             		);
		if (isset($_SESSION["userdata"]["c_force_del"]) && strtolower($_SESSION["userdata"]["c_force_del"]) == "y") {
			$fields["force"] = 1;
		}		
		if (!$this->connect->execute_request("domain-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->delete_form();
		} else {
			unset($_SESSION["userdata"]["c_force_del"]);
			unset($_SESSION["formdata"]["c_force_del"]);
			$this->tools->show_request_status();
		}
	}

	/**
	 * Shows a domain lock/unlock form.
	 *
	 * @access    public
	 * @return	void
	 */
	function lock_unlock_form()
	{
		$this->nav_submain = $this->nav["lock_unlock"];
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		if (!isset($_SESSION["formdata"]["r_lock"])) {
			$this->tools->tpl->set_var("R_LOCK_LOCK","checked");			
		}		
		$this->tools->tpl->parse("CONTENT", "domain_lock_unlock_form");
	}

	/**	
	 * Lock/unlock of a domain. Asynchronous request - the final status of this request
         * should be checked with result_list()
         * 
         * on success - success status message
         * on failure - back to the domain owner change form
	 *
	 * @access	private
	 * @return	void
         * @see		User::result_list()
         * @see		lock_unlock_form()
	 */
	function lock_unlock()
	{
		$this->nav_submain = $this->nav["lock_unlock"];
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$fields = array(
			"domain"	=> $_SESSION["userdata"]["t_domain"],			
             		);
		switch (strtolower($_SESSION["userdata"]["r_lock"]))
		{
			case "lock":
				$status = $this->connect->execute_request("domain-lock", $fields, $_SESSION["response"], $_SESSION["auth-sid"]);
				break;
			case "unlock":
				$status = $this->connect->execute_request("domain-unlock", $fields, $_SESSION["response"], $_SESSION["auth-sid"]);
				break;
		}
		if (!$status) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->lock_unlock_form();
		} else {			
			$this->tools->show_request_status();
		}
	}

	/**
	 * Shows a redemption form.
	 *
	 * @access    public
	 * @return	void
	 */
	function redemption_form()
	{
		$this->nav_submain = $this->nav["redemption"];
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->parse("CONTENT", "domain_redemption_form");
	}

	/**	
	 * Sends an email to initialize redemption. Not related to DMAPI in any way.
	 *
	 * @access	private
	 * @return	void
         * @see		redemption_form()
	 */
	function redemption()
	{
		$this->nav_submain = $this->nav["redemption"];
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$email_body = "Request from user: ".$_SESSION["username"]."\n";
		$email_body .= "Domain in question: ".$_SESSION["userdata"]["t_domain"]."\n";
		$email_body .= "Additional information: ".(empty($_SESSION["userdata"]["t_add_info"]) ? $this->config["no_content"] : $_SESSION["userdata"]["t_add_info"])."\n";		
		if ($this->tools->send_mail($this->config["redemption_email"],$this->config["dmapi_mp_email"],$this->config["dmapi_mp_email"],"","Redemption request - DMAPI",$email_body,"","","")) {	
			$this->tools->tpl->set_block("repository","general_success_box");
			$this->tools->tpl->set_var("STATUS_MSG",$this->msg["_request_sent"]);
			$this->tools->tpl->parse("CONTENT","general_success_box");
		} else {
			//not needed - see validation()
			//$this->tools->tpl->set_block("repository","general_error_box");
			$this->tools->tpl->set_var("ERROR_MSG",$this->msg["_request_not_sent"]." ".$this->msg["_error_check_logs"]);
			$this->tools->tpl->parse("CONTENT","general_error_box");
		}
	}
	
	/**
	 * Shows a form allowing you to customize the returned list of domains.
	 *
	 * @access    public
	 * @return	void
	 */
	function list_form()
	{
		$this->nav_submain = $this->nav["domain_list"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		$this->tools->tpl->set_var("MODE","domain_list_result");
		$this->tools->tpl->parse("CONTENT","domain_list_form");
	}

	/**	
	 * Returns a domain list. 
         * 
         * on success - returns a domain list
         * on failure - back to the domain list form
	 *
	 * @access	private
	 * @return	void      
         * @see		list_form()
	 */
	function list_result()
	{
		$this->nav_submain = $this->nav["domain_list"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$this->tools->tpl->set_block("domain_repository","result_list_table");
		$result = $this->tools->domain_list($_SESSION["userdata"]["t_pattern"]);
		if ($result) {			
			if ($result != $this->config["empty_result"] && is_array($result)) {
				$this->tools->tpl->set_block("domain_repository","result_list_row");
				foreach($result as $value)
				{
					$this->tools->tpl->set_var(array(
						"DOMAIN"	=> $value["0"],
						"EXPIRATION"	=> $value["1"],
					));
					$this->tools->tpl->parse("RESULT_LIST", "result_list_row",true);
				}
				$this->tools->tpl->parse("CONTENT", "result_list_table");
			} else {
				$this->tools->tpl->set_block("domain_repository","no_result_row");
				$this->tools->tpl->set_var("NO_RESULT_MESSAGE",$this->msg["_no_result_message"]);
				$this->tools->tpl->parse("RESULT_LIST","no_result_row",true);
				$this->tools->tpl->parse("CONTENT","result_list_table");
			}
		} else {
		    $this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
		    $this->list_form();
		}
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

			case "view":
			    if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_arr["_domain"]["err_msg"]);
			    }
			    break;
			
			case "register":
			    if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_arr["_domain"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid($this->err_arr["_domain_reg_period"]["regexp"],$_SESSION["httpvars"]["s_reg_period"])) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_REG_PERIOD",$this->err_arr["_domain_reg_period"]["err_msg"]);
			    }
			    $dom_arr = $this->tools->get_domain_part($_SESSION["httpvars"]["t_domain"]);			    
			    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_owner"],$dom_arr["tld"])) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_OWNER_CONTACT",$this->err_arr["_contact_hdl"]["err_msg"]." ".$this->err_arr["_contact_hdl_type"]["err_msg"]);
			    }
			    if ($_SESSION["httpvars"]["c_all_as_owner"] != "all") {
				if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_billing"],$dom_arr["tld"])) {
					$error = true;
					$this->tools->field_err("ERROR_INVALID_BILLING_CONTACT",$this->err_arr["_contact_hdl"]["err_msg"]." ".$this->err_arr["_contact_hdl_type"]["err_msg"]);
				}
				if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_admin"],$dom_arr["tld"])) {
					$error = true;
					$this->tools->field_err("ERROR_INVALID_ADMIN_CONTACT",$this->err_arr["_contact_hdl"]["err_msg"]." ".$this->err_arr["_contact_hdl_type"]["err_msg"]);
				}
				if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_tech"],$dom_arr["tld"])) {
					$error = true;
					$this->tools->field_err("ERROR_INVALID_TECH_CONTACT",$this->err_arr["_contact_hdl"]["err_msg"]." ".$this->err_arr["_contact_hdl_type"]["err_msg"]);
				}
			    }
			    switch (strtolower($_SESSION["httpvars"]["r_ns_type"]))
			    {
				case "default":
					//ok
					break;
				
				case "own":
					$ns_count = 0;					
					foreach ($_SESSION["httpvars"] as $key => $value)
					{
						if (preg_match("/^t_ns/i",$key)) {					
							if ($this->tools->is_valid("host",$value,true)) {
								$ns_count++;
							} elseif ($value != "") {								
								$error = true;
								$this->tools->field_err("ERROR_INVALID_NSRV_LIST",$this->err_arr["_ns"]["err_msg"]);
								
							}
						}
						
					}					
					if (!$error && $ns_count < $this->config["ns_min_num"]) {
						$error = true;						
						$this->tools->field_err("ERROR_INVALID_NSRV_LIST",$this->err_arr["_ns_min"]["err_msg"]);
						$this->tools->tpl->set_var("NS_MIN_NUM",$this->config["ns_min_num"]);
					}
					break;
				
				default:
					$this->tools->field_err("ERROR_INVALID_NSRV_SELECT",$this->err_arr["_ns_select"]["err_msg"]);
					$error = true;
					break;
			    }
			    break;
			
			case "renew":
			    if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_arr["_domain"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid($this->err_arr["_domain_reg_period"]["regexp"],$_SESSION["httpvars"]["s_reg_period"])) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_REG_PERIOD",$this->err_arr["_domain_reg_period"]["err_msg"]);
			    }
			    break;
			
			case "transfer":				
			    if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_arr["_domain"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid($this->err_arr["_auth_id"]["regexp"],$_SESSION["httpvars"]["t_auth_id"])) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_AUTH_ID",$this->err_arr["_auth_id"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_billing"])) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_BILLING_CONTACT",$this->err_arr["_contact_hdl"]["err_msg"]);
			    }			    
			    break;
			
			case "modify":
			    if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_arr["_domain"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_billing"]) && !empty($_SESSION["httpvars"]["t_contact_billing"])) {
			    	$error = true;
				$this->tools->field_err("ERROR_INVALID_BILLING_CONTACT",$this->err_arr["_contact_hdl"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_admin"]) && !empty($_SESSION["httpvars"]["t_contact_admin"])) {
			    	$error = true;
				$this->tools->field_err("ERROR_INVALID_ADMIN_CONTACT",$this->err_arr["_contact_hdl"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_tech"]) && !empty($_SESSION["httpvars"]["t_contact_tech"])) {
			    	$error = true;
				$this->tools->field_err("ERROR_INVALID_TECH_CONTACT",$this->err_arr["_contact_hdl"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid("ns_list",$_SESSION["httpvars"]["t_nameserver_list"],true) && !empty($_SESSION["httpvars"]["t_nameserver_list"])) {
			    	$error = true;
			    	$this->tools->field_err("ERROR_INVALID_NSRV_LIST",$this->err_arr["_nameserver_list"]["err_msg"]);
			    }
			    break;
			
			case "delete":
			    if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_arr["_domain"]["err_msg"]);
			    }			    
			    break;
			
			case "lock_unlock":			
			    if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_arr["_domain"]["err_msg"]);
			    }
			    switch (strtolower($_SESSION["httpvars"]["r_lock"]))
			    {
				case "lock":
				case "unlock":
					//ok
					break;
				default:
					$this->tools->field_err("ERROR_INVALID_LOCK_UNLOCK_OPT",$this->err_arr["_dom_status"]["err_msg"]);
					$error = true;
					break;
			    }
			    break;
			
			case "redemption":			
			    if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_arr["_domain"]["err_msg"]);
			    }
		}
		return $error;
	}
}

?>