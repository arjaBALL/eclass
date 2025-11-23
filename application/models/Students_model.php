<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students_model extends CI_Model
{

    public function validate_data($lastname, $firstname, $middlename, $year_level_id, $program_id, $section_id)
    {
        $this->db->where('lastname', $lastname);
        $this->db->where('firstname', $firstname);
        $this->db->where('middlename', $middlename);
        $this->db->where('year_level_id', $year_level_id);
        $this->db->where('program_id', $program_id);
        $this->db->where('section_id', $section_id);
        $query = $this->db->get('tbl_student');

        return $query->num_rows() > 0; // true if duplicate exists
    }
    public function insert_student($data)
    {
        $this->db->insert('tbl_student', $data);
        return $this->db->insert_id();
    }

    public function get_all_students()
    {
        $this->db->select(
            "st.id,
            CONCAT(st.lastname, ', ', st.firstname, ' ', IFNULL(st.middlename, '')) AS fullname,
            st.student_school_id AS school_id,
            st.year_level_id,
            st.program_id,
            st.section_id,
            sec.section,
            y.year_level,
            p.program_name,
            s.status"
        );
        $this->db->from('tbl_student st');
        $this->db->join('tbl_sections sec', 'sec.id = st.section_id', 'left');
        $this->db->join('tbl_year_levels y', 'y.id = st.year_level_id', 'left');
        $this->db->join('tbl_programs p', 'p.id = st.program_id', 'left');
        $this->db->join('tbl_student_status s', 's.id = st.section_id', 'left');
        $this->db->where('st.status !=', 3);
        $this->db->order_by('st.lastname', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
}