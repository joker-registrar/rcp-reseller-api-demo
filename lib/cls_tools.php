<?php

/**
 * Implements the template engine routines, automagically parses already typed in values,
 * a lot of useful tools etc.
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright for now
 */

class Tools
{
	/**
	 * Default template directory
	 * Its value is overridden in the class constructor.
         * 
	 * @var		string
	 * @access	private
         * @see		Tools()
	 */	
	var $tpl_dir = "/tpl";
	
	/**
	 * Halt template engine flag on error
         * Its value is overridden in the class constructor.
	 *
	 * @var		bool
	 * @access	private
         * @see		Tools()
	 */	
	var $tpl_halt_on_error = "on";
	
	/**
	 * Array containing all template files
	 *
	 * @var		array
	 * @access	private
	 */	
	var $template_files = array(
    		"main_tpl"		=> "main/tpl_main.html",
    		"menu_tpl"		=> "main/tpl_menu.html",
		"body_tpl"		=> "main/tpl_body.html",		
		"login_form"		=> "main/tpl_login_form.html",
		"domain_view_form"	=> "domain/tpl_domain_view_form.html",
		"domain_list_form"	=> "domain/tpl_domain_list_form.html",
		"domain_register_form"	=> "domain/tpl_domain_register_form.html",
		"domain_renew_form"	=> "domain/tpl_domain_renew_form.html",
		"domain_transfer_form"	=> "domain/tpl_domain_transfer_form.html",
		"domain_modify_form"	=> "domain/tpl_domain_modify_form.html",
		"domain_delete_form"	=> "domain/tpl_domain_delete_form.html",
		"domain_repository"	=> "domain/tpl_domain_repository.html",
		"domain_lock_unlock_form"	=> "domain/tpl_domain_lock_unlock_form.html",
		"domain_redemption_form"	=> "domain/tpl_domain_redemption_form.html",
		//"domain_owner_change_form" => "tpl_domain_owner_change_form.html",
		"dom_ns_list_form"	=> "ns/tpl_dom_ns_list_form.html",
		"ns_handle_form"	=> "ns/tpl_ns_handle_form.html",
		"contact_list_form"	=> "contacts/tpl_contact_list_form.html",
		"contact_form"		=> "contacts/tpl_contact_form.html",
		"contact_sel_tld_form"	=> "contacts/tpl_contact_select_tld_form.html",
		"repository"		=> "common/tpl_repository.html",
		"country_ls"		=> "common/tpl_countries.html",
		"result_list"		=> "common/tpl_result_list.html",
		"tips"			=> "common/tpl_other_tips.html"
	);

	/**
	 * Class constructor. No optional parameters.
	 *
	 * usage: Tools()
	 *
	 * @access	private
	 * @return	void
	 */
	function Tools()
	{
	    global $error_array, $config;
	    $this->err_arr = $error_array;
	    $this->config = $config;
	    $this->connect = new Connect;
	    $this->log = new Log;
	    $this->tpl_dir = $config["tpl_dir"];
	    $this->tpl_halt_on_error = $config["tpl_halt_on_error"];
	    
	    $this->httpvars =  ($_POST) ? $_POST : $_GET;

	    $_SESSION["httpvars"] = $this->httpvars;

	    if (is_array($this->httpvars)) {
		foreach ($this->httpvars as $key => $value)
		{
		    $_SESSION["userdata"][trim($key)] = trim($value);
		    $_SESSION["formdata"][trim($key)] = trim($value);
		}
	    }
	    if (!isset($_SESSION["userdata"]["mode"])) {
	    	$_SESSION["userdata"]["mode"] = "unset";
	    }
		
	    if (!is_object($this->tpl)) {		
		$this->tpl = new Template($this->tpl_dir,"remove");
		$this->tpl->debug = false;
		$this->tpl->halt_on_error = $this->tpl_halt_on_error;		
		foreach ($this->template_files as $key => $value) 
		{
			if (!isset($_SESSION["userdata"]["lang"])) {
				$tpl_arr[$key] = $this->config["dmapi_default_language"]."/".$value;
			} else {
				if (in_array(strtolower($_SESSION["userdata"]["lang"]),$this->config["dmapi_allowed_languages"])) {					
					$tpl_arr[$key] = $_SESSION["userdata"]["lang"]."/".$value;
				} else {					
					$tpl_arr[$key] = $this->config["dmapi_default_language"]."/".$value;
				}
			}			
		}
		$this->tpl->set_file($tpl_arr);
		$this->tpl->set_var("DMAPI_VER", $this->config["dmapi_ver"]);
		$this->tpl->set_var("ENCODING", $this->config["dmapi_encoding"]);
	    }
	    
	    $this->tpl->set_block("repository","navigation");
	    if (!isset($_SESSION["formdata"])) {
		$_SESSION["formdata"] = array();
	    }
	    $this->fill_form($_SESSION["formdata"]);
	}
	
	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function goto($url="") 
	{
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on'){
			$PROTOCOL='https://';
		} else {
			$PROTOCOL='http://';
		}
		if (isset($_SERVER["SERVER_PORT"])) {
			$PORT_NUM = ":".$_SERVER["SERVER_PORT"];
		} else {
			$PORT_NUM = "";
		}		
		Header("Location: ".$PROTOCOL.$_SERVER["SERVER_NAME"].$PORT_NUM.$_SERVER["PHP_SELF"].$url);
		exit;
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function is_valid($type, $content, $custom=false)
	{
		if (!$custom) {		    
			return(preg_match($type,$content));
		} else {
			$ok = false;
			switch ($type) {
			    
			    case "domain":
			    case "joker_domain":
				$reg = explode(".",$content);
				$tld = array_pop($reg); // strip tld
				$sld = array_pop($reg); // strip sld
				if (count($reg) == 0 && $this->is_valid($this->err_arr["_tld"]["regexp"],$tld) && $this->is_valid($this->err_arr["_sld"]["regexp"],$sld)) {
				    $ok = true;
				}
				// deep-check: Joker-available domain
				if ($ok && "joker_domain" == $type) {
				    $ok = in_array($tld, $this->config["dom_avail_tlds"]);
				}
				break;

			    case "host":
				$reg = explode(".",$content);
				$tld = array_pop($reg); // strip tld
				$sld = array_pop($reg); // strip sld
				$content = (is_array($reg)) ? implode(".",$reg) : "";
				if (preg_match($this->err_arr["_host"]["regexp"], $content)) {
				    $ok = $this->is_valid("domain",$sld.".".$tld,true);
				}
				//limit for hostname!!!
				if (strlen($content) > 180) {
				    $ok = false;
				}
				break;

			    case "email":
				$reg = explode("@",$content);
				$addr= $reg[0];
				$host= $reg[1];
				if (preg_match($this->err_arr["_email"]["regexp"], $addr)) {
				    $ok = (count($reg)==2) ? $this->is_valid("host",$host,true) : false;
				}
				if ($ok && $flag) {
				    $ok =  (checkdnsrr($host.".","MX") || checkdnsrr($host.".","A"));
				    if (!$ok && checkdnsrr($host.".","CNAME")) {
					$ok = true; //we must believe it for now - no way to get CNAME for PHP < 5
				    }
				}
			    break;

			    case "joker_tld":
				if ($this->is_valid($this->err_arr["_tld"]["regexp"], $content)) {
				    $ok = in_array($content, $this->config["dom_avail_tlds"]);
				}
				break;
			}
			return $ok;
		}
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function is_valid_contact_hdl($content, $type = "")
	{
	    $ok = false;
	    switch ($type) {

		case "com":
		case "net":
		    $ok = preg_match("/#[0-9]+$|^JOCO\-[0-9]+$/i",$content);
		    break;

		case "org":
		    $ok = preg_match("/^CORG\-[0-9]+$/i",$content);
		    break;

		case "org":
		    $ok = preg_match("/^CORG\-[0-9]+$/i",$content);
		    break;

	    	case "info":
		    $ok = preg_match("/^C[0-9]+\-LRMS$/i",$content);
		    break;

	    	case "biz":
		    $ok = preg_match("/^CNEU\-[0-9]+$|^NEUL\-[0-9]+$|^RDNA\-[0-9]+$/i",$content);
		    break;

		case "unknown":
		default:
		    if (preg_match("/#[0-9]+$|^JOCO\-[0-9]+$/i",$content)
			|| preg_match("/^CORG\-[0-9]+$/i",$content)
			|| preg_match("/^CORG\-[0-9]+$/i",$content)
			|| preg_match("/^C[0-9]+\-LRMS$/i",$content)
			|| preg_match("/^CNEU\-[0-9]+$|^NEUL\-[0-9]+$|^RDNA\-[0-9]+$/i",$content)) {
			$ok = true;
		    }
		    break;
	    }
	    return $ok;
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function type_of_contact($cnt_hdl)
	{
	    if ($this->is_valid_contact_hdl($cnt_hdl,"com")) return "com";
	    if ($this->is_valid_contact_hdl($cnt_hdl,"net")) return "net";
	    if ($this->is_valid_contact_hdl($cnt_hdl,"org")) return "org";
	    if ($this->is_valid_contact_hdl($cnt_hdl,"info")) return "info";
	    if ($this->is_valid_contact_hdl($cnt_hdl,"biz")) return "biz";
	    if ($this->is_valid_contact_hdl($cnt_hdl,"de")) return "de";
	}

	/**
	 * Returns the domain part of a 'something' (email, hostname, contact) or
         * false in case of incorrect syntax
         * 
	 * @param	string	$string
         * @return	mixed
	 */
	function get_domain_part($string)
	{
	    $reg = Array();
	    $reg = explode(".",$string);
	    $pre_tld = array_pop($reg); // strip tld
	    $pre_sld = array_pop($reg); // strip sld
	    $void = preg_match("/^([a-z]+)(#[0-9]+)?$/i",$pre_tld,$reg);
	    $tld = $reg[1];
	    $void = preg_match("/[.@]?([-a-z0-9]+)$/i",$pre_sld,$reg);
	    $sld = $reg[1];	    
	    if ($this->is_valid("domain",$sld.".".$tld,true)) {		
		return (array("sld" => $sld, "tld" => $tld));
	    } else {
		return false;
	    }
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function fill_form($form_data)
	{
	    if (is_array($form_data)) {
		foreach($form_data as $key => $value)
		{
		    switch (substr($key,0,2)) {
    
		        case "t_":
			    $this->tpl->set_var(strtoupper($key),$value);
			break;

			case "s_":
			    $this->tpl->set_var(strtoupper($key."_".$value),"selected");
			    break;

			case "c_":
			    $this->tpl->set_var(strtoupper($key),"checked");
			    break;
		    
			case "r_":
			    $this->tpl->set_var(strtoupper($key."_".$value),"checked");
			    break;
		    }
		}
	    }
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function fill_form_prep($res_arr,$type)
	{
	    switch ($type) {

		case "contact":
			foreach ($res_arr as $value)
			{
			    preg_match("/^contact\.(.*):$/i",$value["0"],$match);
			    $form_data["t_contact_".str_replace("-","_",$match["1"])] = $value["1"];
			    if (preg_match("/^contact\.country:$/i",$value["0"])) {
				$form_data["s_contact_country"] = $value["1"];
			    }
			}
		    break;
	    }
	    return $form_data;
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function parse_site()
	{		
		$this->tpl->set_var("DMAPI_FORM_ACTION",$this->config["dmapi_form_action"]);

		if (!$this->has_sessid($_SESSION["auth-sid"])) {
			if (isset($_SESSION["auth-sid"])) {
			    $this->general_err("GENERAL_ERROR",$this->err_arr["_sess_expired"]["err_msg"]);
			}
			$this->tpl->parse("SITE_BODY","login_form");			
			$this->tpl->parse("MAIN", "main_tpl");
			$this->tpl->p("MAIN");
		} else {		    
			$this->tpl->set_var("USER_NAME",$_SESSION["username"]);
			$this->tpl->parse("MENU","menu_tpl");
			$this->tpl->parse("SITE_BODY","body_tpl");
			$this->tpl->parse("MAIN","main_tpl");
			$this->tpl->p("MAIN");
		}
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function parse_text($text, $keyval = false)
	{
	    if (trim($text) != "") {
		$raw_arr = explode("\n", trim($text));	    
		if (is_array($raw_arr)) {
		    foreach ($raw_arr as $key => $value)
		    {
			if (!$keyval) {
			    $result[$key] = explode(" ",$value);
			} else {
			    $temp_val = explode(" ", $value);
			    $val1 = array_shift($temp_val);
			    $result[$key] = array($val1,implode(" ",$temp_val));
			}
		    }
		}
	    }
	    return (is_array($result) ? $result : $this->config["empty_result"]);
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function domain_list($pattern)
	{
	    $fields = array(
		"pattern"	=> $pattern
        	);
	    if ($this->connect->execute_request("query-domain-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
	        return ($this->parse_text($_SESSION["response"]["response_body"]));
	    } else {
	        return false;
	    }
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function has_sessid($sessid)
	{
	    if (isset($sessid) && !empty($sessid)) {
	        return true;
	    } else {
	        return false;
	    }
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function get_tracking_id()
	{
	    return "Tracking ID: ".$_SESSION["response"]["response_header"]["tracking-id"];
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function show_request_status($add_info = "", $track_id = true, $proc_id = true)
	{
	    $this->tpl->set_block("repository","general_success_box");
	    if (is_array($_SESSION["response"]["response_header"])) {
		$add_info .= "\n";
		foreach($_SESSION["response"]["response_header"] as $key => $value) {
		    if ($track_id && strtolower($key) == "tracking-id") {
			$add_info .= "Tracking ID: ".$value."\n";
		    }
		    if ($proc_id && strtolower($key) == "proc-id") {
			$add_info .= "Processing ID: ".$value."\n";
		    }
		}
	    }	    
	    $this->tpl->set_var("STATUS_MSG", nl2br($add_info));
	    $this->tpl->parse("GENERAL_ERROR", "general_success_box");
		
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function general_err($varname, $errmsg, $detailed_info = "true", $error_info = "true")
	{
	    $add_info = "";
	    if ($detailed_info && is_array($_SESSION["response"]["response_header"])) {		
		$add_info = "\n";
		if ($error_info) {
			foreach($_SESSION["response"]["response_header"] as $key => $value) {
			    if (strtolower($key) == "error") {
				if (is_array($value)) {
				    foreach($value as $err) {
					$add_info .= "Error: ".$err."\n";
				    }
				} else {
				    $add_info .= "Error: ".$value."\n";
				}
			    }
			    if (strtolower($key) == "status-text") {
				$add_info .= "Status Description: ".$value."\n";
			    }
			    if (strtolower($key) == "tracking-id") {
				$add_info .= "Tracking ID: ".$value."\n";
			    }
			    if (strtolower($key) == "proc-id") {
				$add_info .= "Processing ID: ".$value."\n";
			    }
			}
		} else {
			foreach($_SESSION["response"]["response_header"] as $key => $value) {
			    if (strtolower($key) == "tracking-id") {
				$add_info .= "Tracking ID: ".$value."\n";
			    }
			    if (strtolower($key) == "proc-id") {
				$add_info .= "Processing ID: ".$value."\n";
			    }
			}
		}
	    }
	    $this->tpl->set_var("ERROR_MSG", $errmsg.nl2br($add_info));
	    $this->tpl->parse($varname, "general_error_box");
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function field_err($varname, $errmsg)
	{
	    $this->tpl->set_var("ERROR_MSG", $errmsg);
	    $this->tpl->parse($varname,"field_error_box");
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function query_object($type,$object)
	{
	    switch ($type) {

		case "domain":
		    $fields = array(
			"domain"	=> $object
		    );
		    break;

		case "contact":
		    $fields = array(
			"contact"	=> $object
		    );
		    break;

		case "host":
		    $fields = array(
			"host"	=> $object
		    );
		    break;

		default:
		    $this->log->req_status("e", "function query_object(): Unknown object type: $type");
		    return false;
		    break;
	    }
	    if ($this->connect->execute_request("query-object", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
		return ($this->parse_text($_SESSION["response"]["response_body"]));
	    } else {
	        return false;
	    }
	}

	/**
	 * description
         * 
	 * @todo documentation to be continued
	 */
	function send_mail($to,$from,$replyTo,$cc,$subject="",$text,$html="",$bcc="",$attach="")
	{		
		$text = str_replace("\r\n","\r",$text);
		$mailer = new Email;
		$mailer->setTo($to);
		$mailer->setReplyTo($replyTo);
		$mailer->setCC($cc);
		$mailer->setBCC($bcc);
		$mailer->setFrom($from);
		$mailer->setSubject($subject);
		$mailer->setText($text);
		$mailer->setHTML($html);
		$mailer->setAttachments($attach);
		$mailer->checkEmail($to);
		$mailer->checkEmail($from);
		$mailer->setAddCmdLnParams("-f".$from);
		return $mailer->send();
	}

} //end of class Tools

?>