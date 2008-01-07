<?php

/**
 * Class Connect handles:
 * - connections to the DMAPI web server;
 * - user query buildup;
 * - server response check;
 * - session expiration control;
 * - basic result parsing etc.
 *
 * Note that the normal operation of this class expects you to have CURL installed.
 * on how to install CURL, take a look at the
 * @link http://www.php.net/manual/en/ref.curl.php PHP documentation
 * and  also download the
 * @link http://curl.haxx.se/ CURL library
 *
 * @author Joker.com <info@joker.com>
 * @copyright No copyright
 */

class Connect //ivity
{

    /**
     * String that contains the current request
     *
     * @var     string
     * @access  private
     */
    var $http_query = "";

    /**
     * String that contains part of the current request - only its parameters
     *
     * @var     string
     * @access  private
     */
    var $http_query_params = "";

    /**
     * String that contains the current request. Its content will be written
         * in the log files. That way sensitive information could be additionally handled
     *
     * @var     string
     * @access  private
     * @see     $hide_field_values
     */
    var $log_http_query = "";

    /**
     * Array of field names. Its values should be hidden.
     * Used for data like passwords, billing info etc.
     *
     * @var     array
     * @access  private
     * @see     $log_http_query
     */
    var $hide_field_values = array();

    /**
     * The text that will be used to hide the values
     * of the array $hide_field_values
     *
     * @var     string
     * @access  private
     */
    var $hide_value_text = "";

    /**
     * Class constructor. No optional parameters.
     *
     * usage: Connect()
     *
     * @access  private
     * @return  void
     */
    function Connect()
    {
        global $jpc_config;
        $this->config = $jpc_config;
        $this->log = new Log;
        $this->hide_field_values = $this->config["hide_field_values"];
        $this->hide_value_text = $this->config["hide_value_text"];
    }

    /**
     * Parses the response (not the whole HTTP request - only its body) from the DMAPI server.
     *
     * on success - returns an associative array containing the header and body of the response (not the HTTP header!!!)
     * on failure - returns empty string
     *
     * @param   string  $res contains the HTTP body
     * @access  private
     * @return  mixed
     * @see     execute_request()
     */
    function parse_response($res)
    {
        $raw_arr = explode("\n\n", trim($res));
        $arr_elements = count($raw_arr);
        if ($arr_elements > 0) {
            if (is_array($raw_arr) && 1 == count($raw_arr)) {
                $temp["response_header"] = $this->parse_response_header($raw_arr["0"]);

            } elseif (is_array($raw_arr) && 2 == count($raw_arr)) {
                $temp["response_header"] = $this->parse_response_header($raw_arr["0"]);
                $temp["response_body"] = $raw_arr["1"];
            } else {
                $temp["response_header"] = $this->parse_response_header($raw_arr["0"]);
                $felem = array_shift($raw_arr);
                $temp["response_body"] = implode("\n\n",$raw_arr);
            }
        } else {
            $this->log->req_status("e", "function parse_response(): Couldn't split the response into response header and response body\nRaw result:\n$res");
            $temp = "";
        }
        return $temp;
    }

    /**
     * Parses the header of the DMAPI response.
     *
     * on success - returns an associative array containing the header elements
     * on failure - returns empty string
     *
     * @param   string  $header contains the response's header
     * @access  private
     * @return  mixed
     * @see     execute_request()
     */
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
            $arr = "";
            $this->log->req_status("e", "function parse_response_header(): Unidentified error\nRaw header:\n$header");
        }
        return $arr;
    }

    /**
     * Prepares the request to be sent, submits it and checks the DMAPI response.
     * The HTTP response is being parsed and saved in an associative array.
     *
     * @param   string  $request which request should be executed
     * @param   array   $params array containing the request parameteres
     * @param   array   $response HTTP response (by reference!)
     * @param   string  $sessid session id (by reference!)
     * @access  public
     * @return  boolean
     */
    function execute_request($request, $params, &$response, &$sessid)
    {        
        if ($this->is_request_available($request)) {
            //build the query
            $this->assemble_query($request, $params, $sessid);
            $this->log->req_status("i", "function execute_request(): Request string was sent: " . $this->log_http_query);
            //send the request
            $raw_res = $this->query_host($this->config["dmapi_url"], $this->http_query, true);
            $temp_arr = @explode("\r\n\r\n", $raw_res, 2);
            //split the response for further processing
            if (is_array($temp_arr) && 2 == count($temp_arr)) {
                $response = $this->parse_response($temp_arr[1]);
                $response["http_header"] = $temp_arr[0];
                //get account balance
                if (isset($response["response_header"]["account-balance"])) {
                    $_SESSION["auto_config"]["account_balance"] = $response["response_header"]["account-balance"];
                }
            } else {
                $this->log->req_status("e", "function execute_request(): Couldn't split the response into http header and response header/body\nRaw result:\n$raw_res");
                return false;
            }
            //status
            if ($this->http_srv_response($response["http_header"]) && $this->request_status($response)) {
                $this->log->req_status("i", "function execute_request(): Request was successful");
                return true;
            } else {
                $http_code = $this->get_http_code($response["http_header"]);
                if ("401" == $http_code) {
                    //kills web session
                    session_destroy();
                    //deletes session auth-id
                    $sessid = "";
                }            
            }
        } else {
            $this->log->req_status("e", "function execute_request(): Request $request is not supported in this version of DMAPI.");
        }
        return false;
    }

    function is_request_available($request)
    {
        if ($request == "login" || $request == "query-request-list") {
            return true;
        }
        foreach ($_SESSION["auto_config"]["dmapi_avail_requests"] as $item) {
            if ($request == $item) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Sets the auth-id.
     *
     * @param   string  $sessid DMAPI server session id (by reference!)
     * @param   array   $sessdata parsed server response data
     * @access  public
     * @return  boolean
     */
    function set_auth_id(&$sessid, $sessdata)
    {
        if (isset($sessdata["response_header"]["auth-sid"]) && $sessdata["response_header"]["auth-sid"]) {
            $sessid = $sessdata["response_header"]["auth-sid"];
            return true;
        }
        return false;
    }

    /**
     * Extracts the HTTP response code.
     *
     * @param   string  $http_header
     * @access  public
     * @return  string
     */
    function get_http_code($http_header)
    {
        $regex = "/^HTTP\/1.[0-1]\b ([0-9]{3}) /i";
        preg_match($regex, $http_header, $matches);
        if (is_array($matches) && $matches[1]) {
            return $matches[1];
        } else {
            $this->log->req_status("e", "function get_http_code(): Invalid HTTP code. HTTP header follows:\n$http_header");
            return false;
        }
    }

    /**
     * Checks if the HTTP request was successful.
     *
     * @param   string  $http_header
     * @access  public
     * @return  boolean
     * @see     execute_request()
     */
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

    /**
     * Checks if the DMAPI request was successful.
     *
     * @param   string  $http_header
     * @access  public
     * @return  boolean
     * @see     execute_request()
     */
    function request_status($sessdata)
    {
        if (!isset($sessdata["response_header"]["status-code"]) || $sessdata["response_header"]["status-code"] != "0") {
            $this->log->req_status("e", "function request_status(): Request was not successful - Possible reason could be network or request error");
            return false;
        }
        return true;
    }

    /**
     * Builds an HTTP query from a given set of parameters.
     * Replace function http_build_query(). This function was slightly modified
     * from its original. So be careful when you migrate to PHP5+.
     *
     * @package PHP_Compat
     * @link    http://php.net/function.http-build-query
     * @param   array   $formdata contains the query parameters
     * @param   string  $sessid session id
     * @param   boolean $build_log_query enables hiding of sensitive information for inclusion in the log files
     * @param   mixed   $numeric_prefix
     * @author  Stephan Schmidt <schst@php.net>
     * @author  Aidan Lister <aidan@php.net>
     * @NOTE!!! There is a similar function in PHP 5.x. If you want to use the built-in one, please rename this 
     * one and change all the function calls. This function doesn't do what the built-in function, so manual 
     * modifications are needed.
     */
    function http_build_query($formdata, $sessid, $build_log_query = false, $numeric_prefix = null)
    {
        if ($sessid && $sessid != $this->config["no_content"]) {
            $formdata["auth-sid"] = $sessid;
        }

        //Check if we have an array to work with
        if (!is_array($formdata)) {
            $this->log->req_status("e", "function http_build_query(): Parameter 1 expected to be Array or Object. Incorrect value given.");
            return false;
        }

        //The IP of the user should be always present in the requests
        $formdata["client-ip"] = $GLOBALS["HTTP_SERVER_VARS"]["REMOTE_ADDR"];

        //Some values should not be present in the logs!!
        if ($build_log_query) {
            foreach ($this->hide_field_values as $value)
            {
                if (isset($formdata[$value])) {
                    $formdata[$value] = $this->hide_value_text;
                }
            }
        }

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
                if (trim(strtolower($val)) == $this->config["empty_field_value"]) {                    
                    $val = "";
                }
                if (!$build_log_query) {
                    $tmp_val = urlencode($key).'='.urlencode(trim($val));
                } else {
                    $tmp_val = $key.'='.trim($val);
                }
                array_push($tmp,$tmp_val);
                continue;
            }
        }
        $http_request = implode('&', $tmp);
        $this->http_query_params = $http_request;
        return $http_request;
    }

    /**
     * Intermediate function to prepare the HTTP requests and log file data (DMAPI related)
     *
     * @param   string  $request DMAPI specific request
     * @param   arrays  $params contains all required requet parameters
     * @param   string  $sessid session id
     * @access  public
     * @return  void
     * @see     execute_request()
     */
    function assemble_query($request, $params, $sessid)
    {
        $this->http_query = "/request/" . $request . "?" . $this->http_build_query($params,$sessid);
        $this->log_http_query = "/request/" . $request . "?" . $this->http_build_query($params,$sessid,true);
    }

    /**
     * Intermediate function to prepare the HTTP requests and log file data
     *
     * @param   string  $request header for specific request
     * @param   arrays  $params contains all required requet parameters
     * @access  public
     * @return  void
     * @see     execute_request()
     */
    function assemble_any_query($request, $params)
    {
        $this->http_query = $request . "?" . $this->http_build_query($params, "");
        $this->log_http_query = $request . "?" . $this->http_build_query($params, "", true);
    }

    /**
     * Establishing a CURL connection. Returns the HTTP response.
     *
     * @param   string  $conn_server remote server to connect
     * @param   string  $request DMAPI specific request
     * @param   boolean $get_header on/off HTTP header
     * @access  public
     * @return  string
         * @see     execute_request()
     */
    function query_host($conn_server, $params = "", $get_header = false)
    {        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $conn_server.$params);
        if (preg_match("/^https:\/\//i", $conn_server)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        }
        if ($this->config["set_outgoing_network_interface"]) {
            curl_setopt($ch, CURLOPT_INTERFACE, $this->config["outgoing_network_interface"]);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->config["curlopt_connecttimeout"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->config["curlopt_timeout"]);        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($get_header) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        } else {
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }
        
        if ($this->config["curlexec_proceed"]) {
            $result = curl_exec($ch);
        }

        if (curl_errno($ch)) {
            $this->log->req_status("e", "function query_host(): ".curl_error($ch));
        } else {
            $_SESSION["last_request_time"] = time();
            curl_close($ch);
        }       
        return $result;
    }

//  function get_curlinfo($handle)
//  {
//      return curl_getinfo($handle);
//  }

} //end of class Connect

?>
