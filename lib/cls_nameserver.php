<?php

/**
 * Class Nameserver contains all name server related implementations.
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class Nameserver
{
   /**
     * Contains array of regular expressions for verification
     *
     * @var     array
     * @access  private
     * @see     Nameserver()
     */
    var $err_regexp  = array();

    /**
     * Contains array of error messages used in verification
     *
     * @var     array
     * @access  private
     * @see     Nameserver()
     */
    var $err_msg  = array();

    /**
     * Represents the uppermost level of the current user position.
     * Its value is usually set in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     Nameserver()
     */
    var $nav_main  = "";

    /**
     * Represents the 2nd level of the current user position
     * Its value is set for every function.
     *
     * @var     string
     * @access  private
     * @see     Nameserver()
     */
    var $nav_submain  = "";

    /**
     * Represents the 3rd level of the current user position
     * Its value is set for every function.
     *
     * @var     string
     * @access  private
     * @see     Nameserver()
     */
    var $nav_subsubmain  = "";

    /**
     * Array that defines how many entries are shown per page.
     *
     * @var     array
     * @access  private
     * @see     Nameserver()
     */
    var $ns_list_entries_per_page = array(20, 50, 100);

    /**
     * Default entry page
     *
     * @var     integer
     * @access  private
     * @see     Nameserver()
     */
    var $ns_list_default_entry_page = 20;

    /**
     * Defines the number of paging links on every page
     *
     * @var     integer
     * @access  private
     * @see     Nameserver()
     */
    var $ns_list_page_links_per_page = 10;

    /**
     * Default page for paging
     *
     * @var     integer
     * @access  private
     * @see     Nameserver()
     */
    var $ns_list_default_page = 1;

    /**
     * Array that defines how many entries are shown per page.
     *
     * @var     array
     * @access  private
     * @see     Nameserver()
     */
    var $domain_list_entries_per_page = array(50, 100, 200);

    /**
     * Default entry page
     *
     * @var     integer
     * @access  private
     * @see     Nameserver()
     */
    var $domain_list_default_entry_page = 50;

    /**
     * Defines the number of paging links on every page
     *
     * @var     integer
     * @access  private
     * @see     Nameserver()
     */
    var $domain_list_page_links_per_page = 10;

    /**
     * Default page for paging
     *
     * @var     integer
     * @access  private
     * @see     Nameserver()
     */
    var $domain_list_default_page = 1;

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Nameserver()
     *
     * @access  private
     * @return  void
     */
    function Nameserver()
    {
        global $error_messages, $error_regexp, $jpc_config, $tools, $nav;
        $this->err_msg  = $error_messages;
        $this->err_regexp = $error_regexp;
        $this->config = $jpc_config;
        $this->tools = $tools;
        $this->nav = $nav;
        $this->connect = new Connect;
        $this->nav_main = $this->nav["ns"];
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
        switch ($mode) {

            case "create":
                $is_valid = $this->is_valid_input("create");
                if (!$is_valid) {
                    $this->create_form();
                } else {
                    $this->create();
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

            case "mass_modify_form_step1":
                $this->mass_modify_form_step1();
                break;

            case "mass_modify_form_step2":
                $is_valid = $this->is_valid_input("mass_modify_form_step1");
                if (!$is_valid) {
                    $this->mass_modify_form_step1();
                } else {
                    $this->mass_modify_form_step2();
                }
                break;
            case "mass_modify":
                $is_valid = $this->is_valid_input("mass_modify_form_step2");
                if (!$is_valid) {
                    $this->mass_modify_form_step2();
                } else {
                    $this->mass_modify();
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

            case "list_result":
                $this->list_result();
                break;

            case "view":
                $this->view($_SESSION["httpvars"]["t_ns"]);
                break;
        }
    }

    /**
     * Shows a form for name server creation
     *
     * @access    public
     * @return  void
     */
    function create_form()
    {
        $this->nav_submain = $this->nav["create_ns"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");   
        
        $this->tools->tpl->set_block("ns_repository", "info_ns_create_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_ns_create_row");
        $this->tools->tpl->set_block("ns_handle_form","ns_handle_ip","ns_hdl_ip");
        $this->tools->tpl->set_block("ns_handle_form","ns_handle_textbox","ns_hdl_textbox");
        $this->tools->tpl->set_block("ns_handle_form","ns_handle_selbox","ns_hdl_selbox");
        $this->tools->tpl->parse("ns_hdl_textbox", "ns_handle_textbox");
        $this->tools->tpl->set_var("MODE","ns_create");
        $this->tools->tpl->parse("ns_hdl_ip", "ns_handle_ip");
        $this->tools->tpl->parse("CONTENT", "ns_handle_form");

    }

    /**
     * Creates a name server. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the name server creation form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     create_form()
     */
    function create()
    {
        $this->nav_submain = $this->nav["create_ns"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $fields = array(
            "host"  => $_SESSION["userdata"]["t_ns"],
            "ip"    => $_SESSION["userdata"]["t_ip"],
                    );
        if (!$this->connect->execute_request("ns-create", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->create_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows a form for name server modification
     *
     * @access    public
     * @return  void
     */
    function modify_form()
    {
        $this->nav_submain = $this->nav["modify_ns"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("ns_repository", "info_ns_modify_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_ns_modify_row");
        $this->tools->tpl->set_block("ns_handle_form","ns_handle_ip","ns_hdl_ip");
        $this->tools->tpl->set_block("ns_handle_form","list_ns_option","ls_ns_opt");
        $this->tools->tpl->set_block("ns_handle_form","ns_handle_textbox","ns_hdl_textbox");
        $this->tools->tpl->set_block("ns_handle_form","ns_handle_selbox","ns_hdl_selbox");
             
        //cache results
        if (isset($_SESSION["storagedata"]["nameservers"]) &&
            isset($_SESSION["storagedata"]["nameservers"]["list"]) &&
            isset($_SESSION["storagedata"]["nameservers"]["pattern"]) &&            
            empty($_SESSION["storagedata"]["nameservers"]["pattern"]) &&
            isset($_SESSION["storagedata"]["nameservers"]["last_updated"]) &&
            $_SESSION["storagedata"]["nameservers"]["last_updated"] + $this->config["ns_list_caching_period"] > time()) {
            $ns_arr = $_SESSION["storagedata"]["nameservers"]["list"];
        } else {
            $_SESSION["storagedata"]["nameservers"]["pattern"] = "";
            $_SESSION["storagedata"]["nameservers"]["last_updated"] = time();
            $ns_arr = $_SESSION["storagedata"]["nameservers"]["list"] = $this->ns_list($_SESSION["storagedata"]["nameservers"]["pattern"]);
        }

        if (is_array($ns_arr)) {
            foreach($ns_arr as $value)
            {
                $this->tools->tpl->set_var("S_NS",$value["0"]);
                if (isset($_SESSION["httpvars"]["s_ns"]) && strtolower($_SESSION["httpvars"]["s_ns"]) == strtolower($value["0"])) {
                    $this->tools->tpl->set_var("S_NS_SELECTED","selected");
                } else {
                    $this->tools->tpl->set_var("S_NS_SELECTED","");
                }
                $this->tools->tpl->parse("ls_ns_opt","list_ns_option",true);
            }
            $this->tools->tpl->parse("ns_hdl_selbox", "ns_handle_selbox");
            $this->tools->tpl->set_var("MODE","ns_modify");
            $this->tools->tpl->parse("ns_hdl_ip", "ns_handle_ip");
            $this->tools->tpl->parse("CONTENT", "ns_handle_form");
        } else {
            $this->tools->tpl->set_block("repository", "no_ns_result", "no_ns_res");
            $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_submit_btn");
            $this->tools->tpl->set_block("repository","result_table");
            $this->tools->tpl->parse("FORMTABLEROWS", "no_ns_result");
            $this->tools->tpl->parse("CONTENT", "result_table");
        }
    }

    /**
     * Mass modification of name servers for a list of domains.
     * Step 1 - selection of nameservers for mass modification
     *
     * @access    public
     * @return  void
     */
    function mass_modify_form_step1()
    {
        $this->nav_submain = $this->nav["mass_modification"];
        $this->nav_subsubmain = $this->nav["provide_ns"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain."  &raquo; ".$this->nav_subsubmain);
        $this->tools->tpl->set_block("ns_repository", "info_ns_mass_modify_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_ns_mass_modify_row");       
        if (!isset($_SESSION["formdata"]["r_ns_type"])) {
            $this->tools->tpl->set_var("R_NS_TYPE_DEFAULT", "checked");
        }
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->parse("CONTENT", "ns_mass_modify_form_step1");
        unset($_SESSION["userdata"]["p"]);
        unset($_SESSION["userdata"]["s"]);
    }

    /**
     * Mass modification of name servers for a list of domains.
     * Step 2 - selection of domains for mass modification
     *
     * @access    public
     * @return  void
     */
    function mass_modify_form_step2()
    {
        unset($_SESSION["userdata"]["c_ns_mass_mod"]);
        $this->nav_submain = $this->nav["mass_modification"];
        $this->nav_subsubmain = $this->nav["provide_doms"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain."  &raquo; ".$this->nav_subsubmain);
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->set_block("ns_repository", "info_ns_mass_modify_step2_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_ns_mass_modify_step2_row");
        $this->tools->tpl->set_block("domain_repository", "result_list_table");

        if (isset($_SESSION["storagedata"]["domains"]) &&
            isset($_SESSION["storagedata"]["domains"]["list"]) &&
            isset($_SESSION["storagedata"]["domains"]["pattern"]) &&
            empty($_SESSION["storagedata"]["domains"]["pattern"]) &&
            isset($_SESSION["storagedata"]["domains"]["last_updated"]) &&
            $_SESSION["storagedata"]["domains"]["last_updated"] + $this->config["dom_list_caching_period"] > time()) {
            $result = $_SESSION["storagedata"]["domains"]["list"];
        } else {
            $_SESSION["storagedata"]["domains"]["pattern"] = "";
            $_SESSION["storagedata"]["domains"]["last_updated"] = time();
            $result = $_SESSION["storagedata"]["domains"]["list"] = $this->tools->domain_list($_SESSION["storagedata"]["domains"]["pattern"]);
        }

        $paging = new Paging();
        $paging->setAvailableEntriesPerPage($this->domain_list_entries_per_page);
        $paging->setPageLinksPerPage($this->domain_list_page_links_per_page);
        $total_domains = count($result);
        $paging->initSelectedEntriesPerPage($_SESSION["userdata"]["s"], $this->domain_list_default_entry_page);
        $total_pages = ceil($total_domains / $paging->getPageLinksPerPage());
        $paging->initSelectedPageNumber($_SESSION["userdata"]["p"], $this->domain_list_default_page, $total_pages);
        $this->tools->tpl->set_var("PAGING_RESULTS_PER_PAGE", $paging->buildEntriesPerPageBlock($_SESSION["userdata"]["s"], "ns_mass"));
        $this->tools->tpl->set_var("PAGING_PAGES", $paging->buildPagingBlock($total_domains, $_SESSION["userdata"]["s"], $_SESSION["userdata"]["p"], "ns_mass"));
        $paging->parsePagingToolbar("paging_repository", "paging_toolbar_c2", "PAGE_TOOLBAR");
        $this->tools->tpl->set_block("repository", "result_table_submit_btn", "res_tbl_submit_btn");

        if ($result) {
            $this->tools->tpl->set_block("repository", "result_table");
            if ($result != $this->config["empty_result"] && is_array($result)) {
                $this->tools->tpl->set_block("repository", "ns_list_row");
                $is = $paging->calculateResultsStartIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                $ie = $paging->calculateResultsEndIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                for ($i=$is; $i < $ie; $i++)
                {
                    if (isset($result[$i])) {
                        $this->tools->tpl->set_var(array(
                                "DOMAIN"    => $result[$i]["0"],
                                ));
                        $this->tools->tpl->parse("RESULT_LIST", "ns_list_row",true);
                    }
                }
            } else {
                $this->tools->tpl->set_block("domain_repository","no_result_row");
                $this->tools->tpl->set_var("NO_RESULT_MESSAGE",$this->msg["_no_result_message"]);
                $this->tools->tpl->parse("RESULT_LIST","no_result_row",true);
            }
        } else {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->mass_modify_form_step1();
        }
        $this->tools->tpl->parse("CONTENT", "ns_mass_modify_form_step2");
    }

    /**
     * Mass modification of name servers for a list of domains.
     * Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the name server modification form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     mass_modify_form_step1(), mass_modify_form_step2()
     */
    function mass_modify()
    {
        $this->nav_submain = $this->nav["mass_modification"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $full_success = true;
        $failed_domains = array();
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
                    if (preg_match("/^t_ns/i",$key) && !empty($_SESSION["userdata"][$key])) {
                        $str[] = $value;
                    }
                }
                $ns_str = implode(":",$str);
                break;
        }
        foreach ($_SESSION["userdata"]["c_ns_mass_mod"] as $domain) {
            $fields = array(
                "domain"    => $domain,
                "ns-list"   => $ns_str
            );
            if (!$this->connect->execute_request("domain-modify", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
                $full_success = false;
                $failed_domains[] = $domain;
            }
        }
        if (!$full_success) {
            $failed_domains_ls = implode(", ", $failed_domains);
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_part_failed"].$failed_domains_ls, false, false);
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Modification of a name server. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the name server modification form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     modify_form()
     */
    function modify()
    {
        $this->nav_submain = $this->nav["modify_ns"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $fields = array(
                    "host"  => $_SESSION["userdata"]["s_ns"],
                    "ip"    => $_SESSION["userdata"]["t_ip"],
                    );
        if (!$this->connect->execute_request("ns-modify", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->modify_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows a form for name server deletion
     *
     * @access    public
     * @return  void
     */
    function delete_form()
    {
        $this->nav_submain = $this->nav["delete_ns"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $this->tools->tpl->set_block("ns_handle_form","ns_handle_ip","ns_hdl_ip");
        $this->tools->tpl->set_block("ns_handle_form","list_ns_option","ls_ns_opt");
        $this->tools->tpl->set_block("ns_handle_form","ns_handle_textbox","ns_hdl_textbox");
        $this->tools->tpl->set_block("ns_handle_form","ns_handle_selbox","ns_hdl_selbox");
        
        //cache results
        if (isset($_SESSION["storagedata"]["nameservers"]) &&
            isset($_SESSION["storagedata"]["nameservers"]["list"]) &&
            isset($_SESSION["storagedata"]["nameservers"]["pattern"]) &&            
            empty($_SESSION["storagedata"]["nameservers"]["pattern"]) &&
            isset($_SESSION["storagedata"]["nameservers"]["last_updated"]) &&
            $_SESSION["storagedata"]["nameservers"]["last_updated"] + $this->config["ns_list_caching_period"] > time()) {
            $ns_arr = $_SESSION["storagedata"]["nameservers"]["list"];
        } else {
            $_SESSION["storagedata"]["nameservers"]["pattern"] = "";
            $_SESSION["storagedata"]["nameservers"]["last_updated"] = time();
            $ns_arr = $_SESSION["storagedata"]["nameservers"]["list"] = $this->ns_list($_SESSION["storagedata"]["nameservers"]["pattern"]);
        }
                
        if (is_array($ns_arr)) {
            foreach($ns_arr as $value)
            {
                $this->tools->tpl->set_var("S_NS",$value["0"]);
                $this->tools->tpl->parse("ls_ns_opt","list_ns_option",true);
            }
            $this->tools->tpl->parse("ns_hdl_selbox", "ns_handle_selbox");
            $this->tools->tpl->set_var("MODE","ns_delete");
            $this->tools->tpl->parse("CONTENT", "ns_handle_form");
        } else {
            $this->tools->tpl->set_block("repository", "no_ns_result", "no_ns_res");
            $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_submit_btn");
            $this->tools->tpl->set_block("repository","result_table");
            $this->tools->tpl->parse("FORMTABLEROWS", "no_ns_result");
            $this->tools->tpl->parse("CONTENT", "result_table");
        }
    }

    /**
     * Deletes a name server entry. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the name server deletion form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     delete_form()
     */
    function delete()
    {
        $this->nav_submain = $this->nav["delete_ns"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        $fields = array(
            "host"  => $_SESSION["userdata"]["s_ns"],
                    );
        if (!$this->connect->execute_request("ns-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->delete_form();
        } else {
            $this->tools->show_request_status();
        }
    }

    /**
     * Shows a form allowing you to customize the returned list of name servers.
     *
     * @access    public
     * @return  void
     */
    function list_form()
    {
        $this->nav_submain = $this->nav["ns_list"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("ns_repository", "info_ns_list_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_ns_list_row");
        $this->tools->tpl->set_var("MODE","ns_list_result");
        $this->tools->tpl->parse("CONTENT","dom_ns_list_form");
        unset($_SESSION["userdata"]["p"]);
        unset($_SESSION["userdata"]["s"]);
    }


    /**
     * Shows a list of name servers
     *
     * on success - list of name servers
     * on failure - back to the name server list form
     *
     * @access  private
     * @return  void
     * @see     list_form()
     */
    function list_result()
    {
        $this->nav_submain = $this->nav["ns_list"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        if (isset($_SESSION["storagedata"]["nameservers"]) &&
            isset($_SESSION["storagedata"]["nameservers"]["list"]) &&
            isset($_SESSION["storagedata"]["nameservers"]["pattern"]) &&
            $_SESSION["storagedata"]["nameservers"]["pattern"] == $_SESSION["userdata"]["t_pattern"] &&
            isset($_SESSION["storagedata"]["nameservers"]["last_updated"]) &&
            $_SESSION["storagedata"]["nameservers"]["last_updated"] + $this->config["ns_list_caching_period"] > time()) {
            $result = $_SESSION["storagedata"]["nameservers"]["list"];
        } else {
            $_SESSION["storagedata"]["nameservers"]["pattern"] = $_SESSION["userdata"]["t_pattern"];
            $_SESSION["storagedata"]["nameservers"]["last_updated"] = time();
            $result = $_SESSION["storagedata"]["nameservers"]["list"] = $this->ns_list($_SESSION["userdata"]["t_pattern"]);
        }

        $paging = new Paging();
        $paging->setAvailableEntriesPerPage($this->ns_list_entries_per_page);
        $paging->setPageLinksPerPage($this->ns_list_page_links_per_page);
        $total_domains = count($result);
        $paging->initSelectedEntriesPerPage($_SESSION["userdata"]["s"], $this->ns_list_default_entry_page);
        $total_pages = ceil($total_domains / $paging->getPageLinksPerPage());
        $paging->initSelectedPageNumber($_SESSION["userdata"]["p"], $this->ns_list_default_page, $total_pages);
        $this->tools->tpl->set_var("PAGING_RESULTS_PER_PAGE", $paging->buildEntriesPerPageBlock($_SESSION["userdata"]["s"], "nameserver"));
        $this->tools->tpl->set_var("PAGING_PAGES", $paging->buildPagingBlock($total_domains, $_SESSION["userdata"]["s"], $_SESSION["userdata"]["p"], "nameserver"));
        $paging->parsePagingToolbar("paging_repository", "paging_toolbar_c2", "PAGE_TOOLBAR");
        $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_submit_btn");

        if ($result) {
            $this->tools->tpl->set_block("repository","result_table");
            if ($result != $this->config["empty_result"] && is_array($result)) {
                $this->tools->tpl->set_block("repository","result_ns_table_row");
                $is = $paging->calculateResultsStartIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                $ie = $paging->calculateResultsEndIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                for ($i=$is; $i < $ie; $i++)
                {
                    if (isset($result[$i])) {
                        $this->tools->tpl->set_var(array(
                                "NS"    => $result[$i]["0"]
                                ));
                        $this->tools->tpl->parse("FORMTABLEROWS", "result_ns_table_row", true);
                    }
                }
                $this->tools->tpl->parse("CONTENT", "result_table");
            } else {
                $this->tools->tpl->set_block("repository", "no_ns_result", "no_ns_res");
                $this->tools->tpl->parse("FORMTABLEROWS", "no_ns_result");
                $this->tools->tpl->parse("CONTENT", "result_table");
            }
        } else {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->list_form();
        }
    }

    /**
     * Returns a list of name servers
     *
     * @param   array   $pattern seed for the name server list
     * @access  public
     * @return  mixed
     * @see     list_result()
     */
    function ns_list($pattern)
    {
        $fields = array(
            "pattern"   => $pattern
            );
        if ($this->connect->execute_request("query-ns-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            return ($this->tools->parse_text($_SESSION["response"]["response_body"]));
        } else {
            return false;
        }
    }

    /**
     * Returns information about a name server.
     *
     * on success - visualizes name server data
     * on failure - error message
     *
     * @access  private
     * @return  void
     * @see     view_form()
     */
    function view($host)
    {
        $this->nav_submain = $this->nav["view_info"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("repository","result_table_row","result_table_r");
        $this->tools->tpl->set_block("repository","std_result_table","std_result_tbl");

        $result = $this->tools->query_object("host", $host);
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
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
        }
    }

    /**
     * Redirects the function calls. Used for input verification.
     *
     * @param   $mode
     * @access  public
     * @return  void
     */
    function is_valid_input($mode)
    {
        $this->tools->tpl->set_block("repository","general_error_box");
        $this->tools->tpl->set_block("repository","field_error_box");
        $is_valid = true;
        switch ($mode) {

            case "create":
                if (!$this->tools->is_valid("host", $_SESSION["httpvars"]["t_ns"], true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_NS", $this->err_msg["_ns"]);
                }
                if (!$this->tools->is_valid($this->err_regexp["_ipv4"], $_SESSION["httpvars"]["t_ip"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_IP", $this->err_msg["_ipv4"]);
                }
                break;

            case "modify":
                if (!$this->tools->is_valid("host", $_SESSION["httpvars"]["s_ns"],true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_NS", $this->err_msg["_ns"]);
                }
                if (!$this->tools->is_valid($this->err_regexp["_ipv4"], $_SESSION["httpvars"]["t_ip"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_IP", $this->err_msg["_ipv4"]);
                }
                break;

            case "mass_modify_form_step1":
                switch (strtolower($_SESSION["userdata"]["r_ns_type"]))
                {
                    case "default":
                        //ok
                        break;
                    case "own":
                        $ns_count = 0;
                        foreach ($_SESSION["userdata"] as $key => $value)
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

            case "mass_modify_form_step2":
                if (!(is_array($_SESSION["httpvars"]["c_ns_mass_mod"]) && !empty($_SESSION["httpvars"]["c_ns_mass_mod"]))) {
                    $this->tools->field_err("ERROR_INVALID_DOMAIN_SELECT",$this->err_msg["_select_domain"]);
                    $is_valid = false;
                }
                break;

            case "delete":
                if (!$this->tools->is_valid("host", $_SESSION["httpvars"]["s_ns"], true)) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_NS", $this->err_msg["_ns"]);
                }
                break;
        }
        return $is_valid;
    }
}

?>
