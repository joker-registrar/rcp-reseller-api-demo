<?php

/*
 * Container for regular expressions and their corresponding error messages
 * Note that the error message could be overridden at a later stage in the code
 */

$error_regexp = array(

		"_username"         => '/^[\x20-\x7e]{6,255}$/',
		"_password"         => '/^[\x20-\x7e]{4,18}$/',
		"_auth_failed"      => "",		
		"_srv_req_failed"   => "",
		"_domain"           => "",
		"_tld"              => '/^[.a-z]{2,}$/i',
		"_sld"              => '/^([a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?){1}$/i',
		"_host"             => '/^(([-a-z0-9_]{1,63}\.)*[-a-z0-9_]{1,63})?$/i',
		"_ns"               => "",
		"_ns_select"        => "",
		"_ns_min"           => "",
		"_dom_status"       => "",
		"_email"            => '/^[-+.a-z0-9_=&]+$/i',
		"_ipv4"             => '/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/',
		"_ipv6"             => '/^([0-9a-f]{0,4}:){2,7}[0-9a-f]{0,4}$/i',
		"_contact_hdl"      => "",
		"_contact_hdl_type" => "",
		"_domain_reg_period"=> '/[1-9]|10/',
		"_name"             => "/[\x20-\x7e]/i",
		"_ascii_string"     => '/^[ \#\&\(\)\+\,\-\.\/\x30-\x39\x40-\x5a_\x61-\x7a]+$/',
		"_utf8_string"      => '/^[^\x00-\x1f\!\"\$\%\'\*\:\;\[\]\<\=\>\?\{\}\\\\\^\`\|\~\x7f]+$/',
		"_overall_text"     => "/[\x20-\x7e]/i",
		"_individual"       => "/^\bYes\b|\bY\b|\bNo\b|\bN\b$/i",
		"_is_individual"    => "/\bYes\b|\bY\b/i",
		"_invalid_chars_in_field"       => "",
		"_invalid_chars_in_opt_field"   => "",
		"_invalid_field_length"         => "",
		"_svtrid"	    => '/^[a-z0-9]+$/i',
		"_auth_id"	    => '/^([\x20-\x7e]{6,32})|none$/i',
		"_app_purpose"      => '/p1|p2|p3|p4|p5/i',
		"_nexus_category"   => '/c11|c12|c21|c31|c32/i',
                "_account_type"     => '/^(ltd|plc|ptnr|stra|llp|ip|ind|sch|rchar|gov|crc|stat|other|find|fcorp|fother)$/i',
                "_company_number_required"   => '/^(ltd|plc|llp|ip|sch|rchar)$/i',
		"_sess_expired"     => "",
		"_com_tld"	    => "/^CCOM\-[0-9]+$/i",
                "_net_tld"	    => "/^CNET\-[0-9]+$/i",
                "_org_tld"	    => "/^CORG\-[0-9]+$/i",
                "_info_tld"	    => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
                "_biz_tld"	    => "/^CNEU\-[0-9]+$|^NEUL\-[0-9]+$|^RDNA\-[0-9]+$/i",
                "_us_tld"	    => "/^COUS\-[0-9]+$/i",
                "_de_tld"	    => "/^CODE\-[0-9]+$/i",
                "_cn_tld"	    => "/^COCN\-[0-9]+$/i",
                "_eu_tld"	    => "/^COEU\-[0-9]+$|^C[0-9]+$/i",
                "_me_tld"	    => "/^COME\-[0-9]+$/i",
                "_co.uk_tld"	    => "/^COUK\-[0-9]+$/i",
                "_me.uk_tld"	    => "/^COUK\-[0-9]+$/i",
                "_org.uk_tld"	    => "/^COUK\-[0-9]+$/i",
                "_mobi_tld"	    => "/^COMO\-[0-9]+$/i",
                "_tel_tld"	    => "/^CTEL\-[0-9]+$/i",
                "_name_tld"         => "/^CNAM\-[0-9]+$/i",
                "_ag_tld"   	    => "/^CGRS\-[0-9]+$/i",
                "_asia_tld"   	    => "/^COAS\-[0-9]+$/i",
                "_bz_tld"           => "/^CGRS\-[0-9]+$/i",
                "_hn_tld"           => "/^CGRS\-[0-9]+$/i",
                "_lc_tld"           => "/^CGRS\-[0-9]+$/i",
                "_mn_tld"           => "/^CGRS\-[0-9]+$/i",
                "_sc_tld"   	    => "/^CGRS\-[0-9]+$/i",
                "_vc_tld"           => "/^CGRS\-[0-9]+$/i",
                "_at_tld"	    => "/^COAT\-[A-Z]{0,4}[0-9]+$/i",
                "_tv_tld"           => "/^CONS\-[0-9]+$/i",
                "_cc_tld"           => "/^CONS\-[0-9]+$/i",
                "_xxx_tld"          => "/^CONX\-[0-9]+$/i",
                "_pw_tld"           => "/^CCEN\-[0-9]+$/i",
                "_nl_tld"           => "/^CONL\-[A-Z]{3}[0-9]{6}\-[A-Z]{5}$/i",
                "_de.com_tld"       => "/^CCEN\-[0-9]+$/i",
                "_com.de_tld"       => "/^CMUL\-[0-9]+$/i",
                "_co_tld"           => "/^COCO\-[0-9]+$/i",
                "_com.co_tld"       => "/^COCO\-[0-9]+$/i",
                "_net.co_tld"       => "/^COCO\-[0-9]+$/i",
                "_nam.co_tld"       => "/^COCO\-[0-9]+$/i",
                "_ruhr_tld"       => "/^COTA\-[0-9]+$/i",
                "_berlin_tld"       => "/^COTB[0-9]+$/i",
                "_email_tld"       => "/^CODO[0-9]+$/i",
                "_bike_tld"       => "/^CODO[0-9]+$/i",
                "_guru_tld"       => "/^CODO[0-9]+$/i",
                "_solar_tld"       => "/^CODO[0-9]+$/i",
                "_coffee_tld"       => "/^CODO[0-9]+$/i",
                "_international_tld"   => "/^CODO[0-9]+$/i",
                "_florist_tld"       => "/^CODO[0-9]+$/i",
                "_tips_tld"       => "/^CODO[0-9]+$/i",
                "_house_tld"       => "/^CODO[0-9]+$/i",
                "_florist_tld" => "/^CODO[0-9]+$/i",
                "_tips_tld" => "/^CODO[0-9]+$/i",
                "_ventures_tld" => "/^CODO[0-9]+$/i",
                "_singles_tld" => "/^CODO[0-9]+$/i",
                "_holdings_tld" => "/^CODO[0-9]+$/i",
                "_plumbing_tld" => "/^CODO[0-9]+$/i",
                "_clothing_tld" => "/^CODO[0-9]+$/i",
                "_camera_tld" => "/^CODO[0-9]+$/i",
                "_equipment_tld" => "/^CODO[0-9]+$/i",
                "_estate_tld" => "/^CODO[0-9]+$/i",
                "_gallery_tld" => "/^CODO[0-9]+$/i",
                "_graphics_tld" => "/^CODO[0-9]+$/i",
                "_lighting_tld" => "/^CODO[0-9]+$/i",
                "_photography_tld" => "/^CODO[0-9]+$/i",
                "_contractors_tld" => "/^CODO[0-9]+$/i",
                "_land_tld" => "/^CODO[0-9]+$/i",
                "_technology_tld" => "/^CODO[0-9]+$/i",
                "_construction_tld" => "/^CODO[0-9]+$/i",
                "_directory_tld" => "/^CODO[0-9]+$/i",
                "_kitchen_tld" => "/^CODO[0-9]+$/i",
                "_today_tld" => "/^CODO[0-9]+$/i",
                "_diamonds_tld" => "/^CODO[0-9]+$/i",
                "_enterprises_tld" => "/^CODO[0-9]+$/i",
                "_voyage_tld" => "/^CODO[0-9]+$/i",
                "_shoes_tld" => "/^CODO[0-9]+$/i",
                "_careers_tld" => "/^CODO[0-9]+$/i",
                "_photos_tld" => "/^CODO[0-9]+$/i",
                "_recipes_tld" => "/^CODO[0-9]+$/i",
                "_limo_tld" => "/^CODO[0-9]+$/i",
                "_domains_tld" => "/^CODO[0-9]+$/i",
                "_cab_tld" => "/^CODO[0-9]+$/i",
                "_company_tld" => "/^CODO[0-9]+$/i",
                "_computer_tld" => "/^CODO[0-9]+$/i",
                "_center_tld" => "/^CODO[0-9]+$/i",
                "_systems_tld" => "/^CODO[0-9]+$/i",
                "_academy_tld" => "/^CODO[0-9]+$/i",
                "_management_tld" => "/^CODO[0-9]+$/i",
                "_training_tld" => "/^CODO[0-9]+$/i",
                "_solutions_tld" => "/^CODO[0-9]+$/i",
                "_support_tld" => "/^CODO[0-9]+$/i",
                "_builders_tld" => "/^CODO[0-9]+$/i",
                "_education_tld" => "/^CODO[0-9]+$/i",
                "_institute_tld" => "/^CODO[0-9]+$/i",
                "_repair_tld" => "/^CODO[0-9]+$/i",
                "_camp_tld" => "/^CODO[0-9]+$/i",
                "_glass_tld" => "/^CODO[0-9]+$/i",
                "_holiday_tld" => "/^CODO[0-9]+$/i",
                "_marketing_tld" => "/^CODO[0-9]+$/i",
                "_viajes_tld" => "/^CODO[0-9]+$/i",
                "_farm_tld" => "/^CODO[0-9]+$/i",
                "_codes_tld" => "/^CODO[0-9]+$/i",
                "_blue_tld" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
                "_shiksha_tld" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
                "_kim_tld" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
                "_red_tld" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
                "_pink_tld" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
                "_cheap_tld" => "/^CODO[0-9]+$/i",
                "_zone_tld" => "/^CODO[0-9]+$/i",
                "_agency_tld" => "/^CODO[0-9]+$/i",
                "_bargains_tld" => "/^CODO[0-9]+$/i",
                "_boutique_tld" => "/^CODO[0-9]+$/i",
                "_cool_tld" => "/^CODO[0-9]+$/i",
                "_watch_tld" => "/^CODO[0-9]+$/i",
                "_works_tld" => "/^CODO[0-9]+$/i",
                "_expert_tld" => "/^CODO[0-9]+$/i",
                "_wiki_tld" => "/^CCEN\-[0-9]+$/i",
                "_villas_tld" => "/^CODO[0-9]+$/i",
                "_flights_tld" => "/^CODO[0-9]+$/i",
                "_rentals_tld" => "/^CODO[0-9]+$/i",
                "_cruises_tld" => "/^CODO[0-9]+$/i",
                "_vacations_tld" => "/^CODO[0-9]+$/i",
                "_xyz_tld" => "/^CCEN\-[0-9]+$/i",
                "_condos_tld" => "/^CODO[0-9]+$/i",
                "_properties_tld" => "/^CODO[0-9]+$/i",
                "_maison_tld" => "/^CODO[0-9]+$/i",
                "_tienda_tld" => "/^CODO[0-9]+$/i",
);
// 		"_auth_id"	    => '/^[\x20-\x7e]*$/i',

?>
