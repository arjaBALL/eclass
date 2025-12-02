<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Record_score_model extends CI_Model
{
    public function validate_data($recordScoreBtn, $criteria, $weight, $grading_period)
    {
        $this->db->where('schedule_id', $recordScoreBtn);
        $this->db->where('criteria', $criteria);
        $this->db->where('grading_id', $grading_period);
        $this->db->where('weight', $weight);
        $query = $this->db->get('tbl_subject_criteria');

        return $query->num_rows() > 0; // true if duplicate exists
    }
    public function insert_score_record($data)
    {
        $this->db->insert('tbl_subject_criteria', $data);
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
        $this->db->select('sa.id, s.subject_name, s.subject_code, sm.semester');
        $this->db->from('tbl_subject_assignments sa');
        $this->db->join('tbl_subjects s', 's.id = sa.subject_id', 'left');
        $this->db->join('tbl_semesters sm', 'sm.id = sa.semester_id', 'left');
        $this->db->where('sa.teacher_id', $teacher_id);
        $query = $this->db->get();
        return $query->result();
    }


}