<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subjects_model extends CI_Model
{
    public function validate_data($subject, $subjectCode, $departmentSelect, $statusSelect)
    {
        $this->db->where('subject_name', $subject);
        $this->db->where('subject_code', $subjectCode);
        $this->db->where('department_id', $departmentSelect);
        $this->db->where('status_id', $statusSelect);
        $query = $this->db->get('tbl_subjects');

        return $query->num_rows() > 0; // true if duplicate exists
    }
    public function insert_subjects($data)
    {
        $this->db->insert('tbl_subjects', $data);
        return $this->db->insert_id();
    }

    public function get_all_subjects()
    {
        $this->db->select("
        s.id AS subject_id,
        s.subject_code,
        s.subject_name,
        s.program_id,
        s.status_id,
        p.program_name,
        st.id AS status_table_id,
        st.status
    ");
        $this->db->from("tbl_subjects s");
        $this->db->join("tbl_programs p", "p.id = s.program_id", "left");
        $this->db->join("tbl_status st", "st.id = s.status_id", "left");
        $this->db->order_by("s.subject_name", "ASC");

        return $this->db->get()->result();
    }

}

