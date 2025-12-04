<!-- Sidebar -->
<div id="sidebar">
    <!-- Toggle Button -->
    <div class="d-flex justify-content-end align-items-center px-4 py-2">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Logo / Branding -->
    <div class="d-flex justify-content-center align-items-center">
        <img src="<?= base_url('public/logo.png') ?>" class="rounded-circle float-start" style="width: 65px" alt="">
    </div>
    <div class="d-flex justify-content-center">
        <div class="fs-5 fw-bold">Eclassrecord</div>
    </div>

    <!-- Navigation -->
    <nav class="nav flex-column mt-3 px-2">
        <a class="nav-link mb-2" href="<?= site_url('admin_dashboard') ?>">
            <i class="fa-solid fa-pager"></i>
            <span>Dashboard</span>
        </a>

        <a class="nav-link mb-2" href="<?= site_url('dashboard') ?>">
            <i class="fa-solid fa-pager"></i>
            <span>Dashboard</span>
        </a>

        <a class="nav-link mb-2" href="<?= site_url('classess') ?>">
            <i class="fa-solid fa-user-group"></i>
            <span>Classes</span>
        </a>

        <a class="nav-link mb-2" href="<?= site_url('attendance') ?>">
            <i class="fa-solid fa-user-check"></i>
            <span>Attendance</span>
        </a>

        <!-- Assessments Dropdown -->
        <div class="dropdown">
            <a class="btn btn-dark dropdown-toggle w-100 text-start" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-circle-check"></i>
                <span>Assessments</span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= site_url('record_score') ?>">Record Score</a></li>
                <li><a class="dropdown-item" href="<?= site_url('class_record') ?>">Class Record</a></li>
            </ul>
        </div>

        <!-- Workload Allocation Dropdown -->
        <div class="dropdown mt-2">
            <a class="btn btn-dark dropdown-toggle w-100 text-start" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-list-check"></i>
                <span>Subject Allocation</span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= site_url('schedules') ?>">Schedule</a></li>
                <li><a class="dropdown-item" href="<?= site_url('subject_assignment') ?>">Subject Assignment</a></li>
            </ul>
        </div>

        <!-- User Management Dropdown -->
        <div class="dropdown mt-2">
            <a class="btn btn-dark dropdown-toggle w-100 text-start" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-user-gear"></i>
                <span>User Management</span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= site_url('shifts') ?>">Account Status</a></li>
                <li><a class="dropdown-item" href="#">List of Shift Reports</a></li>
            </ul>
        </div>

        <!-- File Maintenance Dropdown -->
        <div class="dropdown mt-2">
            <a class="btn btn-dark dropdown-toggle w-100 text-start" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-gears"></i>
                <span>File Maintenance</span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= site_url('program') ?>">Program</a></li>
                <li><a class="dropdown-item" href="<?= site_url('student') ?>">Students</a></li>
                <li><a class="dropdown-item" href="<?= site_url('subject') ?>">Subjects</a></li>
                <li><a class="dropdown-item" href="<?= site_url('teacher') ?>">Teachers</a></li>
                <li><a class="dropdown-item" href="<?= site_url('section') ?>">Section</a></li>
                <li><a class="dropdown-item" href="<?= site_url('department') ?>">Department</a></li>
            </ul>
        </div>

        <!-- Logout -->
        <a class="nav-link mt-2" href="<?= site_url('Auth/logout') ?>">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>