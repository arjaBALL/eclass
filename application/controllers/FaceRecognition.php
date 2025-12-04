<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FaceRecognition extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('FaceRecognition_model');
        $this->load->helper('url');

        // Enable error logging
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
    }

    // Validate captured face image
    public function validate_face()
    {
        header('Content-Type: application/json');

        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (!isset($data['image_data'])) {
                echo json_encode(['success' => false, 'message' => 'Missing image data']);
                return;
            }

            // Decode base64 image
            $image_data = str_replace('data:image/jpeg;base64,', '', $data['image_data']);
            $image_data = str_replace(' ', '+', $image_data);
            $decoded_image = base64_decode($image_data);

            // Create temp directory
            $temp_dir = FCPATH . 'temp';
            if (!file_exists($temp_dir)) {
                mkdir($temp_dir, 0755, true);
            }

            // Save temporary file
            $temp_file = $temp_dir . '/temp_' . time() . '.jpg';
            file_put_contents($temp_file, $decoded_image);

            // Call Python validation script
            $python_script = FCPATH . 'python_scripts/validate_face.py';

            if (!file_exists($python_script)) {
                echo json_encode(['success' => false, 'message' => 'Validation script not found']);
                return;
            }

            $command = "python " . escapeshellarg($python_script) . " " . escapeshellarg($temp_file);
            $output = shell_exec($command . " 2>&1");

            // Clean up
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }

            $result = json_decode($output, true);
            echo json_encode($result ?: ['success' => false, 'message' => 'Validation failed']);

        } catch (Exception $e) {
            error_log("Validate Face Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // Register student face
    public function register_face()
    {
        header('Content-Type: application/json');

        try {
            error_log("=== Register Face Called ===");

            $json_data = file_get_contents('php://input');
            error_log("Raw JSON: " . substr($json_data, 0, 200));

            $data = json_decode($json_data, true);

            if (!isset($data['student_id']) || !isset($data['student_name']) || !isset($data['images'])) {
                error_log("Missing data - student_id: " . (isset($data['student_id']) ? 'yes' : 'no') .
                    ", student_name: " . (isset($data['student_name']) ? 'yes' : 'no') .
                    ", images: " . (isset($data['images']) ? 'yes' : 'no'));
                echo json_encode(['success' => false, 'message' => 'Missing required data']);
                return;
            }

            $student_id = $data['student_id'];
            $student_name = $data['student_name'];
            $images = $data['images'];

            error_log("Processing registration for: $student_id - $student_name");
            error_log("Number of images: " . count($images));

            // Create face_data directory for student
            $face_data_dir = FCPATH . 'face_data/' . $student_id;
            if (!file_exists($face_data_dir)) {
                mkdir($face_data_dir, 0755, true);
                error_log("Created directory: $face_data_dir");
            }

            // Save all images
            $saved_count = 0;
            foreach ($images as $index => $image_data) {
                $image_data = str_replace('data:image/jpeg;base64,', '', $image_data);
                $image_data = str_replace(' ', '+', $image_data);
                $decoded_image = base64_decode($image_data);

                $file_path = $face_data_dir . '/' . $index . '.jpg';
                if (file_put_contents($file_path, $decoded_image)) {
                    $saved_count++;
                }
            }

            error_log("Saved $saved_count images");

            // Call Python script to generate face model
            $python_script = FCPATH . 'python_scripts/generate_face_model.py';

            if (!file_exists($python_script)) {
                error_log("Python script not found: $python_script");
                echo json_encode(['success' => false, 'message' => 'Face model generator not found']);
                return;
            }

            $command = "python " . escapeshellarg($python_script) . " " .
                escapeshellarg($student_id) . " " .
                escapeshellarg($student_name);

            error_log("Executing command: $command");
            $output = shell_exec($command . " 2>&1");
            error_log("Python output: $output");

            $result = json_decode($output, true);

            if ($result && $result['success']) {
                // Update database
                $this->FaceRecognition_model->save_face_registration($student_id, $result);
                echo json_encode($result);
            } else {
                $error_msg = $result['message'] ?? 'Failed to generate face model';
                error_log("Face model generation failed: $error_msg");
                echo json_encode(['success' => false, 'message' => $error_msg]);
            }

        } catch (Exception $e) {
            error_log("Register Face Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // Get section students
    public function get_section_students($section_id)
    {
        header('Content-Type: application/json');

        try {
            if (!$section_id || !is_numeric($section_id)) {
                echo json_encode(['success' => false, 'message' => 'Invalid section ID']);
                return;
            }

            $students = $this->FaceRecognition_model->get_students_by_section($section_id);

            echo json_encode([
                'success' => true,
                'students' => $students
            ]);

        } catch (Exception $e) {
            error_log("Get Section Students Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // Recognize faces for attendance
    public function recognize_faces()
    {
        header('Content-Type: application/json');

        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (!isset($data['image_data']) || !isset($data['section_id'])) {
                echo json_encode(['success' => false, 'message' => 'Missing required data']);
                return;
            }

            // Decode and save temporary image
            $image_data = str_replace('data:image/jpeg;base64,', '', $data['image_data']);
            $decoded_image = base64_decode($image_data);

            $temp_dir = FCPATH . 'temp';
            if (!file_exists($temp_dir)) {
                mkdir($temp_dir, 0755, true);
            }

            $temp_file = $temp_dir . '/attendance_' . time() . '.jpg';
            file_put_contents($temp_file, $decoded_image);

            // Get students in section
            $students = $this->FaceRecognition_model->get_students_by_section($data['section_id']);
            $student_ids = array_column($students, 'student_school_id');
            $student_ids_str = implode(',', $student_ids);

            // Call Python recognition script
            $python_script = FCPATH . 'python_scripts/recognize_faces.py';

            if (!file_exists($python_script)) {
                echo json_encode(['success' => false, 'message' => 'Recognition script not found']);
                return;
            }

            $command = "python " . escapeshellarg($python_script) . " " .
                escapeshellarg($temp_file) . " " .
                escapeshellarg($student_ids_str);
            $output = shell_exec($command . " 2>&1");

            // Clean up
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }

            $result = json_decode($output, true);

            if ($result && isset($result['recognized_ids'])) {
                // Map recognized IDs to student data
                $recognized_students = [];
                foreach ($result['recognized_ids'] as $id) {
                    foreach ($students as $student) {
                        if ($student['student_school_id'] == $id) {
                            $recognized_students[] = $student;
                            break;
                        }
                    }
                }
                $result['recognized_students'] = $recognized_students;
            }

            echo json_encode($result ?: ['success' => false, 'message' => 'Recognition failed']);

        } catch (Exception $e) {
            error_log("Recognize Faces Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // Mark attendance
    // Mark attendance
    public function mark_attendance()
    {
        header('Content-Type: application/json');

        try {
            $json_data = file_get_contents('php://input');
            error_log("=== Mark Attendance Request ===");
            error_log("Raw JSON: " . $json_data);

            $data = json_decode($json_data, true);

            if (!isset($data['student_ids']) || !isset($data['schedule_id'])) {
                error_log("Missing data - student_ids: " . (isset($data['student_ids']) ? 'yes' : 'no') .
                    ", schedule_id: " . (isset($data['schedule_id']) ? 'yes' : 'no'));
                echo json_encode(['success' => false, 'message' => 'Missing required data']);
                return;
            }

            $student_ids = $data['student_ids'];
            $schedule_id = $data['schedule_id'];

            error_log("Student IDs: " . json_encode($student_ids));
            error_log("Schedule ID: " . $schedule_id);

            // Validate schedule_id exists
            $this->db->where('id', $schedule_id);
            $schedule_check = $this->db->get('tbl_schedules');

            if ($schedule_check->num_rows() == 0) {
                error_log("Schedule not found: " . $schedule_id);
                echo json_encode(['success' => false, 'message' => 'Invalid schedule ID']);
                return;
            }

            $result = $this->FaceRecognition_model->mark_attendance($student_ids, $schedule_id);
            error_log("Attendance result: " . json_encode($result));

            echo json_encode($result);

        } catch (Exception $e) {
            error_log("Mark Attendance Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // Add this method to your FaceRecognition controller for debugging

    public function test_registration()
    {
        header('Content-Type: application/json');

        $checks = [
            'php_version' => phpversion(),
            'fcpath' => FCPATH,
            'temp_dir_exists' => file_exists(FCPATH . 'temp'),
            'temp_dir_writable' => is_writable(FCPATH . 'temp'),
            'face_data_dir_exists' => file_exists(FCPATH . 'face_data'),
            'face_data_dir_writable' => is_writable(FCPATH . 'face_data'),
            'face_models_dir_exists' => file_exists(FCPATH . 'face_models'),
            'face_models_dir_writable' => is_writable(FCPATH . 'face_models'),
            'validate_script_exists' => file_exists(FCPATH . 'python_scripts/validate_face.py'),
            'generate_script_exists' => file_exists(FCPATH . 'python_scripts/generate_face_model.py'),
            'recognize_script_exists' => file_exists(FCPATH . 'python_scripts/recognize_faces.py'),
            'python_version' => shell_exec('python --version 2>&1'),
        ];

        echo json_encode([
            'success' => true,
            'checks' => $checks
        ], JSON_PRETTY_PRINT);
    }
}