<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedules_model extends CI_Model
{
  public function validate_data($dailySchedule, $roomSelect, $startTime, $endTime, $yearSelect, $sectionSelect, $subjectTeacherId, $classCode)
{
    $this->db->where('year_level_id', $yearSelect);
     $this->db->where('class_code', $classCode);
    $this->db->where('section_id', $sectionSelect);
    $this->db->where('teacher_subject_id', $subjectTeacherId);
    $this->db->where('days_schedule', $dailySchedule);
    $this->db->where('room_id', $roomSelect);
    $this->db->where('time_start', $startTime);
    $this->db->where('time_end', $endTime);
    $query = $this->db->get('tbl_schedules');
    return $query->num_rows() > 0;
}

public function insert_schedules($data)
{
    $this->db->insert('tbl_schedules', $data);
    return $this->db->insert_id();
}

public function auto_assign_students($section_id, $year_level_id, $schedule_id)
{
    // Get all active students matching section + year
    $students = $this->db
        ->where('section_id', $section_id)
        ->where('year_level_id', $year_level_id)
        ->where('status', 1) // active only
        ->get('tbl_student')
        ->result_array();

    // Insert into tbl_student_schedules
    foreach ($students as $std) {
        $this->db->insert('tbl_student_schedules', [
            'student_id' => $std['id'],
            'schedule_id' => $schedule_id,
            'status_id' => 1
        ]);
    }

    return count($students); // return number of assigned students
}


    public function get_all_subjects_schedules($teacher_id)
    {
        $this->db->select("
       s.*,
       r.room
    ");
        $this->db->from("tbl_schedules s");
        $this->db->join("tbl_rooms r", "r.id = s.room_id", "left");
        $this->db->where('s.teacher_subject_id', $teacher_id);
        $this->db->order_by("s.class_code", "ASC");
        return $this->db->get()->result();
    }

}