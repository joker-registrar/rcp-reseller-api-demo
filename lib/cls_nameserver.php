<?php

class Nameserver
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
	
	function Nameserver()
	{
		global $error_array, $config, $tools, $nav;
		$this->err_arr = $error_array;
		$this->config = $config;
		$this->tools = $tools;
		$this->nav = $nav;
		$this->connect = new Connect;		
		$this->nav_main = $this->nav["ns"];
	}

	function dispatch($mode)
	{
		switch ($mode) {

			case "create":
				$error = $this->validation("create");
				if ($error) {
					$this->create_form();
				} else {
					$this->create();
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

			case "list_result":
				$this->list_result();
			break;
		}
	}

	function create_form()
	{
		$this->nav_submain = $this->nav["create_ns"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_ip","ns_hdl_ip");
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_textbox","ns_hdl_textbox");
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_selbox","ns_hdl_selbox");
		$this->tools->tpl->parse("ns_hdl_textbox", "ns_handle_textbox");
		$this->tools->tpl->set_var("MODE","ns_create");
		$this->tools->tpl->parse("ns_hdl_ip", "ns_handle_ip");
		$this->tools->tpl->parse("CONTENT", "ns_handle_form");

	}

	function create()
	{
		$this->nav_submain = $this->nav["create_ns"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$fields = array(
			"host"	=> $_SESSION["userdata"]["t_ns"],
			"ip"	=> $_SESSION["userdata"]["t_ip"],
             		);
		if (!$this->connect->execute_request("ns-create", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->create_form();
		} else {
		    $this->tools->show_request_status();
		}
	}
	
	function modify_form()
	{
		$this->nav_submain = $this->nav["modify_ns"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_ip","ns_hdl_ip");
		$this->tools->tpl->set_block("ns_handle_form","list_ns_option","ls_ns_opt");
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_textbox","ns_hdl_textbox");
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_selbox","ns_hdl_selbox");
		$ns_arr = $this->ns_list("*");		
		if (is_array($ns_arr)) {
		    foreach($ns_arr as $value)
		    {
		    	$this->tools->tpl->set_var("S_NS",$value["0"]);
			$this->tools->tpl->parse("ls_ns_opt","list_ns_option",true);
		    }
		}
		$this->tools->tpl->parse("ns_hdl_selbox", "ns_handle_selbox");
		$this->tools->tpl->set_var("MODE","ns_modify");
		$this->tools->tpl->parse("ns_hdl_ip", "ns_handle_ip");
		$this->tools->tpl->parse("CONTENT", "ns_handle_form");

	}

	function modify()
	{
		$this->nav_submain = $this->nav["modify_ns"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$fields = array(
			"host"	=> $_SESSION["userdata"]["s_ns"],
			"ip"	=> $_SESSION["userdata"]["t_ip"],
             		);
		if (!$this->connect->execute_request("ns-modify", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->modify_form();
		} else {
		    $this->tools->show_request_status();
		}
	}
	
	function delete_form()
	{
		$this->nav_submain = $this->nav["delete_ns"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_ip","ns_hdl_ip");
		$this->tools->tpl->set_block("ns_handle_form","list_ns_option","ls_ns_opt");
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_textbox","ns_hdl_textbox");
		$this->tools->tpl->set_block("ns_handle_form","ns_handle_selbox","ns_hdl_selbox");
		$ns_arr = $this->ns_list("*");		
		if (is_array($ns_arr)) {
		    foreach($ns_arr as $value)
		    {
		    	$this->tools->tpl->set_var("S_NS",$value["0"]);
			$this->tools->tpl->parse("ls_ns_opt","list_ns_option",true);
		    }
		}
		$this->tools->tpl->parse("ns_hdl_selbox", "ns_handle_selbox");
		$this->tools->tpl->set_var("MODE","ns_delete");
		$this->tools->tpl->parse("CONTENT", "ns_handle_form");

	}

	function delete()
	{
		$this->nav_submain = $this->nav["delete_ns"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");

		$fields = array(
			"host"	=> $_SESSION["userdata"]["s_ns"],			
             		);
		if (!$this->connect->execute_request("ns-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
			$this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
			$this->delete_form();
		} else {
		    $this->tools->show_request_status();
		}
	}

	function list_form()
	{
		$this->nav_submain = $this->nav["ns_list"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");

		$this->tools->tpl->set_var("MODE","ns_list_result");
		$this->tools->tpl->parse("CONTENT","dom_ns_list_form");
	}

	function list_result()
	{
		$this->nav_submain = $this->nav["ns_list"];		
		$this->tools->tpl->set_var("NAV_LINKS",$this->nav_main." > ".$this->nav_submain);
		$this->tools->tpl->parse("NAV","navigation");
		
	    $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_submit_btn");
	    $result = $this->ns_list($_SESSION["userdata"]["t_pattern"]);
	    if ($result) {
		if ($result != $this->config["empty_result"] && is_array($result)) {
		    $this->tools->tpl->set_block("repository","result_table_row");
		    $this->tools->tpl->set_block("repository","result_table");
		    foreach($result as $value)
		    {
			$this->tools->tpl->set_var(array(
				"FIELD1"	=> $value["0"],
				"FIELD2"	=> "",
				));
			$this->tools->tpl->parse("FORMTABLEROWS", "result_table_row",true);
		    }
		    $this->tools->tpl->parse("CONTENT", "result_table");
		}
	    } else {
	        $this->tools->general_err("GENERAL_ERROR",$this->err_arr["_srv_req_failed"]["err_msg"]);
	        $this->list_form();
	    }
	}

	function ns_list($pattern)
	{
	    $fields = array(
		"pattern"	=> $pattern
        	);
	    if ($this->connect->execute_request("query-ns-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
	        return ($this->tools->parse_text($_SESSION["response"]["response_body"]));
	    } else {
	        return false;
	    }
	}

	function validation($mode)
	{
	    	$this->tools->tpl->set_block("repository","general_error_box");
		$this->tools->tpl->set_block("repository","field_error_box");
		$error = false;
		switch ($mode) {

			case "create":
			    if (!$this->tools->is_valid("host",$_SESSION["httpvars"]["t_ns"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_NS",$this->err_arr["_ns"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid($this->err_arr["_ipv4"]["regexp"],$_SESSION["httpvars"]["t_ip"])) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_IP",$this->err_arr["_ipv4"]["err_msg"]);
			    }
			    break;
			
			case "modify":
			    if (!$this->tools->is_valid("host",$_SESSION["httpvars"]["s_ns"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_NS",$this->err_arr["_ns"]["err_msg"]);
			    }
			    if (!$this->tools->is_valid($this->err_arr["_ipv4"]["regexp"],$_SESSION["httpvars"]["t_ip"])) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_IP",$this->err_arr["_ipv4"]["err_msg"]);
			    }
			    break;

			case "delete":
			    if (!$this->tools->is_valid("host",$_SESSION["httpvars"]["s_ns"],true)) {
				$error = true;
				$this->tools->field_err("ERROR_INVALID_NS",$this->err_arr["_ns"]["err_msg"]);
			    }
			    break;
		}
		return $error;
	}
}

?>