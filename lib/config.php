<?php

//site specifics
$config["dmapi_ver"] = "1.0-rc1";
$config["dmapi_encoding"] = "utf-8";
$config["dmapi_form_action"] = "index.php";
$config["dmapi_default_language"] = "eng";
//to be removed at a later stage
$config["dmapi_allowed_languages"] = array("eng","de");

//remote server URL
$config["joker_url"] = "https://dmapi.joker.com";

//allowed tlds at Joker.com
$config["dom_avail_tlds"] = array("com","net","org","info","biz");

//max registration period
$config["max_reg_period"] = 10; //in years

//list of default name servers
$config["ns_joker_default"] = array(
			
			array(	"ip"	=> "194.176.0.2",
				"host"	=> "a.ns.joker.com",
			),
			array(	"ip"	=> "194.245.101.19",
				"host"	=> "b.ns.joker.com",
			),
			array(	"ip"	=> "194.245.50.1",
				"host"	=> "c.ns.joker.com",
			),
);
//minimum number of nameservers to proceed with registration etc.
$config["ns_min_num"] = 2;

//service emails
$config["redemption_email"] = "christo@nrw.net";
//$config["redemption_email"] = "redemption@joker.com";
//dmapi multi purpose email
$config["dmapi_mp_email"] = "noreply@dmapi.com";

//parsing specifics
$config["empty_result"] = "nothing";
$config["empty_field"] = "[empty]";

$config["no_content"] = "none";

//logfile config
$config["log_dir"] = "../log";
$config["run_log"] = true;
$config["log_filename"] = "dmapi.log";
$config["log_msg"] =	
		array(
			"i"	=> "INFO",
			"w"	=> "WARNING",
			"e"	=> "ERROR",
			"u"	=> "UNKNOWN"
		);
$config["log_default_msg"] = "u";	


// profile values
$config["unknown_field_size"] = 80;

// profile for com domains
$config["domain"]["com"]["contact"]["fields"] =

array(	"name"		=> array(
			    "required" => "n"
			    ),
	"fname"		=> array(
			    "size" => 80,
			    "required" => "y"
			    ),
	"lname"		=> array(
			    "size" => 80,
			    "required" => "y"
			    ),
	"title"		=> array(
			    "size" => 80,
			    "required" => "opt"
			    ),
	"individual"	=> array(
			    "size" => 3,
			    "required" => "opt"
			    ),
	"organization"	=> array(
			    "size" => 80,
			    "required" => "y"
			    ),
	"email"		=> array(
			    "size" => 255,
			    "required" => "y"
			    ),
	"address-1"	=> array(
			    "size" => 80,
			    "required" => "y"
			    ),
	"address-2"	=> array(
			    "size" => 80,
			    "required" => "opt"
			    ),
	"address-3"	=> array(
			    "size" => 80,
			    "required" => "opt"
			    ),
	"city"		=> array(
			    "size" => 80,
			    "required" => "y"
			    ),
	"state"		=> array(
			    "size" => 80,
			    "required" => "opt"
			    ),
	"postal-code"	=> array(
			    "size" => 50,
			    "required" => "y"
			    ),
	"country"	=> array(
			    "size" => 2,
			    "required" => "y"
			    ),
	"phone"		=> array(
			    "size" => 50,
			    "required" => "y"
			    ),
	"extension"	=> array(
			    "size" => 10,
			    "required" => "opt"
			    ),
	"fax"		=> array(
			    "size" => 50,
			    "required" => "opt"
			    ),
);

// profile for net domains
$config["domain"]["net"] = $config["domain"]["com"];

// profile for org domains
$config["domain"]["org"]["contact"]["fields"] =

array(	"name"		=> array(
			    "size" => 255,
			    "required" => "y"
			    ),
	"fname"		=> array(
			    "required" => "n"
			    ),
	"lname"		=> array(
			    "required" => "n"
			    ),
	"title"		=> array(
			    "size" => $config["unknown_field_size"],
			    "required" => "opt"
			    ),
	"individual"	=> array(
			    "size" => 3,
			    "required" => "opt"
			    ),
	"organization"	=> array(
			    "size" => 255,
			    "required" => "y"
			    ),
	"email"	=> array(
			    "size" => 255,
			    "required" => "y"
			    ),
	"address-1"	=> array(
			    "size" => 255,
			    "required" => "y"
			    ),
	"address-2"	=> array(
			    "size" => 255,
			    "required" => "opt"
			    ),
	"address-3"	=> array(
			    "size" => 255,
			    "required" => "opt"
			    ),
	"city"		=> array(
			    "size" => 100,
			    "required" => "y"
			    ),
	"state"		=> array(
			    "size" => 100,
			    "required" => "opt"
			    ),
	"postal-code"	=> array(
			    "size" => 50,
			    "required" => "y"
			    ),
	"country"	=> array(
			    "size" => 2,
			    "required" => "y"
			    ),
	"phone"		=> array(
			    "size" => 20,
			    "required" => "y"
			    ),
	"extension"	=> array(
			    "size" => 10,
			    "required" => "opt"
			    ),
	"fax"		=> array(
			    "size" => 20,
			    "required" => "opt"
			    ),
);

// profile for org domains
$config["domain"]["info"] = $config["domain"]["org"];
$config["domain"]["biz"] = $config["domain"]["org"];

$config["domain"]["de"]["contact"]["fields"] =

array(	"name"		=> array(
			    "size" => 255,
			    "required" => "y"
			    ),
	"fname"		=> array(
			    "required" => "n"
			    ),
	"lname"		=> array(
			    "required" => "n"
			    ),
	"title"		=> array(
			    "size" => $config["unknown_field_size"],
			    "required" => "opt"
			    ),
	"individual"	=> array(
			    "size" => 3,
			    "required" => "opt"
			    ),
	"organization"	=> array(
			    "required" => "n"
			    ),
	"email"	=> array(
			    "size" => 255,
			    "required" => "y"
			    ),
	"address-1"	=> array(
			    "size" => 255,
			    "required" => "y"
			    ),
	"address-2"	=> array(
			    "size" => 255,
			    "required" => "opt"
			    ),
	"address-3"	=> array(
			    "size" => 255,
			    "required" => "opt"
			    ),
	"city"		=> array(
			    "size" => 100,
			    "required" => "y"
			    ),
	"state"		=> array(
			    "size" => 100,
			    "required" => "opt"
			    ),
	"postal-code"	=> array(
			    "size" => 50,
			    "required" => "y"
			    ),
	"country"	=> array(
			    "size" => 2,
			    "required" => "y"
			    ),
	"phone"		=> array(
			    "size" => 20,
			    "required" => "y"
			    ),
	"extension"	=> array(
			    "size" => 10,
			    "required" => "opt"
			    ),
	"fax"		=> array(
			    "size" => 20,
			    "required" => "opt"
			    ),
);

?>