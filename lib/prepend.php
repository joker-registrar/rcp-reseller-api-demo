<?php

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