<script>
  const GRADING_PERIODS = <?= json_encode($grading_periods) ?>;
</script>

<body>
  <div class="row">
    <div class="mb-3 col-2">
      <label for="filterSubjectSelect" class="form-label mb-0">
        <small>Subject</small>
      </label>
      <select id="filterSubjectSelect" name="filterSubjectSelect" class="form-select form-select-sm">
        <option value="">Choose:</option>
        <?php foreach ($subjects as $subject): ?>
          <option value="<?= $subject['id'] ?>"><?= $subject['subject_code'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col">
      <div class="col-3">
        <button id="resetFiltersBtn" class="btn border btn-sm mt-4"><small><i
              class="fa-solid fa-arrow-rotate-right"></i>
            Reset Filters</small></button>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <table id="vessels" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr>
            <th scope="col">Subjects</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="subjectsData">
        </tbody>
      </table>
    </div>
    <div class="col-md-8">
      <table id="subjects" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr>
            <th scope="col">Class Code</th>
            <th scope="col">Day Schedule</th>
            <th>Time Start - End</th>
            <th>Section</th>
            <th>Year</th>
            <th>Room</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="subjectSchedulesData">
        </tbody>
      </table>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <table id="students" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr>
            <th scope="col">Student ID</th>
            <th scope="col">Student Name</th>
            <th scope="col">Section</th>
            <th scope="col">Year</th>
            <th scope="col">Program</th>
            <th scope="col">Status</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="studentsData">
        </tbody>
      </table>
    </div>
  </div>

  <!-- Face Registration Modal -->
  <div class="modal fade" id="faceRegistrationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Face Registration - <span id="studentNameDisplay"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Video Feed Container -->
          <div class="text-center">
            <div id="registrationIdle" class="mb-3">
              <p class="mb-3">
                Position the student's face inside the frame and ensure good lighting.
              </p>
              <button class="btn btn-primary" id="startRegistrationBtn">
                <i class="fas fa-camera me-2"></i>
                Start Camera
              </button>
            </div>

            <div id="registrationActive" class="position-relative webcam-container" style="display: none;">
              <video id="registrationVideo" class="webcam-video" autoplay playsinline></video>
              <canvas id="registrationCanvas" style="display: none;"></canvas>

              <!-- Guide rectangle overlay -->
              <div class="guide-rectangle"></div>

              <!-- Registration status info -->
              <div class="registration-info">
                <div class="registration-progress">
                  <div class="progress mb-2">
                    <div id="registrationProgressBar" class="progress-bar" style="width: 0%"></div>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span id="registrationProgressText">Progress: 0/5</span>
                    <span id="registrationError" class="text-danger"></span>
                  </div>
                </div>
              </div>
            </div>

            <div id="registrationProcessing" class="text-center my-4" style="display: none;">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Processing...</span>
              </div>
              <p class="mt-2">Processing face data...</p>
            </div>

            <div id="registrationSuccess" class="alert alert-success my-3" style="display: none;">
              <i class="fas fa-check-circle me-2"></i>
              Face registered successfully!
            </div>

            <div id="registrationErrorAlert" class="alert alert-danger my-3" style="display: none;">
              <i class="fas fa-times-circle me-2"></i>
              <span id="registrationErrorMessage"></span>
              <button class="btn btn-sm btn-outline-danger ms-2" id="retryRegistrationBtn">
                Try Again
              </button>
            </div>
          </div>

          <!-- Instructions -->
          <div id="registrationInstructions" class="mt-3" style="display: none;">
            <div class="card">
              <div class="card-header">Instructions</div>
              <div class="card-body">
                <ul class="mb-0">
                  <li>Ensure the face is properly centered in the green rectangle.</li>
                  <li>Make sure there is adequate lighting to avoid dark images.</li>
                  <li>Keep the head still to avoid blurry captures.</li>
                  <li>We'll take 5 different samples with slight variations.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button id="stopRegistrationBtn" class="btn btn-danger me-auto" style="display: none;">
            <i class="fas fa-times me-1"></i>
            Stop Camera
          </button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Attendance Modal -->
  <div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Attendance - <span id="attendanceSubjectDisplay"></span> (<span id="attendanceSectionDisplay"></span>)
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Video Feed Container -->
          <div class="text-center">
            <div id="attendanceIdle" class="mb-3">
              <p class="mb-3">Start the attendance session to begin facial recognition.</p>
              <button class="btn btn-primary" id="startAttendanceBtn">
                <i class="fas fa-camera me-2"></i>
                Start Camera
              </button>
            </div>

            <div id="attendanceActive" class="position-relative webcam-container" style="display: none;">
              <video id="attendanceVideo" class="webcam-video" autoplay playsinline></video>
              <canvas id="attendanceCanvas" style="display: none;"></canvas>

              <!-- Attendance info panel -->
              <div class="attendance-info">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="fw-bold">Students Present: <span id="presentCount">0</span>/<span
                      id="totalCount">0</span></span>
                  <div>
                    <span class="badge bg-success me-2">Recognized: <span id="recognizedCount">0</span></span>
                    <span class="badge bg-danger">Absent: <span id="absentCount">0</span></span>
                  </div>
                </div>
                <div class="progress">
                  <div id="attendanceProgressBar" class="progress-bar bg-success" style="width: 0%"></div>
                </div>
              </div>
            </div>

            <div id="attendanceProcessing" class="text-center my-4" style="display: none;">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Saving attendance...</span>
              </div>
              <p class="mt-2">Saving attendance data...</p>
            </div>

            <div id="attendanceSuccess" class="alert alert-success my-3" style="display: none;">
              <i class="fas fa-check-circle me-2"></i>
              Attendance recorded successfully!
            </div>
          </div>

          <!-- Live Attendance Results -->
          <div id="attendanceResults" class="mt-4" style="display: none;">
            <h6 class="mb-3">Live Attendance Results</h6>
            <div class="table-responsive">
              <table class="table table-sm table-bordered table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Time</th>
                  </tr>
                </thead>
                <tbody id="attendanceResultsData">
                </tbody>
              </table>
            </div>
          </div>

          <!-- Instructions -->
          <div id="attendanceInstructions" class="mt-3" style="display: none;">
            <div class="card">
              <div class="card-header">Tips for Better Recognition</div>
              <div class="card-body">
                <ul class="mb-0">
                  <li>Ensure students face the camera directly</li>
                  <li>Good lighting is essential for accurate recognition</li>
                  <li>Students should remove hats, heavy glasses or face coverings</li>
                  <li>Students should stand 2-3 feet from the camera</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <div class="me-auto">
            <button id="saveAttendanceBtn" class="btn btn-success me-2" style="display: none;">
              <i class="fas fa-save me-1"></i>
              Save Attendance
            </button>
          </div>
          <button id="stopAttendanceBtn" class="btn btn-danger" style="display: none;">
            <i class="fas fa-times me-1"></i>
            Stop Camera
          </button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <style>
    .webcam-container {
      position: relative;
      display: inline-block;
      width: 100%;
      max-width: 640px;
      margin: 0 auto;
    }

    .webcam-video {
      width: 100%;
      height: auto;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .guide-rectangle {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 60%;
      height: 70%;
      border: 3px solid #2ecc71;
      border-radius: 8px;
      pointer-events: none;
    }

    .registration-info,
    .attendance-info {
      position: absolute;
      bottom: 20px;
      left: 20px;
      right: 20px;
      background: rgba(255, 255, 255, 0.95);
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .detection-overlay {
      position: absolute;
      border: 2px solid;
      pointer-events: none;
    }

    .detection-label {
      position: absolute;
      top: -25px;
      left: 0;
      color: white;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: bold;
    }
  </style>

</body>