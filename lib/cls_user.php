<?php

/**
 * Class User is a container for methods which do not fit in the outline
 * of the other classes.
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class User
{
    /**
     * Represents the uppermost level of the current user position.
     * Its value is usually set in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     User()
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
     * @see     User()
     */
    var $config  = array();

    /**
     * Array containing the possible number of rows per page
     * in result list. Its values are overridden in the class constructor.
     *
     * @var     array
     * @access  private
     * @see     User()
     */
    var $result_list_rows = array();

    /**
     * Default number of rows per page in result list.
     * Its value is overridden in the class constructor.
     *
     * @var     int
     * @access  private
     * @see     User()
     */
    var $result_list_def_rows = 15;

    /**
     * Default entry page
     *
     * @var     integer
     * @access  private
     * @see     User()
     */
    var $result_list_default_entry_page = 20;

    /**
     * Defines the number of paging links on every page
     *
     * @var     integer
     * @access  private
     * @see     User()
     */
    var $result_list_page_links_per_page = 15;

    /**
     * Default page for paging
     *
     * @var     integer
     * @access  private
     * @see     User()
     */
    var $result_list_default_page = 1;    

    /**
     * Default filename for the exported result list
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     User()
     */
    var $result_list_filename = "results";

    /**
     * Defines the number of requests shown on home page
     *
     * @var     integer
     * @access  private
     * @see     User()
     */
    var $result_list_home_page = 5;
    
    /**
     * Default temp directory
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     User()
     */
    var $tmp_dir = "/tmp";

    /**
     * permissions for temp directory
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     User()
     */
    var $tmp_perm = "/tmp";
    
    //var $AUTH_ID = "";
    //var $RESPONSE = array();

    /**
     * Class constructor. No optional parameters.
     *
     * usage: User()
     *
     * @access  private
     * @return  void
     */
    function User()
    {
        global $error_messages, $error_regexp, $jpc_config, $tools, $requests, $request_status, $nav, $messages;
        $this->err_msg  = $error_messages;
        $this->err_regexp = $error_regexp;         
        $this->config = &$jpc_config;
        $this->tools = $tools;
        $this->requests = $requests;
        $this->request_status = $request_status;
        $this->messages = $messages;
        $this->nav = $nav;
        $this->log = new Log;
        $this->connect = new Connect;
        $this->nav_main = $this->nav["other"];
        $this->result_list_rows = $jpc_config["result_list_rows"];
        $this->result_list_def_rows = $jpc_config["result_list_def_rows"];
        $this->temp_dir = $jpc_config["temp_dir"];
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
        switch ($mode) {
            case "login":
                $is_valid = $this->is_valid_input("login");
                if (!$is_valid) {
                    $this->login_form();
                } else {
                    $this->login();
                }
            break;
            
            case "property":
                $is_valid = $this->is_valid_input("property");
                if (!$is_valid) {
                    $this->property_form();
                } else {
                    $this->property();
                }
                break;

        }
    }

    /**
     * Shows the login form.
     *
     * @access    public
     * @return  void
     */
    function login_form()
    {
        $this->tools->tpl->set_block("repository", "INTRO_TEXT_SECTION", "INTRO_TEXT_SEC");
        $this->tools->tpl->parse("INTRO_TEXT", "INTRO_TEXT_SECTION");
        $this->tools->tpl->set_var("HTTPS_LINK","https://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);
        $this->tools->tpl->parse("BODY", "login_form");
    }

    /**
     * Login in the web interface.
     *
     * on success - go to main screen
     * on failure - back to the login form
     *
     * @access  private
     * @return  void
     * @see     login_form()
     */
    function login()
    {
        $fields = array(
                "username"  => $_SESSION["userdata"]["t_username"],
                "password"  => $_SESSION["userdata"]["t_password"]
                );
        if ($this->connect->execute_request("login", $fields, $_SESSION["response"], $this->config["no_content"])
            && $this->connect->set_auth_id($_SESSION["auth-sid"],$_SESSION["response"])) {
            $_SESSION["username"] = $_SESSION["userdata"]["t_username"];
            $_SESSION["password"] = $_SESSION["userdata"]["t_password"];
            $_SESSION["uid"]      = $_SESSION["response"]["response_header"]["uid"];
            $result = $this->tools->parse_text($_SESSION["response"]["response_body"],true);
            if ($result != $this->config["empty_result"] && is_array($result)) {
	        foreach($result as $value)
            	{
                	$list[] = $value["0"];
            	}
            	sort($list, SORT_STRING);
            	$_SESSION["auto_config"]["avail_tlds"] = $list;
	    } else {
	        session_destroy();
              	die("System error: No available tlds to handle.");
	    }

            //list of available requests
            $_SESSION["auto_config"]["dmapi_avail_requests"] = $this->tools->get_request_list();
            $_SESSION["auto_config"]["dmapi_ver"] = $this->tools->get_dmapi_version();
            $this->tools->tpl->set_var("DMAPI_VER", $_SESSION["jpc_config"]["dmapi_ver"]);

            // retrieve user properties and features
            if (! $this->get_property("*","*") ) {
              // print "something is wrong...<br>";
            }

            $this->home_page();
        } else {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_auth_failed"]);
            $this->login_form();
        }
    }

    /**
     * Logs out the user. Terminates the session and goes to the login screen.
     *
     * @access    public
     * @return  void
     */
    function logout()
    {
        session_destroy();        
        $this->tools->go_to();
    }

    /**
     * Shows a user property form.
     *
     * @access    public
     * @return  void
     */
    function property_form()
    {
        $this->nav_submain = $this->nav["user_props"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("domain_repository", "info_ar_row");
        $this->tools->tpl->set_block("domain_repository", "info_domain_list_pattern_row");
        //$this->tools->tpl->parse("INFO_CONTAINER", "info_domain_list_pattern_row");
        $this->tools->tpl->parse("INFO_GENERAL", "info_ar_row");
        if (isset($_SESSION["profile"]["user"]["property"]["autorenew"]) && $_SESSION["profile"]["user"]["property"]["autorenew"] == 1) {
            $this->tools->tpl->set_var("R_AUTORENEW_ON","checked");
        } else {
            $this->tools->tpl->set_var("R_AUTORENEW_OFF","checked");
        }
        $this->tools->tpl->parse("CONTENT", "user_property_form");
    }

    /**
     * Set User Properties. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * on success - success status message
     * on failure - back to the domain owner change form
     *
     * @access  private
     * @return  void
     * @see     User::result_list()
     * @see     property_form()
     */
    function property()
    {
        $this->nav_submain = $this->nav["user_props"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $val = (strtolower($_SESSION["userdata"]["r_up_autorenew"])=="ar_on") ? 1 : 0;
        $fields = array(
                    "pname"	 => "autorenew",
                    "pvalue"	 => $val,
                    "uid"        => $_SESSION["uid"]
                    );
        $status = $this->connect->execute_request("user-set-property", $fields, $_SESSION["response"], $_SESSION["auth-sid"]);
        if (!$status) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->property_form();
        } else {
            $_SESSION["profile"]["user"]["property"]["autorenew"] = $val;
            $this->tools->show_request_status();
        }
    }
    
    /**
     * Get User Properties. Asynchronous request - the final status of this request
     * should be checked with result_list()
     *
     * @access  private
     * @return  void
     */
    function get_property($pname = 'autorenew', $ptype = 'property')
    {
        $fields = array(
                    "pname"	 => $pname,
                    "ptype"	 => $ptype,
                    "uid"        => $_SESSION["uid"]
                    );
        $result = $this->connect->execute_request("user-get-property", $fields, $_SESSION["response"], $_SESSION["auth-sid"]);
        if (!$result) {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $ok = false;
        } else {
            $response = $this->tools->parse_text($_SESSION["response"]["response_body"]);
            foreach($response as $val) {
              $tmp_arr = explode(".", $val[0]);
              $_SESSION["profile"][$tmp_arr[0]][$tmp_arr[1]][rtrim($tmp_arr[2],":")] = $val[1];
            }
            $ok = true;
        }
        return($ok);
    }
    
    /**
     * Returns summary of all user requests to the DMAPI server and their status.
     * Take in mind that the request data is extracted once and then saved in the session.
     * Every consequent access to this data is through the session array.
     *
     * @access  public
     * @return  void
     */
    function result_list()
    {
        $this->nav_submain = $this->nav["result_list"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main . "  &raquo; " . $this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
               
        $fields = array("showdeleted"=>1, "showpending"=>1);
        if (!isset($_SESSION["userdata"]["request_results"]) || isset($_SESSION["httpvars"]["refresh"])) {
            if (!$this->connect->execute_request("result-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
                $this->tools->tpl->set_block("repository","general_error_box");
                $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            } else {
                $_SESSION["userdata"]["request_results"] = array_reverse($this->tools->parse_text($_SESSION["response"]["response_body"]));
            }
        }
        $paging = new Paging();
        $paging->setAvailableEntriesPerPage($this->result_list_rows);
        $paging->setPageLinksPerPage($this->result_list_def_rows);        
        $requests = count($_SESSION["userdata"]["request_results"]);
        $paging->initSelectedEntriesPerPage($_SESSION["userdata"]["s"], $this->result_list_default_entry_page);
        $total_pages = ceil($requests / $paging->getPageLinksPerPage());
        $paging->initSelectedPageNumber($_SESSION["userdata"]["p"], $this->result_list_default_page, $total_pages);
        $this->tools->tpl->set_var("PAGING_RESULTS_PER_PAGE", $paging->buildEntriesPerPageBlock($_SESSION["userdata"]["s"], "result"));
        $this->tools->tpl->set_var("PAGING_PAGES", $paging->buildPagingBlock($requests, $_SESSION["userdata"]["s"], $_SESSION["userdata"]["p"], "result"));
        $paging->parsePagingToolbar("paging_repository", "paging_toolbar_c4", "PAGE_TOOLBAR");
        
        if (isset($_SESSION["userdata"]["request_results"]) && is_array($_SESSION["userdata"]["request_results"])) {                       
            $this->tools->tpl->set_block("repository","dom_result_row","dom_res_r");
            $this->tools->tpl->set_block("repository","cnt_result_row","cnt_res_r");
            $this->tools->tpl->set_block("repository","ns_result_row","ns_res_r");
            $this->tools->tpl->set_block("result_list","result_row","res_row");
            $is = $paging->calculateResultsStartIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
            $ie = $paging->calculateResultsEndIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
            for ($i=$is; $i < $ie; $i++)
            {
                if (isset($_SESSION["userdata"]["request_results"][$i])) {
                    $val = $_SESSION["userdata"]["request_results"][$i];
                    $this->tools->tpl->set_var(
                        array(
                            "TIMESTAMP" => $this->tools->prepare_date($val["0"]),
                            "SVTRID"  => $val["1"],
                            "PROC_ID"   => $val["2"],
                            "REQUEST_TYPE"  => (is_array($this->requests[$val["3"]]) ? $this->requests[$val["3"]]["text"] : $this->requests["unknown"]["text"]),
                            "REQUEST_OBJECT"=> $val["4"],
                            "REQUEST_OBJECT_ENC"=> urlencode($val["4"]),
                            "STATUS"    => (is_array($this->request_status[$val["5"]]) ? $this->request_status[$val["5"]]["text"] : $this->request_status["unknown"]["text"]),
                            "CLTRID"    => $val["6"],
                        ));                    
                    if ($this->tools->is_valid_contact_hdl($val["4"])) {                        
                        $this->tools->tpl->parse("REQUEST_OBJECT_LINK", "cnt_result_row");                    
                    } elseif ($this->tools->is_valid("joker_domain", $val["4"], true)) {
                        $this->tools->tpl->set_var("REQUEST_OBJECT", $this->tools->format_fqdn($val["4"], "unicode", "domain", true));
                        $this->tools->tpl->parse("REQUEST_OBJECT_LINK", "dom_result_row");
                    } elseif ($this->tools->is_valid("host", $val["4"], true)) {
                        $this->tools->tpl->set_var("REQUEST_OBJECT", $this->tools->format_fqdn($val["4"], "unicode", "host", true));                        
                        $this->tools->tpl->parse("REQUEST_OBJECT_LINK", "ns_result_row");
                    } else {
                        $this->tools->tpl->set_var("REQUEST_OBJECT_LINK", $val["4"]);
                    }
                    $this->tools->tpl->parse("res_row", "result_row", true);
                }
            }
            $this->tools->tpl->parse("CONTENT", "result_list");
        }
    }

    /**
     * Deletes all available user requests from the session array
     *
     * @access  public
     * @return  void
     * @see     result_list()
     * @see     result_delete()
     */
    function empty_result_list()
    {
        if (isset($_SESSION["userdata"]["request_results"]) && is_array($_SESSION["userdata"]["request_results"])) {
            $req_status = true;
            foreach ($_SESSION["userdata"]["request_results"] as $val)
            {
                if (!$this->result_delete($val["1"])) {
                    $req_status = false;
                }
            }
            if (!$req_status) {
                $this->tools->show_request_status($this->messages["_request_partial_success"],false,false);
            } else {
                $this->tools->show_request_status($this->messages["_request_successful"],false,false);
            }
        }
        //hack for cleaning the result array in the session
        $_SESSION["httpvars"]["refresh"] = "";
        $this->result_list();
    }

    /**
     * Deletes a record from the result list based on its SvTrId
     *
     * @param   string  $svtrid server tracking id
     * @access  public
     * @return  boolean
     * @see     empty_result_list()
     */
    function result_delete($svtrid)
    {
        $fields = array(
            "SvTrId" => $svtrid,
                    );
        return $this->connect->execute_request("result-delete", $fields, $_SESSION["response"], $_SESSION["auth-sid"]);
    }

    /**
     * Exports the result list into file with user specified filetype
     *
     * @param   string  $filetype   e.g. csv, xsl etc.
     * @access  public
     * @return  void
     */
    function result_export($filetype)
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
                    $text[] = $csv->arrayToCsvString(array("TIMESTAMP","SVTRID","REQUEST TYPE","REQUEST OBJECT","STATUS","PROC ID","CLTRID"));
                    foreach ($_SESSION["userdata"]["request_results"] as $val)
                    {
                        $row_arr = array(
                            $this->tools->prepare_date($val["0"]),
                            $val["1"],
                            (is_array($this->requests[$val["3"]]) ? $this->requests[$val["3"]]["text"] : $this->requests["unknown"]["text"]),
                            $val["4"],
                            (is_array($this->request_status[$val["5"]]) ? $this->request_status[$val["5"]]["text"] : $this->request_status["unknown"]["text"]),
                            $val["6"],
                            $val["2"]
                        );
                        $text[] = $csv->arrayToCsvString($row_arr);
                    }
                    $text = implode("\n", $text);

                    $path_to_file = $path.$sub_dir.$separator.$this->result_list_filename . ".csv";
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
                    //$this->log->req_status("e", "function result_export($filetype): The file $path_to_file is not writable");
                    header("Pragma: ");
                    header("Cache-Control: ");
                    header('Content-type: application/octet-stream');
                    header("Content-Length: " . strlen($text));
                    header('Content-Disposition: attachment; filename="'.trim($this->result_list_filename.".csv").'"');
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
                $this->result_list();
                break;
        }
    }

    /**
     * Prints a result summary
     *
     * @access  public
     * @return  void
     */
    function result_retrieve($id, $is_proc_id = true)
    {
        $this->nav_submain = $this->nav["result_retrieve"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");

        if ($is_proc_id) {
            $fields = array(
                    "Proc-ID"   => $id
                    );
        } else {
            $fields = array(
                    "SvTrId"   => $id
                    );
        }
        if ($this->connect->execute_request("result-retrieve", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $result = $this->tools->parse_text($_SESSION["response"]["response_body"],true);
        }
        if ($result != $this->config["empty_result"] && is_array($result)) {
            $this->tools->tpl->set_block("repository","result_table_submit_btn","res_tbl_submit_btn");
            $this->tools->tpl->set_block("repository","result_monospace_table_row");
            $this->tools->tpl->set_block("repository","result_table");
            foreach($result as $value)
            {
                $this->tools->tpl->set_var(array(
                    "FIELD1"    => htmlspecialchars($value["0"]." ".$value["1"])
                    ));
                $this->tools->tpl->parse("FORMTABLEROWS", "result_monospace_table_row",true);
            }            
            $this->tools->tpl->parse("CONTENT", "result_table");
        } else {
            $this->tools->tpl->set_block("repository","general_error_box");
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->empty_content();
        }
    }

    /**
     * Shows the user profile
     *
     * @access  public
     * @return  void
     */
    function query_profile()
    {
        $this->nav_submain = $this->nav["query_profile"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");

        $fields = "";
        if ($this->connect->execute_request("query-profile", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $result = $this->tools->parse_text($_SESSION["response"]["response_body"],true);
        }
        if ($result != $this->config["empty_result"] && is_array($result)) {
            $this->tools->tpl->set_block("repository", "result_table_submit_btn", "res_tbl_submit_btn");
            $this->tools->tpl->set_block("repository", "result_table_row");
            $this->tools->tpl->set_block("repository", "result_table");
            foreach ($result as $value)
            {
                $this->tools->tpl->set_var(array(
                    "FIELD1"    => $value["0"],
                    "FIELD2"    => $value["1"],
                    ));
                if ("robot_email:" == $value["0"] || "admin_email:" == $value["0"]) {                    
                    $this->tools->tpl->set_var("FIELD2", $this->tools->format_fqdn($value["1"], "unicode", "email", true));
                }
                $this->tools->tpl->parse("FORMTABLEROWS", "result_table_row",true);
            }
            $this->tools->tpl->parse("CONTENT", "result_table");
        } else {
            $this->tools->tpl->set_block("repository","general_error_box");
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
            $this->empty_content();
        }
    }
    
    /**
     * Shows tips for using the interface
     *
     * @access  public
     * @return  void
     */
    function tips()
    {
        $this->nav_submain = $this->nav["tips"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->parse("CONTENT", "tips");
    }

    /**
     * Home page
     *
     * @access  public
     * @return  void
     */
    function home_page()
    {
        $this->nav_main = $this->nav["home"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main);
        $this->tools->tpl->parse("NAV", "navigation");

        $fields = "";
        if ($this->connect->execute_request("query-profile", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {
            $result = $this->tools->parse_text($_SESSION["response"]["response_body"],true);
        }        
        if ($result != $this->config["empty_result"] && is_array($result)) {                        
            foreach($result as $value)
            {
                $value["0"] = rtrim($value["0"], ":");
                if ("fname" == $value["0"]) {
                    $this->tools->tpl->set_var("FNAME", $value["1"]);
                }
                if ("lname" == $value["0"]) {
                    $this->tools->tpl->set_var("LNAME", $value["1"]);
                }
                if ("organization" == $value["0"]) {
                    $this->tools->tpl->set_var("ORGANIZATION", $value["1"]);
                }
                if ("customer-id" == $value["0"]) {
                    $this->tools->tpl->set_var("ID", $value["1"]);
                }
                if ("balance" == $value["0"]) {
                    $this->tools->tpl->set_var("ACCOUNT_BALANCE", $value["1"]);
                }
                if ("last-payment" == $value["0"]) {
                    $this->tools->tpl->set_var("LAST_PAYMENT", $value["1"]);
                }                
            }
        }
                
        // show property status
        if (isset($_SESSION["profile"])) {
          $this->tools->tpl->set_block("home_page", "CUT_NO_PROP_RESULTS", "CUT_NO_PTP_RES");
          $this->tools->tpl->set_block("repository","all_props_row","all_props_r");
          foreach($_SESSION["profile"]["user"] as $key => $val) {
            foreach($val as $key2 => $val2) {
              $this->tools->tpl->set_var("PROP_TYPE", $key);
              $this->tools->tpl->set_var("PROP_NAME", $key2);
              $this->tools->tpl->set_var("PROP_VAL", $val2);
              $this->tools->tpl->parse("LIST_OF_PROPERTIES", "all_props_row",true);
            }
          }
        }
        
        // check last results
        $fields = "";
        if (!isset($_SESSION["userdata"]["request_results"]) || isset($_SESSION["httpvars"]["refresh"])) {
            if ($this->connect->execute_request("result-list", $fields, $_SESSION["response"], $_SESSION["auth-sid"])) {                
                $_SESSION["userdata"]["request_results"] = array_reverse($this->tools->parse_text($_SESSION["response"]["response_body"]));
            }
        }
        $requests = count($_SESSION["userdata"]["request_results"]);
        if (isset($_SESSION["userdata"]["request_results"]) && is_array($_SESSION["userdata"]["request_results"])) {                       
            $this->tools->tpl->set_block("repository","dom_result_row","dom_res_r");
            $this->tools->tpl->set_block("repository","cnt_result_row","cnt_res_r");
            $this->tools->tpl->set_block("repository","ns_result_row","ns_res_r");
            $this->tools->tpl->set_block("result_list","result_row","res_row");
            for ($i=0; $i < $requests; $i++)
            {
                $val = $_SESSION["userdata"]["request_results"][$i];
                $this->tools->tpl->set_var(
                    array(
                        "TIMESTAMP" => $this->tools->prepare_date($val["0"]),
                        "SVTRID"    => $val["1"],
                        "PROC_ID"   => $val["2"],
                        "REQUEST_TYPE"  => (is_array($this->requests[$val["3"]]) ? $this->requests[$val["3"]]["text"] : $this->requests["unknown"]["text"]),
                        "REQUEST_OBJECT"=> $val["4"],
                        "REQUEST_OBJECT_ENC"=> urlencode($val["4"]),
                        "STATUS"    => (is_array($this->request_status[$val["5"]]) ? $this->request_status[$val["5"]]["text"] : $this->request_status["unknown"]["text"]),
                        "CLTRID"    => $val["6"],
                    ));                    
                if ($this->tools->is_valid_contact_hdl($val["4"])) {                    
                    $this->tools->tpl->parse("REQUEST_OBJECT_LINK", "cnt_result_row");                    
                } elseif ($this->tools->is_valid("joker_domain", $val["4"], true)) {
                    $this->tools->tpl->set_var("REQUEST_OBJECT", $this->tools->format_fqdn($val["4"], "unicode", "domain", true));
                    $this->tools->tpl->parse("REQUEST_OBJECT_LINK", "dom_result_row");
                } elseif ($this->tools->is_valid("host", $val["4"], true)) {
                    $this->tools->tpl->set_var("REQUEST_OBJECT", $this->tools->format_fqdn($val["4"], "unicode", "host", true));                    
                    $this->tools->tpl->parse("REQUEST_OBJECT_LINK", "ns_result_row");
                } else {
                    $this->tools->tpl->set_var("REQUEST_OBJECT_LINK", "");
                }
                $this->tools->tpl->parse("LIST_OF_REQUEST_RESULTS", "result_row", true);
                //print only 5 entries
                if ($this->result_list_home_page == $i+1) break;
            }            
        }
        if ($requests) {
            $this->tools->tpl->set_block("home_page", "CUT_NO_RESULTS", "CUT_NO_RES");
        }        
        $support_url = $this->config["joker_url"]."index.joker?mode=support";
        $this->tools->tpl->set_var("SUPPORT_URL", $support_url);
        $this->tools->tpl->set_block("repository", "INTRO_TEXT_SECTION", "INTRO_TEXT_SEC");
        $this->tools->tpl->parse("INTRO_TEXT", "INTRO_TEXT_SECTION");
        $this->tools->tpl->parse("CONTENT", "home_page");
    }

    /**
     * Parses empty content
     *
     * @access  public
     * @return  void
     */
    function empty_content()
    {
        $this->tools->tpl->set_var("CONTENT", "");
    }

    /**
     * Main verification method. Verification rules for every mode
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
        $this->tools->tpl->set_block("repository","general_error_box");
        $this->tools->tpl->set_block("repository","field_error_box");
        $is_valid = true;
        switch ($mode) {
            case "login":
                if (!$this->tools->is_valid($this->err_regexp["_username"],$_SESSION["httpvars"]["t_username"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_USERNAME",$this->err_msg["_username"]);
                }
                if (!$this->tools->is_valid($this->err_regexp["_password"],$_SESSION["httpvars"]["t_password"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_PASSWORD",$this->err_msg["_password"]);
                }
                break;
            case "property":
            break;
            
        }
        return $is_valid;
    }
}

?>
