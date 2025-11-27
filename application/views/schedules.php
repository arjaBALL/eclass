<main>
    <div class=" m-3">
        <div class="row">

        </div>
    </div>

    <!-- add schedule Modal -->
    <div class="modal fade" id="subjectScheduleModal" tabindex="-1" aria-labelledby="subjectScheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subjectScheduleModalLabel">Add New Subject Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="schedulesForm">
                    <div class="modal-body">
                        <input type="hidden" id="subjectTeacherId" name="subjectTeacherId">
                        <div class="row">
                            <div class="mb-2">
                                <label for="classCode" class="form-label"><small>Class Code</small></label>
                                <input type="text" name="classCode" class="form-control form-control-sm" id="classCode"
                                    aria-describedby="" required>
                            </div>
                            <div class="mb-2">
                                <label for="dailySchedule" class="form-label"><small>Scheduled Day/s</small></label>
                                <input type="text" name="dailySchedule" class="form-control form-control-sm"
                                    id="dailySchedule" aria-describedby="" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="programSelect" class="form-label mb-0">
                                    <small>Year Level</small>
                                </label>
                                <select id="yearSelect" name="yearSelect" class="form-select form-select-sm">
                                    <option value="">Choose:</option>
                                    <?php foreach ($year_levels as $year_level): ?>
                                    <option value="<?= $year_level['id'] ?>"><?= $year_level['year_level'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="roomSelect" class="form-label mb-0">
                                    <small>Section</small>
                                </label>
                                <select id="sectionSelect" name="sectionSelect" class="form-select form-select-sm">
                                    <label for="dailySchedule" class="form-label"><small>Scheduled Day/s</small></label>
                                    <option value="">Choose:</option>
                                    <?php foreach ($sections as $section): ?>
                                    <option value="<?= $section['id'] ?>"><?= $section['section'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-12 col-sm-6 col-md-6 mb-2">
                                <label for="startTime"><small>Start Time</small></label>
                                <input type="time" id="startTime" class="form-control form-control-sm" name="startTime">
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 mb-2">
                                <label for="End Time"><small>End Time</small></label>
                                <input type="time" id="endTime" class="form-control form-control-sm" name="endTime">
                            </div>
                        </div>

                        <div class="mb-3 col">
                            <label for="roomSelect" class="form-label mb-0">
                                <small>Room</small>
                            </label>
                            <select id="roomSelect" name="roomSelect" class="form-select form-select-sm">
                                <option value="">Choose:</option>
                                <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room['id'] ?>"><?= $room['room'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--add student to the schedule modal-->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Add New Subject Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row">
                    <div class="mb-3 col-2">
                        <label for="filterYearSelect" class="form-label mb-0">
                            <small>Year</small>
                        </label>
                        <select id="filterYearSelect" name="filterYearSelect" class="form-select form-select-sm">
                            <option value="">Choose:</option>
                            <?php foreach ($year_levels as $year_level): ?>
                            <option value="<?= $year_level['id'] ?>"><?= $year_level['year_level'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 col-2">
                        <label for="filterProgramSelect" class="form-label mb-0">
                            <small>Program</small>
                        </label>
                        <select id="filterProgramSelect" name="filterProgramSelect" class="form-select form-select-sm">
                            <option value="">Choose:</option>
                            <?php foreach ($programs as $program): ?>
                            <option value="<?= $program['id'] ?>"><?= $program['program_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 col-2">
                        <label for="filterSectionSelect" class="form-label mb-0">
                            <small>Section</small>
                        </label>
                        <select id="filterSectionSelect" name="filterSectionSelect" class="form-select form-select-sm">
                            <option value="">Choose:</option>
                            <?php foreach ($sections as $section): ?>
                            <option value="<?= $section['id'] ?>"><?= $section['section'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col">
                        <div class="col-3">
                            <button id="resetFiltersBtn1" class="btn border btn-sm mt-4"><small><i
                                        class="fa-solid fa-arrow-rotate-right"></i> Reset Filters</small></button>
                        </div>
                    </div>
                </div>

                <table id="students" class="table table-hover table-bordered text-center table-responsive">
                    <thead>
                        <tr>
                            <th scope="col">
                                <input type="checkbox" id="selectAllToday" class="form-check-input">
                            </th>
                            <th scope="col">Student ID</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Section</th>
                            <th scope="col">Year</th>
                            <th scope="col">Program</th>
                            <th scope="col">Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="studentsData">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--view student modal-->
    <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentModalLabel">Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table id="viewStudent" class="table table-hover table-bordered text-center table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">Student Name</th>
                                <th scope="col">Section</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="viewStudentData">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="mb-3 col-2">
            <label for="filterDepartmentSelect" class="form-label mb-0">
                <small>Department</small>
            </label>
            <select id="filterDepartmentSelect" name="filterDepartmentSelect" class="form-select form-select-sm">
                <option value="">Choose:</option>
                <?php foreach ($departments as $department): ?>
                <option value="<?= $department['id'] ?>"><?= $department['department'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <div class="col-3">
                <button id="resetFiltersBtn" class="btn border btn-sm mt-4"><small><i
                            class="fa-solid fa-arrow-rotate-right"></i> Reset Filters</small></button>
            </div>
        </div>
    </div>

    <di class="row">
        <div class="col-md-4">
            <table id="vessels" class="table table-hover table-bordered text-center table-responsive">
                <thead>
                    <tr>
                        <th scope="col">Teacher</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="teachersData">
                </tbody>
            </table>
        </div>
        <div class="col-md-8">
            <table id="subjects" class="table table-hover table-bordered text-center table-responsive">
                <thead>
                    <tr>
                        <th scope="col">Subjects</th>
                        <th scope="col">Subject Code</th>
                        <th>Semester</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="teacherSubjectData">
                </tbody>
            </table>
        </div>
    </di>
    <div class="row">
        <div class="col-md-12">
            <table id="schedules" class="table table-hover table-bordered text-center table-responsive">
                <thead>
                    <tr>
                        <th scope="col">Class Code</th>
                        <th scope="col">Day Scheduled</th>
                        <th>Time Start - End</th>
                        <th>Room</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="subjectSchedulesData">
                </tbody>
            </table>
        </div>
    </div>
</main>