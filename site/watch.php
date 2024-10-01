<?php
/** watchdog load for rpanel.joker.com
 *
 *  @author CSL Team [dev@joker.com]
 *
*/

require_once(dirname(__FILE__)."/../lib/prepend.php");

$tools  = new Tools;
$result = 0;
$amsg=Array(0 => "success", 1 => "dmapi not reachable");

// fetch dmapi version
$dampi_version = $tools->get_dmapi_version();

if ($dmapi_version === "" || $dmapi_version === false) {
 $result = 1;
}

print $result.":".$amsg[$result]."\n";
