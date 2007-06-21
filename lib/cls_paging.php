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
    function setAvailableDomainEntriesPerPage($num_entries_arr)
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
    function getAvailableDomainEntriesPerPage()
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
        if (!isset($entry) || !in_array($entry, $this->getAvailableDomainEntriesPerPage())) {
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
     * Returns a html snippet with links for inc/reducing num of entries
     *
     * @var     integer $selected_num_entries_id
     * @access  public
     * @return  string
     */
    function buildDomainEntriesPerPageBlock($selected_num_entries_id)
    {
        $this->tools->tpl->set_block("paging_repository", "domain_list_entries", "domain_ls_entries");        
        $this->tools->tpl->set_block("paging_repository", "selected_domain_list_entry", "selected_domain_ls_entry");        
        foreach ($this->entries_per_page as $num_entries)
        {
            $this->tools->tpl->set_var("ENTRIES_PER_PAGE", $num_entries);
            if ($selected_num_entries_id == $num_entries) {                 
                $this->tools->tpl->parse("domain_ls_entries", "selected_domain_list_entry", true);
            } else {                
                $this->tools->tpl->parse("domain_ls_entries", "domain_list_entries", true);
            }
        }        
        return $this->tools->tpl->get("domain_ls_entries");
    }
    
    /**
     * Returns a html snippet with page numbers
     *
     * @var     integer $total_domains
     * @var     integer $current_page
     * @access  public
     * @return  string
     */
    function buildDomainPagingBlock($total_domains, $entries_per_page, &$current_page)
    {
        $this->tools->tpl->set_block("paging_repository", "domain_list_pages", "domain_ls_pages");
        $this->tools->tpl->set_block("paging_repository", "selected_domain_list_pages", "selected_domain_ls_pages");
        $this->tools->tpl->set_block("paging_repository", "domain_list_pages_go_back", "domain_ls_pages_go_back");
        $this->tools->tpl->set_block("paging_repository", "domain_list_pages_go_forth", "domain_ls_pages_go_forth");        
        $total_pages = ceil($total_domains / $entries_per_page);
        $this->fixCurrentPageOverflow($current_page, $total_pages);        
        $before_pages = 0;
        $after_pages  = 0;
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
        
        $is = $current_page-$before_pages;
        $ie = $current_page;
        if ($is > 1) {
            //prints <<                
            $this->tools->tpl->set_var("PAGE_NUM", $is - 1);
            $this->tools->tpl->parse("domain_ls_pages", "domain_list_pages_go_back", true);
        }
        for ($i=$is; $i < $ie; $i++)
        {
            $this->tools->tpl->set_var("PAGE_NUM", $i);
            $this->tools->tpl->parse("domain_ls_pages", "domain_list_pages", true);
        }
        $this->tools->tpl->set_var("PAGE_NUM", $current_page);
        $this->tools->tpl->parse("domain_ls_pages", "selected_domain_list_pages", true);
        $is = $current_page+1;
        $ie = $current_page + $after_pages;
        for ($i=$is; $i <= $ie; $i++)
        {
            $this->tools->tpl->set_var("PAGE_NUM", $i);
            $this->tools->tpl->parse("domain_ls_pages", "domain_list_pages", true);
        }
        if ($ie < $total_pages) {
            //prints >>              
            $this->tools->tpl->set_var("PAGE_NUM", $ie + 1);
            $this->tools->tpl->parse("domain_ls_pages", "domain_list_pages_go_forth", true);
        }
        return $this->tools->tpl->get("domain_ls_pages");
    }
}

?>
