<?php

/**
 * English language translation of the Web interface.
 *
 * If you want to introduce another language version, please copy this file,
 * save the copy in the same directory as this one and then translate it. Don't
 * forget when you name your file to include the new language in the filename.
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright for now
 */

//navigation data
$nav =

array(  
    "home"          => "Home",
    
    "domain"        => "Domain",
    "view_info"     => "view info",
    "domain_list"   => "domain list",
    "registration"  => "registration",
    "renewal"       => "renewal",
    "transfer"      => "transfer",
    "modification"  => "modification",
    "mass_modification"  => "mass modification",
    "provide_ns"    => "provide nameservers",
    "provide_doms"  => "provide domains",
    "deletion"      => "deletion",
    "owner_change"  => "owner change",
    "owner_change_dom_select"   => "domain selection",
    "owner_change_cnt_entry"    => "new owner contact",
    "lock_unlock"   => "lock/unlock",
    "redemption"    => "redemption",
    
    "ns"            => "Name server",
    "create_ns"     => "create",
    "modify_ns"     => "modification",
    "delete_ns"     => "deletion",
    "ns_list"       => "list",
    
    "other"         => "Miscellaneous",
    "tips"          => "beginner tips",
    "query_profile" => "query profile",
    "show_requests" => "show available requests",
    "result_list"   => "result list",
    "result_retrieve"   => "view result",
    "support"       => "support",
    
    "contacts"      => "Contacts",
    "list"          => "list",
    "show"          => "preview",
    "select"        => "select",
    "create"        => "create",
    "edit"          => "modification",
    "delete"        => "deletion",

    "where_you_are" => "You are in: ",
);

//set of messages used through the site
$messages =

array(  
    "_no_result_message"    => "No result",
    "_request_sent"         => "Your request was sent!",
    "_request_not_sent"     => "Your request was not sent!",
    "_request_successful"   => "Your request was completed successfully!",
    "_request_failed"       => "Your request failed!",
    "_request_partial_success" => "Your request didn't fully succeed!",        
    "_error_check_logs"     => "Check your log files!",        
    "_unknown"              => "unknown",
    "_individual_help_txt"  => "Y(es) or N(o) or leave empty"
);

//mapping of requests to human readable text
$requests =

array(  
    "contact-create"    => array(
                "text" => "contact creation"
                ),
    "contact-modify"    => array(
                "text" => "contact modification"
                ),
    "contact-delete"    => array(
                "text" => "contact deletion"
                ),
    "ns-create"     => array(
                "text" => "name server creation"
                ),
    "host-create"       => array(
                "text" => "name server creation"
                ),
    "ns-modify"     => array(
                "text" => "name server modification"
                ),
    "host-modify"       => array(
                "text" => "name server modification"
                ),
    "ns-delete"     => array(
                "text" => "name server deletion"
                ),
    "host-delete"       => array(
                "text" => "name server deletion"
                ),
    "domain-register"   => array(
                "text" => "domain registration"
                ),
    "domain-renew"      => array(
                "text" => "domain renewal"
                ),
    "domain-transfer-in"    => array(
                "text" => "domain transfer"
                ),
    "domain-modify"     => array(
                "text" => "domain modification"
                ),
    "domain-delete"     => array(
                "text" => "domain deletion"
                ),
    "domain-owner-change"   => array(
                "text" => "domain owner change"
                ),
    "domain-lock"       => array(
                "text" => "domain lock"
                ),
    "domain-unlock" => array(
                "text" => "domain unlock"
                ),
    "unknown"   => array(
                "text" => "unknown request"
                ),
);

$request_status =

array(  
    "ack"   => array(
                "text" => "success"
                ),
    "nack"  => array (
                "text" => "failed"
                ),
    "?" => array (
                "text" => "unknown"
                ),
    "unknown"   => array (
                "text" => "error"
                ),
);

//Container for error messages
// Note that the error message could be overridden at a 
//later stage in the code
$error_messages = array(

		"_username"         => "Invalid username",
		"_password"         => "Invalid password",
		"_auth_failed"      => "Server error! Authorization failed",
		"_srv_req_failed"   => "Server error! The request was not processed.",
		"_srv_req_part_failed"  => "Server error! The request was only partially processed. <br />Check \"request results\" list for details. The failed items are:<br />",
		"_domain"           => "Invalid domain name.",
		"_select_domain"    => "Invalid domain selection.",
		"_tld"              => "Invalid top level domain.",
		"_sld"              => "Invalid second level domain.",
		"_host"             => "Invalid host name.",
		"_ns"               => "Invalid name server(s).",
		"_ns_select"        => "Your name server selection is invalid.",
		"_ns_min"           => "Please, provide at least {NS_MIN_NUM} name servers.",
		"_dom_status"       => "Invalid status of a domain",	
		"_email"            => "Invalid email.",
		"_ipv4"             => "Invalid IP.",
		"_ipv6"             => "Invalid IPv6.",
		"_contact_hdl"      => "Invalid contact handle.",
		"_contact_hdl_type" => "Check if your contact handle matches the syntax for the specified tld.",
		"_domain_reg_period"=> "Invalid registration period. It should be provided in months (e.g \"120\" for 10 years). The max. registration is for 10 years.",
		"_name"             => "The field contains invalid characters.",
		"_overall_text"     => "The field contains invalid characters.",
		"_individual"       => "The field contains invalid characters.",
		"_is_individual"    => "Enter \"Yes\" or \"Y\" if you are an individual.",
		"_invalid_chars_in_field"       => "The field is empty or contains invalid characters.",
		"_invalid_chars_in_opt_field"   => "The field contains invalid characters.",
		"_invalid_field_length"         => "The field could be up to {ERROR_FIELD_LENGTH} character long.",
		"_svtrid"	        => "Invalid SvTrID.",
		"_auth_id"	        => "Invalid AUTH-ID.",
		"_sess_expired"	    => "Your session has expired!",
		"_com_tld"	        => "",
	    "_net_tld"	        => "",			
	    "_org_tld"	        => "",		
	    "_info_tld"	        => "",			
	    "_biz_tld"	        => "",		
	    "_us_tld"           => "",
	    "_de_tld"	        => "",	
	    "_cn_tld"	        => "",		
	    "_eu_tld"	        => "",
	    "_mobi_tld"	        => ""	    
);

?>
