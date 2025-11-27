<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Teacher_model');
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index() {
        // Redirect if already logged in
        if ($this->session->userdata('logged_in')) {
            redirect('user');
        }
        $this->load->view('login');
    }

public function login() {
    $username = $this->input->post('username');
    $password = $this->input->post('password');

    if (empty($username) || empty($password)) {
        echo json_encode(['status'=> 'error', 'message'=> 'All fields required']);
        return;
    }

    $user = $this->Teacher_model->validate_user_login($username, $password);

    if ($user) {
        // Map role_id to role_name
        $role_map = [
            1 => 'Teacher',
            2 => 'Admin',
        ];
        $role_name = isset($role_map[$user->role_id]) ? $role_map[$user->role_id] : 'user';

        // Store user session
        $this->session->set_userdata([
            'user_id'   => $user->id,
            'username'  => $user->teacher_school_id,
            'role_id'   => $user->role_id,
            'role' => $role_name,
            'logged_in' => TRUE
        ]);

        // Return JSON with role for front-end redirect
        echo json_encode([
            'status' => 'success',
            'role'   => $role_name
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }
}


    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }
}