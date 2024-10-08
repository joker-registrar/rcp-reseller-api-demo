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
     * Contains array of regular expressions for special contact handles
     *
     * @var     array
     * @access  private
     */
    var $tld_regexp  = array();

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
     * template engine object
     *
     * @var     Template
     * @access  private
     * @see     Tools()
     */

    var $tpl = null;

    /**
     * Array containing all template files
     *
     * @var     array
     * @access  private
     */
    var $template_files = array(
        "main_tpl"                      => "main/tpl_main.html",
        "menu_tpl"                      => "main/tpl_menu.html",
        "body_tpl"                      => "main/tpl_body.html",
        "popup_tpl"                     => "main/tpl_popup.html",
        "login_form"                    => "main/tpl_login_form.html",
        "domain_view_form"              => "domain/tpl_domain_view_form.html",
        "domain_list_form"              => "domain/tpl_domain_list_form.html",
        "domain_register_form"          => "domain/tpl_domain_register_form.html",
        "domain_register_overview_form" => "domain/tpl_domain_register_overview_form.html",
        "domain_renew_form"             => "domain/tpl_domain_renew_form.html",
        "domain_set_privacy_form"       => "domain/tpl_domain_set_privacy_form.html",
        "domain_privacy_form"           => "domain/tpl_domain_privacy_form.html",
        "domain_grants_form"            => "domain/tpl_domain_grants_form.html",
        "domain_transfer_form"          => "domain/tpl_domain_transfer_form.html",
        "domain_fast_transfer_form"     => "domain/tpl_fast_domain_transfer_form.html",
        "domain_bulk_transfer_step1"    => "domain/tpl_domain_bulk_transfer_step1_form.html",
        "domain_bulk_transfer_step2"    => "domain/tpl_domain_bulk_transfer_step2_form.html",
        "domain_modify_form"            => "domain/tpl_domain_modify_form.html",
        "domain_delete_form"            => "domain/tpl_domain_delete_form.html",
        "domain_repository"             => "domain/tpl_domain_repository.html",
        "domain_lock_unlock_form"       => "domain/tpl_domain_lock_unlock_form.html",
        "domain_autorenew_form"         => "domain/tpl_domains_autorenew_form.html",
        "domain_authid_form"            => "domain/tpl_domain_authid_form.html",
        "domain_redemption_form"        => "domain/tpl_domain_redemption_form.html",
        "domain_grants_change_step1"     => "domain/tpl_domain_grants_change_step1_form.html",
        "domain_owner_change_step1"     => "domain/tpl_domain_owner_change_step1_form.html",
        "domain_owner_change_step2"     => "domain/tpl_domain_owner_change_step2_form.html",
        "zone_list_form"                => "zone/tpl_zone_list_form.html",
        "zone_repository"               => "zone/tpl_zone_repository.html",
        "dom_ns_list_form"              => "ns/tpl_dom_ns_list_form.html",
        "ns_handle_form"                => "ns/tpl_ns_handle_form.html",
        "ns_repository"                 => "ns/tpl_ns_repository.html",
        "ns_mass_modify_form_step1"     => "ns/tpl_ns_mass_modify_form_step1.html",
        "ns_mass_modify_form_step2"     => "ns/tpl_ns_mass_modify_form_step2.html",
        "contact_list_form"             => "contacts/tpl_contact_list_form.html",
        "contact_form"                  => "contacts/tpl_contact_form.html",
        "contact_sel_tld_form"          => "contacts/tpl_contact_select_tld_form.html",
        "contact_repository"            => "contacts/tpl_contact_repository.html",
        "contact_verified_form"          => "contacts/tpl_contact_verified_form.html",
        "user_property_form"            => "user/tpl_user_property_form.html",
        "repository"                    => "common/tpl_repository.html",
        "country_ls"                    => "common/tpl_countries.html",
        "language_ls"                   => "common/tpl_eu_languages.html",
        "result_list"                   => "common/tpl_result_list.html",
        "tips"                          => "common/tpl_other_tips.html",
        "home_page"                     => "common/tpl_home_page.html",
        "nexus_category"                => "common/tpl_nexus_category.html",
        "nexus_category_country"        => "common/tpl_nexus_category_country.html",
        "paging_repository"             => "common/tpl_paging_repository.html",
        "nexus_application_purpose"     => "common/tpl_nexus_application_purpose.html",
        "idn_language"                  => "common/tpl_idn_languages.html",
        "account_type"                  => "common/tpl_account_type.html",
        "idn_convert_form"              => "service/tpl_idn_convert_form.html",
        "js_inc"                       => "common/tpl_jsinc.html"
    );

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Tools()
     *
     * @access  private
     * @return  void
     */
    function __construct()
    {
        global $error_messages, $error_regexp, $tld_regexp, $jpc_config, $messages, $nav, $tools;
        if (!isset($tools)) $tools = $this;
        $this->err_msg  = $error_messages;
        $this->err_regexp = $error_regexp;
        $this->tld_regexp = $tld_regexp;
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
    function go_to($url="")
    {
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on'){
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        //if (isset($_SERVER["SERVER_PORT"])) {
        //    $port_num = ":" . $_SERVER["SERVER_PORT"];
        //} else {
            $port_num = "";
        //}
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
                    $content = $this->format_fqdn($content, "ascii");                                    

	            $reg = explode(".", $content);
	            $rlen = count($reg);
	            if ($rlen >3 || $rlen <2) return (false);
	            $tld = $reg[1].($rlen>2 ? ".".$reg[2] : ""); // co.uk possible
	            $sld = $reg[0]; // sld always 1st part
	            if ($this->is_valid($this->err_regexp["_tld"],$tld) && $this->is_valid($this->err_regexp["_sld"],$sld)) {
	                $ok = true;
	            }
	            // deep-check: Joker-available domain?
	            if ($ok && $type == "joker_domain") {
	                $ok = in_array(strtolower($tld), $_SESSION["auto_config"]["avail_tlds"]);
	            }
                    break;

                case "host":
                    $content = $this->format_fqdn($content, "ascii");                                    
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

        $tlds = $this->type_of_contact($content);

        if (count($tlds) > 0) {
            if ($tld === "") {
                $ok = true;
            } elseif (in_array(strtolower($tld), $tlds)) {
                $ok = true;
            }
        }
        return $ok;
    }

    /**
     * Returns the domain tld corresponding to a contact handle.
     *
     * @param   string  $cnt_hdl contact handles
     * @access  public
     * @return  string
     */
    function type_of_contact($cnt_hdl)
    {

        $tlds = [];

        foreach ($this->tld_regexp as $tld => $contactHandleMatchRegex) {

            if (preg_match($contactHandleMatchRegex, $cnt_hdl)) {
                $tlds[] = $tld;
            }

        }

        foreach ($_SESSION["auto_config"]["contact_prefixes"] as $tld => $prefix) {

            if (strlen($prefix)>=4) {
                if (preg_match("/^".$prefix."/i", $cnt_hdl)) {
                    $tlds[] = $tld;
                }
            }

        }

        return array_unique($tlds);

    }

    /**
     * Handles presentation of an IDN
     *
     * @param   string   $fqdn                  fqdn value
     * @param   string   $to                    conversion type: unicode, ascii
     * @param   string   $type                  ns, domain, email etc.
     * @param   boolean  $add_xn_presentation   if true add punycode presentation of the fqdn
     * @access  public
     * @return  string
     */
    function format_fqdn($fqdn, $to, $type = "domain", $add_xn_presentation = false)
    {
        if ($this->config["idn_compatibility"]) {
            switch ($to)
            {
                case "unicode":
                    if (("host" == $type || "email" == $type) ? strstr($fqdn, "xn--") : strpos($fqdn, "xn--") === 0) {
                        $unicode_fqdn = $this->idn_codec($fqdn, "unicode");
                        if ($add_xn_presentation) {
                            $fqdn = $unicode_fqdn . " (" . $fqdn . ")";
                        } else {
                            $fqdn = $unicode_fqdn;
                        }
                    }
                    break;
                case "ascii":
                    $fqdn = $this->idn_codec($fqdn, "ascii");
                    break;
            }
        }
        return $fqdn;
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
        $tld = "";
        $pre_sld = "";
        foreach ($_SESSION["auto_config"]["avail_tlds"] as $a_tld)
        {
           $result = array();
           if (preg_match("/^((.*)\.)?$a_tld$/",strtolower(trim($string)),$result)) {
                if (strlen($a_tld) > strlen($tld) ) {
                    $tld = $a_tld;
                    if (count($result)>=2) {
                        $pre_sld = $result[2];
                    } else {
                        $pre_sld = "";
                    }
                }
            }
        }
        if (strlen($tld) === 0) {
            $reg = explode(".",$string);
            $tld = array_pop($reg); // strip tld
            $pre_sld = array_pop($reg); // strip sld
        }
        $result = array();
        if (preg_match("/[@]?([-a-z0-9]+)$/i",$pre_sld,$result)) $sld = $result[1];
        if (isset($sld) && $this->is_valid("domain",$sld.".".$tld,true)) {
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
        $form_data = array();
        switch ($type) {

        case "contact":
            foreach ($res_arr as $value)
            {
                preg_match("/^contact\.(.*):$/i",$value["0"],$match);
                $form_data["t_contact_".str_replace("-","_",$match["1"])] = $value["1"];
                if (preg_match("/^contact\.country:$/i",$value["0"])) {
                $form_data["s_contact_country"] = $value["1"];
                }
                if (preg_match("/^contact\.account-type:$/i",$value["0"])) {
                $form_data["s_contact_account_type"] = $value["1"];
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
                if (preg_match("/^domain\.account-type:$/i",$value["0"])) {
                $form_data["s_contact_account_type"] = $value["1"];
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
        $this->tpl->set_var("CURRENT_YEAR", date("Y"));
        $this->tpl->set_var("RPANEL_LOCATION_INFO", htmlentities($this->config["rpanel_location_info"], ENT_QUOTES).' ');
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
        if (!empty($this->config["rpanel_background"])) {
          $this->tpl->set_var("BACKGROUND", "background='".$this->config["rpanel_background"]."'");
        }
        if (isset($_SESSION["userdata"]["viewmode"]) && $_SESSION["userdata"]["viewmode"]=="popup") {
            $this->tpl->set_block("js_inc","MOOTOOLS","MOO");
            $this->tpl->parse("ADDITIONAL_HEAD", "MOOTOOLS",true);
            $this->tpl->parse("MAIN", "popup_tpl");
            unset($_SESSION["userdata"]["viewmode"]);
        } else {
            $this->tpl->parse("MAIN", "main_tpl");
        }
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
    function parse_text($text, $keyval = false, $limit = 0, $separator = " ")
    {
        $result=array();
        $text = trim($text);
        if ($text != "") {
            $raw_arr = explode("\n", $text);
            if (is_array($raw_arr)) {
                foreach ($raw_arr as $key => $value)
                {
                    if (!$keyval) {
                        if ($limit>0) {
                            $result[$key] = explode($separator,$value,$limit);
                        } else {
                            $result[$key] = explode($separator,$value);
                        }
                    } else {
                        $temp_val = explode($separator, $value);
                        $val1 = array_shift($temp_val);
                        $result[$key] = array($val1,implode($separator,$temp_val));
                    }
                }
            }
        }
        return (is_array($result) ? $result : $this->config["empty_result"]);
    }

    /**
     * Parses raw server responses into an array with columns as key
     *
     * @param   mixed  $response
     * @access  public
     * @return  void
     */
    function parse_response_list($response, $separator = " ")
    {
        $text = isset($response["response_body"]) ? trim($response["response_body"]) : "";
        $columns = array();
        if (isset($response["response_header"]["separator"])) {
            switch ($response["response_header"]["separator"]) {
                case "SPACE":
                    $separator = " ";
                    break;
                case "TAB":
                    $separator = "\t";
                    break;
            }
        }
        if (!isset($response["response_header"]["columns"])) {
            return $this->parse_text($text,false,0,$separator);
        } else {
            $columns = explode(",", $response["response_header"]["columns"]);
        }
        if ($text != "") {
            $raw_arr = explode("\n", $text);
            if (is_array($raw_arr)) {
                foreach ($raw_arr as $key => $value)
                {
                    $temp_val = explode($separator, $value, count($columns));
                    for ($i=count($temp_val);$i<count($columns);$i++) { $temp_val[] = "";}
                    $result[$key] = array_combine($columns,$temp_val);

                }
            }
        }
        return (isset($result) && is_array($result) ? $result : $this->config["empty_result"]);
    }

    /**
     * Formats a raw DMAPI date string
     *
     * @param   string  $raw_date date DMAPI string
     * @access  public
     * @return  string
     */
    function prepare_date($raw_date)
    {
        if (strpos($raw_date, "-") === false) {
            $year   = substr($raw_date, 0, 4);
            $month  = substr($raw_date, 4, 2);
            $day    = substr($raw_date, 6, 2);
            $hour   = substr($raw_date, 8, 2);
            $min    = substr($raw_date, 10, 2);
            $sec    = substr($raw_date, 12, 2);
        } else {
            $year   = substr($raw_date, 0, 4);
            $month  = substr($raw_date, 5, 2);
            $day    = substr($raw_date, 8, 2);
            $hour   = substr($raw_date, 11, 2);
            $min    = substr($raw_date, 14, 2);
            $sec    = substr($raw_date, 17, 2);
        }
        return date($this->config["date_format_results"], mktime($hour, $min, $sec, $month, $day, $year));
    }

    /**
     * Returns an array containing a domain list or false in case of failure
     *
     * @param   string  $pattern customizes the returned result
     * @access  public
     * @return  mixed
     */
    function domain_list($pattern,$from=1,$to=0)
    {
        $fields = array(
        "pattern"   => $pattern,
        "showstatus" => 1,
        "showgrants" => 1,
        "showprivacy" => 1,
            );
        if ($to>0) {
            $fields["from"] = $from;
            $fields["to"] = $to;
        }
        if ($this->connect->execute_request("query-domain-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            return $this->parse_response_list($_SESSION["response"]);
        } else {
            return false;
        }
    }

    /**
     * strcmp on the elements of an array
     *
     * @param   array  $arr1 customizes the returned result
     * @param   array  $arr2 customizes the returned result
     * @access  public
     * @return  mixed
     */
    function strcmparr($arr1, $arr2)
    {
        return strcmp($arr1["2"], $arr2["2"]);
    }

    /**
     * Reorder domain array
     *
     * @param   array    $list           domains
     * @param   boolean  $idn_compatible true if IDNs are enabled, false otherwise
     * @access  public
     * @return  mixed
     */
    function set_domain_order(&$list, $idn_compatible)
    {
        if ($idn_compatible && is_array($list)) {
            foreach ($list as $key => $data_set)
            {
                $list[$key]["2"] = $this->format_fqdn($data_set["0"], "unicode", "domain", false);
            }
            usort($list, array("Tools", "strcmparr"));
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
            return isset($_SESSION["response"]["response_body"]) ? $this->parse_text($_SESSION["response"]["response_body"]) : array();
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
        $fields = array();
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
        $fields = array();
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
        $fields = array();
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
     * Check the pattern type
     *
     * @param   string  $pattern pattern content
     * @param   string  $type    pattern type
     * @access  public
     * @return  boolean
     */
    function is_pattern($pattern, $type = "catch_all")
    {
        switch ($type)
        {
            case "catch_all":
                if ("" == $pattern || "*" == $pattern) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * Convert UNICODE to PUNYCODE or backwards
     *
     * @param   string  $string string to convert - could be email, domain etc.
     * @param   string  $to     conversion type
     * @access  public
     * @return  boolean
     */
    function idn_codec($string, $to)
    {
        require_once(dirname(__FILE__)."/idn/idna_convert.class.php");
        switch (strtolower($to))
        {
            case "ascii":
                $IDN = new idna_convert();
                // Encode it to its punycode presentation
                return $IDN->encode($string);
                break;
            case "unicode":
                $IDN = new idna_convert();
                // Encode it to its UTF-8 presentation
                return $IDN->decode($string);
                break;
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
            if (strtolower($key) == "result") {
                $add_info .= "Result: " .  $value . "\n";
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
                "domain"    => $object,
                "internal"  => 1
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
    function send_mail($to,$from,$replyTo,$cc,$text,$subject="", $html="",$bcc="",$attach="")
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
    function sanitize_bulk_entries(&$list, $delimiter, $multiplecolumns=true)
    {
        if ($multiplecolumns) {
            $pattern = "/[,;\t\ ]+/";
            $list = str_replace("\r", "", $list);
            $list = preg_replace($pattern, $delimiter, $list);
        } else {
            // 2 columns are handled separate, so just the first delimiter will be replaced
            $pattern = "/^(.*?)[,;\t\ ]+(.*)/m";
            $list = str_replace("\r", "", $list);
            $list = preg_replace($pattern, "\\1".$delimiter."\\2", $list);
        }

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
                $this->sanitize_bulk_entries($list, $element_delimiter, false);
                $temp_list = array();
                $list = explode($line_delimiter, $list);
                if (is_array($list)) {
                    foreach ($list as $key => $entry)
                    {
                        if (!empty($entry)) {
                            $pair = array();
                            $pair = explode($element_delimiter, $entry, 2);
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
