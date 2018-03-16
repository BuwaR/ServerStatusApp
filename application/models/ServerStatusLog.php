<?php
/**
 * Created by PhpStorm.
 * User: Janitha
 * Date: 3/15/2018
 * Time: 12:31 PM
 */

class ServerStatusLog extends CI_Model{

    public function addStatusLog($date, $error, $status, $description){
        $data = array(
            'date' =>  $date,
            'error' => $error,
            'status' => $status,
            'description' =>$description
        );
        $this->db->insert('status_logger', $data);
        return true;
    }

}