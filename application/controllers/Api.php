<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Students_model');
        $this->load->model('Subjects_model');
        $this->load->model('Teacher_model');
        $this->load->model('Sections_model');
        $this->load->model('Department_model');
        $this->load->model('Subject_assignment_model');
    }

    public function get_subjects()
    {
        header('Content-Type: application/json');
        $subjects = $this->Subjects_model->get_all_subjects();
        echo json_encode($subjects);
    }

    public function get_students()
    {
        header('Content-Type: application/json');
        $students = $this->Students_model->get_all_students();
        echo json_encode($students);
    }

    public function get_teachers()
    {
        header('Content-Type: application/json');
        $teachers = $this->Teacher_model->get_all_teachers();
        echo json_encode($teachers);
    }

    public function get_sections()
    {
        header('Content-Type: application/json');
        $sections = $this->Sections_model->get_all_sections();
        echo json_encode($sections);
    }

    public function get_departments()
    {
        header('Content-Type: application/json');
        $departments = $this->Department_model->get_all_departments();
        echo json_encode($departments);
    }

    public function addStudent()
    {
        $lastName = $this->input->post('lastName');
        $firstName = $this->input->post('firstName');
        $middleName = $this->input->post('middleName');
        $studentSchoolId = $this->input->post('studentSchoolId');
        $yearSelect = $this->input->post('yearSelect');
        $programSelect = $this->input->post('programSelect');
        $sectionSelect = $this->input->post('sectionSelect');
        $statusSelect = $this->input->post('statusSelect');

        if (
            empty($lastName) ||
            empty($firstName) ||
            empty($middleName) ||
            empty($studentSchoolId) ||
            empty($yearSelect) ||
            empty($programSelect) ||
            empty($sectionSelect) ||
            empty($statusSelect)

        ) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $exists = $this->Students_model->validate_data($lastName, $firstName, $middleName, $yearSelect, $programSelect, $sectionSelect);

        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Student already exists']);
            return;
        }

        $data = [
            'lastname' => $lastName,
            'firstname' => $firstName,
            'middlename' => $middleName,
            'student_school_id' => $studentSchoolId,
            'year_level_id' => $yearSelect,
            'program_id' => $programSelect,
            'section_id' => $sectionSelect,
            'status' => $statusSelect,
        ];

        $insertData = $this->Students_model->insert_student($data);

        if ($insertData) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert student']);
        }
    }

    public function addSubjects()
    {
        $subject = $this->input->post('subject');
        $subjectCode = $this->input->post('subjectCode');
        $departmentSelect = $this->input->post('departmentSelect');
        $statusSelect = $this->input->post('statusSelect');

        if (
            empty($subject) ||
            empty($subjectCode) ||
            empty($departmentSelect) ||
            empty($statusSelect)
        ) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $exists = $this->Subjects_model->validate_data($subject, $subjectCode, $departmentSelect, $statusSelect);

        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Subject already exists']);
            return;
        }

        $data = [
            'subject_name' => $subject,
            'subject_code' => $subjectCode,
            'department_id' => $departmentSelect,
            'status_id' => $statusSelect,
        ];

        $insertData = $this->Subjects_model->insert_subjects($data);

        if ($insertData) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert subject']);
        }
    }

    public function addTeachers()
    {
        $teacherSchoolId = $this->input->post('teacherSchoolId');
        $lastName = $this->input->post('lastName');
        $firstName = $this->input->post('firstName');
        $middleName = $this->input->post('middleName');
        $role = $this->input->post('teacherRoleSelect');
        $department = $this->input->post('departmentSelect');
        $status = $this->input->post('statusSelect');
        $password = $this->input->post('password');

        if (empty($lastName) || empty($firstName) || empty($middleName) || empty($teacherSchoolId) || empty($role) || empty($department) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'All Fields Required']);
            return;
        }

        $exists = $this->Teacher_model->validate_data($teacherSchoolId, $lastName, $firstName, $middleName);

        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Subject already exists']);
            return;
        }

        $data = [
            'teacher_school_id' => $teacherSchoolId,
            'lastname' => $lastName,
            'firstname' => $firstName,
            'middlename' => $middleName,
            'role_id' => $role,
            'department_id' => $department,
            'status_id' => $status,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        $insert_id = $this->Teacher_model->insert_teachers($data);
        if ($insert_id) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert teacher']);
        }
    }

    public function get_subjectAssignments()
    {
        header('Content-Type: application/json');
        $subjectAssignment = $this->Subject_assignment_model->get_all_subject_assignments();
        echo json_encode($subjectAssignment);
    }


    public function addSections()
    {
        $section = $this->input->post('section');
        $programSelect = $this->input->post('programSelect');

        if (
            empty($section) ||
            empty($programSelect)
        ) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $exists = $this->Sections_model->validate_data($section, $programSelect);

        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Section already exists']);
            return;
        }

        $data = [
            'section' => $section,
            'program_id' => $programSelect,
        ];

        $insertData = $this->Sections_model->insert_sections($data);

        if ($insertData) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert section']);
        }
    }

    public function addDepartments()
    {
        $department = $this->input->post('department');
        $statusSelect = $this->input->post('statusSelect');

        if (
            empty($department) ||
            empty($statusSelect)
        ) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $exists = $this->Department_model->validate_data($department, $statusSelect);

        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Department already exists']);
            return;
        }

        $data = [
            'department' => $department,
            'status_id' => $statusSelect,
        ];

        $insertData = $this->Department_model->insert_departments($data);

        if ($insertData) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert department']);
        }
    }

    public function addSubjectAssignments()
    {
        $subjectAssignmentSelect = $this->input->post('subjectAssignmentSelect');
        $teacherSelect = $this->input->post('teacherSelect');
        $semesterSelect = $this->input->post('semesterSelect');

        if (
            empty($subjectAssignmentSelect) ||
            empty($teacherSelect) ||
            empty($semesterSelect)
        ) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $exists = $this->Subject_assignment_model->validate_data($subjectAssignmentSelect, $teacherSelect, $semesterSelect);

        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Subject already exists']);
            return;
        }

        $data = [
            'subject_id' => $subjectAssignmentSelect,
            'teacher_id' => $teacherSelect,
            'semester_id' => $semesterSelect,
        ];

        $insertData = $this->Subject_assignment_model->insert_subject_assignments($data);

        if ($insertData) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert subject']);
        }
    }
}

