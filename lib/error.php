<?php

//here is the place to add regular expressions and their corresponding error messages
//note that the error message could be rewritten at a later stage in the code

$error_array = array(

		"_username" => array(
				"regexp" 	=> '/^[\x20-\x7e]{7,255}$/',
				"err_msg"	=> "Invalid username",
				),
		"_password" => array(
				"regexp" 	=> '/^[\x20-\x7e]{4,18}$/',
				"err_msg"	=> "Invalid password",
				),
		"_auth_failed" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Server error! Authorization failed",
				),
		"_srv_req_failed" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Server error! The request was not processed.",
				),
		"_domain" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Invalid domain name.",
				),
		"_tld" => array(
				"regexp" 	=> '/^[a-z]{2,6}$/i',
				"err_msg"	=> "Invalid top level domain.",
				),
		"_sld" => array(
				"regexp" 	=> '/^([a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?){1}$/i',
				"err_msg"	=> "Invalid second level domain.",
				),
		"_host" => array(
				"regexp" 	=> '/^(([-a-z0-9_]{1,63}\.)*[-a-z0-9_]{1,63})?$/i',
				"err_msg"	=> "Invalid host name.",
				),
		"_ns" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Invalid name server(s).",
				),
		"_ns_select" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Your name server selection is invalid.",
				),
		"_ns_min" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Please, provide at least {NS_MIN_NUM} name servers.",
				),
		"_dom_status" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Invalid status of a domain",
				),				
		"_email" => array(
				"regexp" 	=> '/^[-+.a-z0-9_=&]+$/i',
				"err_msg"	=> "Invalid email.",
				),
		"_ipv4" => array(
				"regexp" 	=> '/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/',
				"err_msg"	=> "Invalid IP.",
				),
		"_ipv6" => array(
				"regexp" 	=> '/^([0-9a-f]{0,4}:){2,7}[0-9a-f]{0,4}$/i',
				"err_msg"	=> "Invalid IPv6.",
				),
		"_contact_hdl" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Invalid contact handle.",
				),
		"_contact_hdl_type" => array(
				"regexp" 	=> "",
				"err_msg"	=> "Check if your contact handle matches the syntax for the specified tld.",
				),
		"_domain_reg_period" => array(
				"regexp" 	=> '/[1-9]|10/',
				"err_msg"	=> "Invalid registration period. It should be provided in months (e.g \"120\" for 10 years). The max. registration is for 10 years.",
				),
//		"_nameserver_list" => array(
//				"regexp" 	=> "/[-a-z0-9_:]/i",
//				"err_msg"	=> "There is an error in your name server list. Note that you should separate every name server entry with a colon (\":\").",
//				),
		"_name" => array(
				"regexp" 	=> "/[\x20-\x7e]/i",
				"err_msg"	=> "The field contains invalid characters.",
				),
		"_overall_text" => array(
				"regexp" 	=> "/[\x20-\x7e]/i",
				"err_msg"	=> "The field contains invalid characters.",
				),
		"_individual" => array(
				"regexp" 	=> "/\bYes\b|\bY\b|\bNo\b|\bN\b/i",
				"err_msg"	=> "The field contains invalid characters.",
				),
		"_is_individual" => array(
				"regexp" 	=> "/\bYes\b|\bY\b/i",
				"err_msg"	=> "Enter \"Yes\" or \"Y\" if you are an individual.",
				),
		"_invalid_chars_in_field" => array(
				"regexp" 	=> "",
				"err_msg"	=> "The field is empty or contains invalid characters.",
				),
		"_invalid_chars_in_opt_field" => array(
				"regexp" 	=> "",
				"err_msg"	=> "The field contains invalid characters.",
				),
		"_invalid_field_length" => array(
				"regexp" 	=> "",
				"err_msg"	=> "The field could be up to {ERROR_FIELD_LENGTH} character long.",
				),
		"_svtrid"	=> array(
				"regexp" 	=> '/^[a-z0-9]+$/i',
				"err_msg"	=> "Invalid SvTrID.",
				),
		"_auth_id"	=> array(
				"regexp" 	=> '/^[\x20-\x7e]*$/i',
				"err_msg"	=> "Invalid AUTH-ID.",
				),
		"_sess_expired"	=> array(
				"regexp" 	=> "",
				"err_msg"	=> "Your session has expired!",
				),

);

?>