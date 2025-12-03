<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grade_report_model extends CI_Model
{
    public function getGradeReportBySchedule($schedule_id)
    {
        $sql = "
            SELECT 
                s.id AS student_id,
                CONCAT(s.lastname, ', ', s.firstname, ' ', s.middlename) AS student_name,
                sc.schedule_id,

                /* Midterm */
                SUM(CASE 
                    WHEN gp.grading_period = 'Midterm' 
                    THEN gr.weighted_grade 
                    ELSE 0 
                END) AS midterm_grade,

                /* Finals */
                SUM(CASE 
                    WHEN gp.grading_period = 'Finals' 
                    THEN gr.weighted_grade 
                    ELSE 0 
                END) AS final_grade,

                /* Final Average */
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
                ) / 2 AS final_rating

            FROM tbl_scores sc

            JOIN tbl_grade_report gr 
                ON gr.id = sc.id

            JOIN tbl_student s 
                ON s.id = sc.student_id

            JOIN tbl_subject_criteria c 
                ON c.id = sc.criteria_id

            JOIN tbl_grading_period gp 
                ON gp.id = c.grading_id

            WHERE sc.schedule_id = ?

            GROUP BY s.id, sc.schedule_id
            ORDER BY final_rating DESC
        ";

        return $this->db->query($sql, [$schedule_id])->result();
    }
}