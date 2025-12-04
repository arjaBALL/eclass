<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';
use Mpdf\Mpdf;

class Class_list extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Class_list_model');
    }

    public function generate_pdf() {
        try {
            // Get schedule_id from POST
            $schedule_id = $this->input->post('schedule_id');
            
            if (!$schedule_id) {
                echo json_encode(['success' => false, 'message' => 'Schedule ID is required']);
                return;
            }
            
            // Get data from model
            $data = $this->Class_list_model->get_class_list_data($schedule_id);
            
            if (empty($data)) {
                echo json_encode(['success' => false, 'message' => 'No data found']);
                return;
            }
            
            // Generate PDF
            $mpdf = new Mpdf([
                'format' => 'A4',
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 10,
                'margin_right' => 10
            ]);
            
            $html = $this->generate_html($data);
            $mpdf->WriteHTML($html);
            
            // Output PDF
            $filename = 'class_list_' . date('Y-m-d_His') . '.pdf';
            $mpdf->Output($filename, 'I'); // 'I' for inline display, 'D' for download
            
        } catch (Exception $e) {
            log_message('error', 'PDF Generation Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error generating PDF: ' . $e->getMessage()]);
        }
    }
    
    private function generate_html($data) {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: "Times New Roman", Times, serif;
                    font-size: 11px;
                }
                h3, h4, p {
                    margin: 3px 0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 15px;
                }
                .header h3 {
                    font-size: 16px;
                    font-weight: bold;
                    margin-bottom: 2px;
                }
                .header p {
                    font-size: 11px;
                    margin-bottom: 8px;
                }
                .header h4 {
                    font-size: 14px;
                    font-weight: bold;
                    margin-top: 5px;
                }
                .course-info {
                    margin-bottom: 10px;
                    line-height: 1.6;
                }
                .info-table {
                    width: 100%;
                    border: none;
                    margin-top: 0;
                }
                .info-table td {
                    border: none;
                    padding: 2px;
                    text-align: left;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 5px;
                    border: 1px solid #000;
                }
                th, td {
                    border: none;
                    padding: 6px 8px;
                    text-align: center;
                    vertical-align: middle;
                }
                th:first-child, td:first-child {
                    border-left: 1px solid #000;
                }
                th:last-child, td:last-child {
                    border-right: 1px solid #000;
                }
                thead tr:first-child th {
                    border-top: 1px solid #000;
                }
                tbody tr:last-child td {
                    border-bottom: 1px solid #000;
                }
                th {
                    background-color: #e8e8e8;
                    font-weight: bold;
                    font-size: 11px;
                }
                td {
                    font-size: 10px;
                }
                td:nth-child(2) {
                    text-align: left;
                    padding-left: 8px;
                }
                .footer {
                    margin-top: 30px;
                    font-size: 10px;
                }
                .footer p {
                    text-align: center;
                    font-weight: bold;
                    margin-bottom: 15px;
                }
                .signature-table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                    border: none;
                }
                .signature-table td {
                    width: 33.33%;
                    padding: 8px;
                    vertical-align: top;
                    text-align: left;
                    border: none;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h3>Asian Development Foundation College</h3>
                <p>P. Burgos St., Tacloban City</p>
                <h4>CLASS LIST</h4>
            </div>

            <div class="course-info">
                <table class="info-table">
                    <tr>
                        <td colspan="4">
                            <strong>School Year:</strong> ' . htmlspecialchars($data['school_year']) . ' | 
                            <strong>Semester:</strong> ' . htmlspecialchars($data['semester']) . '
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <strong>Code:</strong> ' . htmlspecialchars($data['code']) . '
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <strong>Title:</strong> ' . htmlspecialchars($data['subject']) . '
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%;">
                            <strong>Units:</strong> 3.0
                        </td>
                        <td style="width: 35%;">
                            <strong>Meeting:</strong> ' . htmlspecialchars($data['meeting']) . '
                        </td>
                        <td style="width: 15%;">
                            <strong>Room:</strong> ' . htmlspecialchars($data['room']) . '
                        </td>
                        <td style="width: 25%;">
                            <strong>Instructor:</strong> ' . htmlspecialchars($data['instructor']) . '
                        </td>
                    </tr>
                </table>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">Name</th>
                        <th style="width: 8%;">Course</th>
                        <th style="width: 7%;">Year</th>
                        <th style="width: 10%;">Midterm</th>
                        <th style="width: 10%;">Pre-Final</th>
                        <th style="width: 10%;">Finals</th>
                        <th style="width: 12%;">Absences</th>
                        <th style="width: 13%;">Drop Date</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data['students'])) {
            $html .= '<tr><td colspan="9" style="text-align: center;">No students found</td></tr>';
        } else {
            foreach ($data['students'] as $student) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($student['number']) . '</td>
                    <td>' . htmlspecialchars($student['name']) . '</td>
                    <td>' . htmlspecialchars($student['program']) . '</td>
                    <td>' . htmlspecialchars($student['year']) . '</td>
                    <td>' . htmlspecialchars($student['midterm']) . '</td>
                    <td>' . htmlspecialchars($student['pre_final']) . '</td>
                    <td>' . htmlspecialchars($student['finals']) . '</td>
                    <td>' . htmlspecialchars($student['absences']) . '</td>
                    <td>' . (!empty($student['drop_date']) ? htmlspecialchars($student['drop_date']) : 'N/A') . '</td>

                </tr>';
            }
        }
        
        $html .= '</tbody>
            </table>

            <div class="footer">
                <p>***** Nothing Follows *****</p>
                <table class="signature-table">
                    <tr>
                        <td width="33.33%">Data Encoder: _________________________</td>
                        <td width="33.33%">Date Encoded: _________________________</td>
                        <td width="33.33%">VP Academic Affairs: _________________________</td>
                    </tr>
                    <tr>
                        <td width="33.33%">Date Received: _________________________</td>
                        <td width="33.33%">Date Submitted: _________________________</td>
                        <td width="33.33%">Teacher: _________________________</td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
?>