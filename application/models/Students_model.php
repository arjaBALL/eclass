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

public function get_students_with_scores($schedule_id, $criteria_id, $grade_period) {
    $students = $this->get_students_by_schedule($schedule_id);
    
    foreach ($students as &$student) {
        // Fetch student scores including total_items
        $student->scores = $this->get_student_scores_with_items(
            $student->id, 
            $schedule_id, 
            $criteria_id
        );
        
        $student->grade_report = $this->get_student_grade_report(
            $student->id, 
            $schedule_id, 
            $criteria_id, 
            $grade_period
        );
    }
    
    return $students;
}

// Modified function to include total_items
public function get_student_scores_with_items($student_id, $schedule_id, $criteria_id) {
    $this->db->select("score, col_index, total_items, total_score");
    $this->db->from("tbl_scores");
    $this->db->where("student_id", $student_id);
    $this->db->where("schedule_id", $schedule_id);
    $this->db->where("criteria_id", $criteria_id);
    
    $query = $this->db->get();
    return $query->result();
}


    public function get_students_by_schedule($schedule_id) {
        return $this->db->select('s.id, CONCAT(s.firstname, " ", s.lastname) as fullname')
            ->from('tbl_student s')
            ->join('tbl_student_schedules ss', 'ss.student_id = s.id')
            ->where('ss.schedule_id', $schedule_id)
            ->get()
            ->result();
    }

    public function get_student_scores($student_id, $schedule_id, $criteria_id) {
        return $this->db->select('score, col_index, score')
            ->from('tbl_scores')
            ->where('student_id', $student_id)
            ->where('schedule_id', $schedule_id)
            ->where('criteria_id', $criteria_id)
            ->order_by('col_index', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_student_grade_report($student_id, $schedule_id, $criteria_id, $grade_period) {
        return $this->db->select('average, weighted_grade')
            ->from('tbl_grade_report')
            ->where('student_id', $student_id)
            ->where('schedule_id', $schedule_id)
            ->where('criteria_id', $criteria_id)
            ->where('grade_period', $grade_period)
            ->get()
                 ->row_array();
    }

    public function getStudentById($id)
{
    return $this->db->where('id', $id)
        ->get('tbl_student')
        ->row_array();
}

public function updateStudent($id, $data)
{
    return $this->db->where('id', $id)->update('tbl_student', $data);
}

public function deleteStudent($id)
{
    return $this->db->where('id', $id)->delete('tbl_student');
}
}