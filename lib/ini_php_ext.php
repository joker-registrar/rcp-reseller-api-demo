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
    //you have to set the correct directory here - be careful
    //to use path corresponding to your OS
    "session.save_path"         => "../sess",
    //"session.save_path"       => "d:\www\dmapi\sess",
    "session.serialize_handler" => "php",
    "session.use_cookies"       => "1",
    "session.use_only_cookies"  => "1",
    "session.cookie_lifetime"   => "0",
    "session.cookie_path"       => "/",
    "session.cookie_domain"     => "",
    "session.cookie_secure"     => "",
    "display_errors"        => "0"
);

if (version_compare(phpversion(), '5.6', '<')) {
    $ini_php_ext["mbstring.internal_encoding"] = "UTF-8";
}

foreach ($ini_php_ext as $key => $value) {
     ini_set($key, $value);
}

?>
