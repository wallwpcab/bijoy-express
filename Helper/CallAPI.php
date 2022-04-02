<?php
use Illuminate\Support\Facades\Http;

class CallAPI {

    /**
     * @var array
     */
    private $data;
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $endpoint;
    /**
     * @var string
     */
    private $api_url;
    /**
     * @var mixed|string
     */
    private $auth_token;

    public function __construct($endpoint,$data = array(), $method = null)
    {
        $this->data = $data;
        $this->method = $method == null ? $_SERVER['REQUEST_METHOD'] : $method;
        $this->endpoint = $endpoint;
        $this->api_url = getEnvData('API_URL');
        $this->auth_token = session('auth_token') != null ? session('auth_token') : '';

    }

    protected function setup(){
        if(empty($this->data) || empty($this->method) || empty($this->endpoint) || empty($this->api_url)){
            return [
                'success' => false,
                'message' => 'Invalid form field'
            ];
        }

        return true;
    }

    public function apiCall(){
        if($this->setup()){
            $headers = array(
                "Accept: application/json",
                "Authorization: Bearer {$this->auth_token}",
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->api_url.'/'.$this->endpoint);
            // SSL important
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
            $output = curl_exec($ch);
            $result = json_decode($output);
            curl_close($ch);

            return $result;
        }

    }

}
