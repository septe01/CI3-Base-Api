<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

class Report extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // load model report
        
        $this->load->model('MReport');        

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['index_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['show_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['create_post']['limit'] = 500; // 500 requests per hour per user/key

        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_get(){
        echo "hello septe";
    }

    public function show_get(){ //get all data
        $data_report = $this->MReport->getData();
        if($data_report->num_rows() > 0){
            $res_data = [
                "status"=> true,
                "message"=> "data ready ",
                "data"=> $data_report->result()
            ];
        }else{
            $res_data = [
                "status"=> true,
                "message"=> "data is empty",
                "data"=>[]
            ];
        }
        $this->response($res_data, REST_Controller::HTTP_OK);
    }

    public function create_post(){ //create data

        $config['upload_path']          = './uploads/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['file_name']        = 'NC-'.time();
        $config['max_size']             = 2048;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('log_file'))
        {
                $res_data = array('error' => $this->upload->display_errors());
        }else
        {
                $data = [
                    "crq" => $this->input->post('crq'),
                    "requester" =>	$this->input->post('requester'),
                    "status" => $this->input->post('status'),
                    "log_file"	=> $this->upload->data('file_name'),
                ];
                $store_data = $this->MReport->insertData($data);
                if($store_data){
                    $res_data = [
                        "status"=> true,
                        "message"=> "success insert data!",
                    ];
                }else{
                    $res_data = [
                        "status"=> true,
                        "message"=> "failed insert data!",
                    ];
                }
        }

        $this->response($res_data, REST_Controller::HTTP_OK);
    }





    public function users_get()
    {
        // Users from a data store e.g. database
        $users = [
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com', 'fact' => 'Loves coding'],
            ['id' => 2, 'name' => 'Jim', 'email' => 'jim@example.com', 'fact' => 'Developed on CodeIgniter'],
            ['id' => 3, 'name' => 'Jane', 'email' => 'jane@example.com', 'fact' => 'Lives in the USA', ['hobbies' => ['guitar', 'cycling']]],
        ];

        $id = $this->get('id');

        // If the id parameter doesn't exist return all the users

        if ($id === NULL)
        {
            // Check if the users data store contains users (in case the database result returns NULL)
            if ($users)
            {
                // Set the response and exit
                $this->response($users, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No users were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

        // Find and return a single record for a particular user.
        else {
            $id = (int) $id;

            // Validate the id.
            if ($id <= 0)
            {
                // Invalid id, set the response and exit.
                $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }

            // Get the user from the array, using the id as key for retrieval.
            // Usually a model is to be used for this.

            $user = NULL;

            if (!empty($users))
            {
                foreach ($users as $key => $value)
                {
                    if (isset($value['id']) && $value['id'] === $id)
                    {
                        $user = $value;
                    }
                }
            }

            if (!empty($user))
            {
                $this->set_response($user, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'User could not be found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }

    public function users_post()
    {
        // $this->some_model->update_user( ... );
        $message = [
            'id' => 100, // Automatically generated by the model
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'message' => 'Added a resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function users_delete()
    {
        $id = (int) $this->get('id');

        // Validate the id.
        if ($id <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

}
