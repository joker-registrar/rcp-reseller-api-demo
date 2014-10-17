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
		"_sess_expired"     => "",

		// TLD configuration
"_com_tld" => "/^CCOM\-[0-9]+$|^CCOM\-TRANSFER$/i",
"_net_tld" => "/^CNET\-[0-9]+$/i",
"_org_tld" => "/^CORG\-[0-9]+$/i",
"_info_tld" => "/^C[0-9]+\-LRMS$|^CAFI\-[0-9]+$/i",
"_biz_tld" => "/^CNEU\-[0-9]+$|^NEUL\-[0-9]+$|^RDNA\-[0-9]+$/i",
"_de_tld" => "/^CODE\-[0-9]+$/i",
"_uk_tld" => "/^COUK\-[0-9]+$/i",
"_co.uk_tld" => "/^COUK\-[0-9]+$/i",
"_me.uk_tld" => "/^COUK\-[0-9]+$/i",
"_org.uk_tld" => "/^COUK\-[0-9]+$/i",
"_eu_tld" => "/^COEU\-[0-9]+$|^C[0-9]+$/i",
"_be_tld" => "/^COBE\-c[0-9]+$/i",
"_at_tld" => "/^COAT\-[A-Z]{0,4}[0-9]+$/i",
"_me_tld" => "/^COME\-[0-9]+$/i",
"_asia_tld" => "/^COAS\-[0-9]+$/i",
"_us_tld" => "/^COUS\-[0-9]+$/i",
"_cn_tld" => "/^COCN\-[0-9]+$/i",
"_mobi_tld" => "/^COMO\-[0-9]+$/i",
"_tel_tld" => "/^CTEL\-[0-9]+$/i",
"_name_tld" => "/^CNAM\-[0-9]+$/i",
"_ag_tld" => "/^CGRS\-[0-9]+$/i",
"_bz_tld" => "/^CGRS\-[0-9]+$/i",
"_hn_tld" => "/^CGRS\-[0-9]+$/i",
"_mn_tld" => "/^CGRS\-[0-9]+$/i",
"_sc_tld" => "/^CGRS\-[0-9]+$/i",
"_lc_tld" => "/^CGRS\-[0-9]+$/i",
"_vc_tld" => "/^CGRS\-[0-9]+$/i",
"_tv_tld" => "/^CONS\-[0-9]+$/i",
"_cc_tld" => "/^CONS\-[0-9]+$/i",
"_nl_tld" => "/^CONL\-[A-Z]{3}[0-9]{6}\-[A-Z]{5}$/i",
"_co.at_tld" => "/^COAT\-[A-Z]{0,4}[0-9]+$/i",
"_or.at_tld" => "/^COAT\-[A-Z]{0,4}[0-9]+$/i",
"_xxx_tld" => "/^CONX\-[0-9]+$/i",
"_pw_tld" => "/^CCEN\-[0-9]+$/i",
"_de.com_tld" => "/^CCEN\-[0-9]+$/i",
"_com.de_tld" => "/^CMUL\-[0-9]+$/i",
"_co_tld" => "/^COCO\-[0-9]+$/i",
"_com.co_tld" => "/^COCO\-[0-9]+$/i",
"_net.co_tld" => "/^COCO\-[0-9]+$/i",
"_nam.co_tld" => "/^COCO\-[0-9]+$/i",
"_ruhr_tld" => "/^COTA\-[0-9]+$/i",
"_berlin_tld" => "/^COTB[0-9]+$/i",
"_bike_tld" => "/^CODO[0-9]+$/i",
"_guru_tld" => "/^CODO[0-9]+$/i",
"_email_tld" => "/^CODO[0-9]+$/i",
"_coffee_tld" => "/^CODO[0-9]+$/i",
"_solar_tld" => "/^CODO[0-9]+$/i",
"_international_tld" => "/^CODO[0-9]+$/i",
"_house_tld" => "/^CODO[0-9]+$/i",
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
"_productions_tld" => "/^CODO[0-9]+$/i",
"_partners_tld" => "/^CODO[0-9]+$/i",
"_events_tld" => "/^CODO[0-9]+$/i",
"_dating_tld" => "/^CODO[0-9]+$/i",
"_cards_tld" => "/^CODO[0-9]+$/i",
"_catering_tld" => "/^CODO[0-9]+$/i",
"_cleaning_tld" => "/^CODO[0-9]+$/i",
"_community_tld" => "/^CODO[0-9]+$/i",
"_industries_tld" => "/^CODO[0-9]+$/i",
"_parts_tld" => "/^CODO[0-9]+$/i",
"_supplies_tld" => "/^CODO[0-9]+$/i",
"_supply_tld" => "/^CODO[0-9]+$/i",
"_tools_tld" => "/^CODO[0-9]+$/i",
"_jetzt_tld" => "/^CJET\-[0-9]+$/i",
"_fish_tld" => "/^CODO[0-9]+$/i",
"_report_tld" => "/^CODO[0-9]+$/i",
"_vision_tld" => "/^CODO[0-9]+$/i",
"_ink_tld" => "/^CCEN\-[0-9]+$/i",
"_in.net_tld" => "/^CCEN\-[0-9]+$/i",
"_gift_tld" => "/^CUNR-[0-9]+$/i",
"_guitars_tld" => "/^CUNR-[0-9]+$/i",
"_link_tld" => "/^CUNR-[0-9]+$/i",
"_photo_tld" => "/^CUNR-[0-9]+$/i",
"_pics_tld" => "/^CUNR-[0-9]+$/i",
"_sexy_tld" => "/^CUNR-[0-9]+$/i",
"_tattoo_tld" => "/^CUNR-[0-9]+$/i",
"_blackfriday_tld" => "/^CUNR-[0-9]+$/i",
"_christmas_tld" => "/^CUNR-[0-9]+$/i",
"_services_tld" => "/^CODO[0-9]+$/i",
"_dance_tld" => "/^CRIG[0-9]+$/i",
"_democrat_tld" => "/^CRIG[0-9]+$/i",
"_ninja_tld" => "/^CRIG[0-9]+$/i",
"_immobilien_tld" => "/^CRIG[0-9]+$/i",
"_futbol_tld" => "/^CRIG[0-9]+$/i",
"_reviews_tld" => "/^CRIG[0-9]+$/i",
"_social_tld" => "/^CRIG[0-9]+$/i",
"_pub_tld" => "/^CRIG[0-9]+$/i",
"_bar_tld" => "/^CCEN-[0-9]+$/i",
"_rest_tld" => "/^CCEN-[0-9]+$/i",
"_wien_tld" => "/^CWIE[0-9]+$/i",
"_capital_tld" => "/^CODO[0-9]+$/i",
"_engineering_tld" => "/^CODO[0-9]+$/i",
"_exchange_tld" => "/^CODO[0-9]+$/i",
"_gripe_tld" => "/^CODO[0-9]+$/i",
"_moda_tld" => "/^CRIG[0-9]+$/i",
"_consulting_tld" => "/^CRIG[0-9]+$/i",
"_kaufen_tld" => "/^CRIG[0-9]+$/i",
"_media_tld" => "/^CODO[0-9]+$/i",
"_pictures_tld" => "/^CODO[0-9]+$/i",
"_actor_tld" => "/^CRIG[0-9]+$/i",
"_fail_tld" => "/^CODO[0-9]+$/i",
"_financial_tld" => "/^CODO[0-9]+$/i",
"_limited_tld" => "/^CODO[0-9]+$/i",
"_rocks_tld" => "/^CRIG[0-9]+$/i",
"_wtf_tld" => "/^CODO[0-9]+$/i",
"_care_tld" => "/^CODO[0-9]+$/i",
"_clinic_tld" => "/^CODO[0-9]+$/i",
"_dental_tld" => "/^CODO[0-9]+$/i",
"_surgery_tld" => "/^CODO[0-9]+$/i",
"_cash_tld" => "/^CODO[0-9]+$/i",
"_fund_tld" => "/^CODO[0-9]+$/i",
"_bid_tld" => "/^CBID\-[0-9]+$/i",
"_trade_tld" => "/^CTRA\-[0-9]+$/i",
"_webcam_tld" => "/^CWEB\-[0-9]+$/i",
"_town_tld" => "/^CODO[0-9]+$/i",
"_toys_tld" => "/^CODO[0-9]+$/i",
"_university_tld" => "/^CODO[0-9]+$/i",
"_reisen_tld" => "/^CODO[0-9]+$/i",
"_co.com_tld" => "/^CCEN[0-9]+$/i",
"_investments_tld" => "/^CODO[0-9]+$/i",
"_haus_tld" => "/^CRIG[0-9]+$/i",
"_tax_tld" => "/^CODO[0-9]+$/i",
"_black_tld" => "/^CAFI-[0-9]+$/i",
"_meet_tld" => "/^CAFI-[0-9]+$/i",
"_hamburg_tld" => "/^CHAM[0-9]+$/i",
"_discount_tld" => "/^CODO[0-9]+$/i",
"_fitness_tld" => "/^CODO[0-9]+$/i",
"_furniture_tld" => "/^CODO[0-9]+$/i",
"_schule_tld" => "/^CODO[0-9]+$/i",
"_claims_tld" => "/^CODO[0-9]+$/i",
"_credit_tld" => "/^CODO[0-9]+$/i",
"_creditcard_tld" => "/^CODO[0-9]+$/i",
"_gratis_tld" => "/^CODO[0-9]+$/i",
"_build_tld" => "/^CARI-[0-9]+$/i",
"_exposed_tld" => "/^CODO[0-9]+$/i",
"_foundation_tld" => "/^CODO[0-9]+$/i",
"_associates_tld" => "/^CODO[0-9]+$/i",
"_lease_tld" => "/^CODO[0-9]+$/i",
"_audio_tld" => "/^CUNR-[0-9]+$/i",
"_hiphop_tld" => "/^CUNR-[0-9]+$/i",
"_juegos_tld" => "/^CUNR-[0-9]+$/i",
"_accountants_tld" => "/^CODO[0-9]+$/i",
"_digital_tld" => "/^CODO[0-9]+$/i",
"_finance_tld" => "/^CODO[0-9]+$/i",
"_insure_tld" => "/^CODO[0-9]+$/i",
"_cooking_tld" => "/^CMIN-[0-9]+$/i",
"_country_tld" => "/^CMIN-[0-9]+$/i",
"_fishing_tld" => "/^CMIN-[0-9]+$/i",
"_horse_tld" => "/^CMIN-[0-9]+$/i",
"_rodeo_tld" => "/^CMIN-[0-9]+$/i",
"_vodka_tld" => "/^CMIN-[0-9]+$/i",
"_host_tld" => "/^CCEN-[0-9]+$/i",
"_press_tld" => "/^CCEN-[0-9]+$/i",
"_website_tld" => "/^CCEN-[0-9]+$/i",
"_republican_tld" => "/^CRIG[0-9]+$/i",
"_church_tld" => "/^CODO[0-9]+$/i",
"_guide_tld" => "/^CODO[0-9]+$/i",
"_life_tld" => "/^CODO[0-9]+$/i",
"_loans_tld" => "/^CODO[0-9]+$/i",
"_bayern_tld" => "/^CMIN-[0-9]+$/i",
"_direct_tld" => "/^CODO[0-9]+$/i",
"_place_tld" => "/^CODO[0-9]+$/i",
"_beer_tld" => "/^CMIN-[0-9]+$/i",
"_br.com_tld" => "/^CCEN-[0-9]+$/i",
"_cn.com_tld" => "/^CCEN-[0-9]+$/i",
"_eu.com_tld" => "/^CCEN-[0-9]+$/i",
"_gb.com_tld" => "/^CCEN-[0-9]+$/i",
"_gb.net_tld" => "/^CCEN-[0-9]+$/i",
"_uk.com_tld" => "/^CCEN-[0-9]+$/i",
"_uk.net_tld" => "/^CCEN-[0-9]+$/i",
"_us.com_tld" => "/^CCEN-[0-9]+$/i",
"_uy.com_tld" => "/^CCEN-[0-9]+$/i",
"_hu.com_tld" => "/^CCEN-[0-9]+$/i",
"_no.com_tld" => "/^CCEN-[0-9]+$/i",
"_qc.com_tld" => "/^CCEN-[0-9]+$/i",
"_ru.com_tld" => "/^CCEN-[0-9]+$/i",
"_sa.com_tld" => "/^CCEN-[0-9]+$/i",
"_se.com_tld" => "/^CCEN-[0-9]+$/i",
"_se.net_tld" => "/^CCEN-[0-9]+$/i",
"_za.com_tld" => "/^CCEN-[0-9]+$/i",
"_jpn.com_tld" => "/^CCEN-[0-9]+$/i",
"_ae.org_tld" => "/^CCEN-[0-9]+$/i",
"_kr.com_tld" => "/^CCEN-[0-9]+$/i",
"_la_tld" => "/^CCEN-[0-9]+$/i",
"_ar.com_tld" => "/^CCEN-[0-9]+$/i",
"_us.org_tld" => "/^CCEN-[0-9]+$/i",
"_gr.com_tld" => "/^CCEN-[0-9]+$/i",
"_jp.net_tld" => "/^CCEN-[0-9]+$/i",
"_hu.net_tld" => "/^CCEN-[0-9]+$/i",
"_africa.com_tld" => "/^CCEN-[0-9]+$/i",
"_college_tld" => "/^CCEN-[0-9]+$/i",
"_mex.com_tld" => "/^CCEN-[0-9]+$/i",
"_com.se_tld" => "/^CCEN-[0-9]+$/i",
"_surf_tld" => "/^CMIN-[0-9]+$/i",
"_deals_tld" => "/^CODO[0-9]+$/i",
"_city_tld" => "/^CODO[0-9]+$/i",
"_ltd.uk_tld" => "/^COUK\-[0-9]+$/i",
"_net.uk_tld" => "/^COUK\-[0-9]+$/i",
"_plc.uk_tld" => "/^COUK\-[0-9]+$/i",		
);
// 		"_auth_id"	    => '/^[\x20-\x7e]*$/i',

?>
