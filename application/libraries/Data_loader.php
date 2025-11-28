<?php
class Data_loader
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        // Load needed models
        $this->CI->load->model([
            'Utilities_model',
            'Subject_assignment_model'  // if needed
        ]);

        // 💡 Load session here
        $this->CI->load->library('session');
    }

    public function dropdowns()
    {
        // SAFE TO USE SESSION NOW
        $user_id = $this->CI->session->userdata('user_id');

        return [
            'year_levels' => $this->CI->Utilities_model->get_all_year_levels(),
            'subjects' => $this->CI->Utilities_model->get_all_subjects($user_id),
            'sections' => $this->CI->Utilities_model->get_all_sections(),
            'student_statuses' => $this->CI->Utilities_model->get_all_student_status(),
            'teacher_status' => $this->CI->Utilities_model->get_all_teacher_status(),
            'user_role' => $this->CI->Utilities_model->get_all_user_role(),
            'programs' => $this->CI->Utilities_model->get_all_programs(),
            'departments' => $this->CI->Utilities_model->get_all_departments(),
            'statuses' => $this->CI->Utilities_model->get_all_status(),
            'roles' => $this->CI->Utilities_model->get_all_roles(),
            'teachers' => $this->CI->Utilities_model->get_all_teachers(),
            'semesters' => $this->CI->Utilities_model->get_all_semesters(),
            'rooms' => $this->CI->Utilities_model->get_all_rooms(),

            // Optional: logged-in teacher_id
            'logged_in_teacher_id' => $user_id
        ];
    }
}
