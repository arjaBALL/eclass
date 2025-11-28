<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller
{

    public function __construct()
    {
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

    public function student()
    {
        $data = array_merge(
            // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            [
                'content' => 'student',
                'scripts' => ['students']
            ],
        );
        $this->load->view('main/main', $data);
    }

    public function teacher()
    {
        $data = array_merge(
            // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            [
                'content' => 'teacher',
                'scripts' => ['teachers']
            ],
        );
        $this->load->view('main/main', $data);
    }

    public function subject()
    {
        $data = array_merge(
            // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            [
                'content' => 'subject',
                'scripts' => ['subjects']
            ],
        );
        $this->load->view('main/main', $data);
    }

    public function section()
    {
        $data = array_merge(
            // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            [
                'content' => 'section',
                'scripts' => ['sections']
            ],
        );
        $this->load->view('main/main', $data);
    }

    public function department()
    {
        $data = array_merge(
            // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            [
                'content' => 'department',
                'scripts' => ['departments']
            ],
        );
        $this->load->view('main/main', $data);
    }

    public function schedule()
    {
        $data = array_merge(
            // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            [
                'content' => 'schedules',
                'scripts' => ['schedules']
            ],
        );
        $this->load->view('main/main', $data);
    }

    public function subject_assignment()
    {
        $data = array_merge(
            // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            [
                'content' => 'subject_assignment',
                'scripts' => ['subject_assignments']
            ],
        );
        $this->load->view('main/main', $data);
    }

    public function record_score()
    {
        $data = array_merge(
            // $this->get_session_data(),
            $this->data_loader->dropdowns(),
            [
                'content' => 'record_score',
                'scripts' => ['record_scores']
            ],
        );
        $this->load->view('main/main', $data);
    }
}