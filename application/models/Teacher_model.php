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
            t.password,
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

public function get_teacher($id) {
    $this->db->select(
        "t.id,
         t.teacher_school_id,
         t.lastname,
         t.firstname,
         t.middlename,
         t.role_id,
         r.role,
         t.department_id,
         t.status_id,
         t.password,
         d.department,
         st.status"
    );
    $this->db->from('tbl_teachers t');
    $this->db->join('tbl_departments d', 'd.id = t.department_id', 'left');
    $this->db->join('tbl_user_role r', 'r.id = t.role_id', 'left');
    $this->db->join('tbl_status st', 'st.id = t.status_id', 'left');
    $this->db->where('t.id', $id);
    $teacher = $this->db->get()->row();

    if ($teacher) {
        $teacher->password = $this->encryption->decrypt($teacher->password); // decrypt password
    }

    return $teacher;
}

    public function validate_user_login($username, $password){
       $query = $this->db->get_where('tbl_teachers', [
            'teacher_school_id' => $username,
            'status_id' => 1
        ]);
        $user = $query->row();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }

    public function update_teacher($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_teachers', $data);
    }

    public function delete_teacher($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_teachers');
    }
}