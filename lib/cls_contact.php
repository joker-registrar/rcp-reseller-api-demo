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
     * @var     string
     * @access  private
     * @see     Contact()
     */
    var $nav_main  = "";

    /**
     * Represents the 2nd level of the current user position.
     * Its value is set for every function.
     *
     * @var     string
     * @access  private
     * @see     Contact()
     */
    var $nav_submain  = "";

    /**
     * Contains array of regular expressions for verification
     *
     * @var     array
     * @access  private
     * @see     Contact()
     */
    var $err_regexp  = array();

    /**
     * Contains array of error messages used in verification
     *
     * @var     array
     * @access  private
     * @see     Contact()
     */
    var $err_msg  = array();

    /**
     * Contains array of messages used in templates
     *
     * @var     array
     * @access  private
     * @see     Contact()
     */
    var $messages  = array();

    /**
     * Array that defines how many entries are shown per page.
     *
     * @var     array
     * @access  private
     * @see     Contact()
     */
    var $contact_list_entries_per_page = array(20, 50, 100);

    /**
     * Default entry page
     *
     * @var     integer
     * @access  private
     * @see     Contact()
     */
    var $contact_list_default_entry_page = 20;

    /**
     * Defines the number of paging links on every page
     *
     * @var     integer
     * @access  private
     * @see     Contact()
     */
    var $contact_list_page_links_per_page = 10;

    /**
     * Default page for paging
     *
     * @var     integer
     * @access  private
     * @see     Contact()
     */
    var $contact_list_default_page = 1;

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Contact()
     *
     * @access  private
     * @return  void
     */
    function Contact()
    {
        global $error_messages, $error_regexp, $jpc_config, $tools, $nav, $messages;
        $this->err_msg    = $error_messages;
        $this->err_regexp = $error_regexp;
        $this->config     = $jpc_config;
        $this->tools      = $tools;
        $this->nav        = $nav;
        $this->msg        = $messages;
        $this->connect    = new Connect;
        $this->user       = new User;
        $this->log        = new Log;
        $this->nav_main   = $this->nav["contacts"];
    }

    /**
     * Redirects the function calls after input verification.
     *
     * @param   $mode
     * @access  public
     * @return  void
     */
    function dispatch($mode)
    {
        switch ($mode) {

            case "contact_list_result":
                $is_valid = $this->is_valid_input("contact_list_result");
                if (!$is_valid) {
                    $this->contact_list_form();
                } else {
                    $this->contact_list_result();
                }
                break;

            case "contact_form":
                $is_valid = $this->is_valid_input("contact_form");
                switch ($_SESSION["userdata"]["op"])
                {
                    case "create_contact":
                        if (!$is_valid) {
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
                        if (!$is_valid) {
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
                $is_valid = $this->is_valid_input(
                            "contact_submission",
                            true,
                            (strtolower($_SESSION["userdata"]["s_tld"]) == "eu" ? true : false));
                if (!$is_valid) {
                    $this->contact_form($_SESSION["userdata"]["s_tld"], $_SESSION["userdata"]["c_opt_fields"]);
                } else {
                    $this->contact_create();
                }
                break;

            case "contact_modify":
                $is_valid = $this->is_valid_input("contact_submission");
                if (!$is_valid) {
                    $this->contact_form($_SESSION["userdata"]["cnt_hdl"],true);
                } else {
                    $this->contact_modify();
                }
                break;

            case "contact_delete":
                $is_valid = $this->is_valid_input("contact_delete");
                if (!$is_valid) {
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
     * @access  public
     * @return  void
     */
    function contact_list_form()
    {
        $this->nav_submain = $this->nav["list"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("contact_repository", "info_view_contact_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_view_contact_row");

        $this->tools->tpl->set_block("contact_list_form","list_contact_option","ls_cnt_opt");

        foreach($_SESSION["auto_config"]["avail_tlds"] as $value)
        {
            $this->tools->tpl->set_var("SELECTED",($_SESSION["userdata"]["s_tld"] == $value) ? "selected" : "");
            $this->tools->tpl->set_var("S_TLD",$value);
            $this->tools->tpl->parse("ls_cnt_opt","list_contact_option",true);
        }
        $this->tools->tpl->parse("CONTENT", "contact_list_form");
        unset($_SESSION["userdata"]["p"]);
        unset($_SESSION["userdata"]["s"]);
    }

    /**
     * Shows a contact list.
     *
     * on success - contact list
     * on failure - back to the contact list form
     *
     * @access  private
     * @return  void
     * @see     contact_list_form()
     */
    function contact_list_result()
    {
        $this->nav_submain = $this->nav["list"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $this->tools->empty_formdata();

        if (isset($_SESSION["storagedata"]["contacts"]) &&
            isset($_SESSION["storagedata"]["contacts"]["list"]) &&
            isset($_SESSION["storagedata"]["contacts"]["pattern"]) &&
            $_SESSION["storagedata"]["contacts"]["pattern"] == $_SESSION["userdata"]["t_pattern"] &&
            $_SESSION["storagedata"]["contacts"]["tld"] == $_SESSION["userdata"]["s_tld"] &&
            isset($_SESSION["storagedata"]["contacts"]["last_updated"]) &&
            $_SESSION["storagedata"]["contacts"]["last_updated"] + $this->config["cnt_list_caching_period"] > time()) {
            $result = $_SESSION["storagedata"]["contacts"]["list"];
        } else {
            $_SESSION["storagedata"]["contacts"]["pattern"] = $_SESSION["userdata"]["t_pattern"];
            $_SESSION["storagedata"]["contacts"]["tld"] = $_SESSION["userdata"]["s_tld"];
            $_SESSION["storagedata"]["contacts"]["last_updated"] = time();
            $result = $_SESSION["storagedata"]["contacts"]["list"] = $this->contact_list($_SESSION["userdata"]["s_tld"], $_SESSION["userdata"]["t_pattern"]);
        }

        $paging = new Paging();
        $paging->setAvailableEntriesPerPage($this->contact_list_entries_per_page);
        $paging->setPageLinksPerPage($this->contact_list_page_links_per_page);
        $total_contacts = count($result);
        $paging->initSelectedEntriesPerPage($_SESSION["userdata"]["s"], $this->contact_list_default_entry_page);
        $total_pages = ceil($total_contacts / $paging->getPageLinksPerPage());
        $paging->initSelectedPageNumber($_SESSION["userdata"]["p"], $this->contact_list_default_page, $total_pages);
        $this->tools->tpl->set_var("PAGING_RESULTS_PER_PAGE", $paging->buildEntriesPerPageBlock($_SESSION["userdata"]["s"], "contact"));
        $this->tools->tpl->set_var("PAGING_PAGES", $paging->buildPagingBlock($total_contacts, $_SESSION["userdata"]["s"], $_SESSION["userdata"]["p"], "contact"));
        $paging->parsePagingToolbar("paging_repository", "paging_toolbar_c2", "PAGE_TOOLBAR");

        if ($result != false) {
            $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_sub_btn");
            $this->tools->tpl->set_block("repository","result_table_row");
            $this->tools->tpl->set_block("repository","result_table");
            $this->tools->tpl->set_block("repository","no_ns_result");
            $this->tools->tpl->set_block("repository","query_for_contact_data");
            $this->tools->tpl->set_block("repository", "choose_contact_row", "choose_contact_r");
            if ($result != $this->config["empty_result"] && is_array($result)) {
                $this->tools->tpl->parse("FORMTABLEROWS", "choose_contact_row", true);
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
                $is = $paging->calculateResultsStartIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                $ie = $paging->calculateResultsEndIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                for ($i=$is; $i < $ie; $i++)
                {
                    if (isset($result[$i])) {
                        $this->tools->tpl->set_var(array(
                            "CONTACT_HANDLE"    => $result[$i]["0"],
                            "URLENC_CONTACT_HANDLE" => urlencode($result[$i]["0"])
                            ));
                        $this->tools->tpl->parse("FIELD1", "query_for_contact_data");
                        $this->tools->tpl->parse("FORMTABLEROWS", "result_table_row",true);
                    }
                }
            } else {
                $this->tools->tpl->parse("FORMTABLEROWS", "no_ns_result");
            }
            $this->tools->tpl->parse("CONTENT", "result_table");
        } else {
            $this->tools->tpl->set_block("repository","general_error_box");
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->contact_list_form();
        }
    }

    /**
     * Returns an array of contacts.
     *
     * @access  public
     * @return  mixed
     * @see     contact_list_result()
     */
    function contact_list()
    {
        $fields = array(
        "pattern"   => $_SESSION["userdata"]["t_pattern"],
        "tld"       => ($_SESSION["userdata"]["s_tld"] == "all" ? "" : $_SESSION["userdata"]["s_tld"])
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
     * @access  private
     * @return  void
     * @see     contact_list_form()
     */
    function show_contact()
    {
        $this->nav_submain = $this->nav["show"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main . "  &raquo; " . $this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $result = $this->tools->query_object("contact", $_SESSION["userdata"]["cnt_hdl"], true);
        if ($result) {
            if ($result != $this->config["empty_result"] && is_array($result)) {
                $this->tools->tpl->set_block("repository", "result_table_submit_btn", "res_tbl_sub_btn");
                $this->tools->tpl->set_block("repository", "result_table_row");
                $this->tools->tpl->set_block("repository", "result_table");
                $this->tools->tpl->set_block("repository", "back_button_js");
                foreach($result as $value)
                {
                    $this->tools->tpl->set_var(
                        array(
                            "FIELD1"    => $value["0"],
                            "FIELD2"    => $value["1"],
                        ));
                    if ("contact.email:" == $value["0"]) {                        
                        $this->tools->tpl->set_var("FIELD2", $this->tools->format_fqdn($value["1"], "unicode", "email", true));
                    }
                    if (in_array($value["0"], array("contact.created.date:", "contact.modified.date:"))) {
                        $this->tools->tpl->set_var("FIELD2", $this->tools->prepare_date($value["1"]));
                    }
                    $this->tools->tpl->parse("FORMTABLEROWS", "result_table_row", true);
                }
                switch ($_SESSION["userdata"]["op"])
                {
                    case "delete_contact":
                        $this->tools->tpl->set_var("MODE","contact_delete");
                        $this->tools->tpl->parse("res_tbl_sub_btn","result_table_submit_btn");
                        break;
                    default:
                        $this->tools->tpl->set_var(
                                    array(
                                        "FIELD1"    => "",
                                        "FIELD2"    => ""
                                    ));
                        $this->tools->tpl->parse("FORMTABLEROWS", "result_table_row", true);
                        $this->tools->tpl->set_var("FIELD1", "");
                        $this->tools->tpl->parse("FIELD2", "back_button_js");
                        $this->tools->tpl->parse("FORMTABLEROWS", "result_table_row", true);
                        break;
                }
                $this->tools->tpl->parse("CONTENT", "result_table");
            }
        } else {
            $this->tools->tpl->set_block("repository","general_error_box");
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->contact_list_form();
        }
    }

    /**
     * Shows a form for choosing which type of contact will be handled.
     *
     * @access  public
     * @return  void
     */
    function contact_select_tld_form()
    {
        $this->nav_submain = $this->nav["select"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
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
        foreach($_SESSION["auto_config"]["avail_tlds"] as $value)
        {
            $this->tools->tpl->set_var("S_TLD",$value);
            $this->tools->tpl->parse("ls_cnt_opt","list_contact_option",true);
        }
        $this->tools->tpl->parse("CONTENT", "contact_sel_tld_form");
        unset($_SESSION["userdata"]["p"]);
        unset($_SESSION["userdata"]["s"]);
    }

    /**
     * Shows a form for contact input.
     *
     * @param   string  $tld needed for referencing the contact profile
     * @param   boolean $opt_fields show optional fields
     * @access  public
     * @return  void
     */
    function contact_form($tld,$opt_fields = false)
    {
        switch ($_SESSION["userdata"]["op"]) {

            case "create_contact":
                $this->nav_submain = $this->nav["create"];
                $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
                $this->tools->tpl->parse("NAV","navigation");

                $this->tools->tpl->set_var("T_TLD", $_SESSION["userdata"]["s_tld"]);
                $this->build_contact_form("contact_form", $tld, $opt_fields, "create_contact");
                $this->tools->tpl->set_var("MODE","contact_create");
                $this->tools->tpl->parse("CONTENT", "contact_form");
                break;

            case "modify_contact":
                $this->nav_submain = $this->nav["edit"];
                $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
                $this->tools->tpl->parse("NAV","navigation");

                $this->build_contact_form("contact_form",$this->tools->type_of_contact($tld),$opt_fields, "modify_contact");
                $result = $this->tools->query_object("contact", $_SESSION["userdata"]["cnt_hdl"], true);
                if ($result != false) {
                    if ($result != $this->config["empty_result"] && is_array($result)) {
                        $form_data_arr = $this->tools->fill_form_prep($result, "contact");
                        if ($tld == "eu") {
                            $this->tool->tpl->set_var("LANG", $form_data_arr["s_contact_country"]);
                        }                                                
                        $form_data_arr["t_contact_email"] = $this->tools->format_fqdn($form_data_arr["t_contact_email"], "unicode", "email", false);
                        if (is_array($form_data_arr)) {
                            $this->tools->fill_form($form_data_arr);
                        }
                    } else {
                        $this->tools->tpl->set_block("repository","general_error_box");
                        $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
                        $this->contact_list_result();
                    }
                }
                $this->tools->tpl->set_var("T_TLD", $_SESSION["userdata"]["cnt_hdl"]);
                $this->tools->tpl->set_var("MODE","contact_modify");
                $this->tools->tpl->parse("CONTENT", "contact_form");
                break;

            case "delete_contact":
                $this->nav_submain = $this->nav["delete"];
                $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
                $this->tools->tpl->parse("NAV","navigation");

                $this->tools->tpl->set_block("contact_sel_tld_form","contact_optional_fields","contact_opt_flds");
                break;
        }
    }

    /**
     * Creates a contact.
     *
     * @access  public
     * @return  mixed
     * @see     contact_form()
     */
    function contact_create()
    {
        $this->nav_submain = $this->nav["create"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $fields = array(
            "tld"       => $_SESSION["userdata"]["s_tld"],
            "name"      => $_SESSION["httpvars"]["t_contact_name"],
            "fname"     => $_SESSION["httpvars"]["t_contact_fname"],
            "lname"     => $_SESSION["httpvars"]["t_contact_lname"],
            "title"     => $_SESSION["httpvars"]["t_contact_title"],
            "individual"    => "" == $_SESSION["httpvars"]["t_contact_individual"] ? "N" : $_SESSION["httpvars"]["t_contact_individual"],
            "organization"  => $_SESSION["httpvars"]["t_contact_organization"],            
            "email"     => $this->tools->format_fqdn($_SESSION["httpvars"]["t_contact_email"], "ascii"),
            "address-1" => $_SESSION["httpvars"]["t_contact_address_1"],
            "address-2" => $_SESSION["httpvars"]["t_contact_address_2"],
            "address-3" => $_SESSION["httpvars"]["t_contact_address_3"],
            "city"      => $_SESSION["httpvars"]["t_contact_city"],
            "state"     => $_SESSION["httpvars"]["t_contact_state"],
            "postal-code"   => $_SESSION["httpvars"]["t_contact_postal_code"],
            "country"   => $_SESSION["httpvars"]["s_contact_country"],
            "phone"     => $_SESSION["httpvars"]["t_contact_phone"],
            "extension" => $_SESSION["httpvars"]["t_contact_extension"],
            "fax"       => $_SESSION["httpvars"]["t_contact_fax"]
        );
        if ("eu" == $_SESSION["userdata"]["s_tld"]) {
            $fields["language"] = $_SESSION["httpvars"]["s_contact_language"];
        }
        if ("us" == $_SESSION["userdata"]["s_tld"]) {
            $fields["app-purpose"] = $_SESSION["httpvars"]["s_contact_app_purpose"];
            $fields["nexus-category"] = $_SESSION["httpvars"]["s_contact_category"];
            $fields["nexus-category-country"] = $_SESSION["httpvars"]["s_nexus_category_country"];
        }
        if (!$this->connect->execute_request("contact-create", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->contact_form($_SESSION["userdata"]["s_tld"], $_SESSION["userdata"]["c_opt_fields"]);
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Modifies a contact.
     *
     * @access  public
     * @return  mixed
     * @see     contact_form()
     */
    function contact_modify()
    {
        $this->nav_submain = $this->nav["edit"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $fields = array(
            "handle"    => $_SESSION["userdata"]["cnt_hdl"],
            "name"      => "" == $_SESSION["httpvars"]["t_contact_name"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_name"],
            "fname"     => "" == $_SESSION["httpvars"]["t_contact_fname"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_fname"],
            "lname"     => "" == $_SESSION["httpvars"]["t_contact_lname"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_lname"],
            "title"     => "" == $_SESSION["httpvars"]["t_contact_title"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_title"],
            "individual"    => "" == $_SESSION["httpvars"]["t_contact_individual"] ? "N" : $_SESSION["httpvars"]["t_contact_individual"],
            "organization"  => "" == $_SESSION["httpvars"]["t_contact_organization"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_organization"],
            "email"     => $this->tools->format_fqdn($_SESSION["httpvars"]["t_contact_email"], "ascii"),
            "address-1" => $_SESSION["httpvars"]["t_contact_address_1"],
            "address-2" => "" == $_SESSION["httpvars"]["t_contact_address_2"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_address_2"],
            "address-3" => "" == $_SESSION["httpvars"]["t_contact_address_3"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_address_3"],
            "city"      => $_SESSION["httpvars"]["t_contact_city"],
            "state"     => "" == $_SESSION["httpvars"]["t_contact_state"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_state"],
            "postal-code"   => $_SESSION["httpvars"]["t_contact_postal_code"],
            "country"   => $_SESSION["httpvars"]["s_contact_country"],
            "phone"     => $_SESSION["httpvars"]["t_contact_phone"],
            "extension" => "" == $_SESSION["httpvars"]["t_contact_extension"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_extension"],
            "fax"       => "" == $_SESSION["httpvars"]["t_contact_fax"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["t_contact_fax"],
            "app-purpose"   => "" == $_SESSION["httpvars"]["s_contact_app_purpose"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["s_contact_app_purpose"],
            "nexus-category"=> "" == $_SESSION["httpvars"]["s_contact_category"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["s_contact_category"],
            "nexus-category-country"    => "" == $_SESSION["httpvars"]["s_nexus_category_country"] ? $this->config["empty_field_value"] : $_SESSION["httpvars"]["s_nexus_category_country"]
        );
        if (!$this->connect->execute_request("contact-modify", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->contact_form($_SESSION["userdata"]["cnt_hdl"],true);
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Deletes a contact.
     *
     * @access  public
     * @return  mixed
     * @see     contact_form()
     */
    function contact_delete()
    {
        $this->nav_submain = $this->nav["delete"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $fields = array(
            "handle"    => $_SESSION["userdata"]["cnt_hdl"],
        );
        if (!$this->connect->execute_request("contact-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->contact_list_result();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Creates a contact input form. Uses the contact profile defined in config.php
     *
     * @param   string  $host_tpl template to be parsed
     * @param   string  $tld needed for referencing the contact profile
     * @param   boolean $opt_fields show optional fields
     * @access  private
     * @return  void
     * @see     contact_form()
     */
    function build_contact_form($host_tpl, $tld, $opt_fields, $type = "")
    {
        $this->tools->tpl->parse("TEMP_TPL_CONTAINER", $host_tpl);
        $tpl_content = $this->tools->tpl->get_var("TEMP_TPL_CONTAINER");
        //catching the subtemplate names
        $reg = "/[ \t]*<!--\s+BEGIN ([a-z0-9_-]+)\s+-->\s*?\n?/sm";
        preg_match_all($reg,$tpl_content, $m);
        foreach ($m[1] as $field)
        {
            $this->tools->tpl->set_block($host_tpl ,$field,"cnt_".$field);
        }
        if (!isset($this->config["domain"][$tld])) {
            $tld = 'default';
        } 
        foreach ($this->config["domain"][$tld]["contact"]["fields"] as $field => $params)
        {
            if ($params["required"]) {
                if ($field == "language" && $tld == "eu" && $type == "modify_contact") {
                    $this->tools->tpl->set_block("contact_repository", "language_input_field");
                    $this->tools->tpl->parse("CONTACT_LANGUAGE", "language_input_field");
                }
                $this->tools->tpl->parse("cnt_".$field, $field);
            } else {
                if ($opt_fields) {
                    $this->tools->tpl->parse("cnt_".$field, $field);
                }
            }
            if (isset($params["size"])) {
                $this->tools->tpl->set_var(strtoupper("MAX_LENGTH_".$field), $params["size"]);
            }
        }
        $this->tools->tpl->set_var("HELP_INDIVIDUAL", $this->msg["_individual_help_txt"]);
        $this->tools->tpl->parse("CONTACT_COUNTRY","country_ls");
        $this->tools->tpl->parse("CONTACT_LANGUAGE","language_ls");
        $this->tools->tpl->parse("CONTACT_APP_PURPOSE","nexus_application_purpose");
        $this->tools->tpl->parse("CONTACT_NEXUS_CATEGORY","nexus_category");
        $this->tools->tpl->parse("CONTACT_NEXUS_CATEGORY_COUNTRY","nexus_category_country");
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
    function is_valid_input($mode, $set_block = true, $has_utf8_chars = false)
    {
        if ($set_block) {
            $this->tools->tpl->set_block("repository","general_error_box");
            $this->tools->tpl->set_block("repository","field_error_box");
        }
        $is_valid = true;
        switch ($mode) {

            case "contact_list_result":
                if (!($this->tools->is_valid("joker_tld", $_SESSION["userdata"]["s_tld"], true) || $_SESSION["userdata"]["s_tld"] == "all")) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_TLD", $this->err_msg["_tld"]);
                }
                break;

            case "contact_form":
                // this code is weak - attention!
                if (isset($_SESSION["httpvars"]["s_tld"]) && !$this->tools->is_valid("joker_tld", $_SESSION["httpvars"]["s_tld"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_TLD",$this->err_msg["_tld"]);
                }
                if (isset($_SESSION["httpvars"]["cnt_hdl"]) && !$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["cnt_hdl"],"unknown")) {
                    $is_valid = false;
                    $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_contact_hdl"]);
                }
                break;

            case "contact_submission":
                if (isset($this->config["domain"][$_SESSION["userdata"]["s_tld"]])) {
                    $tld = $_SESSION["userdata"]["s_tld"];
                } else {
                    $tld = 'default';
                }
                foreach ($this->config["domain"][$tld]["contact"]["fields"] as $field => $params)
                {
                    if ($params["required"]) {
                        switch (strtolower($field)) {

                            case "language":
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["s_contact_language"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_LANGUAGE",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = strlen($_SESSION["httpvars"]["s_contact_language"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_LANGUAGE",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "name":
                                if (!$has_utf8_chars) {
                                    $regexp_name = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_name = $this->err_regexp["_utf8_string"];
                                }
                                if (!$this->tools->is_valid($regexp_name, $_SESSION["httpvars"]["t_contact_name"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_FULL_NAME", $this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_name"], $this->config["site_encoding"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_FULL_NAME", $this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "fname":
                                if (!$has_utf8_chars) {
                                    $regexp_fname = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_fname = $this->err_regexp["_utf8_string"];
                                }
                                if (!$this->tools->is_valid($regexp_fname, $_SESSION["httpvars"]["t_contact_fname"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_FNAME",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_fname"], $this->config["site_encoding"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_FNAME",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "lname":
                                if (!$has_utf8_chars) {
                                    $regexp_lname = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_lname = $this->err_regexp["_utf8_string"];
                                }
                                if (!$this->tools->is_valid($regexp_lname, $_SESSION["httpvars"]["t_contact_lname"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_LNAME",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_lname"], $this->config["site_encoding"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_LNAME",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "organization":
                                if (!$has_utf8_chars) {
                                    $regexp_org = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_org = $this->err_regexp["_utf8_string"];
                                }
                                if (!$this->tools->is_valid($this->err_regexp["_is_individual"], $_SESSION["httpvars"]["t_contact_individual"])) {
                                    if (!$this->tools->is_valid($regexp_org, $_SESSION["httpvars"]["t_contact_organization"])) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_ORGANIZATION", $this->err_msg["_invalid_chars_in_field"]);
                                    } else {
                                        $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_organization"], $this->config["site_encoding"]);
                                        if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                            $is_valid = false;
                                            $this->tools->field_err("ERROR_INVALID_ORGANIZATION", $this->err_msg["_invalid_field_length"]);
                                            $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                        }
                                    }
                                }
                            break;

                            case "email":
                                if (!$this->tools->is_valid("email", $_SESSION["httpvars"]["t_contact_email"],true)) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_EMAIL",$this->err_msg["_email"]);
                                } else {
                                    $str_length = strlen($_SESSION["httpvars"]["t_contact_email"]);
                                    if (is_numeric($params["size"]) && $str_length > $params["size"]) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_EMAIL",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "address-1":
                                if (!$has_utf8_chars) {
                                    $regexp_addr = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_addr = $this->err_regexp["_utf8_string"];
                                }
                                if (!$this->tools->is_valid($regexp_addr, $_SESSION["httpvars"]["t_contact_address_1"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_ADDRESS1", $this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_address_1"], $this->config["site_encoding"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_ADDRESS1",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "city":
                                if (!$has_utf8_chars) {
                                    $regexp_city = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_city = $this->err_regexp["_utf8_string"];
                                }
                                if (!$this->tools->is_valid($regexp_city, $_SESSION["httpvars"]["t_contact_city"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_CITY",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_city"], $this->config["site_encoding"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_CITY",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "postal-code":
                                if (!$has_utf8_chars) {
                                    $regexp_pcode = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_pcode = $this->err_regexp["_utf8_string"];
                                }
                                if (!$this->tools->is_valid($regexp_pcode, $_SESSION["httpvars"]["t_contact_postal_code"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_POSTAL_CODE",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_postal_code"], $this->config["site_encoding"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_POSTAL_CODE",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "country":
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["s_contact_country"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_COUNTRY",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = strlen($_SESSION["httpvars"]["s_contact_country"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_COUNTRY",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "phone":
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_phone"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_PHONE",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = strlen($_SESSION["httpvars"]["t_contact_phone"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_PHONE",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;

                            case "fax":
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_fax"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_FAX",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    $str_length = strlen($_SESSION["httpvars"]["t_contact_fax"]);
                                    if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_FAX",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;
                            case "app-purpose":
                                if (!$this->tools->is_valid($this->err_regexp["_app_purpose"], $_SESSION["httpvars"]["s_contact_app_purpose"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_APP_PURPOSE",$this->err_msg["_invalid_chars_in_field"]);
                                }
                            break;
                            case "nexus-category":
                                if (!$this->tools->is_valid($this->err_regexp["_nexus_category"], $_SESSION["httpvars"]["s_contact_category"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_NEXUS_CATEGORY",$this->err_msg["_invalid_chars_in_field"]);
                                }
                            break;
                            case "nexus-category-country":
                                if (isset($_SESSION["httpvars"]["s_contact_category"]) &&
                                   (strtolower($_SESSION["httpvars"]["s_contact_category"]) == "c31" ||
                                    strtolower($_SESSION["httpvars"]["s_contact_category"]) == "c32"))
                                {
                                    if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["s_nexus_category_country"])) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_NEXUS_CATEGORY_COUNTRY",$this->err_msg["_nexus_category_language"]);
                                    } else {
                                        $str_length = strlen($_SESSION["httpvars"]["s_nexus_category_country"]);
                                        if (is_numeric($params["size"]) && ($str_length > $params["size"] || $str_length == 0)) {
                                            $is_valid = false;
                                            $this->tools->field_err("ERROR_INVALID_NEXUS_CATEGORY_COUNTRY",$this->err_msg["_nexus_category_language"]);
                                            $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                        }
                                    }
                                }
                            break;
                        }
                    } else {
                        switch (strtolower($field)) {
                            case "title":
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_title"]);
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_title"])) {
                                    if (is_numeric($params["size"]) && $str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_TITLE",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif ($str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_TITLE",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;

                            case "individual":
                                $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_individual"], $this->config["site_encoding"]);
                                if (!$this->tools->is_valid($this->err_regexp["_individual"], $_SESSION["httpvars"]["t_contact_individual"])) {
                                    if (is_numeric($params["size"]) && $str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_INDIVIDUAL",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif ($str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_INDIVIDUAL",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;

                            case "address-2":
                                if (!$has_utf8_chars) {
                                    $regexp_addr2 = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_addr2 = $this->err_regexp["_utf8_string"];
                                }
                                $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_address_2"], $this->config["site_encoding"]);
                                if (!$this->tools->is_valid($regexp_addr2, $_SESSION["httpvars"]["t_contact_address_2"])) {
                                    if (is_numeric($params["size"]) && $str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_ADDRESS2",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif ($str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_ADDRESS2",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;

                            case "address-3":
                                if (!$has_utf8_chars) {
                                    $regexp_addr3 = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_addr3 = $this->err_regexp["_utf8_string"];
                                }
                                $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_address_3"], $this->config["site_encoding"]);
                                if (!$this->tools->is_valid($regexp_addr3, $_SESSION["httpvars"]["t_contact_address_3"])) {
                                    if (is_numeric($params["size"]) && $str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_ADDRESS3",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif ($str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_ADDRESS3",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;

                            case "state":
                                if (!$has_utf8_chars) {
                                    $regexp_state = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_state = $this->err_regexp["_utf8_string"];
                                }
                                $str_length = mb_strlen($_SESSION["httpvars"]["t_contact_state"], $this->config["site_encoding"]);
                                if (!$this->tools->is_valid($regexp_state, $_SESSION["httpvars"]["t_contact_state"])) {
                                    if (is_numeric($params["size"]) && $str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_STATE",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif ($str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_STATE",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;

                            case "extension":
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_extension"]);
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_extension"])) {
                                    if (is_numeric($params["size"]) && $str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_EXTENSION",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif ($str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_EXTENSION",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;

                            case "fax":
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_fax"]);
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_fax"])) {
                                    if (is_numeric($params["size"]) && $str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_FAX",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif ($str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_FAX",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;
                        }
                    }
                }
                break;

            case "contact_delete":
                if (!$this->tools->is_valid_contact_hdl($_SESSION["userdata"]["cnt_hdl"])) {
                    $is_valid = false;
                    $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_contact_hdl"]);
                }
                break;
        }
        return $is_valid;
    }
}

?>
