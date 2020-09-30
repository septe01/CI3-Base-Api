<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class MReport extends CI_Model{
    public function getData()
    {
        return $this->db->get('rrdaily');
        
    }
    public function insertData($data)
    {
        // var_dump($data);        
        $result = $this->db->insert('rrdaily', $data);
        return $result;
    }
}