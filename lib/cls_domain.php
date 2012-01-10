<?php

/**
 * Domain management related class. Visualization and request handling
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
     * @var     string
     * @access  private
     * @see     Domain()
     */
    var $nav_main  = "";

    /**
     * Represents the 2nd level of the current user position.
     * Its value is set for every function.
     *
     * @var     string
     * @access  private
     */
    var $nav_submain  = "";

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
     * Array that contains configuration data.
     * Its values are overridden in the class constructor.
     *
     * @var     array
     * @access  private
     * @see     Domain()
     */
    var $config  = array();

    /**
     * Array that defines how many entries are shown per page.
     *
     * @var     array
     * @access  private
     * @see     Domain()
     */
    var $domain_list_entries_per_page = array(20, 50, 100);

    /**
     * Default entry page
     *
     * @var     integer
     * @access  private
     * @see     Domain()
     */
    var $domain_list_default_entry_page = 20;

    /**
     * Defines the number of paging links on every page
     *
     * @var     integer
     * @access  private
     * @see     Domain()
     */
    var $domain_list_page_links_per_page = 10;

    /**
     * Default page for paging
     *
     * @var     integer
     * @access  private
     * @see     Domain()
     */
    var $domain_list_default_page = 1;

    /**
     * Default filename for the exported result list
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     Domain()
     */
    var $domain_list_filename = "domain_list";

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Domain()
     *
     * @access  private
     * @return  void
     */
    function Domain()
    {
        global $error_messages, $error_regexp, $jpc_config, $tools, $messages, $nav, $roles;
        $this->config  = $jpc_config;
        $this->err_msg = $error_messages;
        $this->err_regexp = $error_regexp;
        $this->tools   = $tools;
        $this->msg     = $messages;
        $this->nav     = $nav;
        $this->roles   = $roles;
        $this->connect = new Connect;
        $this->nav_main= $this->nav["domain"];
        $this->temp_dir  = $jpc_config["temp_dir"];
        $this->temp_perm = $jpc_config["temp_file_perm"];
    }

    /**
     * Redirects the function calls after input verification.
     *
     * @param   string  $mode
     * @access  public
     * @return  void
     */
    function dispatch($mode)
    {
        switch ($mode)
        {
            case "view":
                $is_valid = $this->is_valid_input("view");
                if (!$is_valid) {
                    $this->view_form();
                } else {
                    $this->view();
                }
                break;

            case "register_overview":
                $is_valid = $this->is_valid_input("register_overview");
                if (!$is_valid) {
                    $this->register_form();
                } else {
                    $this->register_overview();
                }
                break;

            case "register":
                $this->register();
                break;

            case "renew":
                $is_valid = $this->is_valid_input("renew");
                if (!$is_valid) {
                    $this->renew_form();
                } else {
                    $this->renew();
                }
                break;

            case "add_grant":
                $is_valid = $this->is_valid_input("add_grant");
                if (!$is_valid) {
                    $this->grants_form();
                } else {
                    $this->add_grant();
                }
                break;

            case "revoke_grant":
                $is_valid = $this->is_valid_input("revoke_grant");
                if (!$is_valid) {
                    $this->grants_form();
                } else {
                    $this->revoke_grant();
                }
                break;

            case "transfer":
                $is_valid = $this->is_valid_input("transfer");
                if (!$is_valid) {
                    $this->transfer_form();
                } else {
                    $this->transfer();
                }
                break;

            case "fast_transfer":
                $is_valid = $this->is_valid_input("fast_transfer");
                if (!$is_valid) {
                    $this->fast_transfer_form();
                } else {
                    $this->fast_transfer();
                }
                break;

            case "modify":
                $is_valid = $this->is_valid_input("modify");
                if (!$is_valid) {
                    $this->modify_form();
                } else {
                    $this->modify();
                }
                break;

            case "delete":
                $is_valid = $this->is_valid_input("delete");
                if (!$is_valid) {
                    $this->delete_form();
                } else {
                    $this->delete();
                }
                break;

            case "grants_change_step1":
                    $this->tools->empty_formdata();
                    $this->grants_change_step1();
                break;

            case "grants_change_step2":
                $is_valid = $this->is_valid_input("grants_change_step1");
                if (!$is_valid) {
                    $this->grants_change_step1();
                } else {
                    $this->grants_change_step2();
                }
                break;

            case "owner_change_step1":
                    $this->tools->empty_formdata();
                    $this->owner_change_step1();
                break;

            case "owner_change_step2":
                $is_valid = $this->is_valid_input("owner_change_step1");
                if (!$is_valid) {
                    $this->owner_change_step1();
                } else {
                    $this->owner_change_step2();
                }
                break;

            case "owner_change":
                $is_valid = $this->is_valid_input("owner_change_step2");
                if (!$is_valid) {
                    $this->owner_change_step2(true);
                } else {
                    $this->owner_change();
                }
                break;

            case "lock_unlock":
                $is_valid = $this->is_valid_input("lock_unlock");
                if (!$is_valid) {
                    $this->lock_unlock_form();
                } else {
                    $this->lock_unlock();
                }
                break;

            case "autorenew":
                $is_valid = $this->is_valid_input("autorenew");
                if (!$is_valid) {
                    $this->autorenew_form();
                } else {
                    $this->autorenew();
                }
                break;

            case "domain_authid":
                $is_valid = $this->is_valid_input("domain_authid");
                if (!$is_valid) {
                    $this->domain_authid_form();
                } else {
                    $this->domain_authid();
                }
                break;

            case "redemption":
                $is_valid = $this->is_valid_input("redemption");
                if (!$is_valid) {
                    $this->redemption_form();
                } else {
                    $this->redemption();
                }
                break;

            case "list_result":
                $this->list_result();
                break;

            case "list_export":
                $this->list_export();
                break;

            case "bulk_transfer_step2":
                if (!$this->is_valid_input("bulk_transfer_step1")) {
                    $this->bulk_transfer_step1();
                } else {
                    $this->bulk_transfer_step2();
                }
                break;
        }
    }

    /**
     * Shows domain view form
     *
     * @access    public
     * @return  void
     */
    function view_form()
    {
        $this->nav_submain = $this->nav["view_info"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("domain_repository", "info_domain_view_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_domain_view_row");
        $this->tools->tpl->parse("CONTENT","domain_view_form");
    }

    /**
     * Returns information about a domain.
     *
     * on success - visualizes domain data
     * on failure - error message
     *
     * @access  private
     * @return  void
     * @see     view_form()
     */
    function view()
    {
        $this->nav_submain = $this->nav["view_info"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("repository","result_table_row","result_table_r");
        $this->tools->tpl->set_block("repository","std_result_table","std_result_tbl");
        $this->tools->tpl->set_block("repository", "back_button_block", "back_button_blk");

        $_SESSION["userdata"]["t_domain"] = $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii");

        $result = $this->tools->query_object("domain", $_SESSION["userdata"]["t_domain"]);
        if ($result) {
            foreach ($result as $val)
            {
                $field_value = "";
                $arr = explode(".", $val["0"]);
                //skip the first element
                $arr = array_reverse($arr);
                array_pop($arr);
                $field_name = implode(" ", array_reverse($arr));
                $this->tools->tpl->set_var("FIELD1", $field_name);
                $cnt = count($val);
                for ($i=1; $i < $cnt; $i++)
                {
                    $field_value .= $val[$i]." ";
                }
                if ("fqdn:" == $field_name) {
                    $field_value = $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "unicode", "domain", true);
                }
                if (in_array($field_name, array("created date:", "modified date:", "expires:"))) {
                    $field_value = $this->tools->prepare_date($field_value);
                }                
                $this->tools->tpl->set_var("FIELD2", $field_value);
                $this->tools->tpl->parse("FORMTABLEROWS","result_table_row",true);
            }
            $this->tools->tpl->parse("CONTENT", "std_result_table");
            //back button
            $this->tools->tpl->parse("CONTENT", "back_button_block", true);
        } else {
            if (isset($_SESSION["response"]["response_header"]["status-code"]) && $_SESSION["response"]["response_header"]["status-code"] == "2303") {
                $this->tools->tpl->set_var("GENERAL_ERROR",$_SESSION["response"]["response_header"]["status-text"]);
                $this->tools->tpl->parse("CONTENT", "back_button_block");
            } else {
                $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            }
        }
    }

    /**
     * Shows domain registration form
     *
     * @access    public
     * @return  void
     */
    function register_form()
    {
        $this->nav_submain = $this->nav["registration"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->set_block("domain_repository", "info_register_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_register_row");

        if (!isset($_SESSION["httpvars"]["c_all_as_owner"])) {
            $this->tools->tpl->set_var("C_ALL_AS_OWNER","");
            unset($_SESSION["userdata"]["c_all_as_owner"]);
            unset($_SESSION["formdata"]["c_all_as_owner"]);
        }
        if (!isset($_SESSION["httpvars"]["c_autorenew"])) {
            $checked = (isset($_SESSION["profile"]["user"]["property"]["autorenew"]) && $_SESSION["profile"]["user"]["property"]["autorenew"] == 1);
            $this->tools->tpl->set_var("C_AUTORENEW",$checked?"checked":"");
            unset($_SESSION["userdata"]["c_autorenew"]);
            unset($_SESSION["formdata"]["c_autorenew"]);
        }
        if (!isset($_SESSION["formdata"]["r_ns_type"])) {
            $this->tools->tpl->set_var("R_NS_TYPE_DEFAULT", "checked");
        }
        $this->tools->tpl->set_block("repository", "reg_period_menu", "reg_period_mn");        
        $this->tools->tpl->set_block("domain_repository", "info_dom_reg_container_row");
        $this->tools->tpl->set_block("domain_repository", "info_dom_reg_container2_row");
        $this->tools->tpl->parse("DOMAIN_REG_PERIOD", "reg_period_menu");        
        $this->tools->tpl->parse("INFO_CONTAINER2", "info_dom_reg_container_row");
        $this->tools->tpl->parse("DOMAIN_IDN_LANGUAGE", "idn_language");

        $this->tools->tpl->parse("INFO_CONTAINER3", "info_dom_reg_container2_row");
        $this->tools->tpl->parse("CONTENT", "domain_register_form");
        
        $this->tools->tpl->set_block("js_inc","MOOTOOLS","MOO");
        $this->tools->tpl->set_block("js_inc","ORDER_CONTACTS","ORDER_CNT");
        $this->tools->tpl->parse("ADDITIONAL_HEAD", "MOOTOOLS",true);
        $this->tools->tpl->parse("ADDITIONAL_HEAD", "ORDER_CONTACTS",true);

    }

    /**
     * Shows an overview page of what is to be registered
     *
     * @access    public
     * @return  void
     */
    function register_overview()
    {
        $this->nav_submain = $this->nav["registration"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->set_block("domain_repository", "info_register_overview_row");
        $this->tools->tpl->set_block("domain_register_overview_form", "own_nameservers", "own_ns");
        $this->tools->tpl->set_block("domain_register_overview_form", "joker_nameservers", "joker_ns");
        $this->tools->tpl->set_block("domain_register_overview_form", "own_nameservers_list", "own_ns_list");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_register_overview_row");
        $this->tools->tpl->set_var(
            array(
                "DOMAIN_REG_PERIOD" => ($this->config["max_reg_period"] > $_SESSION["userdata"]["s_reg_period"]) ? $_SESSION["userdata"]["s_reg_period"] : $this->config["max_reg_period"],
                "DOMAIN_IDN_LANGUAGE"=> $_SESSION["userdata"]["s_idn_language"],
                "T_CONTACT_OWNER"   => $_SESSION["userdata"]["t_contact_owner"],
                "T_CONTACT_BILLING" => (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_billing"],
                "T_CONTACT_ADMIN"   => (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_admin"],
                "T_CONTACT_TECH"    => (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_tech"],
                "T_DOMAIN"          => nl2br(implode("\n", $_SESSION["userdata"]["a_domain"])),
                "T_AUTORENEW"       => (strtolower($_SESSION["userdata"]["c_autorenew"]) == "autorenew") ? "On" : "Off"
            ));
        switch (strtolower($_SESSION["userdata"]["r_ns_type"]))
        {
            case "default":
                $this->tools->tpl->parse("joker_ns", "joker_nameservers");
                break;
            case "own":
                $this->tools->tpl->parse("own_ns", "own_nameservers");
                $i = 1;
                foreach ($_SESSION["userdata"] as $key => $value)
                {
                    if (preg_match("/^t_ns/i", $key) && !empty($_SESSION["userdata"][$key])) {
                        $this->tools->tpl->set_var(
                            array("NS_ID"   => $i,
                                  "T_NS"    => $_SESSION["userdata"][$key]
                            ));
                        $this->tools->tpl->parse("own_ns_list", "own_nameservers_list", true);
                        $i++;
                    }
                }
                break;
        }
        $this->tools->tpl->parse("CONTENT", "domain_register_overview_form");

    }

    /**
     * Registers a domain. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the domain registration form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     register_form()
     */
    function register()
    {
        global $user;
        $this->nav_submain = $this->nav["registration"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $error = false;
        foreach ($_SESSION["userdata"]["a_domain"] as $domain)
        {
            $str = array();
            switch (strtolower($_SESSION["userdata"]["r_ns_type"]))
            {
                case "default":
                    foreach ($this->config["ns_joker_default"] as $value)
                    {
                        $str[] = $value["host"];
                    }
                    $ns_str = implode(":", $str);
                    break;

                case "own":
                    foreach ($_SESSION["userdata"] as $key => $value)
                    {
                        if (preg_match("/^t_ns/i", $key) && !empty($_SESSION["userdata"][$key])) {
                            $str[] = $value;
                        }
                    }
                    $ns_str = implode(":", $str);
                    break;
            }
            $domain = $this->tools->format_fqdn($domain, "ascii");            
            $fields = array(
                "domain"    => $domain,
                "period"    => ($this->config["max_reg_period"] > $_SESSION["userdata"]["s_reg_period"]) ? $_SESSION["userdata"]["s_reg_period"]*12 : $this->config["max_reg_period"]*12,
                "language"  => (strpos($domain, "xn--") === 0) ? $_SESSION["userdata"]["s_idn_language"] : "",
                "status"    => "production",
                "owner-c"   => $_SESSION["userdata"]["t_contact_owner"],
                "billing-c" => (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_billing"],
                "admin-c"   => (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_admin"],
                "tech-c"    => (strtolower($_SESSION["userdata"]["c_all_as_owner"]) == "all") ? $_SESSION["userdata"]["t_contact_owner"] : $_SESSION["userdata"]["t_contact_tech"],
                "ns-list"   => $ns_str,
                "autorenew" => (strtolower($_SESSION["userdata"]["c_autorenew"]) == "autorenew")? 1 : 0
                );
            if (!$this->connect->execute_request("domain-register", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
                $error = true;
            }
        }
        if ($error) {
            $this->tools->tpl->set_block("repository", "general_error_box");
            $this->tools->general_err("GENERAL_ERROR", $this->err_msg["_srv_req_part_failed_s"] . "<br />" . $this->err_msg["_domains_partially_reg"]);
            $this->register_form();
        } else {
            $this->tools->show_request_status();
        }
        unset($_SESSION["userdata"]["c_all_as_owner"]);
        unset($_SESSION["formdata"]["c_all_as_owner"]);
    }

    /**
     * Shows domain renewal form
     *
     * @access    public
     * @return  void
     */
    function renew_form()
    {
        $this->nav_submain = $this->nav["renewal"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
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
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     register_form()
     */
    function renew()
    {
        $this->nav_submain = $this->nav["renewal"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $fields = array(
            "domain"    => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"),
            "period"    => ($this->config["max_reg_period"] > $_SESSION["userdata"]["s_reg_period"]) ? $_SESSION["userdata"]["s_reg_period"]*12 : $this->config["max_reg_period"]*12,
                    );
        if (!$this->connect->execute_request("domain-renew", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->renew_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows domain grants form
     *
     * @access    public
     * @return  void
     */
    function grants_form($show_back_button = true)
    {
        $this->nav_submain = $this->nav["grants_change"];
        $this->nav_submain2 = $this->nav["grants_change_form"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main . "  &raquo; " . $this->nav_submain . "  &raquo; " . $this->nav_submain2."  (".$_SESSION["userdata"]["t_domain"].")");
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("repository","roles_menu","roles_mn");
        $this->tools->tpl->parse("ROLES","roles_menu");
        $this->tools->tpl->set_block("repository", "back_button_block", "back_button_blk");

        $this->tools->tpl->set_block("domain_repository", "info_grants_row");
        $this->tools->tpl->set_block("domain_repository", "info_invitation_row");
        
        $this->tools->tpl->set_block("domain_grants_form","grants_list","g_list");
        $this->tools->tpl->set_block("grants_list","grants_list_row","g_list_rows");

        $result = $this->grants_list($this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"));
        if ($result) {
            if ($result == $this->config["empty_result"]) {
                $result = array();
            }
            if (count($result)) {
                for ($i=0; $i < count($result); $i++)
                {
                    if (isset($result[$i])) {
                        $type = $result[$i]["0"];
                        $this->tools->tpl->set_var(array(
                            "TYPE"              => $result[$i]["type"],
                            "NO"                => $result[$i]["number"]+1,
                            "SCOPE"             => $result[$i]["scope"],
                            "USER_DOMAIN_TEXT"  => $this->tools->format_fqdn($result[$i]["object_name"], "unicode", "domain", true),
                            "USER_DOMAIN"       => $this->tools->format_fqdn($result[$i]["object_name"], "unicode", "domain", false),
                            "DOMAIN"            => $result[$i]["object_name"],
                            "ROLE"              => $this->roles[substr($result[$i]["role"],1)],
                            "USER_ROLE"         => substr($result[$i]["role"],1),
                            "INVITER"           => $result[$i]["inviter_login"],
                            "INVITEE"		=> $result[$i]["invited_login"],
                            "INVITEE_EMAIL"	=> $result[$i]["invitee_email"],
                            "INVITEE_UID"	=> $result[$i]["invited_uid"],
                            "INVITATION_KEY"	=> $result[$i]["key"],
                            "CLIENT_UID"	=> is_numeric($result[$i]["invited_uid"])?$result[$i]["invited_uid"]:0,
                            "NICK"		=> $result[$i]["nickname"]
                        ));

                        $this->tools->tpl->parse("g_list_rows","grants_list_row",true);
                    }
                }
                $this->tools->tpl->parse("g_list","grants_list");
            }
        }
        if (isset($_SESSION["userdata"]["invite_form"]) && $_SESSION["userdata"]["invite_form"]=="false") {
            $this->tools->tpl->set_block("domain_grants_form","grants_form","g_from");
            $this->tools->tpl->parse("INFO_CONTAINER", "info_invitation_row");
        } else {
            $this->tools->tpl->parse("INFO_CONTAINER", "info_grants_row");
        }


        $this->tools->tpl->parse("CONTENT", "domain_grants_form");
        
        //back button
        if ($show_back_button) $this->tools->tpl->parse("CONTENT", "back_button_block", true);
    }

    /**
     * Add grant to a domain.
     *
     * on success - success status message
     * on failure - back to the domain renewal form
     *
     * @access  private
     * @return  void
     * @see     register_form()
     */
    function add_grant()
    {
        $this->nav_submain = $this->nav["grants"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $fields = array(
            "domain"   => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"),
            "email"    => $_SESSION["userdata"]["t_email"],
            "role"     => '@'.$_SESSION["userdata"]["s_role"],
            "nickname"     => $_SESSION["userdata"]["t_nick"]
        );
        if (!empty($_SESSION["userdata"]["t_uid"])) {
            $fields['client-uid'] = $_SESSION["userdata"]["t_uid"];
        }
        if (!$this->connect->execute_request("grants-invite", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
        } else {
            $this->tools->show_request_status();
            unset($_SESSION["storagedata"]["domains"]["last_updated"]);
        }
        $this->grants_form();
    }

    /**
     * Revoke grant of a domain.
     *
     * on success - success status message
     * on failure - back to the domain renewal form
     *
     * @access  private
     * @return  void
     * @see     register_form()
     */
    function revoke_grant()
    {
        $this->nav_submain = $this->nav["grants"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $fields = array(
            "domain"   => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"),
            "scope"    => $_SESSION["userdata"]["t_scope"],
            "role"     => '@'.$_SESSION["userdata"]["s_role"],
            "type"     => $_SESSION["userdata"]["t_type"],
            "client-uid"     => $_SESSION["userdata"]["t_client_uid"]
        );
        if (!$this->connect->execute_request("grants-revoke", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
        } else {
            $this->tools->show_request_status();
            unset($_SESSION["storagedata"]["domains"]["last_updated"]);
        }
        $this->grants_form();
    }

    /**
     * Returns an array containing the grants list of a domain or false in case of failure
     *
     * @param   string  $domain
     * @access  public
     * @return  mixed
     */
    function grants_list($domain)
    {
        $fields = array(
        "domain"   => $domain,
        "showkey" => 1
            );
        if ($this->connect->execute_request("grants-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            return $this->tools->parse_response_list($_SESSION["response"]);
        } else {
            return false;
        }
    }


    /**
     * Shows domain transfer form
     *
     * @access  public
     * @return  void
     */
    function transfer_form()
    {
        $this->nav_submain = $this->nav["transfer"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->set_block("domain_repository", "info_transfer_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_transfer_row");
        $this->tools->tpl->parse("CONTENT", "domain_transfer_form");
        $this->tools->tpl->set_block("js_inc","MOOTOOLS","MOO");
        $this->tools->tpl->set_block("js_inc","ORDER_CONTACTS","ORDER_CNT");
        $this->tools->tpl->parse("ADDITIONAL_HEAD", "MOOTOOLS",true);
        $this->tools->tpl->parse("ADDITIONAL_HEAD", "ORDER_CONTACTS",true);
    }

    /**
     * Transfer of a domain. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the domain transfer form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     transfer_form()
     */
    function transfer()
    {
        $this->nav_submain = $this->nav["transfer"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $fields = array(
            "domain"            => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"),
            "transfer-auth-id"  => $_SESSION["userdata"]["t_auth_id"],
            "billing-c"         => $_SESSION["userdata"]["t_contact_billing"],
            );
        if (!$this->connect->execute_request("domain-transfer-in", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->transfer_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows fast domain transfer form
     *
     * @access  public
     * @return  void
     */
    function fast_transfer_form()
    {
        $this->nav_submain = $this->nav["fast_transfer"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");

        if (!isset($_SESSION["httpvars"]["c_all_as_admin"])) {
            $this->tools->tpl->set_var("C_ALL_AS_ADMIN", "");
            unset($_SESSION["userdata"]["c_all_as_admin"]);
            unset($_SESSION["formdata"]["c_all_as_admin"]);
        }
        $this->tools->tpl->set_block("repository", "reg_period_menu", "reg_period_mn");
        $this->tools->tpl->parse("DOMAIN_REG_PERIOD", "reg_period_menu");
        $this->tools->tpl->set_block("repository", "transfer_status_menu", "transfer_status_m");
        $this->tools->tpl->parse("DOMAIN_TRANSFER_STATUS", "transfer_status_menu");

        $this->tools->tpl->set_block("domain_repository", "info_fast_transfer_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_fast_transfer_row");
        $this->tools->tpl->parse("CONTENT", "domain_fast_transfer_form");
        $this->tools->tpl->set_block("js_inc","MOOTOOLS","MOO");
        $this->tools->tpl->set_block("js_inc","ORDER_CONTACTS","ORDER_CNT");
        $this->tools->tpl->parse("ADDITIONAL_HEAD", "MOOTOOLS",true);
        $this->tools->tpl->parse("ADDITIONAL_HEAD", "ORDER_CONTACTS",true);
    }

    /**
     * Fast transfer of a domain. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the domain transfer form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     fast_transfer_form()
     */
    function fast_transfer()
    {
        $this->nav_submain = $this->nav["fast_transfer"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $fields = array(
            "domain"    => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"),
            "period"    => ($this->config["max_reg_period"] > $_SESSION["userdata"]["s_reg_period"]) ? $_SESSION["userdata"]["s_reg_period"]*12 : $this->config["max_reg_period"]*12,
            "transfer-auth-id"  => $_SESSION["userdata"]["t_auth_id"],
            "status"    => $_SESSION["userdata"]["s_new_dom_status"],
            "owner-email"       => $_SESSION["userdata"]["t_contact_owner_email"],
            "admin-c"   => $_SESSION["userdata"]["t_contact_admin"],
            "billing-c" => (strtolower($_SESSION["userdata"]["c_all_as_admin"]) == "all") ? $_SESSION["userdata"]["t_contact_admin"] : $_SESSION["userdata"]["t_contact_billing"],            
            "tech-c"    => (strtolower($_SESSION["userdata"]["c_all_as_admin"]) == "all") ? $_SESSION["userdata"]["t_contact_admin"] : $_SESSION["userdata"]["t_contact_tech"],
            );
        if (!$this->connect->execute_request("domain-transfer-in-reseller", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->fast_transfer_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows bulk domain transfer form
     *
     * @access  public
     * @return  void
     */
    function bulk_transfer_step1()
    {
        $this->nav_submain = $this->nav["bulk_transfer"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");

        $this->tools->tpl->set_block("domain_repository", "bulk_transfer_step1_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "bulk_transfer_step1_row");
        $this->tools->tpl->parse("CONTENT", "domain_bulk_transfer_step1");
    }

    /**
     * Shows bulk domain transfer list after verifying the entries
     *
     * @access  private
     * @return  void
     */
    function bulk_transfer_step2()
    {
        $this->nav_submain = $this->nav["bulk_transfer"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");

        $this->tools->tpl->set_block("domain_repository", "bulk_transfer_step2_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "bulk_transfer_step2_row");
        $this->tools->tpl->set_block("domain_bulk_transfer_step2", "domain_authid_pairs_row", "domain_authid_pairs_r");
        $invalid_domains = array();
        if (is_array($_SESSION["userdata"]["domain_authid_pairs"]) && !empty($_SESSION["userdata"]["domain_authid_pairs"])) {
            foreach ($_SESSION["userdata"]["domain_authid_pairs"] as $domain => $authid)
            {
                if (!$this->tools->is_valid("domain", $domain, true)) {
                    $invalid_domains[] = $domain;
                    unset($_SESSION["userdata"]["domain_authid_pairs"][$domain]);
                } else {
                    $this->tools->tpl->set_var("DOMAIN", $domain);
                    $this->tools->tpl->set_var("AUTHID", $authid);
                    $this->tools->tpl->parse("domain_authid_pairs_r", "domain_authid_pairs_row", true);
                }
            }
        }
        $err_array = array();
        if (!$_SESSION["userdata"]["domain_authid_pairs_all"]) {
            $err_array[] = $this->err_msg["_domain_authid_pairs_parse_not_all"];
        }
        if (is_array($invalid_domains) && !empty($invalid_domains)) {
            $invalid_domains_list = implode(", ", $invalid_domains);
            $err_array[] = $this->err_msg["_domain_authid_pairs_invalid_domain"] . $invalid_domains_list;
        }
        if (count($err_array)) {
            $this->tools->general_err("ERROR_INVALID_ENTRIES", nl2br(implode("\n", $err_array)), false);
        }
        $this->tools->tpl->parse("CONTENT", "domain_bulk_transfer_step2");
    }

    /**
     * Sends an email to initialize bulk transfer. Not related to DMAPI in any way.
     *
     * @access  private
     * @return  void
     * @see     bulk_transfer_step1(), bulk_transfer_step2()
     */
    function bulk_transfer_step3()
    {
        $this->nav_submain = $this->nav["bulk_transfer"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        if (isset($_SESSION["userdata"]["domain_authid_pairs"])) {
            foreach ($_SESSION["userdata"]["domain_authid_pairs"] as $domain => $authid)
            {
                $domain = $this->tools->format_fqdn($domain, "ascii");
                $domain_authid_list .= $domain . "  " . $authid . "\n";
            }
        }
        $email_body .=  "Domain list:\n" . $domain_authid_list . "\nUser: " . $_SESSION["username"] .
                        "\nRemote-IP:" . $_SERVER["REMOTE_ADDR"] . "\nDatum: " . gmdate("d\.m\.Y G:i:s", time()) .
                        " generated by DMAPI";
        if ($this->tools->send_mail($this->config["transfer_email"], $this->config["dmapi_mp_email"], "", "", "Bulk transfer", $email_body, "", "", "")) {
            $this->tools->tpl->set_block("repository", "general_success_box");
            $this->tools->tpl->set_var("STATUS_MSG", $this->msg["_request_sent"]);
            $this->tools->tpl->parse("CONTENT", "general_success_box");
        } else {
            //not needed - see is_valid_input()
            //$this->tools->tpl->set_block("repository","general_error_box");
            $this->tools->tpl->set_var("ERROR_MSG", $this->msg["_request_not_sent"]." ".$this->msg["_error_check_logs"]);
            $this->tools->tpl->parse("CONTENT", "general_error_box");
        }
    }

    /**
     * Shows domain modify form
     *
     * @access  public
     * @return  void
     */
    function modify_form()
    {

        if (!empty($_SESSION["userdata"]["t_domain"])) {
            $result = $this->tools->query_object("domain", $_SESSION["userdata"]["t_domain"],true);
            if ($result) {
                $ns_nr = 1;
                $form_data_arr = array();
                foreach($result as $val) {
                    switch($val[0]) {
                        case "domain.admin-c:":
                            $form_data_arr["t_contact_admin"] = $val[1];
                            break;
                        case "domain.tech-c:":
                            $form_data_arr["t_contact_tech"] = $val[1];
                            break;
                        case "domain.billing-c:":
                            $form_data_arr["t_contact_billing"] = $val[1];
                            break;
                        case "domain.nservers.nserver.no:":
                            $ns_nr = $val[1];
                            break;
                        case "domain.nservers.nserver.handle:":
                            $form_data_arr["t_ns".$ns_nr] = $val[1];
                            break;

                    }
                }
                for ($i=1;$i<=6;$i++) {
                    $form_data_arr["t_ds".$i] = "";
                }
                $this->tools->fill_form($form_data_arr);
            }
        }
        

        $this->nav_submain = $this->nav["modification"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);

        if (!isset($_SESSION["formdata"]["r_ns_type"])) {
            $this->tools->tpl->set_var("R_NS_TYPE_NO_CHANGE", "checked");
        }
        if (!isset($_SESSION["formdata"]["r_dnssec"])) {
            $this->tools->tpl->set_var("R_DNSSEC_NO_CHANGE", "checked");
        }        
        $this->tools->tpl->set_block("domain_repository", "info_modify_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_modify_row");
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->parse("CONTENT", "domain_modify_form");

        $this->tools->tpl->set_block("js_inc","MOOTOOLS","MOO");
        $this->tools->tpl->set_block("js_inc","ORDER_CONTACTS","ORDER_CNT");
        $this->tools->tpl->parse("ADDITIONAL_HEAD", "MOOTOOLS",true);
        $this->tools->tpl->parse("ADDITIONAL_HEAD", "ORDER_CONTACTS",true);

        $this->tools->tpl->set_block("repository", "back_button_block", "back_button_blk");
        $this->tools->tpl->parse("CONTENT", "back_button_block", true);
    }

    /**
     * Modification of a domain. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the domain modification form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     modify_form()
     */
    function modify()
    {
        $this->nav_submain = $this->nav["modification"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        switch (strtolower($_SESSION["userdata"]["r_ns_type"]))
        {
            case "no_change":
                //no action
                break;
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
                    if (preg_match("/^t_ns/i",$key) && !empty($_SESSION["userdata"][$key])) {
                        $str[] = $value;
                    }
                }
                $ns_str = implode(":",$str);
                break;
        }
        $fields = array(
            "domain"    => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"),
            "billing-c" => $_SESSION["userdata"]["t_contact_billing"],
            "admin-c"   => $_SESSION["userdata"]["t_contact_admin"],
            "tech-c"    => $_SESSION["userdata"]["t_contact_tech"]
            );
        switch (strtolower($_SESSION["userdata"]["r_dnssec"]))
        {
            case "no_change":
                //no action
                break;
            case "delete":
                $fields["dnssec"] = 0;
                break;

            case "new":
                $fields["dnssec"] = 1;
                foreach ($_SESSION["userdata"] as $key => $value)
                {
                    if (preg_match("/^t_ds([0-9]+)/i",$key,$matches) && !empty($_SESSION["userdata"][$key])) {
                        $fields["ds-".$matches[1]] = $value;
                    }
                }
                break;
        }
        if ("no_change" != strtolower($_SESSION["userdata"]["r_ns_type"])) {
            $fields["ns-list"] = $ns_str;
        }
        if (!$this->connect->execute_request("domain-modify", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->modify_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows domain deletion form
     *
     * @access    public
     * @return  void
     */
    function delete_form()
    {
        $this->nav_submain = $this->nav["deletion"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("domain_repository", "info_delete_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_delete_row");

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
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     delete_form()
     */
    function delete()
    {
        $this->nav_submain = $this->nav["deletion"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $fields = array(
                    "domain" => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"),
                    "type" => $_SESSION["userdata"]["s_del_type"]
                    );
        if (isset($_SESSION["userdata"]["c_force_del"]) && strtolower($_SESSION["userdata"]["c_force_del"]) == "y") {
            $fields["force"] = 1;
        }
        if (!$this->connect->execute_request("domain-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->delete_form();
        } else {
            unset($_SESSION["userdata"]["c_force_del"]);
            unset($_SESSION["formdata"]["c_force_del"]);
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows domain grants change form - input of a domain name
     *
     * @access  public
     * @return  void
     */
    function grants_change_step1()
    {
        $this->nav_submain = $this->nav["grants_change"];
        $this->nav_submain2 = $this->nav["grants_change_dom_select"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main . "  &raquo; " . $this->nav_submain . "  &raquo; " . $this->nav_submain2);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->parse("CONTENT", "domain_grants_change_step1");
    }

    /**
     * Shows domain grants change form - read domain information and open grants form
     *
     * @access  public
     * @return  void
     */
    function grants_change_step2()
    {
        $result = $this->tools->domain_list($this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"));
        if ($result == $this->config["empty_result"] || count($result) != 1) {
            $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain_not_found"]);
            $this->grants_change_step1();
            return;
        } else {
           $_SESSION["userdata"]["invite_form"] = $result[0]["invitation_possible"];
           $this->grants_form(false);
        }


    }

    /**
     * Shows domain owner change form - input of a domain name
     *
     * @access  public
     * @return  void
     */
    function owner_change_step1()
    {
        $this->nav_submain = $this->nav["owner_change"];
        $this->nav_submain2 = $this->nav["owner_change_dom_select"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main . "  &raquo; " . $this->nav_submain . "  &raquo; " . $this->nav_submain2);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->parse("CONTENT", "domain_owner_change_step1");
    }

    /**
     * Shows domain owner change form - input of a new owner contact
     *
     * @access  public
     * @return  void
     */
    function owner_change_step2($correction = false)
    {
        $this->nav_submain = $this->nav["owner_change"];
        $this->nav_submain2 = $this->nav["owner_change_cnt_entry"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main . "  &raquo; " . $this->nav_submain . "  &raquo; " . $this->nav_submain2);
        $this->tools->tpl->parse("NAV", "navigation");

        if ($res = $this->tools->get_domain_part($_SESSION["userdata"]["t_domain"])) {
            $tld = $res["tld"];
        } else {
            $tld = $this->config["default_tld"];
        }
        $_SESSION["userdata"]["s_tld"] = $tld;
        if (!$correction) {
            $result = $this->tools->query_object("domain", $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"), true);

            if ($result != false) {
                if ($result != $this->config["empty_result"] && is_array($result)) {
                    $form_data_arr = $this->tools->fill_form_prep($result,"domain");
                    if (is_array($form_data_arr)) {
                        $this->tools->fill_form($form_data_arr);
                    }
                }
            } else {
             $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
             $this-> owner_change_step1();
             return;
            }
        }
        $cnt = new Contact;
        $cnt->build_contact_form("contact_form", $tld, true);

        $this->tools->tpl->set_var("T_TLD", "Owner");
        $this->tools->tpl->set_var("MODE", "domain_owner_change");
        $this->tools->tpl->parse("CONTACT_PLACE_HOLDER", "contact_form");
        $this->tools->tpl->parse("CONTENT", "domain_owner_change_step2");
    }

    /**
     * Owner change procedure. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the domain owner change form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     owner_change_step1()
     * @see     owner_change_step2()
     */
    function owner_change()
    {
        $fields = array(
            "domain"    => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii"),
            "tld"       => $_SESSION["userdata"]["s_tld"],
            "name"      => "" == $_SESSION["httpvars"]["t_contact_name"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_name"],
            "fname"     => "" == $_SESSION["httpvars"]["t_contact_fname"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_fname"],
            "lname"     => "" == $_SESSION["httpvars"]["t_contact_lname"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_lname"],
            "title"     => "" == $_SESSION["httpvars"]["t_contact_title"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_title"],
            "individual"    => "" == $_SESSION["httpvars"]["t_contact_individual"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_individual"],
            "organization"  => "" == $_SESSION["httpvars"]["t_contact_organization"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_organization"],
            "email"     => $_SESSION["httpvars"]["t_contact_email"],
            "address-1" => $_SESSION["httpvars"]["t_contact_address_1"],
            "address-2" => "" == $_SESSION["httpvars"]["t_contact_address_2"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_address_2"],
            "address-3" => "" == $_SESSION["httpvars"]["t_contact_address_3"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_address_3"],
            "city"      => $_SESSION["httpvars"]["t_contact_city"],
            "state"     => "" == $_SESSION["httpvars"]["t_contact_state"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_state"],
            "postal-code"   => $_SESSION["httpvars"]["t_contact_postal_code"],
            "country"   => $_SESSION["httpvars"]["s_contact_country"],
            "phone"     => $_SESSION["httpvars"]["t_contact_phone"],
            "extension" => "" == $_SESSION["httpvars"]["t_contact_extension"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_extension"],
            "fax"       => "" == $_SESSION["httpvars"]["t_contact_fax"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_fax"]
            );
        if ("eu" == $_SESSION["userdata"]["s_tld"]) {
            $fields["language"] = $_SESSION["httpvars"]["s_contact_language"];
        }
        if ("us" == $_SESSION["userdata"]["s_tld"]) {
            $fields["app-purpose"] = $_SESSION["httpvars"]["s_contact_app_purpose"];
            $fields["nexus-category"] = $_SESSION["httpvars"]["s_contact_category"];
            $fields["nexus-category-country"] = $_SESSION["httpvars"]["s_nexus_category_country"];
        }
        if (!$this->connect->execute_request("domain-owner-change", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
        } else {
            $this->nav_submain = $this->nav["owner_change"];
            $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main . "  &raquo; " . $this->nav_submain);
            $this->tools->tpl->parse("NAV","navigation");
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows a domain lock/unlock form.
     *
     * @access    public
     * @return  void
     */
    function lock_unlock_form()
    {
        $this->nav_submain = $this->nav["lock_unlock"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("domain_repository", "info_lu_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_lu_row");
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
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     lock_unlock_form()
     */
    function lock_unlock()
    {
        $this->nav_submain = $this->nav["lock_unlock"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $fields = array(
                    "domain"    => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii")
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
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->lock_unlock_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows a domainpattern autorenew form.
     *
     * @access    public
     * @return  void
     */
    function autorenew_form()
    {
        $this->nav_submain = $this->nav["autorenew"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("domain_repository", "info_ar_row");
        $this->tools->tpl->set_block("domain_repository", "info_domain_list_pattern_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_domain_list_pattern_row");
        $this->tools->tpl->parse("INFO_GENERAL", "info_ar_row");
        if (isset($_SESSION["formdata"]["r_autorenew"]) && $_SESSION["formdata"]["r_autorenew"]=="ar_off") {
            $this->tools->tpl->set_var("R_AUTORENEW_OFF","checked");
        } else {
            $this->tools->tpl->set_var("R_AUTORENEW_ON","checked");
        }
        $this->tools->tpl->parse("CONTENT", "domain_autorenew_form");
    }

    /**
     * Autorenew of a domainpattern. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the domain owner change form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     autorenew_form()
     */
    function autorenew()
    {
        $this->nav_submain = $this->nav["autorenew"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $fields = array(
                    "pname"	 => "autorenew",
                    "pvalue"	 => (strtolower($_SESSION["userdata"]["r_autorenew"])=="ar_on") ? 1 : 0,
                    "domain"    => $this->tools->format_fqdn($_SESSION["userdata"]["t_pattern"], "ascii")
                    );
        $status = $this->connect->execute_request("domain-set-property", $fields, $_SESSION["response"], $_SESSION["auth-sid"]);
        if (!$status) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->autorenew_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows a domain auth-id form.
     *
     * @access  public
     * @return  void
     */
    function domain_authid_form()
    {
        $this->nav_submain = $this->nav["authid"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("domain_repository", "info_authid_container_row");
        $this->tools->tpl->parse("INFO_CONTAINER_CUSTOM", "info_authid_container_row");
        $this->tools->tpl->parse("CONTENT", "domain_authid_form");
    }
    /**
     * Sends an email to initialize redemption. Not related to DMAPI in any way.
     *
     * @access  private
     * @return  void
     * @see     redemption_form()
     */
    function domain_authid()
    {
        $this->nav_submain = $this->nav["authid"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $fields = array(
                    "domain"    => $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii")
                    );
        $status = $this->connect->execute_request("domain-transfer-get-auth-id", $fields, $_SESSION["response"], $_SESSION["auth-sid"]);
        if (!$status) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->domain_authid_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows a redemption form.
     *
     * @access  public
     * @return  void
     */
    function redemption_form()
    {
        $this->nav_submain = $this->nav["redemption"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("domain_repository", "info_redemption_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_redemption_row");
        $this->tools->tpl->parse("CONTENT", "domain_redemption_form");
    }

    /**
     * Sends an email to initialize redemption. Not related to DMAPI in any way.
     *
     * @access  private
     * @return  void
     * @see     redemption_form()
     */
    function redemption()
    {
        $this->nav_submain = $this->nav["redemption"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $email_body = "Request from user: " . $_SESSION["username"] . "\n";
        $email_body .= "Domain in question: " . $this->tools->format_fqdn($_SESSION["userdata"]["t_domain"], "ascii") . "\n";
        $email_body .= "Additional information: ".(empty($_SESSION["userdata"]["t_add_info"]) ? $this->config["no_content"] : $_SESSION["userdata"]["t_add_info"])."\n";
        if ($this->tools->send_mail($this->config["redemption_email"], $this->config["dmapi_mp_email"], "", "", "Redemption request - DMAPI", $email_body, "", "", "")) {
            $this->tools->tpl->set_block("repository", "general_success_box");
            $this->tools->tpl->set_var("STATUS_MSG", $this->msg["_request_sent"]);
            $this->tools->tpl->parse("CONTENT", "general_success_box");
        } else {
            //not needed - see is_valid_input()
            //$this->tools->tpl->set_block("repository","general_error_box");
            $this->tools->tpl->set_var("ERROR_MSG", $this->msg["_request_not_sent"]." ".$this->msg["_error_check_logs"]);
            $this->tools->tpl->parse("CONTENT", "general_error_box");
        }
    }

    /**
     * Shows a form allowing you to customize the returned list of domains.
     *
     * @access  public
     * @return  void
     */
    function list_form()
    {
        $this->nav_submain = $this->nav["domain_list"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main . "  &raquo; " . $this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->set_block("domain_repository", "info_domain_list_pattern_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_domain_list_pattern_row");
        $this->tools->tpl->set_var("MODE", "domain_list_result");
        $this->tools->tpl->parse("CONTENT", "domain_list_form");
        unset($_SESSION["userdata"]["p"]);
        unset($_SESSION["userdata"]["s"]);
    }

    /**
     * Returns a domain list.
     *
     * on success - returns a domain list
     * on failure - back to the domain list form
     *
     * @access  private
     * @return  void
     */
    function list_result()
    {
        $this->nav_submain = $this->nav["domain_list"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");

        $result = false;

        $this->tools->tpl->set_block("domain_repository", "result_list_table");
        $this->tools->tpl->set_block("domain_repository", "domain_total");        
        if (isset($_SESSION["storagedata"]["domains"]) &&
            isset($_SESSION["storagedata"]["domains"]["list"]) &&
            isset($_SESSION["storagedata"]["domains"]["pattern"]) &&
            $_SESSION["storagedata"]["domains"]["pattern"] == $_SESSION["userdata"]["t_pattern"] &&
            isset($_SESSION["storagedata"]["domains"]["last_updated"]) &&
            $_SESSION["storagedata"]["domains"]["last_updated"] + $this->config["dom_list_caching_period"] > time() && !isset($_SESSION["httpvars"]["refresh"]) ) {
            $result = $_SESSION["storagedata"]["domains"]["list"];
        } else {
            $_SESSION["storagedata"]["domains"]["pattern"] = $_SESSION["userdata"]["t_pattern"];
            $_SESSION["storagedata"]["domains"]["last_updated"] = time();
            $result = $this->tools->domain_list($_SESSION["userdata"]["t_pattern"]);                                    
            if ($result) {
                if ($result == $this->config["empty_result"]) {
                    $result = array();    
                }
                if ($this->config["idn_compatibility"] && !$this->tools->is_pattern($_SESSION["userdata"]["t_pattern"], "catch_all")) {
                    $idn_result = $this->tools->domain_list("xn--*");
                    $pattern = $_SESSION["userdata"]["t_pattern"];
                    $pattern = str_replace("*", ".*", $pattern);
                    if (is_array($idn_result) && count($idn_result)) {
                        foreach ($idn_result as $key => $domain_set)
                        {
                            if (!preg_match("/^" . $pattern . "$/i", $this->tools->format_fqdn($domain_set["domain"], "unicode", "domain", false))) {
                                unset($idn_result[$key]);
                            }
                        }
                        $result = array_merge($result, $idn_result);
                        $this->tools->set_domain_order($result, $this->config["idn_compatibility"]);                
                    }
                }
#print "Hi: <pre>";
#print_r($result);
            }
            $_SESSION["storagedata"]["domains"]["list"] = $result;
        }        
        $paging = new Paging();
        $paging->setAvailableEntriesPerPage($this->domain_list_entries_per_page);
        $paging->setPageLinksPerPage($this->domain_list_page_links_per_page);
        $total_domains = count($result);
        $paging->initSelectedEntriesPerPage($_SESSION["userdata"]["s"], $this->domain_list_default_entry_page);
        $total_pages = ceil($total_domains / $_SESSION["userdata"]["s"]);
        $paging->initSelectedPageNumber($_SESSION["userdata"]["p"], $this->domain_list_default_page, $total_pages);
        $this->tools->tpl->set_var("PAGING_RESULTS_PER_PAGE", $paging->buildEntriesPerPageBlock($_SESSION["userdata"]["s"], "domain"));
        $this->tools->tpl->set_var("PAGING_PAGES", $paging->buildPagingBlock($total_domains, $_SESSION["userdata"]["s"], $_SESSION["userdata"]["p"], "domain"));
        $paging->parsePagingToolbar("paging_repository", "paging_toolbar_c6", "PAGE_TOOLBAR");
        $this->tools->tpl->set_block("domain_repository", "export_option");
        $this->tools->tpl->set_block("domain_repository", "refresh_option");
        $this->tools->tpl->set_block("domain_repository", "domain_info");
        $this->tools->tpl->parse("EXPORT_DOMAIN_LIST", "export_option");
        $this->tools->tpl->parse("EXPORT_DOMAIN_LIST", "refresh_option", true);
        $this->tools->tpl->set_var("TOTAL_DOMS", $total_domains);
        $this->tools->tpl->parse("TOTAL_DOMAINS", "domain_total");        
        if (is_array($result)) {
            if (count($result)) {
                $this->tools->tpl->set_block("domain_repository","result_list_row");
                $is = $paging->calculateResultsStartIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                $ie = $paging->calculateResultsEndIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                // fetch invitation details per page
                if ($result[$is]["pending_invitations"]=="undef") {
                    $detail = $this->tools->domain_list($_SESSION["storagedata"]["domains"]["pattern"],$is+1,$ie);
                    for ($i=0;$i<count($detail);$i++) {
                        if($result[$is+$i]["domain"] == $detail[$i]["domain"]) {
                            $result[$is+$i] = $detail[$i];
                            $_SESSION["storagedata"]["domains"]["list"][$is+$i] = $detail[$i];
                        } else {
                            break;
                        }
                    }
                }
                for ($i=$is; $i < $ie; $i++)
                {
                    // fetch missed invitation details
                    if ($result[$is]["pending_invitations"]=="undef") {
                        $detail = $this->tools->domain_list($result[$i]["domain"],1,1);
                        $result[$i] = $detail[0];
                        $_SESSION["storagedata"]["domains"]["list"][$i] = $detail[0];
                    }
                    // own_role,invitation_possible,number_of_confirmed_grants,pending_invitations
                    if (isset($result[$i])) {
                        $this->tools->tpl->set_var(array(
                            "TR_CLASS"          => $i%2?"tr_even":"tr_odd",
                            "NO"                => $i+1,
                            "USER_DOMAIN_TEXT"  => $this->tools->format_fqdn($result[$i]["domain"], "unicode", "domain", true),
                            "USER_DOMAIN"       => $this->tools->format_fqdn($result[$i]["domain"], "unicode", "domain", false),
                            "DOMAIN"            => $result[$i]["domain"],
                            "EXPIRATION"        => $result[$i]["expiration_date"],
                            "STATUS"		=> $result[$i]["domain_status"],
                            "GRANTS"            => $result[$i]["number_of_confirmed_grants"],
                            "INVITES"           => $result[$i]["pending_invitations"]=="undef"?"-":$result[$i]["pending_invitations"],
                            "INVFORM"           => $result[$i]["invitation_possible"]
                        ));
                        if ($result[$i]["invitation_possible"]=="false") {
                            $roles_str="";
                            $roles =  explode(",",$result[$i]["own_role"]);
                            foreach($roles as $role) {
                                $roles_str .= ",".$this->roles[substr($role,1)];
                            }
                            $this->tools->tpl->set_var(array(
                                "GRANTS"            => "-",
                                "INVITES"           => "-",
                                "ROLES_TEXT"        => substr($roles_str,1)
                            ));
                            $this->tools->tpl->parse("DOMAIN_INFO", "domain_info");
                        } else {
                            $this->tools->tpl->set_var("DOMAIN_INFO", "");
                        }
                        $this->tools->tpl->parse("RESULT_LIST", "result_list_row", true);
                    }
                }
                $this->tools->tpl->parse("CONTENT", "result_list_table");
            } else {
                $this->tools->tpl->set_block("domain_repository", "no_result_row");
                $this->tools->tpl->set_var("NO_RESULT_MESSAGE", $this->msg["_no_result_message"]);
                $this->tools->tpl->parse("RESULT_LIST", "no_result_row", true);
                $this->tools->tpl->parse("CONTENT", "result_list_table");
            }
        } else {
            $this->tools->tpl->set_block("repository", "general_error_box");
            $this->tools->general_err("GENERAL_ERROR", $this->err_msg["_srv_req_failed"]);            
            $this->list_form();
        }
    }

    /**
     * Exports the domain list into file with user specified filetype
     *
     * @param   string  $filetype   e.g. csv, xsl etc.
     * @access  public
     * @return  void
     */
    function list_export($filetype = "csv")
    {
        switch (strtolower(trim($filetype)))
        {
            case "csv":
                clearstatcache();
                $this->tools->define_dir_separator($separator);
                $this->tools->create_temp_directory($this->temp_dir, $this->temp_perm);
                $path = $this->temp_dir.$separator;
                $sub_dir = md5($_SESSION["username"].rand(1, 99999));
                if (mkdir($path.$sub_dir, $this->temp_perm)) {
                    $csv = new Bs_CsvUtil;
                    //could lead to slow down - dunno how big is the result list array
                    $text[] = $csv->arrayToCsvString(array("DOMAIN","EXPIRATION","STATUS","GRANTS","ASSIGNED BY ROLE"));
                    if (isset($_SESSION["storagedata"]["domains"]["list"])) {
                        foreach ($_SESSION["storagedata"]["domains"]["list"] as $val)
                        {
                            $domain = $val["domain"];
                            $expiration = $val["expiration_date"];
                            $status = $val["domain_status"];
                            $grants = $val["number_of_confirmed_grants"];
                            $own_role = "";
                            if ($val["invitation_possible"]=="false") {
                                $roles_str="";
                                $roles =  explode(",",$val["own_role"]);
                                foreach($roles as $role) {
                                    $roles_str .= ",".$this->roles[substr($role,1)];
                                }
                                $own_role=substr($roles_str,1);
                                $grants = "-";
                            }
                            $row_arr = array($domain, $expiration, $status,$grants,$own_role);
                            $text[] = $csv->arrayToCsvString($row_arr);
                        }
                    }
                    $text = implode("\n", $text);

                    $path_to_file = $path.$sub_dir.$separator.$this->domain_list_filename . ".csv";
                    touch($path_to_file);
                    if (!$fp = fopen($path_to_file, 'a')) {
                        $this->log->req_status("e", "function result_export($filetype): Cannot open file for writing ($path_to_file)");
                        exit;
                    }
                    if (fwrite($fp, $text) === FALSE) {
                        $this->log->req_status("e", "function result_export($filetype): Cannot write file ($path_to_file)");
                        exit;
                    }
                    fclose($fp);
                    header("Pragma: ");
                    header("Cache-Control: ");
                    header('Content-type: application/octet-stream');
                    header("Content-Length: " . strlen($text));
                    header('Content-Disposition: attachment; filename="'.trim($this->domain_list_filename.".csv").'"');
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
                $this->list_result();
                break;
        }
    }

    /**
     * Main verification method. It rules for every mode
     *
     * on success - returns true
     * on failure - returns false
     *
     * @access  private
     * @return  boolean
     * @see     dispatch()
     */
    function is_valid_input($mode)
    {
        $this->tools->tpl->set_block("repository", "general_error_box");
        $this->tools->tpl->set_block("repository", "field_error_box");
        $is_valid = true;
        switch ($mode)
        {
            case "view":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN", $this->err_msg["_domain"]);
                }
                break;

            case "register_overview":
                $this->tools->parse_bulk_entries($_SESSION["userdata"]["a_domain"]);
                $dom_arr = $this->tools->get_domain_part($_SESSION["httpvars"]["a_domain"][0]);
                foreach ($_SESSION["userdata"]["a_domain"] as $domain)
                {
                    //$dom_curr_arr = $this->tools->get_domain_part($domain);
                    if (!$this->tools->is_valid("joker_domain", $domain, true)) {
                        $is_valid = false;
                        $this->tools->field_err("ERROR_INVALID_DOMAIN", $this->err_msg["_domain_custom"] . $domain, true);
                    }
                }
                if (!$this->tools->is_valid($this->err_regexp["_domain_reg_period"],$_SESSION["httpvars"]["s_reg_period"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_REG_PERIOD",$this->err_msg["_domain_reg_period"]);
                }
                if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_owner"],$dom_arr["tld"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_OWNER_CONTACT",$this->err_msg["_contact_hdl"]." ".$this->err_msg["_contact_hdl_type"]);
                }
                if ($_SESSION["httpvars"]["c_all_as_owner"] != "all") {
                    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_billing"],$dom_arr["tld"])) {
                        $is_valid = false;
                        $this->tools->field_err("ERROR_INVALID_BILLING_CONTACT",$this->err_msg["_contact_hdl"]." ".$this->err_msg["_contact_hdl_type"]);
                    }
                    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_admin"],$dom_arr["tld"])) {
                        $is_valid = false;
                        $this->tools->field_err("ERROR_INVALID_ADMIN_CONTACT",$this->err_msg["_contact_hdl"]." ".$this->err_msg["_contact_hdl_type"]);
                    }
                    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_tech"],$dom_arr["tld"])) {
                        $is_valid = false;
                        $this->tools->field_err("ERROR_INVALID_TECH_CONTACT",$this->err_msg["_contact_hdl"]." ".$this->err_msg["_contact_hdl_type"]);
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
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_NSRV_LIST",$this->err_msg["_ns"]);

                                }
                            }

                        }
                        if ($is_valid && $ns_count < $this->config["ns_min_num"]) {
                            $is_valid = false;
                            $this->tools->field_err("ERROR_INVALID_NSRV_LIST",$this->err_msg["_ns_min"]);
                            $this->tools->tpl->set_var("NS_MIN_NUM",$this->config["ns_min_num"]);
                        }
                        break;
                    default:
                        $this->tools->field_err("ERROR_INVALID_NSRV_SELECT",$this->err_msg["_ns_select"]);
                        $is_valid = false;
                        break;
                }
                break;

            case "renew":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                if (!$this->tools->is_valid($this->err_regexp["_domain_reg_period"],$_SESSION["httpvars"]["s_reg_period"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_REG_PERIOD",$this->err_msg["_domain_reg_period"]);
                }
                break;

            case "add_grant":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                if (!$this->tools->is_valid("email",$_SESSION["httpvars"]["t_email"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_EMAIL",$this->err_msg["_email"]);
                }
                if (empty($_SESSION["httpvars"]["t_nick"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_NICK",$this->err_msg["_empty_field"]);
                }
                break;

            case "revoke_grant":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                if (empty($_SESSION["httpvars"]["t_scope"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_SCOPE",$this->err_msg["_empty_field"]);
                }
                if (empty($_SESSION["httpvars"]["s_role"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_ROLE",$this->err_msg["_empty_field"]);
                }
                break;

            case "transfer":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                if (!$this->tools->is_valid($this->err_regexp["_auth_id"],$_SESSION["httpvars"]["t_auth_id"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_AUTH_ID",$this->err_msg["_auth_id"]);
                }
                if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_billing"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_BILLING_CONTACT",$this->err_msg["_contact_hdl"]);
                }
                break;

            case "fast_transfer":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                if (!$this->tools->is_valid($this->err_regexp["_domain_reg_period"],$_SESSION["httpvars"]["s_reg_period"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_REG_PERIOD",$this->err_msg["_domain_reg_period"]);
                }
                if (!$this->tools->is_valid($this->err_regexp["_auth_id"],$_SESSION["httpvars"]["t_auth_id"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_AUTH_ID",$this->err_msg["_auth_id"]);
                }
                if (!in_array($_SESSION["httpvars"]["s_new_dom_status"], array("production", "lock"))) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_NEW_STATUS",$this->err_msg["_new_dom_status"]);
                }                
                if (!$this->tools->is_valid("email", $_SESSION["httpvars"]["t_contact_owner_email"], true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_OWNER_EMAIL", $this->err_msg["_email"]);
                }
                $dom_arr = $this->tools->get_domain_part($_SESSION["httpvars"]["t_domain"]);
                if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_admin"],$dom_arr["tld"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_ADMIN_CONTACT",$this->err_msg["_contact_hdl"]." ".$this->err_msg["_contact_hdl_type"]);
                }                
                if ($_SESSION["httpvars"]["c_all_as_admin"] != "all") {
                    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_billing"],$dom_arr["tld"])) {
                        $is_valid = false;
                        $this->tools->field_err("ERROR_INVALID_BILLING_CONTACT",$this->err_msg["_contact_hdl"]." ".$this->err_msg["_contact_hdl_type"]);
                    }                    
                    if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_tech"],$dom_arr["tld"])) {
                        $is_valid = false;
                        $this->tools->field_err("ERROR_INVALID_TECH_CONTACT",$this->err_msg["_contact_hdl"]." ".$this->err_msg["_contact_hdl_type"]);
                    }
                }
                break;

            case "modify":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_billing"]) && !empty($_SESSION["httpvars"]["t_contact_billing"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_BILLING_CONTACT",$this->err_msg["_contact_hdl"]);
                }
                if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_admin"]) && !empty($_SESSION["httpvars"]["t_contact_admin"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_ADMIN_CONTACT",$this->err_msg["_contact_hdl"]);
                }
                if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_tech"]) && !empty($_SESSION["httpvars"]["t_contact_tech"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_TECH_CONTACT",$this->err_msg["_contact_hdl"]);
                }
                switch (strtolower($_SESSION["httpvars"]["r_ns_type"]))
                {
                    case "default":
                    case "no_change":
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
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_NSRV_LIST",$this->err_msg["_ns"]);

                                }
                            }

                        }
                        if ($is_valid && $ns_count < $this->config["ns_min_num"]) {
                            $is_valid = false;
                            $this->tools->field_err("ERROR_INVALID_NSRV_LIST",$this->err_msg["_ns_min"]);
                            $this->tools->tpl->set_var("NS_MIN_NUM",$this->config["ns_min_num"]);
                        }
                        break;
                    default:
                        $this->tools->field_err("ERROR_INVALID_NSRV_SELECT",$this->err_msg["_ns_select"]);
                        $is_valid = false;
                        break;
                }
                switch (strtolower($_SESSION["httpvars"]["r_dnssec"]))
                {
                    case "delete":
                    case "no_change":
                        //ok
                        break;
                    case "new":
                        $ds_count = 0;
                        foreach ($_SESSION["httpvars"] as $key => $value)
                        {
                            if (preg_match("/^t_ds/i",$key)) {
                                if ($value != "") {
                                    $ds_count++;
                                }
                            }

                        }
                        if ($ds_count < $this->config["ds_min_num"]) {
                            $is_valid = false;
                            $this->tools->field_err("ERROR_INVALID_DNSSEC_LIST",$this->err_msg["_ds_min"]);
                            $this->tools->tpl->set_var("DS_MIN_NUM",$this->config["ds_min_num"]);
                        }
                        break;
                    default:
                        $this->tools->field_err("ERROR_INVALID_DNSSEC_SELECT",$this->err_msg["_ds_select"]);
                        $is_valid = false;
                        break;
                }
                break;

            case "delete":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                break;

            case "lock_unlock":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                switch (strtolower($_SESSION["httpvars"]["r_lock"]))
                {
                    case "lock":
                    case "unlock":
                        //ok
                        break;
                    default:
                        $this->tools->field_err("ERROR_INVALID_LOCK_UNLOCK_OPT",$this->err_msg["_dom_status"]);
                        $is_valid = false;
                        break;
                }
                break;

            case "autorenew":
                //if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_pattern"],true)) {
                //    $is_valid = false;
                //    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                //}
                switch (strtolower($_SESSION["httpvars"]["r_autorenew"]))
                {
                    case "ar_on":
                    case "ar_off":
                        //ok
                        break;
                    default:
                        $this->tools->field_err("ERROR_INVALID_AUTORENEW_OPT",$this->err_msg["_dom_status"]);
                        $is_valid = false;
                        break;
                }
                break;

            case "grants_change_step1":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                break;

            case "owner_change_step1":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                break;

            case "owner_change_step2":
                $cnt = new Contact;
                $is_valid = $cnt->is_valid_input("owner_contact_submission", false);
                break;

            case "redemption":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
            break;

            case "domain_authid":
                if (!$this->tools->is_valid("joker_domain",$_SESSION["httpvars"]["t_domain"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
            break;

            case "bulk_transfer_step1":
                if (!$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["t_contact_billing"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_BILLING_CONTACT",$this->err_msg["_contact_hdl"]);
                }
                if (!isset($_SESSION["httpvars"]["t_add_info"]) || empty($_SESSION["httpvars"]["t_add_info"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_BULK_TRANSFER_ENTRY",$this->err_msg["_domain_authid_pairs_missing"]);
                }
                $list = $_SESSION["userdata"]["t_add_info"];
                if ($is_valid) {
                    $_SESSION["userdata"]["domain_authid_pairs_all"] = true;
                    if (!$this->tools->parse_bulk_entries($list, "bulk_transfer")) {
                        if (is_array($list) && count($list)) {
                            // needed for warning - will be shown at step2
                            $_SESSION["userdata"]["domain_authid_pairs_all"] = false;
                        } else {
                            $is_valid = false;
                            $this->tools->field_err("ERROR_INVALID_BULK_TRANSFER_ENTRY",$this->err_msg["_domain_authid_pairs_parse_error"]);
                        }
                    }
                    $_SESSION["userdata"]["domain_authid_pairs"] = $list;
                }
            break;
        }
        return $is_valid;
    }
}

?>
