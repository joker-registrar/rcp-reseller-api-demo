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
    "view_info"     => "view domain info",
    "domain_list"   => "list & view domains",
    "registration"  => "domain registration",
    "renewal"       => "domain renewal",
    "transfer"      => "trivial domain transfer",
    "fast_transfer"      => "domain transfer",
    "bulk_transfer" => "bulk domain transfer",
    "authid"        => "get AUTH-ID",
    "modification"  => "domain modification",
    "mass_modification"  => "bulk modification",
    "provide_ns"    => "provide nameservers",
    "provide_doms"  => "provide domains",
    "deletion"      => "domain deletion",
    "grants_change"  => "change domain roles",
    "grants_change_dom_select"   => "domain selection",
    "grants_change_form"   => "add/edit roles",
    "owner_change"  => "domain owner change",
    "owner_change_dom_select"   => "domain selection",
    "owner_change_cnt_entry"    => "new owner contact",
    "lock_unlock"   => "lock/unlock domain",
    "autorenew"     => "autorenew domains",
    "user_props"     => "user preferences",
    "redemption"    => "domain redemption",
    
    "ns"            => "Name service & DNS",
    "create_ns"     => "create nameserver",
    "modify_ns"     => "modify nameserver",
    "delete_ns"     => "deletion nameserver",
    "ns_list"       => "list nameservers",
    "zone_list"     => "list domains and zones",
    "zone_info"     => "zone records of selected domain",
    
    "other"         => "Miscellaneous",
    "tips"          => "beginner tips",
    "query_profile" => "query profile",
    "show_requests" => "show available requests",
    "result_list"   => "result list",
    "result_retrieve"   => "view result",
    "support"       => "support",
    "idn_conversion"=> "IDN conversion",
    
    "contacts"      => "Contacts",
    "list"          => "list & view contacts",
    "show"          => "preview",
    "select"        => "select",
    "create"        => "create contact",
    "edit"          => "modify contact",
    "delete"        => "delete contact",
    "verified"      => "email verification status",
    "unverified"    => "list unverified emails",
    "resend_verification" => "resend verification email",

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
    "_individual_help_txt"  => "Y(es), N(o) or leave empty (if empty fallback to No)",
    "_account_type_help_txt" => "Only required for .uk owner contacts",
    "_company_number_help_txt" => "Only required if account type is a company in UK"
);

//mapping of requests to human readable text
$requests =

array(  
    "login"    => array(
                "text" => "login"
                ),
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
    "create-order"   => array(
                "text" => "create domain order"
                ),
    "domain-register"   => array(
                "text" => "domain registration"
                ),
    "domain-renew"      => array(
                "text" => "domain renewal"
                ),
    "domain-transfer-in"    => array(
                "text" => "trivial domain transfer"
                ),
    "domain-transfer-in-reseller"    => array(
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
    "set-domain-property"       => array(
                "text" => "set domain property"
                ),
    "domain-get-property"       => array(
                "text" => "get domain property"
                ),
    "domain-transfer-get-auth-id" => array(
                "text" => "get domain AUTH-ID"
                ),
    "query-contact-list" => array(
                "text" => "query contact list"
                ),
    "query-domain-list" => array(
                "text" => "query domain list"
                ),
    "dns-zone-list" => array(
                "text" => "dns zone list"
                ),    
    "dns-zone-get" => array(
                "text" => "get dns zone"
                ),
    "dns-zone-put" => array(
                "text" => "set dns zone"
                ),
    "user-set-property"       => array(
                "text" => "set user property"
                ),
    "user-get-property"       => array(
                "text" => "get user property"
                ),
    "grants-invite" => array(
                "text" => "send invitation"
                ),
    "grants-revoke" => array(
                "text" => "revoke grant"
                ),
    "wa-email-query-status" => array(
                "text" => "query email status"
                ),
    "wa-email-validate" => array(
                "text" => "resend verification email"
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
    "pending" => array (
                "text" => "pending"
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
		"_srv_req_failed"   => "Server error! The request was not processed or timed out.",		
		"_srv_req_part_failed"  => "Server error! The request was only partially processed. <br />Check \"request results\" list for details. The failed items are:<br />",
		"_srv_req_part_failed_s"=> "Server error! The request was only partially processed. Check \"request results\" list for details.",		
		"_domain"           => "Invalid domain name.",
		"_domain_custom"    => "Invalid domain name: ",
                "_domain_not_found" => "Domain not found",
		"_domains_partially_reg"=> "Some domain registrations failed.",
		"_select_domain"    => "Invalid domain selection.",
		"_tld"              => "Invalid top level domain.",
		"_sld"              => "Invalid second level domain.",
		"_host"             => "Invalid host name.",
		"_ns"               => "Invalid name server(s).",
		"_ns_select"        => "Your name server selection is invalid.",
		"_ns_min"           => "Please, provide at least {NS_MIN_NUM} name servers.",
		"_ds_select"        => "Your DNSSEC selection is invalid.",
		"_ds_min"           => "Please, provide at least {DS_MIN_NUM} DNSSEC entries.",
		"_dom_status"       => "Invalid status of a domain",	
		"_email"            => "Invalid email.",
		"_ipv4"             => "Invalid IP.",
		"_ipv6"             => "Invalid IPv6.",
		"_contact_hdl"      => "Invalid contact handle.",
		"_contact_hdl_type" => "Check if your contact handle matches the syntax for the specified tld.",
		"_domain_reg_period"=> "Invalid registration period. The max. registration is for 10 years.",
		"_name"             => "The field contains invalid characters.",
		"_overall_text"     => "The field contains invalid characters.",
		"_individual"       => "The field contains invalid characters.",
		"_is_individual"    => "Enter \"Yes\" or \"Y\" if you are an individual.",
		"_invalid_chars_in_field"       => "The field is empty or contains invalid characters.",
		"_invalid_chars_in_opt_field"   => "The field contains invalid characters.",
		"_invalid_field_length"         => "The field could be up to {ERROR_FIELD_LENGTH} character long.",
		"_nexus_category_language"      => "Please select a valid category language.",
		"_svtrid"	        => "Invalid SvTrID.",
		"_auth_id"	        => "Invalid AUTH-ID.",
		"_new_dom_status"   => "Please provide valid domain status.",
		"_sess_expired"	    => "Your session has expired!",
		"_empty_field"	    => "Field is empty!",
		"_domain_authid_pairs_missing"  => "Type in Domain/AUTH-ID pairs!",
		"_domain_authid_pairs_parse_error"  => "Domain/AUTH-ID pairs cannot be parsed!",
		"_domain_authid_pairs_parse_not_all"    => "Some Domain/AUTH-ID pairs could not be parsed! Probably a missing AUTH-ID",
                "_domain_authid_pairs_limit"    => "Please be aware of the upper and the lower limit of domains which can be processed with this procedure.",
		"_domain_authid_pairs_invalid_domain"   => "Your list contains invalid domains: ",
		"_idn_conversion"   => "Invalid conversion type.",
                "_membership_token"      => "Membership Token is only valid for .xxx domains",
                "_delete_domain_type"   => "Advanced deletion types are only allowed for .de and .at domains",
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

$roles = array (
    "creator" => "Assignment",
    "admin" => "Admin",
    "tech" => "DNS Admin",
    "billing" => "Billing"
);

?>
