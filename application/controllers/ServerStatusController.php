<?php
/**
 * Created by PhpStorm.
 * User: Buwaneka
 * Date: 3/12/2018
 * Time: 8:40 AM
 */

class ServerStatusController extends CI_Controller{
    public function __construct() {
        parent::__construct();

        $this->load->model('ServerStatusLog');
        $this->load->model('email/EmailTemplate');
        $this->load->model('email/Email');
    }

    public function index(){
        $url = "http://localhost:8080/ServerStatusAPI/status";
        $data = array();
        $response = $this->curlPostCall($url, $data);
        date_default_timezone_set("Asia/Colombo");
        $date = date('Y-m-d H:i:s', time());
        $this->ServerStatusLog->addStatusLog($date, $response['error'], $response['status'], $response['description']);

        if(isset($response) && $response['error']){
            $this->sendErrorReport($response['description'], $date);
        }

    }

    // sends a HTTP POST to a remote site
    private function curlPostCall($url, $data, $method = 'POST') {
        $curl = curl_init();
        $data_string = json_encode($data);

        curl_setopt($curl, CURLOPT_URL, $url);

        switch ($method) {
            case 'POST' :
                curl_setopt($curl, CURLOPT_POST, true);
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // only for testing
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Check if any error occurred
        if(curl_errno($curl))
        {
            $response = array(
                "error" => true,
                "status" => $status,
                "description" => curl_error($curl)
            );
            return $response;
        }
        $server_output = json_decode($server_output);
        $response = array(
            "error" => $server_output->error,
            "status" => $status,
            "description" => $server_output->message
        );
        curl_close($curl);

        return $response;
    }

    public function sendErrorReport($error, $date){
        $email = "buwaneka.atk@gmail.com";
        $username = "buwa";
        $subject = "Warning... SERVER ERROR!!!";
        $content = $this->EmailTemplate->getEmailTemplate($error, $date);
        $result = $this->Email->sendEmail($content, $email, $username, $subject);
        if ($result) {
            return true;
        } else {
            return false;
        }

    }
}