<?php

//navigation data
$nav =

array(	"domain"	=> "Domain",
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
	    "tips"		=> "helpful tips",	
	    "query_profile"	=> "query profile",
	    "result_list"	=> "result list",
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