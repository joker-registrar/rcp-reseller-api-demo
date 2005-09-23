<?php

/**
 * Runtime PHP Configuration
 *
 */
$ini_php_ext = array(
    "session.cache_limiter"     => "nocache",
    "session.entropy_file"      => "",
    "session.entropy_length"    => "0",
    "session.gc_divisor"        => "100",
    "session.gc_maxlifetime"    => "1440",
    "session.gc_probability"    => "1",
    "session.hash_bits_per_character" => "4",
    "session.hash_function"     => "0",
    "session.name"              => "web_sid",
    "session.referer_check"     => "",
    "session.save_handler"      => "files",
    "session.save_path"         => "../sess",
    "session.serialize_handler" => "php",
    "session.use_cookies"       => "1",
    "session.use_only_cookies"  => "0",
    "session.cookie_lifetime"   => "0",
    "session.cookie_path"       => "/",
    "session.cookie_domain"     => "http://194.245.99.48:82",
    "session.cookie_secure"     => "",
    "mbstring.internal_encoding"=> "UTF-8",
    //possible values to change - relying on the defaults for the moment
    //mbstring.detect_order NULL    PHP_INI_ALL
    //mbstring.encoding_translation "0" PHP_INI_PERDIR
    //mbstring.func_overload    "0" PHP_INI_PERDIR
    //mbstring.http_input   "pass"  PHP_INI_ALL
    //mbstring.http_output  "pass"  PHP_INI_ALL
    //mbstring.language "neutral"   PHP_INI_PERDIR
    //mbstring.script_encoding  NULL    PHP_INI_ALL
    //mbstring.substitute_character NULL
    //"error_reporting"     => "E_ALL",// & ~E_NOTICE",
    "display_errors"        => "0"
);

foreach ($ini_php_ext as $key => $value) {
#CODA: no checking for errors!
     ini_set($key, $value);
}

?>
