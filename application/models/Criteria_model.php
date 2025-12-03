<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Criteria_model extends CI_Model
{

    public function get_criteria_by_schedule($schedule_id, $grade_period)
    {
        $this->db->select('*');
        $this->db->from('tbl_subject_criteria');
        $this->db->where('schedule_id', $schedule_id);
        $this->db->where('grading_id', $grade_period);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function validate_data($recordScoreBtn, $criteria, $weight)
    {
        $this->db->where('schedule_id', $recordScoreBtn);
        $this->db->where('criteria', $criteria);
        $this->db->where('weight', $weight);
        $query = $this->db->get('tbl_subject_criteria');

        return $query->num_rows() > 0; // true if duplicate exists
    }
    public function insert_score_record($data)
    {
        $this->db->insert('tbl_subject_criteria', $data);
        return $this->db->insert_id();
    }

    public function get_students_by_schedule_criteria($schedule_id, $criteria_id)
    {
        $this->db->select("st.id, CONCAT(st.lastname, ', ', st.firstname) AS fullname, s.score");
        $this->db->from("tbl_student_schedules ss");
        $this->db->join("tbl_student st", "st.id = ss.student_id");
        $this->db->join("tbl_scores s", "s.student_id = st.id AND s.schedule_id = ss.schedule_id AND s.criteria_id = " . (int) $criteria_id, "left");
        $this->db->where("ss.schedule_id", $schedule_id);
        $query = $this->db->get();
        return $query->result_array();
    }

       public function updateCriteria($id, $data)
    {
        $this->db->where("id", $id);
        $this->db->update("tbl_subject_criteria", $data);

        return $this->db->affected_rows() > 0;
    }

}