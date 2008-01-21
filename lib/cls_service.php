<?php

/**
 * Container for services - IDN converter etc.. Visualization and request handling
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class Service
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
     * Class constructor. No optional parameters.
     *
     * usage: Domain()
     *
     * @access  private
     * @return  void
     */
    function Service()
    {        
        global $error_messages, $error_regexp, $jpc_config, $tools, $messages, $nav;
        $this->config  = $jpc_config;
        $this->err_msg = $error_messages;
        $this->err_regexp = $error_regexp;
        $this->tools   = $tools;
        $this->msg     = $messages;
        $this->nav     = $nav;
        $this->connect = new Connect;
        $this->nav_main= $this->nav["other"];
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
            case "idn_convert_form":
                $this->idn_convert_form();
                break;               
                
            case "idn_convert":                
                $is_valid = $this->is_valid_input("idn_convert");                
                if (!$is_valid) {
                    $this->idn_convert_form();
                } else {
                    $this->idn_convert();
                }
                break;                
        }
    }

    /**
     * Show IDN conversion form
     *
     * @param   string  $prefill_form show/hide set domain value button
     * @access  public
     * @return  void
     */
    function idn_convert_form($prefill_form = false)
    {
        $this->tools->tpl->set_block("idn_convert_form", "CUT_DOMAIN_SET_FORM", "CUT_DOM_SET_FORM");
        if ($prefill_form) {
            $this->tools->tpl->parse("CUT_DOM_SET_FORM", "CUT_DOMAIN_SET_FORM");
        }
        $this->nav_submain = $this->nav["idn_conversion"];
        $this->tools->tpl->set_var("NAV_LINKS", $this->nav_main."  &raquo; ".$this->nav_submain);
        $this->tools->tpl->parse("NAV", "navigation");
        $this->tools->tpl->set_block("domain_repository", "idn_convert_info");
        $this->tools->tpl->parse("INFO_CONTAINER", "idn_convert_info");

        if (!isset($_SESSION["formdata"]["r_idnconv_type"])) {
            $this->tools->tpl->set_var("R_IDNCONV_TYPE_TOASCII", "checked");
        }                
        $this->tools->tpl->parse("CONTENT", "idn_convert_form");
    }
    
    /**
     * Shows the result of the conversion
     *
     * @access    public
     * @return  void
     */
    function idn_convert()
    {
        $result = "";
        switch (strtolower($_SESSION["httpvars"]["r_idnconv_type"]))
        {
            case "toascii":
                $result = $this->tools->idn_codec($_SESSION["httpvars"]["t_domain"], "ascii");                
                break;
            case "tounicode":                
                $result = $this->tools->idn_codec($_SESSION["httpvars"]["t_domain"], "unicode");
                break;
        }
        $this->tools->tpl->set_var("CONVERSION_OUTPUT", $result);
        $this->idn_convert_form(true);
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
            case "idn_convert":            
                if (empty($_SESSION["httpvars"]["t_domain"])) {
                    $is_valid = false;
                    $this->tools->field_err("ERROR_INVALID_DOMAIN",$this->err_msg["_domain"]);
                }
                switch (strtolower($_SESSION["httpvars"]["r_idnconv_type"]))
                {
                    case "toascii":
                    case "tounicode":
                        //do nothing
                        break;
                    default:
                        $this->tools->field_err("ERROR_INVALID_IDN_CONVERSION_TYPE", $this->err_msg["_idn_conversion"]);
                        $is_valid = false;
                        break;
                }
                break;
             default:
                $is_valid = false;
                break;
        }
        return $is_valid;
    }
}

?>
