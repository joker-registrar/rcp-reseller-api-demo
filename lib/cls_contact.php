<?php

/*
 * Class Contact contains all contact related implementations.
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright 
 */

class Contact
{
	/**
	 * Represents the uppermost level of the current user position.
         * Its value is usually set in the class constructor.
	 *
	 * @var		string
	 * @access	private
         * @see		Contact()
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
	 * Class constructor. No optional parameters.
	 *
	 * usage: Contact()
	 *
	 * @access	private
	 * @return	void
	 */
	function Contact()
	{
		global $error_array, $config, $tools, $nav;
		$this->err_arr = $error_array;
		$this->config = $config;
		$this->tools = $tools;
		$this->nav = $nav;
		$this->connect = new Connect;
		$this->user = new User;
		$this->log = new Log;
		$this->nav_main = $this->nav["contacts"];
	}

	/**
	 * Redirects the function calls after input validation.
         * 
	 * @param	$mode
         * @access	public
	 * @return	void
	 */
	function dispatch($mode)
	{
		switch ($mode) {

			case "contact_list_result":
				$error = $this->validation("contact_list_result");
				if ($error) {
					$this->contact_list_form();
				} else {
					$this->contact_list_result();
				}
			break;

			case "contact_form":
				$error = $this->validation("contact_form");
				switch ($_SESSION["userdata"]["op"]) {

					case "create_contact":
						if ($error) {
							$this->contact_select_tld_form();
						} else {
							if (isset($_SESSION["httpvars"]["c_opt_fields"])) {
								$opt_fields = true;
							} else {
								$opt_fields = false;
								unset($_SESSION["userdata"]["c_opt_fields"]);
								unset($_SESSION["formdata"]["c_opt_fields"]);
							}
							$this->contact_form($_SESSION["httpvars"]["s_tld"],$opt_fields);
						}
						break;

					case "modify_contact":
						if ($error) {
							$this->contact_select_tld_form();
						} else {
							$this->contact_form($_SESSION["httpvars"]["cnt_hdl"],true);
						}
						break;

					default:
						$this->log->req_status("e", "function dispatch() in case: \"contact_form\": Unknown op type: ".$_SESSION["userdata"]["op"]);
						return;
						break;
				}
			break;

			case "contact_create":
				$error = $this->validation("contact_submission");
				if ($error) {
					$this->contact_form($_SESSION["userdata"]["s_tld"],$_SESSION["userdata"]["c_opt_fields"]);
				} else {
					$this->contact_create();
				}
			break;

			case "contact_modify":
				$error = $this->validation("contact_submission");
				if ($error) {
					$this->contact_form($_SESSION["userdata"]["cnt_hdl"],true);
				} else {
					$this->contact_modify();
				}
			break;

			case "contact_delete":
				$error = $this->validation("contact_delete");
				if ($error) {
					$this->contact_list_result();
				} else {
					$this->contact_delete();
				}
			break;
		}
	}

	/**
	 * Shows a form allowing you to customize the returned list of contacts.
         * 
	 * @access	public
	 * @return	void
	 */
	function contact_list_form()
	{
		$this->nav_submain = $this->nav["list"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$this->tools->tpl->set_block("contact_list_form","list_contact_option","ls_cnt_opt");
		foreach($this->config["dom_avail_tlds"] as $value)
		{
			$this->tools->tpl->set_var("S_TLD",$value);
			$this->tools->tpl->parse("ls_cnt_opt","list_contact_option",true);
		}
		$this->tools->tpl->parse("CONTENT", "contact_list_form");
	}

	/**
	 * Shows a contact list. 
         * 
         * on success - contact list
         * on failure - back to the contact list form
	 *
	 * @access	private
	 * @return	void      
         * @see		contact_list_form()
	 */
	function contact_list_result()
	{
		$this->nav_submain = $this->nav["list"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
	    $result = $this->contact_list($_SESSION["userdata"]["s_tld"],$_SESSION["userdata"]["t_pattern"]);
	    if ($result != false) {
		if ($result != $this->config["empty_result"] && is_array($result)) {
		    $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_sub_btn");
		    $this->tools->tpl->set_block("repository","result_table_row");
		    $this->tools->tpl->set_block("repository","result_table");
		    $this->tools->tpl->set_block("repository","query_for_contact_data");
		    switch ($_SESSION["userdata"]["op"])
		    {
			case "view_contact":
				$this->tools->tpl->set_var("MODE","show_contact");
				break;

			case "modify_contact":
				$this->tools->tpl->set_var("MODE","contact_form");
				break;

			case "delete_contact":
				$this->tools->tpl->set_var("MODE","show_contact");
				break;
			default:
				$this->tools->tpl->set_var("MODE","show_contact");
				break;
		    }

		    foreach($result as $value)
		    {
			$this->tools->tpl->set_var(array(
				"CONTACT_HANDLE"	=> $value["0"],
				"URLENC_CONTACT_HANDLE"	=> urlencode($value["0"])
				));
			$this->tools->tpl->parse("FIELD1", "query_for_contact_data");
			$this->tools->tpl->parse("FORMTABLEROWS", "result_table_row",true);
		    }
		    $this->tools->tpl->parse("CONTENT", "result_table");
		}
	    } else {
		$this->tools->tpl->set_block("repository","general_error_box");
	        $this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
	        $this->contact_list_form();
	    }
	}

	/**
	 * Returns an array of contacts. 
         *
         * @param	string	$tld binds the returned handles to a specific tld
         * @param	string	$pattern seed for the results
	 * @access	public
	 * @return	mixed      
         * @see		contact_list_result()
	 */
	function contact_list($tld = "", $pattern = "")
	{
	    $fields = array(
		"pattern"	=> $_SESSION["userdata"]["t_pattern"],
		"tld"		=> $_SESSION["userdata"]["s_tld"]
		);
	    if ($this->connect->execute_request("query-contact-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
	        return ($this->tools->parse_text($_SESSION["response"]["response_body"]));
	    } else {
	        return false;
	    }
	}

	/**
	 * Shows detailed contact data. 
         * 
         * on success - contact data
         * on failure - back to the contact list form
	 *
	 * @access	private
	 * @return	void      
         * @see		contact_list_form()
	 */
	function show_contact()
	{
	    $result = $this->tools->query_object("contact",$_SESSION["userdata"]["cnt_hdl"]);
	    if ($result != false) {
		if ($result != $this->config["empty_result"] && is_array($result)) {
		    $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_sub_btn");
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
		    switch ($_SESSION["userdata"]["op"]) {

			case "delete_contact":
				$this->tools->tpl->set_var("MODE","contact_delete");
				$this->tools->tpl->parse("res_tbl_sub_btn","result_table_submit_btn");
				break;
			default:
				//nix
				break;
			}
		    $this->tools->tpl->parse("CONTENT", "result_table");
		}
	    } else {
		$this->tools->tpl->set_block("repository","general_error_box");
		$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
	        $this->contact_list_form();
	    }
	}

	/**
	 * Shows a form for choosing which type of contact handles is relevant.
         * 
	 * @access	public
	 * @return	void
	 */
	function contact_select_tld_form()
	{
		$this->nav_submain = $this->nav["select"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$this->tools->tpl->set_block("contact_sel_tld_form","list_contact_option","ls_cnt_opt");
		switch ($_SESSION["userdata"]["op"]) {

			case "create_contact":
				$this->tools->tpl->set_var("MODE","contact_form");
				break;

			case "modify_contact":
				$this->tools->tpl->set_block("contact_sel_tld_form","contact_optional_fields","contact_opt_flds");
				$this->tools->tpl->set_var("MODE","contact_list_result");
				break;

			case "delete_contact":
				$this->tools->tpl->set_block("contact_sel_tld_form","contact_optional_fields","contact_opt_flds");
				$this->tools->tpl->set_var("MODE","contact_list_result");
				break;
			default:
				$this->log->req_status("e", "function contact_select_tld_form(): Unknown op type: ".$_SESSION["userdata"]["op"]);
				return;
				break;
		}
		foreach($this->config["dom_avail_tlds"] as $value)
		{
			$this->tools->tpl->set_var("S_TLD",$value);
			$this->tools->tpl->parse("ls_cnt_opt","list_contact_option",true);
		}
		$this->tools->tpl->parse("CONTENT", "contact_sel_tld_form");
	}

	/**
	 * Shows a form for contact input.
         *
         * @param	string	$tld needed for referencing the contact profile
         * @param	boolean	$opt_fields show optional fields
	 * @access	public
	 * @return	void
	 */
	function contact_form($tld,$opt_fields = false)
	{
		switch ($_SESSION["userdata"]["op"]) {

			case "create_contact":
				$this->nav_submain = $this->nav["create"];		
				$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
				$this->tools->tpl->parse("NAV","navigation");

				$this->tools->tpl->set_var("T_TLD",$_SESSION["userdata"]["s_tld"]);
				$this->build_contact_form("contact_form",$tld,$opt_fields);
				$this->tools->tpl->set_var("MODE","contact_create");
				$this->tools->tpl->parse("CONTENT", "contact_form");
				break;

			case "modify_contact":
				$this->nav_submain = $this->nav["edit"];		
				$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
				$this->tools->tpl->parse("NAV","navigation");
				
				$this->build_contact_form("contact_form",$this->tools->type_of_contact($tld),$opt_fields);
				$result = $this->tools->query_object("contact",$_SESSION["userdata"]["cnt_hdl"]);
				if ($result != false) {
					if ($result != $this->config["empty_result"] && is_array($result)) {
						$form_data_arr = $this->tools->fill_form_prep($result,"contact");
						if (is_array($form_data_arr)) {
							$this->tools->fill_form($form_data_arr);
						}
					} else {
						$this->tools->tpl->set_block("repository","general_error_box");
						$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
						$this->contact_list_result();
					}
				}
				$this->tools->tpl->set_var("T_TLD",$_SESSION["userdata"]["cnt_hdl"]);
				$this->tools->tpl->set_var("MODE","contact_modify");
				$this->tools->tpl->parse("CONTENT", "contact_form");
				break;

			case "delete_contact":
				$this->nav_submain = $this->nav["delete"];
				$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
				$this->tools->tpl->parse("NAV","navigation");
				
				$this->tools->tpl->set_block("contact_sel_tld_form","contact_optional_fields","contact_opt_flds");
				break;
		}
	}

	/**
	 * Creates a contact. 
         *   
	 * @access	public
	 * @return	mixed      
         * @see		contact_form()
	 */
	function contact_create()
	{
		$this->nav_submain = $this->nav["create"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$fields = array(
			"tld"		=> $_SESSION["userdata"]["s_tld"],
			"name"		=> $_SESSION["httpvars"]["t_contact_name"],
			"fname"		=> $_SESSION["httpvars"]["t_contact_fname"],
			"lname"		=> $_SESSION["httpvars"]["t_contact_lname"],
			"title"		=> $_SESSION["httpvars"]["t_contact_title"],
			"individual"	=> $_SESSION["httpvars"]["t_contact_individual"],
			"organization"	=> $_SESSION["httpvars"]["t_contact_organization"],
			"email"		=> $_SESSION["httpvars"]["t_contact_email"],
			"address-1"	=> $_SESSION["httpvars"]["t_contact_address_1"],
			"address-2"	=> $_SESSION["httpvars"]["t_contact_address_2"],
			"address-3"	=> $_SESSION["httpvars"]["t_contact_address_3"],
			"city"		=> $_SESSION["httpvars"]["t_contact_city"],
			"state"		=> $_SESSION["httpvars"]["t_contact_state"],
			"postal-code"	=> $_SESSION["httpvars"]["t_contact_postal_code"],
			"country"	=> $_SESSION["httpvars"]["s_contact_country"],
			"phone"		=> $_SESSION["httpvars"]["t_contact_phone"],
			"extension"	=> $_SESSION["httpvars"]["t_contact_extension"],
			"fax"		=> $_SESSION["httpvars"]["t_contact_fax"]
		);
		if (!$this->connect->execute_request("contact-create", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->contact_form($_SESSION["userdata"]["s_tld"],$_SESSION["userdata"]["c_opt_fields"]);
		}
	}

	/**
	 * Modifies a contact. 
         *   
	 * @access	public
	 * @return	mixed      
         * @see		contact_form()
	 */
	function contact_modify()
	{
		$this->nav_submain = $this->nav["edit"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$fields = array(
			"handle"	=> $_SESSION["userdata"]["cnt_hdl"],
			"name"		=> $_SESSION["httpvars"]["t_contact_name"],
			"fname"		=> $_SESSION["httpvars"]["t_contact_fname"],
			"lname"		=> $_SESSION["httpvars"]["t_contact_lname"],
			"title"		=> $_SESSION["httpvars"]["t_contact_title"],
			"individual"	=> $_SESSION["httpvars"]["t_contact_individual"],
			"organization"	=> $_SESSION["httpvars"]["t_contact_organization"],
			"email"		=> $_SESSION["httpvars"]["t_contact_email"],
			"address-1"	=> $_SESSION["httpvars"]["t_contact_address_1"],
			"address-2"	=> $_SESSION["httpvars"]["t_contact_address_2"],
			"address-3"	=> $_SESSION["httpvars"]["t_contact_address_3"],
			"city"		=> $_SESSION["httpvars"]["t_contact_city"],
			"state"		=> $_SESSION["httpvars"]["t_contact_state"],
			"postal-code"	=> $_SESSION["httpvars"]["t_contact_postal_code"],
			"country"	=> $_SESSION["httpvars"]["s_contact_country"],
			"phone"		=> $_SESSION["httpvars"]["t_contact_phone"],
			"extension"	=> $_SESSION["httpvars"]["t_contact_extension"],
			"fax"		=> $_SESSION["httpvars"]["t_contact_fax"]
		);
		if (!$this->connect->execute_request("contact-modify", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->contact_form($_SESSION["userdata"]["cnt_hdl"],true);
		}
	}

	/**
	 * Deletes a contact. 
         *   
	 * @access	public
	 * @return	mixed      
         * @see		contact_form()
	 */
	function contact_delete()
	{
		$this->nav_submain = $this->nav["delete"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");

		$fields = array(
			"handle"	=> $_SESSION["userdata"]["cnt_hdl"],
		);
		if (!$this->connect->execute_request("contact-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->contact_list_result();
		}
	}

	/**
	 * Creates a contact input form. Uses the contact profile defined in config.php
         *
         * @param	string	$host_tpl template to be parsed
         * @param	string	$tld needed for referencing the contact profile
         * @param	boolean	$opt_fields show optional fields
	 * @access	private
	 * @return	void      
         * @see		contact_form()
	 */
	function build_contact_form($host_tpl,$tld,$opt_fields)
	{
		$this->tools->tpl->parse("TEMP_TPL_CONTAINER",$host_tpl);
		$tpl_content = $this->tools->tpl->get_var("TEMP_TPL_CONTAINER");		
		//catching the subtemplate names
		$reg = "/[ \t]*<!--\s+BEGIN ([a-z0-9_-]+)\s+-->\s*?\n?/sm";
		preg_match_all($reg,$tpl_content,$m);		
		foreach ($m[1] as $field)
		{
			$this->tools->tpl->set_block($host_tpl,$field,"cnt_".$field);
		}
		
		foreach ($this->config["domain"][$tld]["contact"]["fields"] as $field => $params)
		{			
			if ($params["required"]) {
				$this->tools->tpl->parse("cnt_".$field,$field);
			} else {
				if ($opt_fields) {
					$this->tools->tpl->parse("cnt_".$field,$field);
				}
			}
			if (isset($params["size"])) {
				$this->tools->tpl->set_var(strtoupper("MAX_LENGTH_".$field),$params["size"]);
			}
		}
		$this->tools->tpl->parse("CONTACT_COUNTRY","country_ls");
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

			case "contact_list_result":
			    if (!$this->tools->is_valid("joker_tld",$_SESSION["httpvars"]["s_tld"],true)) {
					$error = true;
					$this->tools->field_err("ERROR_INVALID_TLD",$this->err_arr["_tld"]["err_msg"]);
			    }
			    break;

			case "contact_form":
				// this code is weak - attention!
				if (isset($_SESSION["httpvars"]["s_tld"]) && !$this->tools->is_valid("joker_tld",$_SESSION["httpvars"]["s_tld"],true)) {
					$error = true;
					$this->tools->field_err("ERROR_INVALID_TLD",$this->err_arr["_tld"]["err_msg"]);
				}
				if (isset($_SESSION["httpvars"]["cnt_hdl"]) && !$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["cnt_hdl"],"unknown")) {
					$error = true;
					$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_contact_hdl"]["err_msg"]);
				}
			    break;

			case "contact_submission":
				foreach ($this->config["domain"][$_SESSION["userdata"]["s_tld"]]["contact"]["fields"] as $field => $params)
				{
					if ($params["required"]) {
						switch (strtolower($field)) {

							case "name":
								if (!$this->tools->is_valid($this->err_arr["_name"]["regexp"],$_SESSION["httpvars"]["t_contact_name"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_FULL_NAME",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_name"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_FULL_NAME",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "fname":
								if (!$this->tools->is_valid($this->err_arr["_name"]["regexp"],$_SESSION["httpvars"]["t_contact_fname"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_FNAME",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_fname"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_FNAME",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "lname":
								if (!$this->tools->is_valid($this->err_arr["_name"]["regexp"],$_SESSION["httpvars"]["t_contact_lname"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_LNAME",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_lname"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_LNAME",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "organization":
								if (!$this->tools->is_valid($this->err_arr["_is_individual"]["regexp"],$_SESSION["httpvars"]["t_contact_individual"])) {
									if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_organization"])) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_ORGANIZATION",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
									} else {
										$str_length = strlen($_SESSION["httpvars"]["t_contact_organization"]);
										if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
											$error = true;
											$this->tools->field_err("ERROR_INVALID_ORGANIZATION",$this->err_arr["_invalid_field_length"]["err_msg"]);
											$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
										}
									}
								}
							break;

							case "email":
								if (!$this->tools->is_valid("email",$_SESSION["httpvars"]["t_contact_email"],true)) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_EMAIL",$this->err_arr["_email"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_email"]);
									if (is_numeric($params["size"]) && $str_length > $params["size"]) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_EMAIL",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "address-1":
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_address_1"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_ADDRESS1",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_address_1"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_ADDRESS1",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "city":
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_city"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_CITY",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_city"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_CITY",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "postal-code":
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_postal_code"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_POSTAL_CODE",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_postal_code"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_POSTAL_CODE",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "country":
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["s_contact_country"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_COUNTRY",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["s_contact_country"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_COUNTRY",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "phone":
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_phone"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_PHONE",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_phone"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_PHONE",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;

							case "fax":
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_fax"])) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_FAX",$this->err_arr["_invalid_chars_in_field"]["err_msg"]);
								} else {
									$str_length = strlen($_SESSION["httpvars"]["t_contact_fax"]);
									if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_FAX",$this->err_arr["_invalid_field_length"]["err_msg"]);
										$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
									}
								}
							break;
						}
					} else {
						switch (strtolower($field)) {
							case "title":
								$str_length = strlen($_SESSION["httpvars"]["t_contact_title"]);
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_title"])) {
									if (is_numeric($params["size"]) && $str_length != 0) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_TITLE",$this->err_arr["_invalid_chars_in_opt_field"]["err_msg"]);
									}
								} elseif ($str_length > $params["size"]) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_TITLE",$this->err_arr["_invalid_field_length"]["err_msg"]);
									$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
								}
							break;

							case "individual":
								$str_length = strlen($_SESSION["httpvars"]["t_contact_individual"]);
								if (!$this->tools->is_valid($this->err_arr["_individual"]["regexp"],$_SESSION["httpvars"]["t_contact_individual"])) {
									if (is_numeric($params["size"]) && $str_length != 0) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_INDIVIDUAL",$this->err_arr["_invalid_chars_in_opt_field"]["err_msg"]);
									}
								} elseif ($str_length > $params["size"]) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_INDIVIDUAL",$this->err_arr["_invalid_field_length"]["err_msg"]);
									$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
								}
							break;

							case "address-2":
								$str_length = strlen($_SESSION["httpvars"]["t_contact_address_2"]);
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_address_2"])) {
									if (is_numeric($params["size"]) && $str_length != 0) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_ADDRESS2",$this->err_arr["_invalid_chars_in_opt_field"]["err_msg"]);
									}
								} elseif ($str_length > $params["size"]) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_ADDRESS2",$this->err_arr["_invalid_field_length"]["err_msg"]);
									$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
								}
							break;

							case "address-3":
								$str_length = strlen($_SESSION["httpvars"]["t_contact_address_3"]);
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_address_3"])) {
									if (is_numeric($params["size"]) && $str_length != 0) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_ADDRESS3",$this->err_arr["_invalid_chars_in_opt_field"]["err_msg"]);
									}
								} elseif ($str_length > $params["size"]) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_ADDRESS3",$this->err_arr["_invalid_field_length"]["err_msg"]);
									$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
								}
							break;

							case "state":
								$str_length = strlen($_SESSION["httpvars"]["t_contact_state"]);
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_state"])) {
									if (is_numeric($params["size"]) && $str_length != 0) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_STATE",$this->err_arr["_invalid_chars_in_opt_field"]["err_msg"]);
									}
								} elseif ($str_length > $params["size"]) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_STATE",$this->err_arr["_invalid_field_length"]["err_msg"]);
									$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
								}
							break;

							case "extension":
								$str_length = strlen($_SESSION["httpvars"]["t_contact_extension"]);
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_extension"])) {
									if (is_numeric($params["size"]) && $str_length != 0) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_EXTENSION",$this->err_arr["_invalid_chars_in_opt_field"]["err_msg"]);
									}
								} elseif ($str_length > $params["size"]) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_EXTENSION",$this->err_arr["_invalid_field_length"]["err_msg"]);
									$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
								}
							break;

							case "fax":
								$str_length = strlen($_SESSION["httpvars"]["t_contact_fax"]);
								if (!$this->tools->is_valid($this->err_arr["_overall_text"]["regexp"],$_SESSION["httpvars"]["t_contact_fax"])) {
									if (is_numeric($params["size"]) && $str_length != 0) {
										$error = true;
										$this->tools->field_err("ERROR_INVALID_FAX",$this->err_arr["_invalid_chars_in_opt_field"]["err_msg"]);
									}
								} elseif ($str_length > $params["size"]) {
									$error = true;
									$this->tools->field_err("ERROR_INVALID_FAX",$this->err_arr["_invalid_field_length"]["err_msg"]);
									$this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
								}
							break;
						}
					}
				}
			    break;

			case "contact_delete":
				if (!$this->tools->is_valid_contact_hdl($_SESSION["userdata"]["cnt_hdl"])) {
					$error = true;
					$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_contact_hdl"]["err_msg"]);
				}
			    break;
		}
		return $error;
	}
}

?>