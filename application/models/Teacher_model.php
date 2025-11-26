<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teacher_model extends CI_Model
{
    public function validate_data($teacherSchoolId, $lastName, $firstName, $middleName)
    {
        $this->db->where('teacher_school_id', $teacherSchoolId);
        $this->db->where('lastname', $lastName);
        $this->db->where('firstname', $firstName);
        $this->db->where('middlename', $middleName);
        $query = $this->db->get('tbl_teachers');

        return $query->num_rows() > 0; // true if duplicate exists
    }
    public function insert_teachers($data)
    {
        $this->db->insert('tbl_teachers', $data);
        return $this->db->insert_id();
    }

    public function get_all_teachers()
    {
        $this->db->select(
            "t.id,
            t.teacher_school_id,
            CONCAT(t.lastname, ', ', t.firstname, ' ', IFNULL(t.middlename, '')) AS fullname,
            t.role_id,
            r.role,
            t.department_id,
            t.status_id,
            d.department,
            st.id,
            st.status"
        );
        $this->db->from('tbl_teachers t');
        $this->db->join('tbl_departments d', 'd.id = t.department_id', 'left');
        $this->db->join('tbl_user_role r', 'r.id = t.role_id', 'left');
        $this->db->join('tbl_status st', 'st.id = t.status_id', 'left');
        $this->db->order_by('t.lastname', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
}