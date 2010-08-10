<?php

/**
 * Configuration area for the entire site
 * Class properties could be configured from this script.
 * Please edit with care!
 *
 */

####### BEGIN General Section #########################

//site specifics
$jpc_config["rpanel_ver"] = "1.62";
//specify "win" for windows, "lnx" for *nix server
//Note: OS of the server hosting this application. NOT the server providing DMAPI to you.
$jpc_config["dmapi_server_os"] = "lnx";
//DMAPI session will be destroyed after the specified period of inactivity (no request sent).
//This timeout is defined by the DMAPI service and the correct value can be 
//taken from the documentation. Right now it is 60 minutes. Please specify in minutes.
//It will be used to log you out of the application as the DMAPI service will anyway ask you 
//to authorize again. Could be modified to keep you always logged in (not programmed yet), 
//but due to security considerations that is not available.
$jpc_config["dmapi_inactivity_timeout"] = 60; //minutes
$jpc_config["site_encoding"] = "utf-8";
$jpc_config["site_form_action"] = "index.php";
$jpc_config["site_default_language"] = "en";
//to be removed at a later stage
$jpc_config["site_allowed_languages"] = array("en");
//remote server URL - pointing to the OT&E version - comment it to use the production DMAPI
//$jpc_config["dmapi_url"] = "https://dmapi.ote.joker.com";
//uncomment to use the production DMAPI
$jpc_config["dmapi_url"] = "https://dmapi.joker.com";
$jpc_config["joker_url"] = "https://joker.com/";
//make OT&E visible
$jpc_config["rpanel_background"] = ""; //"img/background_devel.gif";
//these two options are relevant for cls_connect.php and more precisely 
//for the curl library - useful if you run several virtual servers on different IPs
$jpc_config["set_outgoing_network_interface"] = false;
//$jpc_config["outgoing_network_interface"] will be used only if $jpc_config["set_outgoing_network_interface"] = true
$jpc_config["outgoing_network_interface"] = $_SERVER["SERVER_ADDR"];
//$jpc_config["curlopt_connecttimeout"] - The number of seconds to wait whilst trying to connect. Use 0 to wait indefinitely.
$jpc_config["curlopt_connecttimeout"] = 5;
//$jpc_config["curlopt_timeout"] - The maximum number of seconds to allow cURL functions to execute.
$jpc_config["curlopt_timeout"] = 60;
//$jpc_config["curlexec_proceed"] - useful to suppress the submission of a request. This is useful for 
//testing functions that can cost you money :-). The request string is written in the logfiles but is not executed.
$jpc_config["curlexec_proceed"] = true;
//$jpc_config["idn_compatibility"] enables the automatic conversion of IDNs. The conversion is done from a third party code.
//Due to this the correctness of the results from the conversion cannot be guaranteed, but it is highly unprobable that you get wrong results.
//If you switch compatibility off, then you will get errors when typing domains in your native language. Their presentation
//will be also as is, which means PUNYCODE (ASCII).
$jpc_config["idn_compatibility"]= true;
//default tld in case of error
$jpc_config["default_tld"] = "com";
//max registration period
$jpc_config["max_reg_period"] = 10; //in years
//domain list caching period
$jpc_config["dom_list_caching_period"]  = 1800; //in seconds
//zone list caching period
$jpc_config["zone_list_caching_period"] = 1800; //in seconds
//contact list caching period
$jpc_config["cnt_list_caching_period"]  = 1800; //in seconds
//nameserver list caching period
$jpc_config["ns_list_caching_period"]   = 1800; //in seconds
//list of default name servers
$jpc_config["ns_joker_default"] = array(

            array(
                "ip"    => "194.176.0.2",
                "host"  => "a.ns.joker.com",
            ),
            array(
                "ip"    => "194.245.101.19",
                "host"  => "b.ns.joker.com",
            ),
            array(
                "ip"    => "194.245.50.1",
                "host"  => "c.ns.joker.com",
            )
);
//minimum number of nameservers to proceed with registration etc.
$jpc_config["ns_min_num"] = 2;
//service emails
$jpc_config["redemption_email"] = "redemption@joker.com";
//transfer emails
$jpc_config["transfer_email"] = "transfer@joker.com";
//dmapi multi purpose email
$jpc_config["dmapi_mp_email"] = "info@joker.com";
//Joker.com session name
$jpc_config["joker_session_name"] = "Joker_Session";
//Joker.com session duration (in minutes)
$jpc_config["joker_session_duration"] = 90;
//Joker.com session domain
$jpc_config["joker_session_domain"] = ".joker.com";
//session needs a magic word for generating a session id in Joker.com
//could be changed to any string
$jpc_config["magic_session_word"] = "Fm435rjsdFk";
//parsing specifics
$jpc_config["empty_result"] = "nothing";
$jpc_config["no_content"] = "none";
$jpc_config["empty_field_value"] = "[empty]";

####### END General Section #########################

####### BEGIN Log Section ###########################

//logfile config
//you have to set the correct directory here - be carefull to use
//path corresponding to your OS
$jpc_config["log_dir"] = "../log"; //one level above the document root
//$jpc_config["log_dir"] = "d:\\www\\dmapi\\log";
$jpc_config["run_log"] = true;
$jpc_config["debug"] = 0; //1=log, 2=log & print
$jpc_config["log_file_perm"] = "0750";
$jpc_config["log_filename"] = "dmapi";
$jpc_config["log_msg"] =
        array(
            "i" => "INFO",
            "w" => "WARNING",
            "e" => "ERROR",
            "u" => "UNKNOWN"
        );
$jpc_config["log_default_msg"] = "u";
//field values which should be hidden in the logs
$jpc_config["hide_field_values"] =
        array(
            "password",
            "p_password",
            "Joker_Session"
        );
//field values which should be hidden in the logs
//will be substituted with this string
$jpc_config["hide_value_text"] = "********";

####### END Log Section #############################

####### BEGIN Result List Section ###################

//result list - array with the possible number of rows per page
$jpc_config["result_list_rows"] =
        array(
            20,
            50,
            100
        );
//result list - default number of rows per page
$jpc_config["result_list_def_rows"] = 15;

//filename of result list reports
$jpc_config["result_list_filename"] = "results";

//date format for results
$jpc_config["date_format_results"] = "Y-m-d H:i:s";

####### END Result List Section #####################

####### BEGIN Temp Directory Section ################

//name of the temp directory
$jpc_config["temp_dir"] = "../tmp"; //one level above the document root
//$jpc_config["temp_dir"] = "d:\\www\\dmapi\\tmp"; //one level above the document root
$jpc_config["temp_file_perm"] = "0750";

####### END Temp Directory Section ##################

####### BEGIN Template Directory Section ############

//name of the template directory
$jpc_config["tpl_dir"] = "../tpl"; //one level above the document root
//flag whether the template engine should halt on error
$jpc_config["tpl_halt_on_error"] = "on";
//template cleanup mode on|off
$jpc_config["tpl_cleanup_mode"] = "off";

####### END Template Directory Section ##############

####### BEGIN Profile Section #######################
// profile values
$jpc_config["unknown_field_size"] = 80;

// profile for most domains - EPP
$jpc_config["domain"]["default"]["contact"]["fields"] =

array(  
    "name"      => array(
                "size" => 255,
                "required" => true
                ),
    "title"     => array(
                "size" => $jpc_config["unknown_field_size"],
                "required" => false
                ),
    "individual"    => array(
                "size" => 3,
                "required" => true
                ),
    "organization"  => array(
                "size" => 255,
                "required" => true
                ),
    "email" => array(
                "size" => 255,
                "required" => true
                ),
    "address-1" => array(
                "size" => 255,
                "required" => true
                ),
    "address-2" => array(
                "size" => 255,
                "required" => false
                ),
    "address-3" => array(
                "size" => 255,
                "required" => false
                ),
    "city"      => array(
                "size" => 100,
                "required" => true
                ),
    "state"     => array(
                "size" => 100,
                "required" => false
                ),
    "postal-code"   => array(
                "size" => 50,
                "required" => true
                ),
    "country"   => array(
                "size" => 2,
                "required" => true
                ),
    "phone"     => array(
                "size" => 20,
                "required" => true
                ),
    "extension" => array(
                "size" => 10,
                "required" => false
                ),
    "fax"       => array(
                "size" => 20,
                "required" => false
                )
);

$jpc_config["domain"]["de"]["contact"]["fields"] =

array(  
    "name"      => array(
                "size" => 255,
                "required" => true
                ),
    "title"     => array(
                "size" => $jpc_config["unknown_field_size"],
                "required" => false
                ),
    "individual"    => array(
                "size" => 3,
                "required" => true
                ),
    "organization"  => array(
                "size" => 255,
                "required" => true
                ),
    "email" => array(
                "size" => 255,
                "required" => true
                ),
    "address-1" => array(
                "size" => 255,
                "required" => true
                ),
    "address-2" => array(
                "size" => 255,
                "required" => false
                ),
    "address-3" => array(
                "size" => 255,
                "required" => false
                ),
    "city"      => array(
                "size" => 100,
                "required" => true
                ),
    "state"     => array(
                "size" => 100,
                "required" => false
                ),
    "postal-code"   => array(
                "size" => 50,
                "required" => true
                ),
    "country"   => array(
                "size" => 2,
                "required" => true
                ),
    "phone"     => array(
                "size" => 20,
                "required" => true
                ),
    "extension" => array(
                "size" => 10,
                "required" => false
                ),
    "fax"       => array(
                "size" => 20,
                "required" => true
                )
);

$jpc_config["domain"]["eu"]["contact"]["fields"] =

array(
    "language"  => array(
                "size" => 2,
                "required" => true
                ),
    "name"      => array(
                "size" => 50,
                "required" => true
                ),
    "organization"  => array(
                "size" => 100,
                "required" => true
                ),
    "title"     => array(
                "size" => $jpc_config["unknown_field_size"],
                "required" => false
                ),
    "individual"    => array(
                "size" => 3,
                "required" => true
                ),
    "email" => array(
                "size" => 255,
                "required" => true
                ),
    "address-1" => array(
                "size" => 80,
                "required" => true
                ),
    "address-2" => array(
                "size" => 80,
                "required" => false
                ),
    "address-3" => array(
                "size" => 255,
                "required" => false
                ),
    "city"      => array(
                "size" => 80,
                "required" => true
                ),
    "state"     => array(
                "size" => 80,
                "required" => false
                ),
    "postal-code"   => array(
                "size" => 16,
                "required" => true
                ),
    "country"   => array(
                "size" => 2,
                "required" => true
                ),
    "phone"     => array(
                "size" => 17,
                "required" => true
                ),
    "extension" => array(
                "size" => 10,
                "required" => false
                ),
    "fax"       => array(
                "size" => 17,
                "required" => false
                )
);

$jpc_config["domain"]["us"]["contact"]["fields"] =

array(  
    "name"      => array(
                "size" => 255,
                "required" => true
                ),
    "title"     => array(
                "size" => $jpc_config["unknown_field_size"],
                "required" => false
                ),
    "individual"    => array(
                "size" => 3,
                "required" => true
                ),
    "organization"  => array(
                "size" => 255,
                "required" => true
                ),
    "email" => array(
                "size" => 255,
                "required" => true
                ),
    "address-1" => array(
                "size" => 255,
                "required" => true
                ),
    "address-2" => array(
                "size" => 255,
                "required" => false
                ),
    "address-3" => array(
                "size" => 255,
                "required" => false
                ),
    "city"      => array(
                "size" => 100,
                "required" => true
                ),
    "state"     => array(
                "size" => 100,
                "required" => false
                ),
    "postal-code"   => array(
                "size" => 50,
                "required" => true
                ),
    "country"   => array(
                "size" => 2,
                "required" => true
                ),
    "phone"     => array(
                "size" => 20,
                "required" => true
                ),
    "extension" => array(
                "size" => 10,
                "required" => false
                ),
    "fax"       => array(
                "size" => 20,
                "required" => false
                ),
    "app-purpose"   => array(
                "size" => 2,
                "required" => true
                ),
    "nexus-category"   => array(
                "size" => 3,
                "required" => true
                ),
    "nexus-category-country"   => array(
                "size" => 2,
                "required" => true
                )
);


####### END Profile Section #########################

?>
