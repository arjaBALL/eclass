<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Students_model');
        $this->load->model('Subjects_model');
        $this->load->model('Teacher_model');
        $this->load->model('Sections_model');
        $this->load->model('Department_model');
        $this->load->model('Subject_assignment_model');
        $this->load->model('Schedules_model');
        $this->load->model('Record_score_model');
        $this->load->model('Criteria_model');
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

    public function get_teacher_subjects()
    {
        header('Content-Type: application/json');
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            echo json_encode(['error' => 'Not logged in']);
            return;
        }
        $teacherSubject = $this->Subject_assignment_model->get_all_teacher_subjects($user_id);

        echo json_encode($teacherSubject);
    }

    public function get_subject_schedules()
    {
        header('Content-Type: application/json');
        $user_id = $this->input->get('user_id');
        $teacherSubject = $this->Schedules_model->get_all_subjects_schedules($user_id);
        echo json_encode($teacherSubject);
    }

    public function get_schedule_students()
    {
        header('Content-Type: application/json');
        $user_id = $this->input->get('user_id');
        $teacherSubject = $this->Schedules_model->get_all_schedule_students($user_id);
        echo json_encode($teacherSubject);
    }

        public function getCriteria()
        {
            $schedule_id = $this->input->get('schedule_id');
            $grade_period = $this->input->get('grade_period'); // Get grade period

            if (!$schedule_id || !$grade_period) {
                echo json_encode([]);
                return;
            }

            $criteria = $this->Criteria_model->get_criteria_by_schedule($schedule_id, $grade_period);
            echo json_encode($criteria);
        }


    public function getStudents()
    {
        $schedule_id = $this->input->get("schedule_id");
        $criteria_id = $this->input->get("criteria_id");

        if (!$schedule_id || !$criteria_id) {
            echo json_encode([]);
            return;
        }

        $this->load->model("Criteria_model");
        $students = $this->Criteria_model->get_students_by_schedule_criteria($schedule_id, $criteria_id);
        echo json_encode($students);
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
    public function addCriteria()
    {
        $recordScoreBtn = $this->input->post('recordScoreBtn');
        $criteria = $this->input->post('criteria');
        $grading_period = $this->input->post('gradingPeriodSelect');
        $weight = $this->input->post('weight');

        if (
            empty($recordScoreBtn) ||
            empty($criteria) ||
            empty($grading_period) ||
            empty($weight)
        ) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        $exists = $this->Record_score_model->validate_data($recordScoreBtn, $criteria, $weight, $grading_period);

        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Subject already exists']);
            return;
        }

        $data = [
            'schedule_id' => $recordScoreBtn,
            'criteria' => $criteria,
            'grading_id' => $grading_period,
            'weight' => $weight
        ];

        $insertData = $this->Record_score_model->insert_score_record($data);

        if ($insertData) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert recordScoreBtn']);
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

    private function get_room_name($room_id)
    {
        $room = $this->db->where('id', $room_id)->get('tbl_rooms')->row();
        return $room ? $room->room : 'Unknown';
    }

    public function addSchedules()
    {
        $subjectTeacherId = $this->input->post('subjectTeacherId');
        $yearSelect = $this->input->post('yearSelect');
        $classCode = $this->input->post('classCode');
        $sectionSelect = $this->input->post('sectionSelect');
        $dailySchedule = $this->input->post('dailySchedule');
        $startTime = $this->input->post('startTime');
        $endTime = $this->input->post('endTime');
        $roomSelect = $this->input->post('roomSelect');

        // Validate required fields
        if (
            empty($subjectTeacherId) ||
            empty($yearSelect) ||
            empty($classCode) ||
            empty($sectionSelect) ||
            empty($dailySchedule) ||
            empty($startTime) ||
            empty($endTime) ||
            empty($roomSelect)
        ) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        // Check for exact duplicate schedule
        $exists = $this->Schedules_model->validate_data(
            $dailySchedule,
            $roomSelect,
            $classCode,
            $startTime,
            $endTime,
            $yearSelect,
            $sectionSelect,
            $subjectTeacherId
        );

        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'This schedule already exists']);
            return;
        }

        // Check if teacher already has a schedule at this time on this day (EVEN WITH DIFFERENT ROOM)
        $teacher_conflict = $this->Schedules_model->check_teacher_time_conflict(
            $subjectTeacherId,
            $dailySchedule,
            $startTime,
            $endTime
        );

        if ($teacher_conflict) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Teacher already has a schedule on ' . $dailySchedule . ' from ' .
                    date('H:i', strtotime($teacher_conflict->time_start)) . ' to ' .
                    date('H:i', strtotime($teacher_conflict->time_end)) .
                    ' (Room: ' . $this->get_room_name($teacher_conflict->room_id) .
                    '). A teacher cannot have overlapping schedules.'
            ]);
            return;
        }

        // Check if room is already occupied at this time
        $room_conflict = $this->Schedules_model->check_room_conflict(
            $roomSelect,
            $dailySchedule,
            $startTime,
            $endTime
        );

        if ($room_conflict) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Room is already occupied on ' . $dailySchedule . ' from ' .
                    date('H:i', strtotime($room_conflict->time_start)) . ' to ' .
                    date('H:i', strtotime($room_conflict->time_end))
            ]);
            return;
        }

        // Create the schedule
        $data = [
            'class_code' => $classCode,
            'teacher_subject_id' => $subjectTeacherId,
            'year_level_id' => $yearSelect,
            'section_id' => $sectionSelect,
            'days_schedule' => $dailySchedule,
            'time_start' => $startTime,
            'time_end' => $endTime,
            'room_id' => $roomSelect
        ];

        $schedule_id = $this->Schedules_model->insert_schedules($data);

        if (!$schedule_id) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert schedule']);
            return;
        }

        // Get total students in this section/year
        $total_students = $this->db
            ->where('section_id', $sectionSelect)
            ->where('year_level_id', $yearSelect)
            ->where('status', 1)
            ->get('tbl_student')
            ->num_rows();

        // Auto-assign students (with conflict checking)
        $assigned_count = $this->Schedules_model->auto_assign_students(
            $sectionSelect,
            $yearSelect,
            $schedule_id
        );

        // Prepare success message
        if ($assigned_count === $total_students) {
            $message = 'Schedule created successfully. All ' . $assigned_count . ' students assigned.';
        } else {
            $skipped = $total_students - $assigned_count;
            $message = 'Schedule created successfully. ' . $assigned_count . ' of ' . $total_students .
                ' students assigned. (' . $skipped . ' students have conflicting schedules)';
        }

        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'schedule_id' => $schedule_id,
            'assigned_count' => $assigned_count,
            'total_students' => $total_students,
            'skipped_count' => $total_students - $assigned_count
        ]);
    }

    public function addStudentToSchedule()
    {
        $student_id = $this->input->post('student_id');
        $status_id = $this->input->post('status_id');
        $schedule_id = $this->input->post('schedule_id');

        if (
            empty($schedule_id) ||
            empty($student_id) ||
            empty($status_id)
        ) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            return;
        }

        // Get the new schedule details
        $new_schedule = $this->db
            ->where('id', $schedule_id)
            ->get('tbl_schedules')
            ->row();

        if (!$new_schedule) {
            echo json_encode(['status' => 'error', 'message' => 'Schedule not found']);
            return;
        }

        // Check if student already exists in this exact schedule
        $already_exists = $this->db
            ->where('student_id', $student_id)
            ->where('schedule_id', $schedule_id)
            ->get('tbl_student_schedules')
            ->num_rows();

        if ($already_exists > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Student is already assigned to this schedule']);
            return;
        }

        // Check if student has a schedule at the SAME DAY and SAME TIME (conflict check)
        $conflict = $this->db
            ->select('s.*, r.room')
            ->from('tbl_student_schedules ss')
            ->join('tbl_schedules s', 's.id = ss.schedule_id', 'inner')
            ->join('tbl_rooms r', 'r.id = s.room_id', 'left')
            ->where('ss.student_id', $student_id)
            ->where('s.days_schedule', $new_schedule->days_schedule)
            // Check for time overlap: schedule_start < new_end AND schedule_end > new_start
            ->where('s.time_start <', $new_schedule->time_end)
            ->where('s.time_end >', $new_schedule->time_start)
            // Only exclude dropped students (status_id = 3 for 'Drop')
            ->where('ss.status_id !=', 3)
            ->get()
            ->row();

        if ($conflict) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Student already has a schedule on ' . $conflict->days_schedule .
                    ' from ' . date('H:i', strtotime($conflict->time_start)) . ' to ' .
                    date('H:i', strtotime($conflict->time_end)) .
                    ' (Room: ' . $conflict->room . '). Cannot add to overlapping time slot.'
            ]);
            return;
        }

        // Assign student to schedule
        $assigned_count = $this->Schedules_model->assign_students(
            $student_id,
            $schedule_id,
            $status_id
        );

        if ($assigned_count > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Student assigned to schedule successfully.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to assign student to schedule.'
            ]);
        }
    }

    public function drop_student()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }

        $student_schedule_id = $this->input->post('student_schedule_id');

        if (empty($student_schedule_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Student schedule ID is required']);
            return;
        }

        // Get the student schedule record
        $student_schedule = $this->db
            ->where('id', $student_schedule_id)
            ->get('tbl_student_schedules')
            ->row();

        if (!$student_schedule) {
            echo json_encode(['status' => 'error', 'message' => 'Student schedule not found']);
            return;
        }

        // Get the drop status ID from tbl_student_status (status = 'Drop')
        $drop_status = $this->db
            ->where('status', 'Drop')
            ->get('tbl_student_status')
            ->row();

        if (!$drop_status) {
            echo json_encode(['status' => 'error', 'message' => 'Drop status not found in system']);
            return;
        }

        // Update the student status in tbl_student_schedules to 'Drop'
        $update_data = [
            'status_id' => $drop_status->id
        ];

        $updated = $this->db
            ->where('id', $student_schedule_id)
            ->update('tbl_student_schedules', $update_data);

        if ($updated) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Student has been dropped from the schedule'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to drop student']);
        }
    }

    public function inlist_student()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }

        $student_schedule_id = $this->input->post('student_schedule_id');

        if (empty($student_schedule_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Student schedule ID is required']);
            return;
        }

        // Get the student schedule record
        $student_schedule = $this->db
            ->where('id', $student_schedule_id)
            ->get('tbl_student_schedules')
            ->row();

        if (!$student_schedule) {
            echo json_encode(['status' => 'error', 'message' => 'Student schedule not found']);
            return;
        }

        // Get the Regular status ID from tbl_student_status (status = 'Regular')
        $regular_status = $this->db
            ->where('status', 'Regular')
            ->get('tbl_student_status')
            ->row();

        if (!$regular_status) {
            echo json_encode(['status' => 'error', 'message' => 'Regular status not found in system']);
            return;
        }

        // Update the student status in tbl_student_schedules back to 'Regular'
        $update_data = [
            'status_id' => $regular_status->id
        ];

        $updated = $this->db
            ->where('id', $student_schedule_id)
            ->update('tbl_student_schedules', $update_data);

        if ($updated) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Student has been restored to the schedule'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to restore student']);
        }
    }

     public function saveAllStudentScores()
    {
        header('Content-Type: application/json');

        $schedule_id  = $this->input->post('schedule_id');
        $criteria_id  = $this->input->post('criteria_id');
        $grade_period = $this->input->post('grade_period');
        $scores        = $this->input->post('scores');

        if (empty($schedule_id) || empty($criteria_id) || empty($grade_period) || empty($scores)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required fields'
            ]);
            return;
        }

        $success_count = 0;

        foreach ($scores as $score_item) {

            $score_data = [
                'student_id'  => $score_item['student_id'],
                'schedule_id' => $schedule_id,
                'criteria_id' => $criteria_id,
                'col_index'   => $score_item['col_index'],
                'score'       => $score_item['score'] ?: 0.00,
                'total_score' => $score_item['total_score'] ?: 0.00
            ];

            // Insert or update score
            $score_id = $this->Record_score_model->insert_or_update_score($score_data);

            if ($score_id) {

                $grade_report_id = $this->Record_score_model->getOrCreateGradeReport(
                    $score_item['student_id'],
                    $schedule_id,
                    $criteria_id,
                    $score_item['average'],
                    $score_item['weighted_grade'],
                    $grade_period
                );

                // Link
                $this->Record_score_model->update_score_grade_report($score_id, $grade_report_id);

                $success_count++;
            }
        }

        if ($success_count > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'All scores saved successfully',
                'count' => $success_count
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to save scores'
            ]);
        }
    }

    // SAVE SINGLE STUDENT SCORE PER COLUMN
    public function saveStudentScoreColumn()
    {
        header('Content-Type: application/json');

        $student_id     = $this->input->post('student_id');
        $schedule_id    = $this->input->post('schedule_id');
        $criteria_id    = $this->input->post('criteria_id');
        $grade_period   = $this->input->post('grade_period');
        $col_index      = $this->input->post('col_index');
        $score          = $this->input->post('score');
        $total_score    = $this->input->post('total_score');
        $average        = $this->input->post('average');
        $weighted_grade = $this->input->post('weighted_grade');

        if (
            empty($student_id) ||
            empty($schedule_id) ||
            empty($criteria_id) ||
            empty($grade_period) ||
            !isset($col_index)
        ) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required fields'
            ]);
            return;
        }

        $score_data = [
            'student_id'  => $student_id,
            'schedule_id' => $schedule_id,
            'criteria_id' => $criteria_id,
            'col_index'   => $col_index,
            'score'       => $score ?: 0.00,
            'total_score' => $total_score ?: 0.00
        ];

        $score_id = $this->Record_score_model->insert_or_update_score($score_data);

        if (!$score_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to save score'
            ]);
            return;
        }

        $grade_report_id = $this->Record_score_model->getOrCreateGradeReport(
            $student_id,
            $schedule_id,
            $criteria_id,
            $average,
            $weighted_grade,
            $grade_period
        );

        if (!$grade_report_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to save grade report'
            ]);
            return;
        }

        $this->Record_score_model->update_score_grade_report($score_id, $grade_report_id);

        echo json_encode([
            'status'          => 'success',
            'message'         => 'Score and grade report saved successfully',
            'score_id'        => $score_id,
            'grade_report_id' => $grade_report_id
        ]);
    }

    public function getStudentScore() {
        $schedule_id = $this->input->get('schedule_id');
        $criteria_id = $this->input->get('criteria_id');
        $grade_period = $this->input->get('grade_period');
        
        $students = $this->Students_model->get_students_with_scores(
            $schedule_id, 
            $criteria_id, 
            $grade_period
        );
        
        echo json_encode($students);
    }

}