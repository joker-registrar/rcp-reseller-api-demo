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

array(	"home"		=> "Home",
	"domain"	=> "Domain",
	    "view_info"		=> "view info",
	    "domain_list"	=> "domain list",
	    "registration"	=> "registration",
	    "renewal"		=> "renewal",
	    "transfer"		=> "transfer",
	    "modification"	=> "modification",
	    "deletion"		=> "deletion",
	    "owner_change"	=> "owner change",
	    "lock_unlock"	=> "lock/unlock",
	    "redemption"	=> "redemption",
	"ns"		=> "Name server",
	    "create_ns"		=> "create",
	    "modify_ns"		=> "modification",
	    "delete_ns"		=> "deletion",
	    "ns_list"		=> "list",
	"other"		=> "Other",
	    "tips"		=> "beginner tips",	
	    "query_profile"	=> "query profile",
	    "result_list"	=> "result list",
	    "result_retrieve"	=> "view result",
	"contacts"		=> "Contacts",
	    "list"		=> "list",
	    "select"		=> "select",
	    "create"		=> "create",
	    "edit"		=> "modification",
	    "delete"		=> "deletion",
);

//set of messages used through the site
$messages =

array(	"_no_result_message"	=> "No result",
	"_request_sent"		=> "Your request was sent!",
	"_request_not_sent"	=> "Your request was not sent!",
	"_request_successful"	=> "Your request was completed successfully!",
	"_request_failed"	=> "Your request failed!",
	"_request_partial_success" => "Your request didn't fully succeed!",
	
	"_error_check_logs"	=> "Check your log files!",
	
	"_unknown"		=> "unknown",
);

//mapping of requests to human readable text
$requests =

array(	"contact-create"	=> array(
			    "text" => "contact creation"
			    ),
	"contact-modify"	=> array(
			    "text" => "contact modification"
			    ),
	"contact-delete"	=> array(
			    "text" => "contact deletion"
			    ),
	"ns-create"		=> array(
			    "text" => "name server creation"
			    ),
	"host-create"		=> array(
			    "text" => "name server creation"
			    ),
	"ns-modify"		=> array(
			    "text" => "name server modification"
			    ),
	"host-modify"		=> array(
			    "text" => "name server modification"
			    ),
	"ns-delete"		=> array(
			    "text" => "name server deletion"
			    ),
	"host-delete"		=> array(
			    "text" => "name server deletion"
			    ),
	"domain-register"	=> array(
			    "text" => "domain registration"
			    ),
	"domain-renew"		=> array(
			    "text" => "domain renewal"
			    ),
	"domain-transfer-in"	=> array(
			    "text" => "domain transfer"
			    ),			    
	"domain-modify"		=> array(
			    "text" => "domain modification"
			    ),
	"domain-delete"		=> array(
			    "text" => "domain deletion"
			    ),
	"domain-owner-change"	=> array(
			    "text" => "domain owner change"
			    ),
	"domain-lock"		=> array(
			    "text" => "domain lock"
			    ),
	"domain-unlock"	=> array(
			    "text" => "domain unlock"
			    ),
	"unknown"	=> array(
			    "text" => "unknown request"
			    ),
);

$request_status =

array(	"ack"	=> array(
			    "text" => "success"
			    ),
	"nack"	=> array (
			    "text" => "failed"
			    ),
	"?"	=> array (
			    "text" => "unknown"
			    ),
	"unknown"	=> array (
			    "text" => "totally unknown"
			    ),
);

?>