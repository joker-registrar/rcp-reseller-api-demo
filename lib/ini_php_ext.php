<?php

$ini_php_ext = array(
//possible values to change - relying on the defaults for the moment
	"mbstring.internal_encoding" =>	"UTF-8",	//PHP_INI_ALL
	
#mbstring.detect_order	NULL	PHP_INI_ALL
#mbstring.encoding_translation	"0"	PHP_INI_PERDIR
#mbstring.func_overload	"0"	PHP_INI_PERDIR
#mbstring.http_input	"pass"	PHP_INI_ALL
#mbstring.http_output	"pass"	PHP_INI_ALL		
#mbstring.language	"neutral"	PHP_INI_PERDIR
#mbstring.script_encoding	NULL	PHP_INI_ALL
#mbstring.substitute_character	NULL
);

foreach ($ini_php_ext as $key => $value) {
	 ini_set($key, $value);
}

?>