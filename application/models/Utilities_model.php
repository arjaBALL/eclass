<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilities_model extends CI_Model
{

    public function get_all_year_levels()
    {
        $query = $this->db->get('tbl_year_levels');
        return $query->result_array();
    }

    public function get_all_sections()
    {
        $query = $this->db->get('tbl_sections');
        return $query->result_array();
    }

    public function get_all_subjects()
    {
        $query = $this->db->get('tbl_subjects');
        return $query->result_array();
    }
    public function get_all_student_status()
    {
        $query = $this->db->get('tbl_student_status');
        return $query->result_array();
    }

    public function get_all_teacher_status()
    {
        $query = $this->db->get('tbl_teacher_status');
        return $query->result_array();
    }

    public function get_all_user_role()
    {
        $query = $this->db->get('tbl_user_role');
        return $query->result_array();
    }

    public function get_all_programs()
    {
        $query = $this->db->get('tbl_programs');
        return $query->result_array();
    }

    public function get_all_departments()
    {
        $query = $this->db->get('tbl_departments');
        return $query->result_array();
    }

    public function get_all_status()
    {
        $query = $this->db->get('tbl_status');
        return $query->result_array();
    }

    public function get_all_roles()
    {
        $query = $this->db->get('tbl_user_role');
        return $query->result_array();
    }

    public function get_all_teachers()
    {
        $query = $this->db->get('tbl_teachers');
        return $query->result_array();
    }

    public function get_all_semesters()
    {
        $query = $this->db->get('tbl_semesters');
        return $query->result_array();
    }

    public function get_all_rooms()
    {
        $query = $this->db->get('tbl_rooms');
        return $query->result_array();
    }

    public function get_all_grading_period()
    {
        $query = $this->db->get('tbl_grading_period');
        return $query->result_array();
    }
}