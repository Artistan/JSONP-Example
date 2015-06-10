<?php

class regonline{

    private $uname = "me";
    private $pass = "pass";
    private $auth_token = false;
    private $api_url = 'https://www.regonline.com/api/default.asmx/';

    public function __construct(){

    }

    public function get_event($id){
        if(is_numeric($id)){
            return $this->callUrl('GetEvent','id='.$id);
        }
        return array('success' => FALSE, 'message' => 'Not Numeric ID for get_event');
    }

    public function get_events($filter='',$order_by=''){
        return $this->callUrl('GetEvents','filter='.$filter.'&orderBy='.$order_by);
    }

    public function get_future_events($filter='',$order_by='StartDate DESC'){
        if($filter!=''){
            $filter .= "&";
        }
        $filter .= "StartDate >= DateTime.Now";
        return $this->get_events($filter,$order_by);
    }

    private function authenticate(){
        $result = $this->curlIt('Login','username=' . urlencode($this->uname) . '&password=' . urlencode($this->pass));
        if(!empty($result['message']->Success) && !empty($result['message']->Data->APIToken)){
            $this->auth_token = $result['message']->Data->APIToken;
        }
        return false;
    }

    /**
     * @param $url
     * @param $params http://curl.haxx.se/libcurl/c/CURLOPT_POSTFIELDS.html
     * @return array( 'success'=> boolean, 'message' => string | array )
     */
    private function callUrl($url,$params) {
        if(!$this->auth_token) {
            $this->authenticate();
            if(!$this->auth_token) {
                return false;
            }
        }
        return $this->curlIt($url,$params);
    }

    private function curlIt($url,$params) {
        echo $this->api_url.$url."\n";
        $ch = curl_init($this->api_url.$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $params);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        $xml_response = curl_exec($ch);
        if( !$xml_response ) {
            trigger_error(curl_error($ch));
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($http_code >= 400) {
                return array('success' => FALSE, 'message' => t($xml_response));
            }
        }
        curl_close($ch);
        $data = simplexml_load_string($xml_response);
        return array('success' => TRUE, 'message' => $data);
    }
}