<?php

/**
 * Configuration area for the entire site
 * Class properties could be configured from this script.
 * Please edit with care!
 *
 */

####### BEGIN General Section #########################

//site specifics
$jpc_config["rpanel_ver"] = "1.1-stable";
//specify "win" for windows, "lnx" for *nix server
$jpc_config["dmapi_server_os"] = "win";
$jpc_config["site_encoding"] = "utf-8";
$jpc_config["site_form_action"] = "index.php";
$jpc_config["site_default_language"] = "en";
//to be removed at a later stage
$jpc_config["site_allowed_languages"] = array("en");
//remote server URL - pointing to the beta version - comment it to use the production DMAPI
$jpc_config["dmapi_url"] = "https://beta.dmapi.joker.com";
//uncomment to use the production DMAPI
//$jpc_config["dmapi_url"] = "https://dmapi.joker.com";
$jpc_config["joker_url"] = "https://joker.com/";
//these two options are relevant for cls_connect.php and more precisely 
//for the curl library - useful if you run several virtual servers on different IPs
$jpc_config["set_outgoing_network_interface"] = false;
//$jpc_config["outgoing_network_interface"] will be used only if $jpc_config["set_outgoing_network_interface"] = true
$jpc_config["outgoing_network_interface"] = $GLOBALS["HTTP_SERVER_VARS"]["SERVER_ADDR"];
//default tld in case of error
$jpc_config["default_tld"] = "com";
//max registration period
$jpc_config["max_reg_period"] = 10; //in years
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
$jpc_config["result_list_def_rows"] = 20;

//filename of result list reports
$jpc_config["result_list_filename"] = "results";

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

// profile for org domains
$jpc_config["domain"]["org"]["contact"]["fields"] =

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

// profile for the rest of domains domains
$jpc_config["domain"]["com"] = $jpc_config["domain"]["org"];
$jpc_config["domain"]["net"] = $jpc_config["domain"]["org"];
$jpc_config["domain"]["info"] = $jpc_config["domain"]["org"];
$jpc_config["domain"]["biz"] = $jpc_config["domain"]["org"];
$jpc_config["domain"]["us"] = $jpc_config["domain"]["org"];
$jpc_config["domain"]["cn"] = $jpc_config["domain"]["org"];

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


####### END Profile Section #########################

?>
