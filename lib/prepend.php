<?php

/**
 * Includes and evaluates the specified files during the execution of the script.
 * If the code from a file has already been included, it will not be included again.
 * In case the iclusion fails for any of the listed files, a FATAL ERROR will be generated
 * 
 */
require_once("template.inc.php");
require_once("cls_log.php");
require_once("cls_tools.php");
require_once("cls_connect.php");
require_once("cls_user.php");
require_once("cls_domain.php");
require_once("cls_contact.php");
require_once("cls_nameserver.php");
require_once("cls_mail.php");
require_once("csvgen.inc.php"); //initially called default.file.php
require_once("ini_php_ext.php");
require_once("config.php");

$classes = dir($_SERVER["DOCUMENT_ROOT"]."/../lib/lang");
while (false !== ($class = $classes->read())) {
	if (substr($class,-4)==".php")		
		require_once("lang/".$class);		
}
$classes->close();

?>