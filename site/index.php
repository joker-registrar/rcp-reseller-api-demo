<?php

/*
 * Main script
 * Dispatches the function calls
 *
 */

//Report all errors except E_NOTICE
error_reporting(E_ALL ^ E_NOTICE);
require_once("../lib/prepend.php");
session_start();

global $tools;
$tools = new Tools;
$user = new User;
$domain = new Domain;
$contact = new Contact;
$ns = new Nameserver;
$log = new Log;

if (isset($_SESSION["auth-sid"])) {
	$sessid = $_SESSION["auth-sid"];

	switch($_SESSION["userdata"]["mode"]) {

		case "logout":
			$user->logout();
		break;

		case "domain_view_form":
			$domain->view_form();
		break;

		case "domain_view":
			$domain->dispatch("view");
		break;
	
		case "domain_register_form":
			$domain->register_form();
		break;

		case "domain_register":
			$domain->dispatch("register");
		break;

		case "domain_renew_form":
			$domain->renew_form();
		break;
	
		case "domain_renew":
			$domain->dispatch("renew");
		break;
	
		case "domain_transfer_form":
			$domain->transfer_form();
		break;
	
		case "domain_transfer":			
			$domain->dispatch("transfer");
		break;

		case "domain_modify_form":
			$domain->modify_form();
		break;
	
		case "domain_modify":
			$domain->dispatch("modify");
		break;

		case "domain_delete_form":
			$domain->delete_form();
		break;
	
		case "domain_delete":
			$domain->dispatch("delete");
		break;
	
		case "domain_owner_change_step1":
			$domain->dispatch("owner_change_step1");			
		break;
		
		case "domain_owner_change_step2":
			$domain->dispatch("owner_change_step2");
		break;
	
		case "domain_owner_change":
			$domain->dispatch("owner_change");
		break;
	
		case "domain_lu_form":
			$domain->lock_unlock_form();
		break;

		case "domain_lu":
			$domain->dispatch("lock_unlock");
		break;
		case "domain_list_form":		
			$domain->list_form();
		break;

		case "domain_list_result":
			$domain->dispatch("list_result");
		break;
	
		case "domain_redemption_form":
			$domain->redemption_form();
		break;

		case "domain_redemption":
			$domain->dispatch("redemption");
		break;

		case "contact_list_form":
			$contact->contact_list_form();
		break;

		case "contact_list_result":
			$contact->dispatch("contact_list_result");
		break;

		case "show_contact":
			$contact->show_contact();
		break;

		case "contact_select_tld_form":
			$contact->contact_select_tld_form();
		break;

		case "contact_form":
			$contact->dispatch("contact_form");
		break;

		case "contact_create":
			$contact->dispatch("contact_create");
		break;

		case "contact_form":
			$contact->dispatch("contact_form");
		break;

		case "contact_modify":
			$contact->dispatch("contact_modify");
		break;

		case "contact_delete":
			$contact->dispatch("contact_delete");
		break;
	
	    case "ns_view":
			$ns->dispatch("view");
		break;
		
		case "ns_list_form":
			$ns->list_form();
		break;

		case "ns_list_result":
			$ns->dispatch("list_result");
		break;

		case "ns_create_form":
			$ns->create_form();
		break;

		case "ns_create":
			$ns->dispatch("create");			
		break;

		case "ns_modify_form":
			$ns->modify_form();
		break;

		case "ns_modify":
			$ns->dispatch("modify");			
		break;

		case "ns_delete_form":
			$ns->delete_form();
		break;

		case "ns_delete":
			$ns->dispatch("delete");
		break;

		case "ns_mass_modify_form_step1":
		    $ns->dispatch("mass_modify_form_step1");
		break;
		
		case "ns_mass_modify_form_step2":
		    $ns->dispatch("mass_modify_form_step2");
		break;
		
		case "ns_mass_modify":
		    $ns->dispatch("mass_modify");
		break;		

		case "query_object":
			$user->query_object();
		break;

		case "query_profile":
			$user->query_profile();
		break;
		
		case "show_request_list":
			$tools->show_request_list();
		break;		
	
		case "result_list":
            $user->result_list();
		break;
	
		case "result_export":
            $user->result_export($_SESSION["httpvars"]["filetype"]);
		break;
		
		case "result_retrieve":
       		$user->result_retrieve($_SESSION["httpvars"]["pid"]);
		break;		

		case "empty_result_list":
			$user->empty_result_list();
		break;
	
		case "tips":
			$user->tips();
		break;
		
		case "home":
			$user->home_page();
		break;

		default:
			$log->req_status("e", "Unknown mode was used: " . $_SESSION["userdata"]["mode"] . " - fallback to start screen");
		break;
	}

} else {
	switch($_SESSION["userdata"]["mode"]) {

		case "login":
			$user->dispatch("login");
		break;

		default:
			$user->login_form();
		break;
	}
}

//parses the menu, content and the rest
$tools->parse_site();

//$tools->prep($_SESSION);

?>