<?php

/**
 * Zone management related class. Handles visualization and request handling
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class Zone
{
    /**
     * Represents the uppermost level of the current user position.
     * Its value is usually set in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     Zone()
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
     * @see     Zone()
     */
    var $config  = array();

    /**
     * Array that defines how many entries are shown per page.
     *
     * @var     array
     * @access  private
     * @see     Zone()
     */
    var $zone_list_entries_per_page = array(20, 50, 100);

    /**
     * Default entry page
     *
     * @var     integer
     * @access  private
     * @see     Zone()
     */
    var $zone_list_default_entry_page = 20;

    /**
     * Defines the number of paging links on every page
     *
     * @var     integer
     * @access  private
     * @see     Zone()
     */
    var $zone_list_page_links_per_page = 10;

    /**
     * Default page for paging
     *
     * @var     integer
     * @access  private
     * @see     Zone()
     */
    var $zone_list_default_page = 1;

    /**
     * Default filename for the exported result list
     * Its value is overridden in the class constructor.
     *
     * @var     string
     * @access  private
     * @see     Zone()
     */
    var $zone_list_filename = "zone_list";

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Zone()
     *
     * @access  private
     * @return  void
     */
    function Zone()
    {
        global $error_messages, $error_regexp, $jpc_config, $tools, $messages, $nav;
        $this->config  = $jpc_config;
        $this->err_msg = $error_messages;
        $this->err_regexp = $error_regexp;
        $this->tools   = $tools;
        $this->msg     = $messages;
        $this->nav     = $nav;
        $this->connect = new Connect;
        $this->nav_main= $this->nav["ns"];
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
            case "list_result":
                $this->list_result();
                break;                
            case "view":
                $this->view();
                break;         
        }
    }

    /**
     * Shows a form allowing you to customize the returned list of zones.
     *
     * @access  public
     * @return  void
     */
    function list_form()
    {
        $this->nav_submain = $this->nav["zone_list"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV","navigation");
        $this->tools->tpl->set_block("zone_repository", "info_zone_list_pattern_row");
        $this->tools->tpl->parse("INFO_CONTAINER", "info_zone_list_pattern_row");
        $this->tools->tpl->set_var("MODE", "zone_list");
        $this->tools->tpl->parse("CONTENT", "domain_list_form");
        unset($_SESSION["userdata"]["p"]);
        unset($_SESSION["userdata"]["s"]);
    }

    /**
     * Returns a zone list.
     *
     * on success - returns a zone list
     * on failure - back to the zone list form
     *
     * @access  private
     * @return  void
     */
    function list_result()
    {
        $this->nav_submain = $this->nav["zone_list"];
        $this->tools->tpl->set_var("NAV_LINKS",$this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->set_block("zone_repository", "result_list_table");
        
        $result = "";
        if (isset($_SESSION["storagedata"]["zones"]) &&
            isset($_SESSION["storagedata"]["zones"]["list"]) &&
            isset($_SESSION["storagedata"]["zones"]["pattern"]) &&
            $_SESSION["storagedata"]["zones"]["pattern"] == $_SESSION["userdata"]["t_pattern"] &&
            isset($_SESSION["storagedata"]["zones"]["last_updated"]) &&
            $_SESSION["storagedata"]["zones"]["last_updated"] + $this->config["zone_list_caching_period"] > time()) {
            $result = $_SESSION["storagedata"]["zones"]["list"];
        } else {
            $_SESSION["storagedata"]["zones"]["pattern"] = $_SESSION["userdata"]["t_pattern"];
            $_SESSION["storagedata"]["zones"]["last_updated"] = time();             
            $result = $this->tools->zone_list($_SESSION["userdata"]["t_pattern"]);                                    
            if ($this->config["idn_compatibility"] && !$this->tools->is_pattern($_SESSION["userdata"]["t_pattern"], "catch_all")) {
                $idn_result = $this->tools->zone_list("xn--*");                
                $pattern = $_SESSION["userdata"]["t_pattern"];
                $pattern = str_replace("*", ".*", $pattern);
                foreach ($idn_result as $key => $zone_set)
                {
                    if (!preg_match("/^" . $pattern . "$/i", $this->tools->format_fqdn($zone_set["0"], "unicode", "domain", false))) {
                        unset($idn_result[$key]);
                    }
                }            
                $result = array_merge($result, $idn_result);
            }
            $this->tools->set_domain_order($result, $this->config["idn_compatibility"]);
            $_SESSION["storagedata"]["zones"]["list"] = $result;
        }
        
        $paging = new Paging();
        $paging->setAvailableEntriesPerPage($this->zone_list_entries_per_page);
        $paging->setPageLinksPerPage($this->zone_list_page_links_per_page);
        $total_domains = count($result);
        $paging->initSelectedEntriesPerPage($_SESSION["userdata"]["s"], $this->zone_list_default_entry_page);
        $total_pages = ceil($total_domains / $paging->getPageLinksPerPage());
        $paging->initSelectedPageNumber($_SESSION["userdata"]["p"], $this->zone_list_default_page, $total_pages);
        $this->tools->tpl->set_var("PAGING_RESULTS_PER_PAGE", $paging->buildEntriesPerPageBlock($_SESSION["userdata"]["s"], "zone"));
        $this->tools->tpl->set_var("PAGING_PAGES", $paging->buildPagingBlock($total_domains, $_SESSION["userdata"]["s"], $_SESSION["userdata"]["p"], "zone"));
        $paging->parsePagingToolbar("paging_repository", "paging_toolbar_c2", "PAGE_TOOLBAR");
        if ($result) {
            if ($result != $this->config["empty_result"] && is_array($result)) {
                $this->tools->tpl->set_block("zone_repository", "result_list_row");
                $is = $paging->calculateResultsStartIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                $ie = $paging->calculateResultsEndIndex($_SESSION["userdata"]["p"], $_SESSION["userdata"]["s"]);
                for ($i=$is; $i < $ie; $i++)
                {
                    if (isset($result[$i])) {
                        $this->tools->tpl->set_var(array(                                                                                
                            "USER_DOMAIN"   => $this->tools->format_fqdn($result[$i]["0"], "unicode", "domain", true),
                            "DOMAIN"        => $result[$i]["0"],
                            "EXPIRATION"    => $result[$i]["1"],
                        ));
                        $this->tools->tpl->parse("RESULT_LIST", "result_list_row", true);
                    }
                }
                $this->tools->tpl->parse("CONTENT", "result_list_table");
            } else {
                $this->tools->tpl->set_block("zone_repository", "no_result_row");
                $this->tools->tpl->set_var("NO_RESULT_MESSAGE", $this->msg["_no_result_message"]);
                $this->tools->tpl->parse("RESULT_LIST", "no_result_row", true);
                $this->tools->tpl->parse("CONTENT", "result_list_table");
            }
        } else {
            $this->tools->general_err("GENERAL_ERROR", $this->err_msg["_srv_req_failed"]);
            $this->list_form();
        }
    }
    
    /**
     * View information about a zone.
     *
     * on success - visualizes zone data
     * on failure - error message
     *
     * @access  private
     * @return  void
     * @see     list_result()
     */
    function view()
    {
        $this->nav_submain = $this->nav["zone_info"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->set_block("zone_repository", "result_table_header_row", "result_table_header_r");
        $this->tools->tpl->set_block("zone_repository", "result_table_row", "result_table_r");
        $this->tools->tpl->set_block("zone_repository", "no_result_table_row", "no_result_table_r");
        $this->tools->tpl->set_block("zone_repository", "result_table", "result_tbl");        

        $result = $this->tools->zone_view($_SESSION["userdata"]["t_domain"]);
        $this->tools->tpl->set_var("ZONE", $_SESSION["userdata"]["t_domain"]);
        $this->tools->tpl->parse("HEADER", "result_table_header_row");
        if ($result) {
            foreach ($result as $val)
            {                                
                $this->tools->tpl->set_var("REC_NAME", htmlspecialchars(array_shift($val)));
	            $this->tools->tpl->set_var("REC_TYPE", $type = array_shift($val));	            
	            $this->tools->tpl->set_var("REC_PRI", array_shift($val));
	            $this->tools->tpl->set_var("REC_TARGET", htmlspecialchars(array_shift($val)));
	            $this->tools->tpl->set_var("REC_TTL", array_shift($val));
	            $this->tools->tpl->set_var("REC_VALID_FROM", array_shift($val));
	            $this->tools->tpl->set_var("REC_VALID_TO", array_shift($val));
	            $this->tools->tpl->set_var("REC_OPTION", array_shift($val));      
	            if ($type != "?") {
                    $this->tools->tpl->parse("FORMTABLEROWS", "result_table_row", true);                    
                } else if (count($result) == 1) {
                    $this->tools->tpl->parse("FORMTABLEROWS", "no_result_table_row", true);                    
                }
            }
            $this->tools->tpl->parse("CONTENT", "result_table");
        } else {
            $this->tools->general_err("GENERAL_ERROR",$this->err_msg["_srv_req_failed"]);
        }
    }

}

?>
