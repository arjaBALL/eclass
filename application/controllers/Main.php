<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

     public function __construct() {
        parent::__construct();
        $this->load->library('Data_loader');
        $this->load->helper('url');
    }

    public function index()
    {
        $data['content'] = 'main/dashboard';  
        $data['data'] = [];            
        $this->load->view('main/main', $data);
    }

    public function student() {
        $data = array_merge(
        // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            ['content' => 'student',
                'scripts' => ['student', 'sweetalert_custom']],  
        );
        $this->load->view('main/main', $data);
    }

    public function teacher() {
        $data = array_merge(
        // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            ['content' => 'teacher',
                'scripts' => ['teacher', 'sweetalert_custom']],  
        );
        $this->load->view('main/main', $data);
    }

    public function subject() {
        $data = array_merge(
        // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            ['content' => 'subject',
                'scripts' => ['subject', 'sweetalert_custom']],  
        );
        $this->load->view('main/main', $data);
    }

    public function section() {
        $data = array_merge(
        // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            ['content' => 'section',
                'scripts' => ['section', 'sweetalert_custom']],  
        );
        $this->load->view('main/main', $data);
    }

    public function department() {
        $data = array_merge(
        // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            ['content' => 'department',
                'scripts' => ['department', 'sweetalert_custom']],  
        );
        $this->load->view('main/main', $data);
    }
}