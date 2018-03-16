<?php
/**
 * Created by PhpStorm.
 * User: Buwaneka
 * Date: 10/19/2017
 * Time: 1:32 PM
 */
class Email extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        require_once(APPPATH . 'libraries/Mandrill/Mandrill.php');
    }
    public function sendEmail($content, $email, $username, $subject) {

        $mandrill = new Mandrill('gULxLropxAlou1WmbBigyA');

        try {
            $message = array(
                'subject' => $subject,
                'html' => $content,
                'from_email' => 'info@evainmotion.com',
                'from_name' => 'EVA in motion', //optional
                'to' => array(
                    array(
                        'email' => $email,
                        'name' => (($username != "-") ? $username : ""),
                        'type' => 'to'
                    )
                ),
            );
            $result = $mandrill->messages->send($message);
        } catch (Mandrill_Error $e) {
            error_log('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
            return false;
        }

        return $result;
    }
}