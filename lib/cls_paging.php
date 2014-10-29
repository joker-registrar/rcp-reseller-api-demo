<?php

/**
 * Provides a paging mechanism for a list of objects
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class Paging
{
    /**
     * Defines the elements of a result set per page
     *
     * @var     string
     * @access  private
     */
    var $entries_per_page  = array(20, 50, 100);

    /**
     * Number of page links
     *
     * @var     integer
     * @access  private
     */
    var $page_links_per_page  = 15;

    /**
     * Class constructor. No optional parameters.
     *
     * @access  public
     * @return  void
     */
    function Paging()
    {
        global $jpc_config, $tools, $messages;
        $this->config  = $jpc_config;
        $this->tools   = $tools;
        $this->msg     = $messages;
    }

    /**
     * Sets number of entries per page
     *
     * @var     array   $num_entries_arr
     * @access  public
     * @return  string
     */
    function setAvailableEntriesPerPage($num_entries_arr)
    {
        $this->entries_per_page = $num_entries_arr;
    }

    /**
     * Sets number of page links per page
     *
     * @var     integer   $page_links_per_page
     * @access  public
     * @return  string
     */
    function setPageLinksPerPage($page_links_per_page)
    {
        $this->page_links_per_page = $page_links_per_page;
    }

    /**
     * Modify current page if over available pages
     *
     * @var     integer   $page
     * @var     integer   $total_pages
     * @access  public
     * @return  string
     */
    function fixCurrentPageOverflow(&$page, $total_pages)
    {
        if ($page > $total_pages) {
            $page = $total_pages;
        }
    }

    /**
     * Gets number of entries per page
     *
     * @access  public
     * @return  string
     */
    function getAvailableEntriesPerPage()
    {
        return $this->entries_per_page;
    }

    /**
     * Gets number of page links per page
     *
     * @access  public
     * @return  string
     */
    function getPageLinksPerPage()
    {
        return $this->page_links_per_page;
    }

    /**
     * Initialize the number of selected entries per page.
     * Sets a default value if nothing was selected.
     *
     * @var     integer $entry
     * @var     integer $default_entry
     * @access  public
     * @return  string
     */
    function initSelectedEntriesPerPage(&$entry, $default_entry)
    {
        if (!isset($entry) || !in_array($entry, $this->getAvailableEntriesPerPage())) {
            $entry = $default_entry;
        }
    }

    /**
     * Initialize selected page number.
     * Sets a default value if nothing was selected.
     *
     * @var     integer $page
     * @var     integer $default_page
     * @var     integer $total_pages
     * @access  public
     * @return  string
     */
    function initSelectedPageNumber(&$page, $default_page, $total_pages)
    {
        if (!isset($page) || $page <= 0 || $page > $total_pages) {
            $page = $default_page;
        }
        $page = intval($page);
    }

    /**
     * Calculates starting index of result listing
     *
     * @var     integer $page
     * @var     integer $entries_per_page
     * @access  public
     * @return  integer
     */
    function calculateResultsStartIndex($page, $entries_per_page)
    {
        return ($page - 1) * $entries_per_page;
    }

    /**
     * Calculates end index of result listing
     *
     * @var     integer $page
     * @var     integer $entries_per_page
     * @access  public
     * @return  integer
     */
    function calculateResultsEndIndex($page, $entries_per_page)
    {
        return $page * $entries_per_page;
    }

    /**
     * Parses page entry set and page links thus building a toolbar
     *
     * @access  public
     * @return  integer
     */
    function parsePagingToolbar($template, $template_block, $template_var)
    {
        $this->tools->tpl->set_block($template, $template_block);
        $this->tools->tpl->parse($template_var, $template_block);
    }

    /**
     * Returns a html snippet with links for inc/reducing num of entries
     *
     * @var     integer $selected_num_entries_id
     * @var     string  $type
     * @access  public
     * @return  string
     */
    function buildEntriesPerPageBlock($selected_num_entries_id, $type)
    {
        switch ($type)
        {
            case "domain":
                $tpl_block_entry = "domain_list_entries";
                $tpl_block_selected_entry = "selected_domain_list_entry";
                break;
            case "zone":
                $tpl_block_entry = "zone_list_entries";
                $tpl_block_selected_entry = "selected_zone_list_entry";
                break;
            case "contact":
                $tpl_block_entry = "contact_list_entries";
                $tpl_block_selected_entry = "selected_contact_list_entry";
                break;
            case "nameserver":
                $tpl_block_entry = "ns_list_entries";
                $tpl_block_selected_entry = "selected_ns_list_entry";
                break;
            case "ns_mass":
                $tpl_block_entry = "ns_mass_list_entries";
                $tpl_block_selected_entry = "selected_ns_mass_list_entry";
                break;
            case "result":
                $tpl_block_entry = "result_list_entries";
                $tpl_block_selected_entry = "selected_result_list_entry";
                break;
            case "contact_unverified":
                $tpl_block_entry = "contact_unverified_list_entries";
                $tpl_block_selected_entry = "selected_contact_unverified_list_entry";
                break;
        }
        $this->tools->tpl->set_block("paging_repository", $tpl_block_entry, "ls_entries");
        $this->tools->tpl->set_block("paging_repository", $tpl_block_selected_entry, "selected_ls_entry");
        foreach ($this->entries_per_page as $num_entries)
        {
            $this->tools->tpl->set_var("ENTRIES_PER_PAGE", $num_entries);
            if ($selected_num_entries_id == $num_entries) {
                $this->tools->tpl->parse("ls_entries", $tpl_block_selected_entry, true);
            } else {
                $this->tools->tpl->parse("ls_entries", $tpl_block_entry, true);
            }
        }
        return $this->tools->tpl->get("ls_entries");
    }

    /**
     * Returns a html snippet with page numbers
     *
     * @var     integer $total_domains
     * @var     integer $current_page
     * @access  public
     * @return  string
     */
    function buildPagingBlock($total_domains, $entries_per_page, &$current_page, $type)
    {
        switch ($type)
        {
            case "domain":
                $tpl_block_page = "domain_list_pages";
                $tpl_block_selected_page = "selected_domain_list_pages";
                $tpl_block_page_go_forth = "domain_list_pages_go_forth";
                $tpl_block_page_go_back = "domain_list_pages_go_back";
                break;
            case "zone":
                $tpl_block_page = "zone_list_pages";
                $tpl_block_selected_page = "selected_zone_list_pages";
                $tpl_block_page_go_forth = "zone_list_pages_go_forth";
                $tpl_block_page_go_back = "zone_list_pages_go_back";
                break;
            case "contact":
                $tpl_block_page = "contact_list_pages";
                $tpl_block_selected_page = "selected_contact_list_pages";
                $tpl_block_page_go_forth = "contact_list_pages_go_forth";
                $tpl_block_page_go_back = "contact_list_pages_go_back";
                break;
            case "nameserver":
                $tpl_block_page = "ns_list_pages";
                $tpl_block_selected_page = "selected_ns_list_pages";
                $tpl_block_page_go_forth = "ns_list_pages_go_forth";
                $tpl_block_page_go_back = "ns_list_pages_go_back";
                break;
            case "ns_mass":
                $tpl_block_page = "ns_mass_list_pages";
                $tpl_block_selected_page = "selected_ns_mass_list_pages";
                $tpl_block_page_go_forth = "ns_mass_list_pages_go_forth";
                $tpl_block_page_go_back = "ns_mass_list_pages_go_back";                
                break;
            case "result":
                $tpl_block_page = "result_list_pages";
                $tpl_block_selected_page = "selected_result_list_pages";
                $tpl_block_page_go_forth = "result_list_pages_go_forth";
                $tpl_block_page_go_back = "result_list_pages_go_back";                
                break;
            case "contact_unverified":
                $tpl_block_page = "contact_unverified_list_pages";
                $tpl_block_selected_page = "selected_contact_unverified_list_pages";
                $tpl_block_page_go_forth = "contact_unverified_list_pages_go_forth";
                $tpl_block_page_go_back = "contact_unverified_list_pages_go_back";
                break;
        }
        $this->tools->tpl->set_block("paging_repository", $tpl_block_page, "ls_pages");
        $this->tools->tpl->set_block("paging_repository", $tpl_block_selected_page, "selected_ls_pages");
        $this->tools->tpl->set_block("paging_repository", $tpl_block_page_go_forth, "ls_pages_go_forth");
        $this->tools->tpl->set_block("paging_repository", $tpl_block_page_go_back, "ls_pages_go_back");
        $total_pages = ceil($total_domains / $entries_per_page);
        $this->fixCurrentPageOverflow($current_page, $total_pages);
        $before_pages = 0;
        $after_pages  = 0;
        if ($total_pages < $this->page_links_per_page) {
            $before_pages = $current_page - 1;
            $after_pages = $total_pages - $current_page;
        } else {
            $before_pages = intval(($this->page_links_per_page - 1) / 2);
            if ($before_pages >= $current_page) {
                $after_pages = $before_pages - $current_page + 1;
                $before_pages = $current_page - 1;
            }
            $after_pages  += intval($this->page_links_per_page / 2);
            if ($after_pages > $total_pages - $current_page) {
                if ($after_pages - $total_pages + $before_pages < 0) {
                    $before_pages += $after_pages - $total_pages + $current_page;
                }
                $after_pages = $total_pages - $current_page;
            }
        }

        $is = $current_page-$before_pages;
        $ie = $current_page;
        if ($is > 1) {
            //prints <<
            $this->tools->tpl->set_var("PAGE_NUM", $is - 1);
            $this->tools->tpl->parse("ls_pages", $tpl_block_page_go_back, true);
        }
        for ($i=$is; $i < $ie; $i++)
        {
            $this->tools->tpl->set_var("PAGE_NUM", $i);
            $this->tools->tpl->parse("ls_pages", $tpl_block_page, true);
        }
        $this->tools->tpl->set_var("PAGE_NUM", $current_page);
        $this->tools->tpl->parse("ls_pages", $tpl_block_selected_page, true);
        $is = $current_page+1;
        $ie = $current_page + $after_pages;
        for ($i=$is; $i <= $ie; $i++)
        {
            $this->tools->tpl->set_var("PAGE_NUM", $i);
            $this->tools->tpl->parse("ls_pages", $tpl_block_page, true);
        }
        if ($ie < $total_pages) {
            //prints >>
            $this->tools->tpl->set_var("PAGE_NUM", $ie + 1);
            $this->tools->tpl->parse("ls_pages", $tpl_block_page_go_forth, true);
        }
        return $this->tools->tpl->get("ls_pages");
    }
}

?>
