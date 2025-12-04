<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FaceRecognition_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    // Get all sections
    public function get_sections()
    {
        $query = $this->db->get('tbl_sections');
        return $query->result_array();
    }

    // Get students by section
    public function get_students_by_section($section_id)
    {
        $this->db->select('st.id AS student_id, st.student_school_id, st.firstname, st.lastname, st.section_id, sec.section');
        $this->db->from('tbl_student st');
        $this->db->join('tbl_sections sec', 'st.section_id = sec.id', 'left');
        $this->db->where('st.section_id', $section_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    // Save face registration info
    public function save_face_registration($student_school_id, $result)
    {
        // Get student ID from tbl_student
        $this->db->select('id');
        $this->db->where('student_school_id', $student_school_id);
        $query = $this->db->get('tbl_student');

        if ($query->num_rows() == 0) {
            return false;
        }

        $student = $query->row_array();
        $student_id = $student['id'];

        $model_version = isset($result['model_version']) ? $result['model_version'] : '2.0';
        $samples = isset($result['samples_collected']) ? $result['samples_collected'] : 0;

        // Check if registration exists
        $this->db->where('student_id', $student_id);
        $existing = $this->db->get('tbl_face_registration');

        $data = [
            'status' => 'COMPLETED',
            'samples_collected' => $samples,
            'model_version' => $model_version,
            'last_updated' => date('Y-m-d H:i:s')
        ];

        if ($existing->num_rows() > 0) {
            // Update existing
            $this->db->where('student_id', $student_id);
            return $this->db->update('tbl_face_registration', $data);
        } else {
            // Insert new
            $data['student_id'] = $student_id;
            $data['registration_date'] = date('Y-m-d H:i:s');
            $data['notes'] = 'Auto-registered via face recognition system';
            return $this->db->insert('tbl_face_registration', $data);
        }
    }

    // Get schedule students (FIXED SQL)
    public function get_schedule_students($schedule_id)
    {
        $this->db->select('st.id, st.student_school_id, st.firstname, st.lastname, 
                      st.middlename, 
                      CONCAT(st.lastname, ", ", st.firstname, " ", st.middlename) as fullname,
                      sec.id as section_id,
                      sec.section, 
                      yl.year_level, 
                      p.program_name, 
                      CASE 
                          WHEN st.status = 1 THEN "Active"
                          WHEN st.status = 0 THEN "Inactive"
                          ELSE "Unknown"
                      END as status');
        $this->db->from('tbl_student st');
        $this->db->join('tbl_sections sec', 'st.section_id = sec.id', 'left');
        $this->db->join('tbl_programs p', 'st.program_id = p.id', 'left');
        $this->db->join('tbl_year_levels yl', 'st.year_level_id = yl.id', 'left');
        $this->db->join('tbl_student_schedules ss', 'st.id = ss.student_id', 'left');
        $this->db->where('ss.schedule_id', $schedule_id);

        return $this->db->get()->result_array();
    }

    // FIXED: Mark attendance for multiple students (REMOVED LOGGING)
    public function mark_attendance($student_ids, $schedule_id)
    {
        $success_count = 0;
        $failed_count = 0;
        $duplicate_count = 0;
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $errors = [];

        error_log("=== Mark Attendance Called ===");
        error_log("Schedule ID: " . $schedule_id);
        error_log("Student IDs: " . json_encode($student_ids));
        error_log("Date: " . $date);

        foreach ($student_ids as $student_school_id) {
            try {
                // Get student internal ID
                $this->db->select('id');
                $this->db->where('student_school_id', $student_school_id);
                $query = $this->db->get('tbl_student');

                if ($query->num_rows() == 0) {
                    error_log("Student not found: " . $student_school_id);
                    $failed_count++;
                    $errors[] = "Student $student_school_id not found";
                    continue;
                }

                $student = $query->row_array();
                $student_id = $student['id'];

                error_log("Processing student ID: $student_id (School ID: $student_school_id)");

                // Check if already marked today for this schedule
                $this->db->where('student_id', $student_id);
                $this->db->where('schedule_id', $schedule_id);
                $this->db->where('attendance_date', $date);
                $existing = $this->db->get('tbl_attendance');

                if ($existing->num_rows() > 0) {
                    error_log("Duplicate attendance for student: $student_school_id");
                    $duplicate_count++;
                    continue;
                }

                // Insert attendance record
                $attendance_data = [
                    'student_id' => $student_id,
                    'schedule_id' => $schedule_id,
                    'attendance_date' => $date,
                    'time_in' => $time,
                    'status' => 'Present',
                    'recognition_method' => 'Face Recognition',
                    'confidence_score' => 95.00,
                    'remarks' => 'Auto-marked via face recognition system'
                ];

                error_log("Attempting to insert: " . json_encode($attendance_data));

                if ($this->db->insert('tbl_attendance', $attendance_data)) {
                    $insert_id = $this->db->insert_id();
                    error_log("✅ Successfully inserted attendance ID: $insert_id");
                    $success_count++;

                    // ✅ REMOVED: Logging to tbl_attendance_logs (table doesn't exist)

                } else {
                    $db_error = $this->db->error();
                    error_log("Database insert failed: " . json_encode($db_error));
                    $failed_count++;
                    $errors[] = "Failed to mark $student_school_id: " . $db_error['message'];
                }

            } catch (Exception $e) {
                error_log("Exception for student $student_school_id: " . $e->getMessage());
                $failed_count++;
                $errors[] = "Error for $student_school_id: " . $e->getMessage();
            }
        }

        error_log("=== Attendance Summary ===");
        error_log("Success: $success_count, Failed: $failed_count, Duplicates: $duplicate_count");

        $message = "Marked $success_count student(s) present";
        if ($duplicate_count > 0) {
            $message .= ", $duplicate_count already marked";
        }
        if ($failed_count > 0) {
            $message .= ", $failed_count failed";
        }

        return [
            'success' => $success_count > 0,
            'message' => $message,
            'count' => $success_count,
            'duplicates' => $duplicate_count,
            'failed' => $failed_count,
            'errors' => $errors
        ];
    }

    // Get today's attendance for a schedule
    public function get_todays_attendance($schedule_id)
    {
        $date = date('Y-m-d');

        $this->db->select('a.*, s.student_school_id, s.firstname, s.lastname');
        $this->db->from('tbl_attendance a');
        $this->db->join('tbl_student s', 'a.student_id = s.id');
        $this->db->where('a.schedule_id', $schedule_id);
        $this->db->where('a.attendance_date', $date);
        $this->db->order_by('a.time_in', 'ASC');

        return $this->db->get()->result_array();
    }

    // Get attendance summary for a student
    public function get_student_attendance_summary($student_id, $schedule_id)
    {
        $this->db->select('
            COUNT(*) as total_days,
            SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late_count
        ');
        $this->db->from('tbl_attendance');
        $this->db->where('student_id', $student_id);
        $this->db->where('schedule_id', $schedule_id);

        $result = $this->db->get()->row_array();

        if ($result && $result['total_days'] > 0) {
            $result['attendance_rate'] = round(($result['present_count'] / $result['total_days']) * 100, 2);
        } else {
            $result['attendance_rate'] = 0;
        }

        return $result;
    }
}