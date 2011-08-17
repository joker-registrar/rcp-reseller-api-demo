<?php

/*
 * Main script
 * Dispatches the function calls
 *
 */

//Report all errors except E_NOTICE
error_reporting(E_ALL ^ E_NOTICE);
require_once(dirname(__FILE__)."/../lib/prepend.php");
session_start();

global $tools;
$tools  = new Tools;

if (isset($_SESSION["auth-sid"]) && !$tools->is_too_long_inactive()) {
	$sessid = $_SESSION["auth-sid"];
	$tools->tpl->set_var("T_PATTERN",$_SESSION["userdata"]["t_pattern"]);
	switch($_SESSION["userdata"]["mode"]) {

		case "logout":
		    $user = new User;
			$user->logout();
		    break;

		case "domain_view_form":
		    $domain = new Domain;
			$domain->view_form();
		    break;

		case "domain_view":
		    $domain = new Domain;
			$domain->dispatch("view");
		    break;
	
		case "domain_register_form":
		    $domain = new Domain;
			$domain->register_form();
		    break;

		case "domain_register_overview":
		    $domain = new Domain;
			$domain->dispatch("register_overview");
		    break;

		case "domain_register":
		    $domain = new Domain;
			$domain->dispatch("register");
		    break;

		case "domain_renew_form":
		    $domain = new Domain;
			$domain->renew_form();
		    break;
	
		case "domain_renew":
		    $domain = new Domain;
			$domain->dispatch("renew");
		    break;

		case "domain_grants_form":
		    $domain = new Domain;
			$domain->grants_form();
		    break;

		case "domain_add_grant":
		    $domain = new Domain;
			$domain->dispatch("add_grant");
		    break;

		case "domain_revoke_grant":
		    $domain = new Domain;
			$domain->dispatch("revoke_grant");
		    break;
	
		case "domain_transfer_form":
		    $domain = new Domain;
			$domain->transfer_form();
		    break;
	
		case "domain_transfer":
		    $domain = new Domain;
			$domain->dispatch("transfer");
		    break;
		    
		case "fast_domain_transfer_form":
		    $domain = new Domain;
			$domain->fast_transfer_form();
		    break;
	
		case "fast_domain_transfer":
		    $domain = new Domain;
			$domain->dispatch("fast_transfer");
		    break;
		
		case "domain_bulk_transfer_step1":
		    $domain = new Domain;
			$domain->bulk_transfer_step1();
		    break;
		
		case "domain_bulk_transfer_step2":
		    $domain = new Domain;
			$domain->dispatch("bulk_transfer_step2");
		    break;
				
		case "domain_bulk_transfer_step3":
		    $domain = new Domain;
			$domain->bulk_transfer_step3();
		    break;				
				
		case "domain_modify_form":
		    $domain = new Domain;
			$domain->modify_form();
		    break;
	
		case "domain_modify":
		    $domain = new Domain;
			$domain->dispatch("modify");
		    break;

		case "domain_delete_form":
		    $domain = new Domain;
			$domain->delete_form();
		    break;
	
		case "domain_delete":
		    $domain = new Domain;
			$domain->dispatch("delete");
		    break;
	
		case "domain_owner_change_step1":
		    $domain = new Domain;
			$domain->dispatch("owner_change_step1");			
		    break;
		
		case "domain_owner_change_step2":
		    $domain = new Domain;
			$domain->dispatch("owner_change_step2");
		    break;

                case "domain_grants_change_step1":
		    $domain = new Domain;
			$domain->dispatch("grants_change_step1");
		    break;

		case "domain_grants_change_step2":
		    $domain = new Domain;
			$domain->dispatch("grants_change_step2");
		    break;
	
		case "domain_owner_change":
		    $domain = new Domain;
			$domain->dispatch("owner_change");
		    break;
	
		case "domain_lu_form":
		    $domain = new Domain;
			$domain->lock_unlock_form();
		    break;
		    
		case "domain_lu":
		    $domain = new Domain;
			$domain->dispatch("lock_unlock");
		    break;		
		    
		case "domain_ar_form":
		    $domain = new Domain;
		    $domain->autorenew_form();
		    break;
		    
		case "domain_ar":
		    $domain = new Domain;
		    $domain->dispatch("autorenew");
		    break;		
		    
		case "domain_authid_form":
		    $domain = new Domain;
		    $domain->domain_authid_form();
		    break;
		    
		case "domain_authid":
		    $domain = new Domain;
		    $domain->dispatch("domain_authid");
		    break;
		    
		case "domain_list_form":
		    $domain = new Domain;
			$domain->list_form();
		    break;

		case "domain_list_result":
		    $domain = new Domain;
			$domain->dispatch("list_result");
		    break;
		
		case "domain_list_export":
		    $domain = new Domain;
			$domain->list_export();
		    break;		
	
		case "domain_redemption_form":
		    $domain = new Domain;
			$domain->redemption_form();
		    break;

		case "domain_redemption":
		    $domain = new Domain;
			$domain->dispatch("redemption");
		    break;
		    
		case "zone_list_form":
		    $zone = new Zone;
			$zone->list_form();
		    break;

		case "zone_list":
		    $zone = new Zone;
			$zone->dispatch("list_result");
		    break;
		    
		case "zone_view":
		    $zone = new Zone;
			$zone->dispatch("view");
		    break;

		case "contact_list_form":
		    $contact = new Contact;
			$contact->contact_list_form();
		    break;

		case "contact_list_result":
		    $contact = new Contact;
			$contact->dispatch("contact_list_result");
		    break;

		case "show_contact":
		    $contact = new Contact;
			$contact->show_contact();
		    break;

		case "contact_select_tld_form":
		    $contact = new Contact;
			$contact->contact_select_tld_form();
		    break;

		case "contact_form":
		    $contact = new Contact;
			$contact->dispatch("contact_form");
		    break;

		case "contact_create":
		    $contact = new Contact;
			$contact->dispatch("contact_create");
		    break;

		case "contact_form":
		    $contact = new Contact;
			$contact->dispatch("contact_form");
		    break;

		case "contact_modify":
		    $contact = new Contact;
			$contact->dispatch("contact_modify");
		    break;

		case "contact_delete":
		    $contact = new Contact;
			$contact->dispatch("contact_delete");
		    break;
	
	    case "ns_view":
	        $ns = new Nameserver;
			$ns->dispatch("view");
		    break;
		
		case "ns_list_form":
		    $ns = new Nameserver;
			$ns->list_form();
		    break;

		case "ns_list_result":
		    $ns = new Nameserver;
			$ns->dispatch("list_result");
		    break;

		case "ns_create_form":
		    $ns = new Nameserver;
			$ns->create_form();
		    break;

		case "ns_create":
		    $ns = new Nameserver;
			$ns->dispatch("create");			
		    break;

		case "ns_modify_form":
		    $ns = new Nameserver;
			$ns->modify_form();
		    break;

		case "ns_modify":
		    $ns = new Nameserver;
			$ns->dispatch("modify");			
		    break;

		case "ns_delete_form":
		    $ns = new Nameserver;
			$ns->delete_form();
		    break;

		case "ns_delete":
		    $ns = new Nameserver;
			$ns->dispatch("delete");
		    break;

		case "ns_mass_modify_form_step1":
		    $ns = new Nameserver;
		    $ns->dispatch("mass_modify_form_step1");
		    break;
		
		case "ns_mass_modify_form_step2":
		    $ns = new Nameserver;
		    $ns->dispatch("mass_modify_form_step2");
		    break;
		
		case "ns_mass_modify":
		    $ns = new Nameserver;
		    $ns->dispatch("mass_modify");
		    break;		

		case "query_object":
		    $user = new User;
			$user->query_object();
		    break;

		case "query_profile":
		    $user = new User;
			$user->query_profile();
		    break;
		
		case "user_property_form":
		    $user = new User;
		    $user->property_form();
		    break;
		    
		case "user_property":
		    $user = new User;
		    $user->dispatch("property");
		    break;		
		    
		case "show_request_list":
			$tools->show_request_list();
		    break;		
	
		case "result_list":
		    $user = new User;
		    $user->result_list();
		    break;
	
		case "result_export":
		    $user = new User;
		    $user->result_export($_SESSION["httpvars"]["filetype"]);
		    break;
		
		case "result_retrieve":
		    $is_proc_id = true;
		    if (isset($_SESSION["httpvars"]["pid"])) {
		        $id = $_SESSION["httpvars"]["pid"];
		    } elseif (isset($_SESSION["httpvars"]["tid"])) {
		        $is_proc_id = false;
		        $id = $_SESSION["httpvars"]["tid"];
		    } else {
		        $id = "";
		    }
		    $user = new User;
       		$user->result_retrieve($id, $is_proc_id);
		    break;		

		case "empty_result_list":
		    $user = new User;
			$user->empty_result_list();
		    break;
	
		case "tips":
		    $user = new User;
			$user->tips();
		    break;
		
		case "home":
		    $user = new User;
			$user->home_page();
		    break;
		    
		case "show_idn_convert_form":
		    $service = new Service;
		    $service->dispatch("idn_convert_form");
            break;

		case "idn_convert":
		    $service = new Service;
            $service->dispatch("idn_convert");
            break;
                        
		default:
		    $log = new Log;
			$log->req_status("e", "Unknown mode was used: " . $_SESSION["userdata"]["mode"] . " - fallback to start screen");
		    break;
	}

} else {
	switch($_SESSION["userdata"]["mode"]) {

		case "login":
		    $user = new User;
			$user->dispatch("login");
		    break;

		default:
		    $user = new User;
			$user->login_form();
		    break;
	}
}

//parses the menu, content and the rest
$tools->parse_site();

//$tools->prep($_SESSION);

?>