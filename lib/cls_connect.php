<?php

class Connect //ivity
{

	function Connect()
	{
		global $config;
		$this->config = $config;
		$this->log = new Log;
	}

	function parse_response($res)
	{
		$raw_arr = explode("\n\n", trim($res));
		if (is_array($raw_arr) && 2 == count($raw_arr)) {
			$result["response_header"] = $this->parse_response_header($raw_arr["0"]);
			$result["response_body"] = $raw_arr["1"];
		} elseif (is_array($raw_arr) && 1 == count($raw_arr)) {
			$result["response_header"] = $this->parse_response_header($raw_arr["0"]);
		} else {
			$this->log->req_status("e", "function parse_response(): Couldn't split the response into response header and response body\nRaw result:\n$res");
			$result = "";
		}
		return $result;
	}

	function parse_response_header($header)
	{
		$raw_arr = explode("\n", trim($header));
		$result = array();
		if (is_array($raw_arr)) {
			foreach ($raw_arr as $key => $value)
			{
				$keyval = array();
				if (preg_match("/^([^\s]+):\s+(.+)\s*$/", $value, $keyval)) {
					$keyval[1] = strtolower($keyval[1]);
					if (isset($arr[$keyval[1]])) {
						if (!is_array($arr[$keyval[1]])) {
							$prev = $arr[$keyval[1]];
							$arr[$keyval[1]] = array();
							$arr[$keyval[1]][] = $prev;
							$arr[$keyval[1]][] = $keyval[2];
						} else {
							$arr[$keyval[1]][] = $keyval[2];
						}
					} else {
						if ($keyval[2] != "") {
							$arr[$keyval[1]] = $keyval[2];
						} else {
							$arr[$keyval[1]] = "";
						}
					}
				} else {
					$this->log->req_status("e", "function parse_response_header(): Header line not parseable - pattern do not match\nRaw header:\n$value");
				}
			}
		} else {
			$this->log->req_status("e", "function parse_response_header(): Unidentified error\nRaw header:\n$header");
		}
		return $arr;
	}

	function execute_request($request, $params, &$response, &$sessid)
	{		
		//build the query
		$http_query = $this->assemble_query($request, $params, $sessid);
		$this->log->req_status("i", "function execute_request(): Request string that is being sent: " . $http_query);
		//send the request
		$raw_res = $this->query_host($http_query, true);
		$temp_arr = @explode("\r\n\r\n", $raw_res, 2);
		//split the response for further processing
		if (is_array($temp_arr) && 2 == count($temp_arr)) {
			$response = $this->parse_response($temp_arr[1]);
			$response["http_header"] = $temp_arr[0];
		} else {
			$this->log->req_status("e", "function execute_request(): Couldn't split the response into http header and response header/body\nRaw result:\n$raw_res");
			return false;
		}
		//status
		if ($this->http_srv_response($response["http_header"]) && $this->request_success($response)) {
			$this->log->req_status("i", "function execute_request(): Request was successful");
			return true;
		} else {
			$http_code = $this->get_http_code($response["http_header"]);
			if ("401" == $http_code) {
				//kill web session
				session_destroy();
				//delete session auth-id
				$sessid = "";				
			}

		}
		return false;
	}

	function has_auth_id(&$sessid, $sessdata)
	{
		if (isset($sessdata["response_header"]["auth-sid"]) && $sessdata["response_header"]["auth-sid"]) {
			$sessid = $sessdata["response_header"]["auth-sid"];
			return true;
		}
		return false;
	}

	function get_http_code($http_header)
	{
		$regex = "/\bHTTP\/1.[0-1]{1}\b ([0-9]{3}) /i";
		preg_match($regex, $http_header, $matches);
		return $matches[1];
	}

	function http_srv_response($http_header)
	{
		$success = false;
		$http_code = $this->get_http_code($http_header);
		switch (substr($http_code,0,1))
		{
			case "2":
				$success = true;
				break;

			default:
				$this->log->req_status("e", "function http_srv_response(): Request was not successful - Server issued the following HTTP status code: ". $http_code . ".");
				break;
		}
		return $success;
	}

	function request_success($sessdata)
	{
		if (!isset($sessdata["response_header"]["status-code"]) || $sessdata["response_header"]["status-code"] != "0") {
			$this->log->req_status("e", "function request_success(): Request was not successful - There was an error in the request you have sent");
			return false;
		}
		return true;
	}

	/**
	 * Replace function http_build_query()
	 *
	 * @package     PHP_Compat
	 * @link        http://php.net/function.http-build-query
	 * @author      Stephan Schmidt <schst@php.net>
	 * @author      Aidan Lister <aidan@php.net>
	 * @NOTE!!!	Don't forget to delete this function if you migrate to PHP5+
	 */
	function http_build_query($formdata, $sessid, $numeric_prefix = null)
	{
		if ($sessid && $sessid != $this->config["no_content"]) {
			$formdata["auth-sid"] = $sessid;
		}
		
		// Check we have an array to work with
		if (!is_array($formdata)) {
			$this->log->req_status("e", "function http_build_query(): Parameter 1 expected to be Array or Object. Incorrect value given.");
			return false;
		}

		//The IP of the user should be always present in the requests                
		$formdata["client-ip"] = $GLOBALS["HTTP_SERVER_VARS"]["REMOTE_ADDR"];
		// If the array is empty, return null
		if (empty($formdata)) {
			return null;
		}

		// Start building the query
		$tmp = array ();
		foreach ($formdata as $key => $val)
		{
			if (is_integer($key) && $numeric_prefix != null) {
				$key = $numeric_prefix . $key;
			}

			if (is_scalar($val) && (trim($val) != "")) {
				if (trim(strtolower($val)) == "[empty]") {
					$val = "";
				}
				array_push($tmp, urlencode($key).'='.urlencode(trim($val)));
				continue;
			}
		}
	        return implode('&', $tmp);
	}

	function assemble_query($request, $params, $sessid)
	{
		$http_query = "/request/" . $request . "?" . $this->http_build_query($params, $sessid);
		return  $http_query;
	}

	function query_host($params = "", $get_header = 0)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->config["joker_url"].$params);
		if (preg_match("/^https:\/\//i", $this->config["joker_url"])) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if ($get_header) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, 0);
		}
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	function get_curlinfo($handle)
	{
		return curl_getinfo($handle);
	}

} //end of class Connect

?>