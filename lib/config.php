<?php

/**
 * Class properties could be configured from this scipt.
 * Please edit with care!
 *
 */

####### BEGIN General Section #########################

//site specifics
$config["dmapi_ver"] = "1.0-rc1";
$config["dmapi_encoding"] = "utf-8";
$config["dmapi_form_action"] = "index.php";
$config["dmapi_default_language"] = "eng";
//to be removed at a later stage
$config["dmapi_allowed_languages"] = array("eng","de");
//remote server URL
//$config["joker_url"] = "https://beta.dmapi.joker.com";
$config["joker_url"] = "https://beta.dmapi.joker.com";
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

####### END General Section #########################

####### BEGIN Log Section ###########################

//logfile config
$config["log_dir"] = "../log"; //one level above the document root
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
//field values which should be hidden in the logs
$config["hide_field_values"] =
		array(
			"password"
		);
//field values which should be hidden in the logs
//will be substituted with this string
$config["hide_value_text"] = "********";

####### END Log Section #############################

####### BEGIN Result List Section ###################

//result list - array with the possible number of rows per page
$config["result_list_rows"] =
		array(
			20,
			50,
			100
		);
//result list - default number of rows per page
$config["result_list_def_rows"] = 20;

//filename of result list reports
$config["result_list_filename"] = "results";

####### END Result List Section #####################

####### BEGIN Temp Directory Section ################

//name of the temp directory
$config["temp_dir"] = "../tmp"; //one level above the document root

####### END Temp Directory Section ##################

####### BEGIN Template Directory Section ############

//name of the template directory
$config["tpl_dir"] = "../tpl"; //one level above the document root
//flag whether the template engine should halt on error
$config["tpl_halt_on_error"] = "on";

####### END Template Directory Section ##############

####### BEGIN Profile Section #######################
// profile values
$config["unknown_field_size"] = 80;

// profile for com domains
$config["domain"]["com"]["contact"]["fields"] =

array(	
	"fname"		=> array(
			    "size" => 80,
			    "required" => true
			    ),
	"lname"		=> array(
			    "size" => 80,
			    "required" => true
			    ),
	"title"		=> array(
			    "size" => 80,
			    "required" => false
			    ),
	"individual"	=> array(
			    "size" => 3,
			    "required" => false
			    ),
	"organization"	=> array(
			    "size" => 80,
			    "required" => true
			    ),
	"email"		=> array(
			    "size" => 255,
			    "required" => true
			    ),
	"address-1"	=> array(
			    "size" => 80,
			    "required" => true
			    ),
	"address-2"	=> array(
			    "size" => 80,
			    "required" => false
			    ),
	"address-3"	=> array(
			    "size" => 80,
			    "required" => false
			    ),
	"city"		=> array(
			    "size" => 80,
			    "required" => true
			    ),
	"state"		=> array(
			    "size" => 80,
			    "required" => false
			    ),
	"postal-code"	=> array(
			    "size" => 50,
			    "required" => true
			    ),
	"country"	=> array(
			    "size" => 2,
			    "required" => true
			    ),
	"phone"		=> array(
			    "size" => 50,
			    "required" => true
			    ),
	"extension"	=> array(
			    "size" => 10,
			    "required" => false
			    ),
	"fax"		=> array(
			    "size" => 50,
			    "required" => false
			    ),
);

// profile for net domains
$config["domain"]["net"] = $config["domain"]["com"];

// profile for org domains
$config["domain"]["org"]["contact"]["fields"] =

array(	"name"		=> array(
			    "size" => 255,
			    "required" => true
			    ),
	"title"		=> array(
			    "size" => $config["unknown_field_size"],
			    "required" => false
			    ),
	"individual"	=> array(
			    "size" => 3,
			    "required" => false
			    ),
	"organization"	=> array(
			    "size" => 255,
			    "required" => true
			    ),
	"email"	=> array(
			    "size" => 255,
			    "required" => true
			    ),
	"address-1"	=> array(
			    "size" => 255,
			    "required" => true
			    ),
	"address-2"	=> array(
			    "size" => 255,
			    "required" => false
			    ),
	"address-3"	=> array(
			    "size" => 255,
			    "required" => false
			    ),
	"city"		=> array(
			    "size" => 100,
			    "required" => true
			    ),
	"state"		=> array(
			    "size" => 100,
			    "required" => false
			    ),
	"postal-code"	=> array(
			    "size" => 50,
			    "required" => true
			    ),
	"country"	=> array(
			    "size" => 2,
			    "required" => true
			    ),
	"phone"		=> array(
			    "size" => 20,
			    "required" => true
			    ),
	"extension"	=> array(
			    "size" => 10,
			    "required" => false
			    ),
	"fax"		=> array(
			    "size" => 20,
			    "required" => false
			    ),
);

// profile for org domains
$config["domain"]["info"] = $config["domain"]["org"];
$config["domain"]["biz"] = $config["domain"]["org"];

$config["domain"]["de"]["contact"]["fields"] =

array(	"name"		=> array(
			    "size" => 255,
			    "required" => true
			    ),
	"title"		=> array(
			    "size" => $config["unknown_field_size"],
			    "required" => false
			    ),
	"individual"	=> array(
			    "size" => 3,
			    "required" => false
			    ),
	"email"	=> array(
			    "size" => 255,
			    "required" => true
			    ),
	"address-1"	=> array(
			    "size" => 255,
			    "required" => true
			    ),
	"address-2"	=> array(
			    "size" => 255,
			    "required" => false
			    ),
	"address-3"	=> array(
			    "size" => 255,
			    "required" => false
			    ),
	"city"		=> array(
			    "size" => 100,
			    "required" => true
			    ),
	"state"		=> array(
			    "size" => 100,
			    "required" => false
			    ),
	"postal-code"	=> array(
			    "size" => 50,
			    "required" => true
			    ),
	"country"	=> array(
			    "size" => 2,
			    "required" => true
			    ),
	"phone"		=> array(
			    "size" => 20,
			    "required" => true
			    ),
	"extension"	=> array(
			    "size" => 10,
			    "required" => false
			    ),
	"fax"		=> array(
			    "size" => 20,
			    "required" => false
			    ),
);

####### END Profile Section #########################

?>