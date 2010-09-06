<?php

/**
 * Includes and evaluates the specified files during the execution of the script.
 * If the code from a file has already been included, it will not be included again.
 * In case the inclusion fails for any of the listed files, a FATAL ERROR will be generated
 * 
 */
require_once(dirname(__FILE__)."/template/template.inc.php");
require_once(dirname(__FILE__)."/cls_log.php");
require_once(dirname(__FILE__)."/cls_tools.php");
require_once(dirname(__FILE__)."/cls_connect.php");
require_once(dirname(__FILE__)."/cls_user.php");
require_once(dirname(__FILE__)."/cls_domain.php");
require_once(dirname(__FILE__)."/cls_zone.php");
require_once(dirname(__FILE__)."/cls_contact.php");
require_once(dirname(__FILE__)."/cls_nameserver.php");
require_once(dirname(__FILE__)."/cls_service.php");
require_once(dirname(__FILE__)."/cls_mail.php");
require_once(dirname(__FILE__)."/csvgen.inc.php"); //initially called default.file.php
require_once(dirname(__FILE__)."/ini_php_ext.php");
require_once(dirname(__FILE__)."/config.php");
include_once(dirname(__FILE__)."/config_local.php");
require_once(dirname(__FILE__)."/regexp.php");
require_once(dirname(__FILE__)."/cls_paging.php");

$classes = dir(dirname(__FILE__)."/lang");
while (false !== ($class = $classes->read())) {
	if (substr($class,-4)==".php")		
		require_once(dirname(__FILE__)."/lang/".$class);		
}
$classes->close();

?>