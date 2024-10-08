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
     * Default filename for the exported result list
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     Domain()
     */
    var $contact_list_filename = "contact_list";

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Contact()
     *
     * @access  private
     * @return  void
     */
    function __construct()
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
        $this->temp_dir  = $jpc_config["temp_dir"];
        $this->temp_perm = $jpc_config["temp_file_perm"];
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
                $is_valid = $this->is_valid_input("modify_contact_submission");
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
            case "contact_verified":
                $is_valid = $this->is_valid_input("contact_verified");
                if (!$is_valid) {
                    $this->contact_verified_form();
                } else {
                    $this->contact_verified();
                }
                break;
            case "contact_resend_email":
                $is_valid = $this->is_valid_input("contact_resend_email");
                if (!$is_valid) {
                    $this->contact_unverified_list_result();
                } else {
                    $this->contact_resend_email();
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
            if (isset($_SESSION["userdata"]["s_tld"])) {
                $this->tools->tpl->set_var("SELECTED",($_SESSION["userdata"]["s_tld"] == $value) ? "selected" : "");
            }
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
            $_SESSION["storagedata"]["contacts"]["last_updated"] + $this->config["cnt_list_caching_period"] > time() &&
            !isset($_SESSION["httpvars"]["refresh"])) {
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
        $total_pages = ceil($total_contacts / $_SESSION["userdata"]["s"]);
        $paging->initSelectedPageNumber($_SESSION["userdata"]["p"], $this->contact_list_default_page, $total_pages);
        $this->tools->tpl->set_var("PAGING_RESULTS_PER_PAGE", $paging->buildEntriesPerPageBlock($_SESSION["userdata"]["s"], "contact"));
        $this->tools->tpl->set_var("PAGING_PAGES", $paging->buildPagingBlock($total_contacts, $_SESSION["userdata"]["s"], $_SESSION["userdata"]["p"], "contact"));
        $paging->parsePagingToolbar("paging_repository", "paging_toolbar_c5", "PAGE_TOOLBAR");

        if ($result != false) {
            $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_sub_btn");
            $this->tools->tpl->set_block("repository","result_contact_table_row");
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
                            "CONTACT_HANDLE"    => $result[$i]["handle"],
                            "URLENC_CONTACT_HANDLE" => urlencode($result[$i]["handle"]),
                            "NAME" => $result[$i]["name"],
                            "ORGANIZATION" => $result[$i]["organization"],
                            "EMAIL" => $result[$i]["email"],
                            "TR_CLASS" => $i%2?"tr_even":"tr_odd"
                            ));
                        $this->tools->tpl->parse("HANDLE", "query_for_contact_data");
                        $this->tools->tpl->parse("FORMTABLEROWS", "result_contact_table_row",true);
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
        "tld"       => ($_SESSION["userdata"]["s_tld"] == "all" ? "" : $_SESSION["userdata"]["s_tld"]),
        "extended-format" => 1
        );
        if ($this->connect->execute_request("query-contact-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            return ($this->tools->parse_response_list($_SESSION["response"]));
        } else {
            return false;
        }
    }

    /**
     * Exports the contact list into file with user specified filetype
     *
     * @param   string  $filetype   e.g. csv, xsl etc.
     * @access  public
     * @return  void
     */
    function contact_list_export($filetype = "csv")
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
                    $text[] = $csv->arrayToCsvString(array("HANDLE","NAME","ORGANIZATION","EMAIL"));
                    if (isset($_SESSION["storagedata"]["contacts"]["list"])) {
                        foreach ($_SESSION["storagedata"]["contacts"]["list"] as $val)
                        {
                            $handle = $val["handle"];
                            $name = $val["name"];
                            $organization = $val["organization"];
                            $email = $val["email"];
                            $row_arr = array($handle, $name, $organization,$email);
                            $text[] = $csv->arrayToCsvString($row_arr);
                        }
                    }
                    $text = implode("\n", $text);

                    $path_to_file = $path.$sub_dir.$separator.$this->contact_list_filename . ".csv";
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
                    header('Content-Disposition: attachment; filename="'.trim($this->contact_list_filename.".csv").'"');
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
                $this->contact_list_result();
                break;
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
                $this->tools->tpl->set_block("repository", "back_button_contact_block");
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
                        $this->tools->tpl->parse("FIELD2", "back_button_contact_block");
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
        unset($_SESSION["userdata"]["t_pattern"]);
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
                // for modify contact $tld is actually the contact handle
                $handle = $tld;
                $tlds = $this->tools->type_of_contact($handle);
                $tld = count($tlds)>0?reset($tlds):"unkown";
                $this->nav_submain = $this->nav["edit"];
                $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
                $this->tools->tpl->parse("NAV","navigation");
                $this->build_contact_form("contact_form", $tld, $opt_fields, "modify_contact");
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
        if ("uk" == $_SESSION["userdata"]["s_tld"] || ".uk" == substr($_SESSION["userdata"]["s_tld"],-3)) {
            $fields["company-number"] = $_SESSION["httpvars"]["t_contact_company_number"];
            $fields["account-type"] = $_SESSION["httpvars"]["s_contact_account_type"];
        }
        if ("se" == $_SESSION["userdata"]["s_tld"] || "nu" == $_SESSION["userdata"]["s_tld"]) {
            $fields["orgid"] = $_SESSION["httpvars"]["t_contact_org_id"];
            $fields["vatid"] = $_SESSION["httpvars"]["t_contact_vat_id"];
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
        $cnt_empty_field_value = "!@!";

        $fields = array(
            "handle"    => $_SESSION["userdata"]["cnt_hdl"],
            "name"      => "" === $_SESSION["httpvars"]["t_contact_name"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_name"],
            "fname"     => "" === $_SESSION["httpvars"]["t_contact_fname"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_fname"],
            "lname"     => "" === $_SESSION["httpvars"]["t_contact_lname"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_lname"],
            "title"     => "" === $_SESSION["httpvars"]["t_contact_title"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_title"],
            "organization"  => "" === $_SESSION["httpvars"]["t_contact_organization"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_organization"],
            "email"     => $this->tools->format_fqdn($_SESSION["httpvars"]["t_contact_email"], "ascii"),
            "address-1" => $_SESSION["httpvars"]["t_contact_address_1"],
            "address-2" => "" === $_SESSION["httpvars"]["t_contact_address_2"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_address_2"],
            "address-3" => "" === $_SESSION["httpvars"]["t_contact_address_3"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_address_3"],
            "city"      => $_SESSION["httpvars"]["t_contact_city"],
            "state"     => "" === $_SESSION["httpvars"]["t_contact_state"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_state"],
            "postal-code"   => $_SESSION["httpvars"]["t_contact_postal_code"],
            "country"   => $_SESSION["httpvars"]["s_contact_country"],
            "phone"     => $_SESSION["httpvars"]["t_contact_phone"],
            "extension" => "" === $_SESSION["httpvars"]["t_contact_extension"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_extension"],
            "fax"       => "" === $_SESSION["httpvars"]["t_contact_fax"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_fax"],
            "app-purpose"   => "" == $_SESSION["httpvars"]["s_contact_app_purpose"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["s_contact_app_purpose"],
            "nexus-category"=> "" == $_SESSION["httpvars"]["s_contact_category"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["s_contact_category"],
            "nexus-category-country"    => "" == $_SESSION["httpvars"]["s_nexus_category_country"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["s_nexus_category_country"],
            "company-number" => "" == $_SESSION["httpvars"]["t_contact_company_number"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_company_number"],
            "account-type" => "" == $_SESSION["httpvars"]["s_contact_account_type"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["s_contact_account_type"],
            //"orgid" => "" == $_SESSION["httpvars"]["t_contact_org_id"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_org_id"], // cannot be modified
            "vatid" => "" == $_SESSION["httpvars"]["t_contact_vat_id"] ? $cnt_empty_field_value : $_SESSION["httpvars"]["t_contact_vat_id"],
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
     * Resends verification email
     *
     * @access  public
     * @return  mixed
     * @see     contact_form()
     */
    function contact_resend_email()
    {
        $this->nav_submain = $this->nav["resend_verification"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $fields = array(
            "email"    => $_SESSION["userdata"]["t_email"],
        );
        if (!$this->connect->execute_request("wa-email-validate", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->contact_unverified_list_result();
        } else {
            $this->tools->show_request_status();
        }
    }
    
    /**
     * Shows list of unverified contacts
     * 
     * @access public
     * @return void
     */
    function contact_unverified_list_result()
    {
        $this->nav_submain = $this->nav["unverified"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        
        $result = false;
        
        if (isset($_SESSION["storagedata"]["emails"]) &&
            isset($_SESSION["storagedata"]["emails"]["list"]) &&
            isset($_SESSION["storagedata"]["emails"]["last_updated"]) &&
            $_SESSION["storagedata"]["emails"]["last_updated"] + $this->config["cnt_list_caching_period"] > time() &&
            !isset($_SESSION["httpvars"]["refresh"])) {
            $result = $_SESSION["storagedata"]["emails"]["list"];
        } else {
            $result = $this->contact_unverified_list();
            if (!$result) {
                $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            } else {
                $_SESSION["storagedata"]["emails"]["last_updated"] = time();
                $_SESSION["storagedata"]["emails"]["list"] = $result;
            }
        }

        if ($result!==false) {
            
            $paging = new Paging();
            $paging->setAvailableEntriesPerPage($this->contact_list_entries_per_page);
            $paging->setPageLinksPerPage($this->contact_list_page_links_per_page);
            $total_contacts = count($result);
            $paging->initSelectedEntriesPerPage($_SESSION["userdata"]["s"], $this->contact_list_default_entry_page);
            $total_pages = ceil($total_contacts / $_SESSION["userdata"]["s"]);
            $paging->initSelectedPageNumber($_SESSION["userdata"]["p"], $this->contact_list_default_page, $total_pages);
            $this->tools->tpl->set_var("PAGING_RESULTS_PER_PAGE", $paging->buildEntriesPerPageBlock($_SESSION["userdata"]["s"], "contact_unverified"));
            $this->tools->tpl->set_var("PAGING_PAGES", $paging->buildPagingBlock($total_contacts, $_SESSION["userdata"]["s"], $_SESSION["userdata"]["p"], "contact_unverified"));
            $paging->parsePagingToolbar("paging_repository", "paging_toolbar_c5", "PAGE_TOOLBAR");
            
            $this->tools->tpl->set_block("repository", "result_table_submit_btn", "res_tbl_sub_btn");
            $this->tools->tpl->set_block("repository","result_table");
            $this->tools->tpl->set_block("repository","no_ns_result");
            $this->tools->tpl->set_block("repository","result_unverified_email_table_head");
            $this->tools->tpl->set_block("repository","result_unverified_email_table_row");           
            
            
            if ($result != $this->config["empty_result"]) {
                $this->tools->tpl->parse("FORMTABLEROWS","result_unverified_email_table_head");
                $is = $paging->calculateResultsStartIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                $ie = $paging->calculateResultsEndIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                if ($ie > $total_contacts) $ie = $total_contacts;
                for ($i=$is; $i < $ie; $i++) {
                 $this->tools->tpl->set_var(array(
                     "EMAIL" => $result[$i]['email'],
                     "DOMAIN" => $result[$i]['domain'],
                     "DATE" => $result[$i]['verification-expires'],
                     "TR_CLASS" => $i%2?"tr_even":"tr_odd"
                 ));
                 $this->tools->tpl->parse("FORMTABLEROWS","result_unverified_email_table_row",true);
                }
            } else {
                $this->tools->tpl->parse("FORMTABLEROWS", "no_ns_result");
            }
            $this->tools->tpl->parse("CONTENT", "result_table");
        }
    }
    
    /**
     * Returns an array of unverified contacts.
     *
     * @access  public
     * @return  mixed
     * @see     contact_unverified_list()
     */
    function contact_unverified_list() {
        $result = false;
        $fields = array();
        if ($this->connect->execute_request("wa-email-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $result = $this->tools->parse_response_list($_SESSION["response"]);
            if (! is_array($result)) return false;
            usort($result, array(get_class($this),"contact_unverified_list_sort"));
        }
        return $result;
    }
    
    /**
     * Returns sort order of unverified list elements
     *
     * @access  public
     * @return  mixed
     * @see     contact_unverified_list()
     */
    static function contact_unverified_list_sort($a,$b) {
        $result = 0;
        $time_a = strtotime($a["verification-expires"]);
        $time_b = strtotime($b["verification-expires"]);
        if ($time_a < $time_b) {
            $result = -1;
        } elseif ($time_a > $time_b) {
            $result  = 1;
        }
        return $result;
    }
   
    /**
     * Shows contact verified result
     *
     * @access    public
     * @return  void
     */
    function contact_verified()
    {
        $this->tools->tpl->set_block("contact_repository", "result_contact_verified_row");
        $this->tools->tpl->parse("RESULT_CONTAINER", "result_contact_verified_row");
        
        $fields = array(
            "email"   => $_SESSION["userdata"]["t_email"],
        );
        if (!$this->connect->execute_request("wa-email-query-status", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
        } else {
            $this->tools->tpl->set_var("EMAIL_STATUS", $_SESSION["response"]["response_body"]);
        }
           
        $this->contact_verified_form();
    }
    
    /**
     * Shows contact verified form
     *
     * @access    public
     * @return  void
     */
    function contact_verified_form()
    {
        $this->nav_submain = $this->nav["verified"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->parse("CONTENT","contact_verified_form");
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
        if (substr($tld,-3)== ".uk") $tld = "uk";
        if (!isset($this->config["domain"][$tld])) {
            $tld = 'default';
        } 
        foreach ($this->config["domain"][$tld]["contact"]["fields"] as $field => $params)
        {
            if ($params["required"]) {
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
        //$this->tools->tpl->set_var("HELP_INDIVIDUAL", $this->msg["_individual_help_txt"]);
        $this->tools->tpl->set_var("HELP_COMPANY_NUMBER", $this->msg["_company_number_help_txt"]);
        $this->tools->tpl->set_var("HELP_ACCOUNT_TYPE", $this->msg["_account_type_help_txt"]);
        $this->tools->tpl->parse("CONTACT_ACCOUNT_TYPE","account_type");
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
            case "contact_verified":
                if (!$this->tools->is_valid("email", $_SESSION["httpvars"]["t_email"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_EMAIL",$this->err_msg["_email"]);
                } 
            case "contact_resend_email":
                if (!$this->tools->is_valid("email", $_SESSION["httpvars"]["t_email"],true)) {
                    $is_valid = false;
                    $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_email"]);
                }
                break;
            case "contact_form":
                // this code is weak - attention!
                if (isset($_SESSION["httpvars"]["s_tld"]) && !$this->tools->is_valid("joker_tld", $_SESSION["httpvars"]["s_tld"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_TLD",$this->err_msg["_tld"]);
                }
                if (isset($_SESSION["httpvars"]["cnt_hdl"]) && !$this->tools->is_valid_contact_hdl($_SESSION["httpvars"]["cnt_hdl"])) {
                    $is_valid = false;
                    $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_contact_hdl"]);
                }
                break;

            case "contact_submission":
            case "owner_contact_submission":
            case "modify_contact_submission":
                $tld = $_SESSION["userdata"]["s_tld"];
                if (substr($tld,-3)== ".uk") $tld = "uk";
                if (!isset($this->config["domain"][$tld])) {
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
                                if (
                                    !isset($_SESSION["httpvars"]["t_contact_organization"]) ||
                                    trim($_SESSION["httpvars"]["t_contact_organization"]) == "" ||
                                    ( 
                                       isset($_SESSION["httpvars"]["t_contact_name"]) &&
                                       trim($_SESSION["httpvars"]["t_contact_name"]) != ""
                                    ) 
                                ) {
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
                                }
                            break;

                            case "fname":
                                if (!$has_utf8_chars) {
                                    $regexp_fname = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_fname = $this->err_regexp["_utf8_string"];
                                }
                                if (
                                    !isset($_SESSION["httpvars"]["t_contact_organization"]) ||
                                    trim($_SESSION["httpvars"]["t_contact_organization"]) == "" ||
                                    ( 
                                       isset($_SESSION["httpvars"]["t_contact_fname"]) &&
                                       trim($_SESSION["httpvars"]["t_contact_fname"]) != ""
                                    ) 
                                ) {
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
                                }
                            break;

                            case "lname":
                                if (!$has_utf8_chars) {
                                    $regexp_lname = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_lname = $this->err_regexp["_utf8_string"];
                                }
                                if (
                                    !isset($_SESSION["httpvars"]["t_contact_organization"]) ||
                                    trim($_SESSION["httpvars"]["t_contact_organization"]) == "" ||
                                    ( 
                                       isset($_SESSION["httpvars"]["t_contact_lname"]) &&
                                       trim($_SESSION["httpvars"]["t_contact_lname"]) != ""
                                    ) 
                                ) {
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
                                }
                            break;

                            case "organization":
                                if (!$has_utf8_chars) {
                                    $regexp_org = $this->err_regexp["_ascii_string"];
                                } else {
                                    $regexp_org = $this->err_regexp["_utf8_string"];
                                }
                                if (
                                    !isset($_SESSION["httpvars"]["t_contact_name"]) ||
                                    trim($_SESSION["httpvars"]["t_contact_name"]) == "" ||
                                    ( 
                                       isset($_SESSION["httpvars"]["t_contact_organization"]) &&
                                       trim($_SESSION["httpvars"]["t_contact_organization"]) != ""
                                    ) 
                                ) {
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
                                // Fix for .de domains, which must have fax only in some contacts
                                if ($tld=="de" && $mode == "owner_contact_submission" ) break;

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
                            case "account-type":
                                if ($mode != "owner_contact_submission" && strlen($_SESSION["httpvars"]["s_contact_account_type"]) == 0 ) break;
                                if (!$this->tools->is_valid($this->err_regexp["_account_type"], $_SESSION["httpvars"]["s_contact_account_type"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_ACCOUNT_TYPE",$this->err_msg["_invalid_chars_in_field"]);
                                }
                            break;
                            case "company-number":
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_company_number"]);
                                if ($this->tools->is_valid($this->err_regexp["_company_number_required"], $_SESSION["httpvars"]["s_contact_account_type"]) || $str_length != 0) {
                                    if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_company_number"])) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_COMPANY_NUMBER",$this->err_msg["_invalid_chars_in_field"]);
                                    } else {
                                        if (is_numeric($params["size"]) && ($str_length > $params["size"])) {
                                            $is_valid = false;
                                            $this->tools->field_err("ERROR_INVALID_COMPANY_NUMBER",$this->err_msg["_invalid_field_length"]);
                                            $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                        }
                                    }
                                }
                            break;
                            case "org-id":
                                if ($mode == "modify_contact_submission" && strlen($_SESSION["httpvars"]["t_contact_org_id"]) == 0 ) break;
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_org_id"]);
                                if (!$this->tools->is_valid($this->err_regexp["_org_id"], $_SESSION["httpvars"]["t_contact_org_id"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_ORG_ID",$this->err_msg["_invalid_chars_in_field"]);
                                } else {
                                    if ((is_numeric($params["size"]) && ($str_length > $params["size"])) || $str_length == 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_ORG_ID",$this->err_msg["_invalid_field_length"]);
                                        $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                    }
                                }
                            break;
                        }
                    } else {
                        switch (strtolower($field)) {
                            case "title":
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_title"]);
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_title"])) {
                                    if ($str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_TITLE",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif (is_numeric($params["size"]) && $str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_TITLE",$this->err_msg["_invalid_field_length"]);
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
                                    if ($str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_ADDRESS2",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif (is_numeric($params["size"]) && $str_length > $params["size"]) {
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
                                    if ($str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_ADDRESS3",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif (is_numeric($params["size"]) && $str_length > $params["size"]) {
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
                                    if ($str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_STATE",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif (is_numeric($params["size"]) && $str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_STATE",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;

                            case "extension":
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_extension"]);
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_extension"])) {
                                    if ($str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_EXTENSION",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif (is_numeric($params["size"]) && $str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_EXTENSION",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;

                            case "fax":
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_fax"]);
                                if (!$this->tools->is_valid($this->err_regexp["_overall_text"], $_SESSION["httpvars"]["t_contact_fax"])) {
                                    if ($str_length != 0) {
                                        $is_valid = false;
                                        $this->tools->field_err("ERROR_INVALID_FAX",$this->err_msg["_invalid_chars_in_opt_field"]);
                                    }
                                } elseif (is_numeric($params["size"]) && $str_length > $params["size"]) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_FAX",$this->err_msg["_invalid_field_length"]);
                                    $this->tools->tpl->set_var("ERROR_FIELD_LENGTH", $params["size"]);
                                }
                            break;
                            case "vat-id":
                                $str_length = strlen($_SESSION["httpvars"]["t_contact_vat_id"]);
                                if ($str_length != 0 && !$this->tools->is_valid($this->err_regexp["_vat_id"], $_SESSION["httpvars"]["t_contact_vat_id"])) {
                                    $is_valid = false;
                                    $this->tools->field_err("ERROR_INVALID_VAT_ID",$this->err_msg["_invalid_chars_in_field"]);
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
