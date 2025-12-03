<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedules_model extends CI_Model
{
    public function validate_data($dailySchedule, $roomSelect, $classCode, $startTime, $endTime, $yearSelect, $sectionSelect, $subjectTeacherId)
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

    public function check_teacher_time_conflict($subjectTeacherId, $dailySchedule, $startTime, $endTime)
    {
        // Get teacher_id from subject_assignments table
        $this->db->select('teacher_id');
        $this->db->where('id', $subjectTeacherId);
        $teacher_query = $this->db->get('tbl_subject_assignments');

        if ($teacher_query->num_rows() === 0) {
            return false;
        }

        $teacher_id = $teacher_query->row()->teacher_id;

        // Check if teacher has any schedule on same day and overlapping time
        $this->db->select('s.*');
        $this->db->from('tbl_schedules s');
        $this->db->join('tbl_subject_assignments sa', 'sa.id = s.teacher_subject_id', 'left');
        $this->db->where('sa.teacher_id', $teacher_id);
        $this->db->where('s.days_schedule', $dailySchedule);

        // Check for time overlap
        $this->db->where('s.time_start <', $endTime);
        $this->db->where('s.time_end >', $startTime);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return false;
    }


    public function check_room_conflict($roomSelect, $dailySchedule, $startTime, $endTime)
    {
        $this->db->select('*');
        $this->db->from('tbl_schedules');
        $this->db->where('room_id', $roomSelect);
        $this->db->where('days_schedule', $dailySchedule);

        // Check for time overlap
        $this->db->where('time_start <', $endTime);
        $this->db->where('time_end >', $startTime);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return false;
    }

    public function assign_students($student_id, $schedule_id, $status_id = 1)
    {
        // Check if student is already assigned to this schedule
        $exists = $this->db
            ->where('student_id', $student_id)
            ->where('schedule_id', $schedule_id)
            ->get('tbl_student_schedules')
            ->num_rows();

        if ($exists > 0) {
            return 0; // already exists
        }

        // Insert new record
        $this->db->insert('tbl_student_schedules', [
            'student_id' => $student_id,
            'schedule_id' => $schedule_id,
            'status_id' => $status_id
        ]);

        return 1; // newly assigned
    }

    public function insert_schedules($data)
    {
        $this->db->insert('tbl_schedules', $data);
        return $this->db->insert_id();
    }


    public function auto_assign_students($section_id, $year_level_id, $schedule_id)
    {
        // Get the schedule details
        $schedule = $this->db
            ->where('id', $schedule_id)
            ->get('tbl_schedules')
            ->row();

        if (!$schedule) {
            return 0;
        }

        // Get all active students matching section + year
        $students = $this->db
            ->where('section_id', $section_id)
            ->where('year_level_id', $year_level_id)
            ->where('status', 1) // active only
            ->get('tbl_student')
            ->result_array();

        $assigned_count = 0;

        // Insert into tbl_student_schedules only if no conflict
        foreach ($students as $student) {
            // Check if student already has this exact schedule
            $already_assigned = $this->db
                ->where('student_id', $student['id'])
                ->where('schedule_id', $schedule_id)
                ->get('tbl_student_schedules')
                ->num_rows();

            if ($already_assigned > 0) {
                continue; // Skip this student
            }

            // Check if student has a conflicting schedule (same day, overlapping time)
            $conflict = $this->db
                ->from('tbl_student_schedules ss')
                ->join('tbl_schedules s', 's.id = ss.schedule_id', 'inner')
                ->where('ss.student_id', $student['id'])
                ->where('s.days_schedule', $schedule->days_schedule)
                // Check for time overlap
                ->where('s.time_start <', $schedule->time_end)
                ->where('s.time_end >', $schedule->time_start)
                // Only exclude dropped students (status_id = 3 for 'Drop')
                ->where('ss.status_id !=', 3)
                ->get()
                ->num_rows();

            // Only assign if no conflict exists
            if ($conflict === 0) {
                $this->db->insert('tbl_student_schedules', [
                    'student_id' => $student['id'],
                    'schedule_id' => $schedule_id,
                    'status_id' => 1 // Active status
                ]);
                $assigned_count++;
            }
        }

        return $assigned_count; // Return number of successfully assigned students
    }

    public function get_all_subjects_schedules($teacher_id)
    {
        $this->db->select("s.*, r.room, sec.section, y.year_level");
        $this->db->from("tbl_schedules s");
        $this->db->join("tbl_rooms r", "r.id = s.room_id", "left");
        $this->db->join("tbl_sections sec", "sec.id = s.section_id", "left");
        $this->db->join("tbl_year_levels y", "y.id = s.year_level_id", "left");
        $this->db->where('s.teacher_subject_id', $teacher_id);
        $this->db->order_by("s.class_code", "ASC");
        return $this->db->get()->result();
    }

    public function get_all_schedule_students($teacher_id)
    {
        $this->db->select("
            ss.*,
            CONCAT(st.lastname, ', ', st.firstname, ' ', IFNULL(st.middlename, '')) AS fullname,
            se.section,
            p.program_name,
            sta.status
        ");
        $this->db->from("tbl_student_schedules ss");
        $this->db->join("tbl_student st", "st.id = ss.student_id", "left");
        $this->db->join("tbl_year_levels yl", "yl.id = st.year_level_id", "left");
        $this->db->join("tbl_sections se", "se.id = st.section_id", "left");
        $this->db->join("tbl_programs p", "p.id = st.program_id", "left");
        $this->db->join("tbl_student_status sta", "sta.id = ss.status_id", "left");
        $this->db->where('ss.schedule_id', $teacher_id);
        $this->db->order_by("st.lastname", "ASC");
        return $this->db->get()->result();
    }

    /**
     * Drop a student from schedule by updating status to 'Drop'
     */
    public function drop_student_from_schedule($student_schedule_id)
    {
        // Get the drop status ID
        $drop_status = $this->db
            ->where('status', 'Drop')
            ->get('tbl_student_status')
            ->row();

        if (!$drop_status) {
            return false;
        }

        // Update status to drop
        $this->db
            ->where('id', $student_schedule_id)
            ->update('tbl_student_schedules', ['status_id' => $drop_status->id]);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Get student schedule with student and schedule details
     */
    public function get_student_schedule_details($student_schedule_id)
    {
        $this->db->select('
        ss.*,
        CONCAT(st.lastname, \', \', st.firstname, \' \', IFNULL(st.middlename, \'\')) AS fullname,
        s.days_schedule,
        s.time_start,
        s.time_end,
        r.room,
        sta.status
    ');
        $this->db->from('tbl_student_schedules ss');
        $this->db->join('tbl_student st', 'st.id = ss.student_id', 'left');
        $this->db->join('tbl_schedules s', 's.id = ss.schedule_id', 'left');
        $this->db->join('tbl_rooms r', 'r.id = s.room_id', 'left');
        $this->db->join('tbl_student_status sta', 'sta.id = ss.status_id', 'left');
        $this->db->where('ss.id', $student_schedule_id);

        return $this->db->get()->row();
    }
}