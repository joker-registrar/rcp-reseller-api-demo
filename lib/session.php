<?php

//$doc_root = $HTTP_SERVER_VARS["DOCUMENT_ROOT"];
$session_arr = array(		
		"session.cache_limiter"		=> "nocache",
		"session.entropy_file"		=> "",
		"session.entropy_length"	=> "0",
		"session.gc_divisor"		=> "100",
		"session.gc_maxlifetime"	=> "1440",
		"session.gc_probability"	=> "1",
		"session.hash_bits_per_character" => "4",
		"session.hash_function"		=> "0",
		"session.name"			=> "web_sid",
		"session.referer_check"		=> "",
		"session.save_handler"		=> "files",
		"session.save_path"		=> "../sess",
		"session.serialize_handler"	=> "php",
		"session.use_cookies"		=> "1",
		"session.use_only_cookies"	=> "0",
		"session.cookie_lifetime"	=> "0",
		"session.cookie_path"		=> "/",
		"session.cookie_domain"		=> "http://194.245.99.48:82",
		"session.cookie_secure"		=> "",		
);

foreach ($session_arr as $key => $value) {
#CODA: no checking for errors!
	 ini_set($key, $value);
}

?>