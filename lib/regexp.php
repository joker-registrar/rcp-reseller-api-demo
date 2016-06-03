<?php

/*
 * Container for regular expressions and their corresponding error messages
 * Note that the error message could be overridden at a later stage in the code
 */

$error_regexp = array(

		"_username"   => '/^[\x20-\x7e]{6,255}$/',
		"_password"   => '/^[\x20-\x7e]{4,18}$/',
		"_auth_failed"      => "",		
		"_srv_req_failed"   => "",
		"_domain"     => "",
		"_tld"        => '/^[.a-z]{2,}$/i',
		"_sld"        => '/^([a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?){1}$/i',
		"_host"       => '/^(([-a-z0-9_]{1,63}\.)*[-a-z0-9_]{1,63})?$/i',
		"_ns"         => "",
		"_ns_select"  => "",
		"_ns_min"     => "",
		"_dom_status" => "",
		"_email"      => '/^[-+.a-z0-9_=&]+$/i',
		"_ipv4"       => '/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/',
		"_ipv6"       => '/^([0-9a-f]{0,4}:){2,7}[0-9a-f]{0,4}$/i',
		"_contact_hdl"      => "",
		"_contact_hdl_type" => "",
		"_domain_reg_period"=> '/[1-9]|10/',
		"_name"       => "/[\x20-\x7e]/i",
		"_ascii_string"     => '/^[ \#\&\(\)\+\,\-\.\/\x30-\x39\x40-\x5a_\x61-\x7a]+$/',
		"_utf8_string"      => '/^[^\x00-\x1f\!\"\$\%\'\*\:\;\[\]\<\=\>\?\{\}\\\\\^\`\|\~\x7f]+$/',
		"_overall_text"     => "/[\x20-\x7e]/i",
		"_individual" => "/^\bYes\b|\bY\b|\bNo\b|\bN\b$/i",
		"_is_individual"    => "/\bYes\b|\bY\b/i",
		"_invalid_chars_in_field" => "",
		"_invalid_chars_in_opt_field"   => "",
		"_invalid_field_length"   => "",
		"_svtrid"	    => '/^[a-z0-9]+$/i',
		"_auth_id"	    => '/^([\x20-\x7e]{6,32})|none$/i',
		"_app_purpose"      => '/p1|p2|p3|p4|p5/i',
		"_nexus_category"   => '/c11|c12|c21|c31|c32/i',
                "_account_type"     => '/^(ltd|plc|ptnr|stra|llp|ip|ind|sch|rchar|gov|crc|stat|other|find|fcorp|fother)$/i',
                "_company_number_required"   => '/^(ltd|plc|llp|ip|sch|rchar)$/i',
                "_org_id"	    => '/^\[[A-Z]{2}\].{1,123}$/',
                "_vat_id"	    => '/^[A-Z]{2}[a-zA-Z0-9]{1,64}$/',
		"_sess_expired"     => "",
    );

// 		"_auth_id"	    => '/^[\x20-\x7e]*$/i',

$tld_regexp = array(
        "info" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
        "biz" => "/^CNEU\-[0-9]+$|^NEUL\-[0-9]+$|^RDNA\-[0-9]+$/i",
        "eu" => "/^COEU\-[0-9]+$|^C[0-9]+$/i",
        "blue" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
        "shiksha" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
        "kim" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
        "red" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
        "pink" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
    );
?>
