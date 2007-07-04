<?php

/**
 * Implements the template engine routines, automagically parses already typed in values,
 * a lot of useful tools etc.
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class Tools
{    
   /**
     * Contains array of regular expressions for verification
     *
     * @var     array
     * @access  private
     */
    var $err_regexp  = array();
    
    /**
     * Contains array of error messages used in verification
     *
     * @var     array
     * @access  private
     */
    var $err_msg  = array();

    /**
     * Default template directory
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     Tools()
     */
    var $tpl_dir = "/tpl";

    /**
     * Halt template engine flag on error
     * Its value is overridden in the class constructor.
     *
     * @var     bool
     * @access  private
     * @see     Tools()
     */
    var $tpl_halt_on_error = "on";

    /**
     * Array containing all template files
     *
     * @var     array
     * @access  private
     */
    var $template_files = array(
        "main_tpl"              => "main/tpl_main.html",
        "menu_tpl"              => "main/tpl_menu.html",
        "body_tpl"              => "main/tpl_body.html",
        "login_form"            => "main/tpl_login_form.html",
        "domain_view_form"      => "domain/tpl_domain_view_form.html",
        "domain_list_form"      => "domain/tpl_domain_list_form.html",
        "domain_register_form"  => "domain/tpl_domain_register_form.html",
        "domain_register_overview_form" => "domain/tpl_domain_register_overview_form.html",
        "domain_renew_form"     => "domain/tpl_domain_renew_form.html",
        "domain_transfer_form"  => "domain/tpl_domain_transfer_form.html",
        "domain_fast_transfer_form"  => "domain/tpl_fast_domain_transfer_form.html",
        "domain_bulk_transfer_step1" => "domain/tpl_domain_bulk_transfer_step1_form.html",
        "domain_bulk_transfer_step2" => "domain/tpl_domain_bulk_transfer_step2_form.html",
        "domain_modify_form"    => "domain/tpl_domain_modify_form.html",        
        "domain_delete_form"    => "domain/tpl_domain_delete_form.html",
        "domain_repository"     => "domain/tpl_domain_repository.html",
        "domain_lock_unlock_form"   => "domain/tpl_domain_lock_unlock_form.html",
        "domain_authid_form"    => "domain/tpl_domain_authid_form.html",
        "domain_redemption_form"    => "domain/tpl_domain_redemption_form.html",
        "domain_owner_change_step1" => "domain/tpl_domain_owner_change_step1_form.html",
        "domain_owner_change_step2" => "domain/tpl_domain_owner_change_step2_form.html",
        "zone_list_form"        => "zone/tpl_zone_list_form.html",
        "zone_repository"       => "zone/tpl_zone_repository.html",
        "dom_ns_list_form"      => "ns/tpl_dom_ns_list_form.html",
        "ns_handle_form"        => "ns/tpl_ns_handle_form.html",
        "ns_repository"         => "ns/tpl_ns_repository.html",
        "ns_mass_modify_form_step1"  => "ns/tpl_ns_mass_modify_form_step1.html",
        "ns_mass_modify_form_step2"  => "ns/tpl_ns_mass_modify_form_step2.html",
        "contact_list_form"     => "contacts/tpl_contact_list_form.html",
        "contact_form"          => "contacts/tpl_contact_form.html",
        "contact_sel_tld_form"  => "contacts/tpl_contact_select_tld_form.html",
        "contact_repository"    => "contacts/tpl_contact_repository.html",
        "repository"            => "common/tpl_repository.html",
        "country_ls"            => "common/tpl_countries.html",
        "language_ls"           => "common/tpl_eu_languages.html",
        "result_list"           => "common/tpl_result_list.html",
        "tips"                  => "common/tpl_other_tips.html",
        "home_page"             => "common/tpl_home_page.html",
        "nexus_category"        => "common/tpl_nexus_category.html",
        "nexus_category_country"=> "common/tpl_nexus_category_country.html",
        "paging_repository"     => "common/tpl_paging_repository.html",        
        "nexus_application_purpose" => "common/tpl_nexus_application_purpose.html"
    );

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Tools()
     *
     * @access  private
     * @return  void
     */
    function Tools()
    {
        global $error_messages, $error_regexp, $jpc_config, $messages, $nav;        
        $this->err_msg  = $error_messages;
        $this->err_regexp = $error_regexp; 
        $this->config   = $jpc_config;
        $this->msg      = $messages;
        $this->nav      = $nav;
        $this->connect  = new Connect;
        $this->log      = new Log;
        $this->tpl_dir  = $jpc_config["tpl_dir"];
        $this->tpl_halt_on_error = $jpc_config["tpl_halt_on_error"];

        $this->httpvars =  ($_POST) ? $_POST : $_GET;

        $_SESSION["httpvars"] = $this->httpvars;

        if (is_array($this->httpvars)) {
        foreach ($this->httpvars as $key => $value)
        {
            $_SESSION["userdata"][trim($key)] = !is_array($value) ? trim($value) : $value;
            $_SESSION["formdata"][trim($key)] = !is_array($value) ? trim($value) : $value;
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
                    $tpl_arr[$key] = $this->config["site_default_language"]."/".$value;
                } else {
                    if (in_array(strtolower($_SESSION["userdata"]["lang"]),$this->config["site_allowed_languages"])) {
                        $tpl_arr[$key] = $_SESSION["userdata"]["lang"]."/".$value;
                    } else {
                        $tpl_arr[$key] = $this->config["site_default_language"]."/".$value;
                    }
                }
            }
            $this->tpl->set_file($tpl_arr);
        }

        $this->tpl->set_block("repository","navigation");
        if (!isset($_SESSION["formdata"])) {
            $_SESSION["formdata"] = array();
        }
        $this->fill_form($_SESSION["formdata"]);
    }

    /**
     * Redirects to the specified URL.
     *
     * @param   string  $url
     * @access  public
     * @return  void
     */
    function goto($url="")
    {
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on'){
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        if (isset($_SERVER["SERVER_PORT"])) {
            $port_num = ":" . $_SERVER["SERVER_PORT"];
        } else {
            $port_num = "";
        }
        Header("Location: " . $protocol . $_SERVER["SERVER_NAME"] . $port_num . $_SERVER["PHP_SELF"] . $url);
        exit;
    }

    /**
     * Verification method. You can choose between custom verification (should be implemented)
     * and the standard regular expressions defined in error.eng.php
     *
     * @param   string  $type depends on $custom - if $custom is true references to the correct verification sequence else regular expression
     * @param   string  $content the value that is going to be validated
     * @param   boolean $custom flag for choosing between custom/standard verification
     * @access  public
     * @return  boolean
     */
    function is_valid($type, $content, $custom = false)
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
                if (count($reg) == 0 && $this->is_valid($this->err_regexp["_tld"],$tld) && $this->is_valid($this->err_regexp["_sld"],$sld)) {
                    $ok = true;
                }
                // deep-check: Joker-available domain
                if ($ok && "joker_domain" == $type) {
                    $ok = in_array($tld, $_SESSION["auto_config"]["avail_tlds"]);
                }
                break;

                case "host":
                $reg = explode(".",$content);
                $tld = array_pop($reg); // strip tld
                $sld = array_pop($reg); // strip sld
                $content = (is_array($reg)) ? implode(".",$reg) : "";
                if (preg_match($this->err_regexp["_host"], $content)) {
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
                if (preg_match($this->err_regexp["_email"], $addr)) {
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
                if ($this->is_valid($this->err_regexp["_tld"], $content)) {
                    $ok = in_array($content, $_SESSION["auto_config"]["avail_tlds"]);
                }
                break;
                
                case "ns_list":
                $ok = preg_match("/[:]/i", $content);
                break;
            }
            return $ok;
        }
    }

    /**
     * Verification method. Checks whether the provided contact handles are correct.
     *
     * @param   string  $tld specifies for which top level domain is the contact handle relevant
     * @param   string  $content contact handle
     * @access  public
     * @return  boolean
     */
    function is_valid_contact_hdl($content, $tld = "")
    {
        $ok = false;
        if (in_array(strtolower($tld), $_SESSION["auto_config"]["avail_tlds"])) {            
            $ok = preg_match($this->err_regexp["_" . trim(strtolower($tld)) . "_tld"], $content);            
        } else {
            foreach ($_SESSION["auto_config"]["avail_tlds"] as $value) {            
                if (isset($this->err_regexp["_" . trim(strtolower($value)) . "_tld"])) {
                    if ($ok = preg_match($this->err_regexp["_" . trim(strtolower($value)) . "_tld"], $content)) {
                        break;
                    }
                }
            }
        }        
        return $ok;
    }

    /**
     * Retunrs the domain tld corresponding to a contact handle.
     *
     * @param   string  $cnt_hdl contact handles
     * @access  public
     * @return  string
     */
    function type_of_contact($cnt_hdl)
    {
        foreach ($_SESSION["auto_config"]["avail_tlds"] as $value) {
            if ($this->is_valid_contact_hdl($cnt_hdl, $value)) return strtolower($value);
        }        
        return "unknown";
    }

    /**
     * Returns the domain part of a 'something' (email, hostname, contact) or
     * false in case of incorrect syntax
     *
     * @param   string  $string
     * @access  public
     * @return  mixed
     */
    function get_domain_part($string)
    {
        $reg = Array();
        $reg = explode(".",$string);
        $pre_tld = array_pop($reg); // strip tld
        $pre_sld = array_pop($reg); // strip sld
        $void = preg_match("/^([a-z]+)(#[0-9]+)?$/i",$pre_tld,$reg);
        $tld = $reg[1];
        $void = preg_match("/[@]?([-a-z0-9]+)$/i",$pre_sld,$reg);
        $sld = $reg[1];
        if ($this->is_valid("domain",$sld.".".$tld,true)) {
            return (array("sld" => $sld, "tld" => $tld));
        } else {
            return false;
        }
    }

    /**
     * Automagically fills all previously typed in form values.
     *
     * @param   string  $form_data array that contains all previously typed in data
     * @access  private
     * @return  void
     */
    function fill_form($form_data)
    {        
        if (is_array($form_data)) {
            foreach($form_data as $key => $value)
            {
                switch (substr($key,0,2)) {
                    case "t_":
                    case "a_":
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
     * Fill the form data array so that the values could be autofilled
     *
     * @param   array   $res_arr array that contains raw request data
     * @param   string  $type which object type is handled
     * @access  public
     * @return  mixed
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
        case "domain":            
            foreach ($res_arr as $value)
            {
                preg_match("/^domain\.(.*):$/i",$value["0"],$match);
                $form_data["t_contact_".str_replace("-","_",$match["1"])] = $value["1"];
                if (preg_match("/^domain\.country:$/i",$value["0"])) {
                $form_data["s_contact_country"] = $value["1"];
                }
            }
            break;
        }
        return $form_data;
    }

    /**
     * Parses the web site
     *
     * @access  public
     * @return  void
     */
    function parse_site()
    {
        $this->tpl->set_var("RPANEL_VER", $this->config["rpanel_ver"]);        
        $this->tpl->set_var("DMAPI_VER", $_SESSION["auto_config"]["dmapi_ver"]);        
        $this->tpl->set_var("RESELLER_ACCOUNT_BALANCE", $_SESSION["auto_config"]["account_balance"]);        
        $this->tpl->set_var("ENCODING", $this->config["site_encoding"]);
        $this->tpl->set_var("DMAPI_FORM_ACTION", $this->config["site_form_action"]);

        if (!$this->has_sessid($_SESSION["auth-sid"])) {
            if (isset($_SESSION["auth-sid"])) {
                $this->general_err("GENERAL_ERROR", $this->err_msg["_sess_expired"]);                
            }
            $this->tpl->parse("SITE_BODY", "login_form");
        } else {
            $this->tpl->set_var("USER_NAME", $_SESSION["username"]);
            $joker_url = $this->config["joker_url"];
            $this->tpl->set_var("JOKER_URL", $joker_url);            
            $this->tpl->parse("MENU","menu_tpl");
            $this->tpl->set_var("NAV_TXT", $this->nav["where_you_are"]);
            $this->tpl->parse("SITE_BODY", "body_tpl");
        }
        $this->tpl->parse("MAIN", "main_tpl");
        if ($this->config["tpl_cleanup_mode"] == "on") {            
            $this->tpl->set_var("MAIN", $this->rm_comments($this->tpl->get("MAIN")));
        }
        $this->tpl->p("MAIN");
    }

    /**
     * Parses raw server responses into an array
     *
     * @param   string  $text part of a raw server response
     * @param   boolean $keyval if true recognizes the second value as a sequence including spaces else considers the space as a delimiter between elements
     * @access  public
     * @return  void
     */
    function parse_text($text, $keyval = false)
    {
        $text = trim($text);
        if ($text != "") {
            $raw_arr = explode("\n", $text);
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
     * Returns an array containing a domain list or false in case of failure
     *
     * @param   string  $pattern customizes the returned result
     * @access  public
     * @return  mixed
     */
    function domain_list($pattern)
    {
        $fields = array(
        "pattern"   => $pattern
            );
        if ($this->connect->execute_request("query-domain-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            return ($this->parse_text($_SESSION["response"]["response_body"]));
        } else {
            return false;
        }
    }
    
    /**
     * Returns an array containing a zone list or false in case of failure
     *
     * @param   string  $pattern customizes the returned result
     * @access  public
     * @return  mixed
     */
    function zone_list($pattern)
    {
        $fields = array(
        "pattern"   => $pattern
            );
        if ($this->connect->execute_request("dns-zone-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            return ($this->parse_text($_SESSION["response"]["response_body"]));
        } else {
            return false;
        }
    }
    
    /**
     * Shows a list of all available requests
     *
     * @access  public
     * @return  void
     */
    function show_request_list()
    {
        $this->nav_submain = $this->nav["show_requests"];
        $this->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tpl->parse("NAV", "navigation");

        $this->tpl->set_block("repository","result_table_submit_btn","res_tbl_submit_btn");
        $this->tpl->set_block("repository","result_table_row");                            
        $this->tpl->set_block("repository","result_table");                                

        $result = $this->get_request_list();
        foreach ($result as $value)
        {            
            $this->tpl->set_var("FIELD1", $value);
            $this->tpl->parse("FORMTABLEROWS", "result_table_row", true);
        }
        $this->tpl->parse("CONTENT", "result_table");
    }

    /**
     * Returns an array of all available requests
     *
     * @access  public
     * @return  array
     */
    function get_request_list()
    {        
        $fields = "";
        $list = array();
        if ($this->connect->execute_request("query-request-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $result = $this->parse_text($_SESSION["response"]["response_body"],true);
        }        
        if ($result != $this->config["empty_result"] && is_array($result)) {
            foreach($result as $value)
            {                    
                $list[] = $value["0"];                                
            }
            return $list;
        }
        return false;
    }

    /**
     * Returns the supported tlds
     *
     * @access  public
     * @return  array
     */
    function get_tld_list()
    {        
        $fields = "";
        $list = array();
        if ($this->connect->execute_request("query-tld-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $result = $this->parse_text($_SESSION["response"]["response_body"],true);
        }        
        if ($result != $this->config["empty_result"] && is_array($result)) {     
            foreach($result as $value)
            {
                $list[] = $value["0"];
            }
            sort($list, SORT_STRING);
            return $list;
        }
        return false;
    }

    /**
     * Returns the actual DMAPI version
     *
     * @access  public
     * @return  array
     */
    function get_dmapi_version()
    {        
        $fields = "";
        $list = array();
        if ($this->connect->execute_request("version", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $result = $this->parse_text($_SESSION["response"]["response_body"],true);
        }
        if ($result != $this->config["empty_result"] && is_array($result)) {     
            foreach($result as $value)
            {
                if ($value["0"] == "Version:") {                    
                    return $value["1"];
                }
            }
        }
        return false;
    }

    /**
     * Check for an existing and valid session id
     *
     * @param   string  $sessid
     * @access  private
     * @return  boolean
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
     * Check whether the application wasn't inactive for too long.
     * If inactivity is over the limit then session will be destroyed 
     * as the DMAPI service will anyway require a new login.
     *
     * @access  public
     * @return  boolean
     */
    function is_too_long_inactive()
    {
        $inactivity_with_dmapi_server = time() - $_SESSION["last_request_time"];
        $dmapi_inactivity_timeout_allowed = $this->config["dmapi_inactivity_timeout"] * 60;
        if ($dmapi_inactivity_timeout_allowed < $inactivity_with_dmapi_server) {
            session_destroy();
            $_SESSION["auth-sid"] = "";
            $this->tpl->set_block("repository", "general_error_box");
            return true;
        }       
        return false;
    }

    /**
     * Returns a descriptive string with the tracking id
     *
     * @access  public
     * @return  string
     */
    function show_tracking_id()
    {
        return "Tracking ID: ".$_SESSION["response"]["response_header"]["tracking-id"];
    }

    /**
     * Prints the tracking id and processing id of a specific request
     *
     * @access  public
     * @param   string  $add_info prints additional information
     * @param   boolean $track_id if true prints the tracking id
     * @param   boolean $proc_id if true prints the processing id
     * @return  void
     */
    function show_request_status($add_info = "", $track_id = true, $proc_id = true)
    {
        $this->tpl->set_block("repository","general_success_box");
        if (is_array($_SESSION["response"]["response_header"])) {
        $add_info .= "\n";
        foreach($_SESSION["response"]["response_header"] as $key => $value) 
        {
            if ($track_id && strtolower($key) == "tracking-id") {
                $add_info .= "Tracking ID: " . $this->get_request_results_link(strtolower($key), $value) . "\n";
            }
            if ($proc_id && strtolower($key) == "proc-id") {                
                $add_info .= "Processing ID: " . $this->get_request_results_link(strtolower($key), $value) . "\n";
            }
        }
        }
        $this->tpl->set_var("STATUS_MSG", nl2br($add_info));
        $this->tpl->parse("GENERAL_ERROR", "general_success_box");
    }

    /**
     * Prints an error message. Take into account that $error_info and $detailed_info are self-excluding
     *
     * @access  public
     * @param   string  $varname name of the variable in which the error message will be printed
     * @param   string  $errmsg additional text to the error message - will be printed on top of it
     * @param   boolean $detailed_info includes all error messages plus status description, tracking id and processing id
     * @param   boolean $error_info includes only tracking id and processing id
     * @return  void
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
        $this->tpl->set_var("ERROR_MSG", $errmsg . nl2br($add_info));
        $this->tpl->parse($varname, "general_error_box");        
    }

    /**
     * Prints a field error message
     *
     * @access  public
     * @param   string  $varname name of the variable in which the error message will be printed
     * @param   string  $errmsg text for the error message
     * @return  void
     */
    function field_err($varname, $errmsg, $accumulate = false, $separator = "<br />")
    {
        if (!$accumulate) {
            $this->tpl->set_var("ERROR_MSG", $errmsg);
        } else {
            $this->tpl->set_var("ERROR_MSG", $this->tpl->get("ERROR_MSG") . $separator . $errmsg);
        }
        $this->tpl->parse($varname, "field_error_box");
    }

    /**
     * Sets a link to request results for status id
     *
     * @access  public
     * @param   string  $request_type type of request to be formated
     * @param   string  $id tracking-id, proc-id
     * @return  string
     */
    function get_request_results_link($request_type, $id)
    {
        switch ($request_type)
        {
            case "proc-id":
                $link = "<a href=\"index.php?mode=result_retrieve&pid=$id\">$id</a>";
                break;
            case "tracking-id":
                $link = "<a href=\"index.php?mode=result_retrieve&tid=$id\">$id</a>";
                break;
            default:
                $link = "error";
                break;    
        }
        return $link;        
    }

    /**
     * Returns an array with all object details
     *
     * @access  public
     * @param   string  $type type of object
     * @param   string  $object defines a query object
     * @param   boolean $keyval if true recognizes the second value as a sequence including spaces else considers the space as a delimiter between elements
     * @return  mixed
     */
    function query_object($type, $object, $keyval = false)
    {
        switch ($type) {

        case "domain":
            $fields = array(
                "domain"    => $object
            );
            break;

        case "contact":
            $fields = array(
                "contact"   => $object
            );
            break;

        case "host":
            $fields = array(
                "host"  => $object
            );
            break;

        default:
            $this->log->req_status("e", "function query_object(): Unknown object type: $type");
            return false;
            break;
        }        
        if ($this->connect->execute_request("query-whois", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            return $this->parse_text($_SESSION["response"]["response_body"], $keyval);
        } else {
            return false;
        }
    }

    /**
     * Returns an array containing complete set of zone records for a given zone
     *
     * @param   string  $domain zone name
     * @access  public
     * @return  mixed
     */
    function zone_view($domain)
    {
        $fields = array(
            "domain"   => $domain
            );
        if ($this->connect->execute_request("dns-zone-get", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            return $this->parse_text($_SESSION["response"]["response_body"]);
        } else {
            return false;
        }
    }

    /**
     * Sends a mail
     *
     * @access  public
     * @param   string  $to
     * @param   string  $from
     * @param   string  $replyTo
     * @param   string  $cc
     * @param   string  $subject
     * @param   string  $text
     * @param   string  $html
     * @param   string  $bcc
     * @param   string  $attach
     * @return  boolean
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
    
    /**
     * Removes comments in the HTML
     *
     * @access  public
     * @param   string  $str
     * @return  string
     */
    function rm_comments($str) 
    {
        return preg_replace('/(<!-- (.*) -->)/Us', '', $str);        
    }
    
    /**
     * Formats and prints a variable/array
     *
     * @access  public
     * @param   mixed  $var
     */
    function prep($var)
    {
        print "<pre>";
        print_r($var);
        print "</pre>";
    }
    
    /**
     * Empty the formdata array
     *
     * @access  public
     */
    function empty_formdata()
    {
        unset($_SESSION["formdata"]);
    }
    
    /**
     * Encode the # character
     *
     * @access  public
     * @param   string  $str
     * @return  string
     */
    function encode_sharp($str)
    {
        return str_replace("#", '&#35;', $str);
    }
    
    /**
     * Simplifies parsing of bulk entries
     *
     * @access  public
     * @param   string  $list
     * @param   string  $delimiter
     */
    function sanitize_bulk_entries(&$list, $delimiter)
    {        
        $pattern = "/[,;\t\ ]+/";
        $list = preg_replace($pattern, $delimiter, $list);
        $list = str_replace("\r", "", $list);
    }
    
    /**
     * Define directory separator based on OS
     *
     * @access  public
     * @param   string  $separator
     */
    function define_dir_separator(&$separator)
    {
        if (strtoupper(substr(php_uname("s"), 0, 3)) === 'WIN') {            
            $separator = "\\";
        } else {
            $separator = "/";
        }
    }
    
    /**
     * Creates a temp directory
     *
     * @access  public
     * @param   string  $temp_dir
     * @param   string  $temp_perm
     */
    function create_temp_directory($temp_dir, $temp_perm)
    {
        if (!is_dir($temp_dir)) {
            if (!mkdir($temp_dir, $temp_perm)) {
                die("Temp dir error: Cannot create " . $temp_dir);                    
            }
        } else {
            if (!chmod($temp_dir, $temp_perm)) {
                die("Temp dir error: Cannot change mod of " . $temp_dir);                    
            }
        } 
        
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, $temp_perm);
        } else {
            chmod($temp_dir, $temp_perm);
        }
    }
    
    /**
     * Bulk entries parser
     *
     * @access  public
     * @param   string  $list
     * @param   string  $type
     * @param   boolean $limit
     * @return  boolean
     */
    function parse_bulk_entries(&$list, $type = "domain", $limit = false)
    {
        $status = true;    
        switch ($type)
        {
            case "domain":                                
                $element_delimiter = $line_delimiter = "\n";
                // FYI: do not set an empty string ("") for a line delimiter!
                //otherwise this code will not work
                $this->sanitize_bulk_entries($list, $element_delimiter);
                $temp_list = array();        
                $list = explode($line_delimiter, $list);        
                if (is_array($list)) {
                    foreach ($list as $key => $entry)
                    {
                        if (!empty($entry)) {                            
                                $temp_list[] = $entry;
                        }
                    }
                }                  
                $list = $temp_list;              
                break;    
            case "bulk_transfer":        
                $element_delimiter = "#";
                // FYI: do not set an empty string ("") for a line delimiter!
                //otherwise this code will not work
                $line_delimiter = "\n";
                $this->sanitize_bulk_entries($list, $element_delimiter);
                $temp_list = array();        
                $list = explode($line_delimiter, $list);        
                if (is_array($list)) {
                    foreach ($list as $key => $entry)
                    {
                        if (!empty($entry)) {
                            $pair = array();
                            $pair = explode($element_delimiter, $entry);
                            if (count($pair) > 1) {
                                $temp_list[$pair[0]] = $pair[1];
                            } else {
                                $status = false;        
                            }
                        }
                    }
                }
                $list = $temp_list;
                break;
        }                
        if (is_array($list) && $limit && count($list) > $limit) {        
            $list = array_slice($list, 0, $limit);
        }
        return $status;
    }
    

} //end of class Tools

?>
