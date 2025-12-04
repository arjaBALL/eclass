<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_list_model extends CI_Model {

   
    public function get_class_list_data($schedule_id) {
        // Initialize result array
        $result = [
            'subject' => 'N/A',
            'code' => 'N/A',
            'meeting' => 'N/A',
            'room' => 'N/A',
            'instructor' => 'N/A',
            'school_year' => 'N/A',
            'semester' => 'N/A',
            'students' => []
        ];

        // Get schedule details
        $schedule_query = $this->db->select('
                sch.id,
                sch.class_code,
                sch.days_schedule,
                sch.time_start,
                sch.time_end,
                r.room,
                subj.subject_name,
                subj.subject_code,
                sy.school_year,
                sem.semester,
                CONCAT(TRIM(t.firstname), " ", TRIM(t.lastname)) as instructor_name
            ')
            ->from('tbl_schedules sch')
            ->join('tbl_rooms r', 'sch.room_id = r.id', 'left')
            ->join('tbl_subject_assignments sa', 'sch.teacher_subject_id = sa.id', 'left')
            ->join('tbl_subjects subj', 'sa.subject_id = subj.id', 'left')
            ->join('tbl_school_year sy', 'sa.school_year_id = sy.id', 'left')
            ->join('tbl_semesters sem', 'sa.semester_id = sem.id', 'left')
            ->join('tbl_teachers t', 'sa.teacher_id = t.id', 'left')
            ->where('sch.id', $schedule_id)
            ->get();

        if ($schedule_query->num_rows() > 0) {
            $schedule = $schedule_query->row();
            
            $result['subject'] = $schedule->subject_name ?? 'N/A';
            $result['code'] = $schedule->subject_code ?? 'N/A';
            $result['room'] = $schedule->room ?? 'N/A';
            $result['instructor'] = $schedule->instructor_name ?? 'N/A';
            $result['school_year'] = $schedule->school_year ?? 'N/A';
            $result['semester'] = $schedule->semester ?? 'N/A';
            
            // Format meeting time
            if ($schedule->time_start && $schedule->time_end && $schedule->days_schedule) {
                $result['meeting'] = date('H:i', strtotime($schedule->time_start)) . 
                                   '–' . 
                                   date('H:i', strtotime($schedule->time_end)) . 
                                   ' ' . 
                                   $schedule->days_schedule;
            }
        }

        // Get students enrolled in this schedule
        $students_query = $this->db->select('
                s.id as student_id,
                CONCAT(TRIM(s.lastname), ", ", TRIM(s.firstname)) as fullname,
                p.program_name as program,
                yl.year_level,
                sec.section
            ')
            ->from('tbl_student_schedules ss')
            ->join('tbl_student s', 'ss.student_id = s.id', 'inner')
            ->join('tbl_programs p', 's.program_id = p.id', 'left')
            ->join('tbl_year_levels yl', 's.year_level_id = yl.id', 'left')
            ->join('tbl_sections sec', 's.section_id = sec.id', 'left')
            ->where('ss.schedule_id', $schedule_id)
            ->where('ss.status_id', 1) // Active students only
            ->order_by('s.lastname, s.firstname', 'ASC')
            ->get();

        $counter = 1;
        
        if ($students_query->num_rows() > 0) {
            foreach ($students_query->result() as $row) {
                // Get grades for this student
                $grades = $this->get_student_grades($row->student_id, $schedule_id);
                
                // Get attendance info
                $attendance = $this->get_student_attendance($row->student_id, $schedule_id);
                
                $result['students'][] = [
                    'number' => $counter++,
                    'name' => $row->fullname,
                    'program' => $row->program ?? 'N/A',
                    'year' => $row->year_level ?? 'N/A',
                    'midterm' => $grades['midterm'] ?? '-',
                    'pre_final' => $grades['pre_final'] ?? '-',
                    'finals' => $grades['finals'] ?? '-',
                    'final_rating' => $grades['final_rating'] ?? '-',
                    'remarks' => $grades['remarks'] ?? '-',
                    'absences' => $attendance['total_absent'] ?? 0,
                    'drop_date' => $attendance['drop_date'] ?? ''
                ];
            }
        }

        return $result;
    }

    private function get_student_grades($student_id, $schedule_id) {
        // Using the same query structure as Grade_report_model
        $sql = "
            SELECT 
                /* Midterm */
                SUM(CASE 
                    WHEN gp.grading_period = 'Midterm' 
                    THEN gr.weighted_grade 
                    ELSE 0 
                END) AS midterm_grade,

                /* Pre-Final (this is the Finals period grade) */
                SUM(CASE 
                    WHEN gp.grading_period = 'Finals' 
                    THEN gr.weighted_grade 
                    ELSE 0 
                END) AS pre_final_grade,

                /* Final Rating - Average of Midterm and Pre-Final */
                (
                    SUM(CASE 
                        WHEN gp.grading_period = 'Midterm' 
                        THEN gr.weighted_grade 
                        ELSE 0 
                    END) +
                    SUM(CASE 
                        WHEN gp.grading_period = 'Finals' 
                        THEN gr.weighted_grade 
                        ELSE 0 
                    END)
                ) / 2 AS final_rating,

                /* Remarks based on Final Rating */
                CASE 
                    WHEN (
                        SUM(CASE 
                            WHEN gp.grading_period = 'Midterm' 
                            THEN gr.weighted_grade 
                            ELSE 0 
                        END) +
                        SUM(CASE 
                            WHEN gp.grading_period = 'Finals' 
                            THEN gr.weighted_grade 
                            ELSE 0 
                        END)
                    ) / 2 > 3.0 
                        THEN 'Failed'
                    ELSE 'Passed'
                END AS remarks

            FROM tbl_scores sc

            JOIN tbl_grade_report gr 
                ON gr.id = sc.id

            JOIN tbl_subject_criteria c 
                ON c.id = sc.criteria_id

            JOIN tbl_grading_period gp 
                ON gp.id = c.grading_id

            WHERE sc.schedule_id = ?
              AND sc.student_id = ?

            GROUP BY sc.student_id, sc.schedule_id
        ";

        $query = $this->db->query($sql, [$schedule_id, $student_id]);

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return [
                'midterm' => $row->midterm_grade > 0 ? number_format($row->midterm_grade, 2) : '-',
                'pre_final' => $row->pre_final_grade > 0 ? number_format($row->pre_final_grade, 2) : '-',
                'finals' => $row->final_rating > 0 ? number_format($row->final_rating, 2) : '-',
                'final_rating' => $row->final_rating > 0 ? number_format($row->final_rating, 2) : '-',
                'remarks' => $row->remarks ?? '-'
            ];
        }

        return [
            'midterm' => '-', 
            'pre_final' => '-',
            'finals' => '-',
            'final_rating' => '-',
            'remarks' => '-'
        ];
    }

    private function get_student_attendance($student_id, $schedule_id) {
        // Count absences
        $count_query = $this->db->select('COUNT(*) as total_absent')
            ->from('tbl_attendance')
            ->where('student_id', $student_id)
            ->where('schedule_id', $schedule_id)
            ->where('status', 'Absent')
            ->get();

        $total_absent = 0;
        if ($count_query->num_rows() > 0) {
            $total_absent = $count_query->row()->total_absent;
        }

        // Get drop date (3rd absence)
        $drop_date = '';
        if ($total_absent >= 3) {
            $drop_query = $this->db->select('DATE(attendance_date) as drop_date')
                ->from('tbl_attendance')
                ->where('student_id', $student_id)
                ->where('schedule_id', $schedule_id)
                ->where('status', 'Absent')
                ->order_by('attendance_date', 'ASC')
                ->limit(1, 2) // OFFSET 2, LIMIT 1
                ->get();

            if ($drop_query->num_rows() > 0) {
                $drop_date = $drop_query->row()->drop_date;
            }
        }

        return [
            'total_absent' => $total_absent,
            'drop_date' => $drop_date
        ];
    }
}
?>