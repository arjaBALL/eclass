<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subject_assignment_model extends CI_Model
{
    public function validate_data($subjectAssignmentSelect, $teacherSelect, $semesterSelect)
    {
        $this->db->where('subject_id', $subjectAssignmentSelect);
        $this->db->where('teacher_id', $teacherSelect);
        $this->db->where('semester_id', $semesterSelect);
        $query = $this->db->get('tbl_subject_assignments');

        return $query->num_rows() > 0; // true if duplicate exists
    }
    public function insert_subject_assignments($data)
    {
        $this->db->insert('tbl_subject_assignments', $data);
        return $this->db->insert_id();
    }

    public function get_all_subject_assignments()
    {
        $this->db->select("
        sa.id,
        sa.subject_id,
        t.id,
        CONCAT(t.lastname, ', ', t.firstname, ' ', IFNULL(t.middlename, '')) AS fullname,
        sub.subject_code,
        sa.semester_id,
        s.semester,
    ");
        $this->db->from("tbl_subject_assignments sa");
        $this->db->join("tbl_semesters s", "s.id = sa.semester_id", "left");
        $this->db->join("tbl_teachers t", "t.id = sa.teacher_id", "left");
        $this->db->join("tbl_subjects sub", "sub.id = sa.subject_id", "left");
        return $this->db->get()->result();
    }

    public function get_all_teacher_subjects($teacher_id)
    {
        $this->db->select('sa.id, sa.subject_id, s.subject_name, s.subject_code, sm.semester');
        $this->db->from('tbl_subject_assignments sa');
        $this->db->join('tbl_subjects s', 's.id = sa.subject_id', 'left');
        $this->db->join('tbl_semesters sm', 'sm.id = sa.semester_id', 'left');
        $this->db->where('sa.teacher_id', $teacher_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_assignment($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tbl_subject_assignments')->row_array();
    }

    public function check_duplicate_edit($id, $subject_id, $teacher_id)
    {
        $this->db->where('subject_id', $subject_id);
        $this->db->where('teacher_id', $teacher_id);
        $this->db->where('id !=', $id);
        $query = $this->db->get('tbl_subject_assignments');
        return $query->num_rows() > 0;
    }

    public function update_assignment($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_subject_assignments', $data);
    }


    // Delete assignment
    public function delete_assignment($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_subject_assignments');
    }

}