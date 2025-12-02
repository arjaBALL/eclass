<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Record_score_model extends CI_Model
{
    // Check duplicate subject criteria
    public function validate_data($schedule_id, $criteria, $weight, $grading_period)
    {
        $this->db->where('schedule_id', $schedule_id);
        $this->db->where('criteria', $criteria);
        $this->db->where('grading_id', $grading_period);
        $this->db->where('weight', $weight);
        $query = $this->db->get('tbl_subject_criteria');

        return $query->num_rows() > 0;
    }

    public function insert_score_record($data)
    {
        $this->db->insert('tbl_subject_criteria', $data);
        return $this->db->insert_id();
    }

    public function insert_score($data)
    {
        $this->db->insert('tbl_scores', $data);
        return $this->db->insert_id();
    }

    public function insert_grade_report($data)
    {
        $this->db->insert('tbl_grade_report', $data);
        return $this->db->insert_id();
    }

    // Link score to grade report
    public function update_score_grade_report($score_id, $grade_report_id)
    {
        $this->db->where('id', $score_id);
        return $this->db->update('tbl_scores', [
            'grade_report_id' => $grade_report_id
        ]);
    }

    // Get or Create Grade Report (no duplicates)
    public function getOrCreateGradeReport($student_id, $schedule_id, $criteria_id, $average, $weighted_grade, $grade_period)
    {
        $this->db->where('student_id', $student_id);
        $this->db->where('schedule_id', $schedule_id);
        $this->db->where('criteria_id', $criteria_id);
        $this->db->where('grade_period', $grade_period);

        $existing = $this->db->get('tbl_grade_report')->row();

        $data = [
            'student_id'     => $student_id,
            'schedule_id'    => $schedule_id,
            'criteria_id'    => $criteria_id,
            'grade_period'   => $grade_period,
            'average'        => $average ?: 0.00,
            'weighted_grade' => $weighted_grade ?: 0.00
        ];

        if ($existing) {
            $this->db->where('id', $existing->id);
            $this->db->update('tbl_grade_report', $data);
            return $existing->id;
        } else {
            $this->db->insert('tbl_grade_report', $data);
            return $this->db->insert_id();
        }
    }

    // Insert/Update Score (no duplicates)
    public function insert_or_update_score($data)
    {
        $this->db->where('student_id', $data['student_id']);
        $this->db->where('schedule_id', $data['schedule_id']);
        $this->db->where('criteria_id', $data['criteria_id']);
        $this->db->where('col_index', $data['col_index']);

        $existing = $this->db->get('tbl_scores')->row();

        if ($existing) {
            $this->db->where('id', $existing->id);
            $this->db->update('tbl_scores', $data);
            return $existing->id;
        } else {
            $this->db->insert('tbl_scores', $data);
            return $this->db->insert_id();
        }
    }
}