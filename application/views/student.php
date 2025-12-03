<main>
    <div class=" m-3">
        <div class="row">
            <div class="col d-flex justify-content-end">
                <!-- Button to trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal">
                    New Student
                </button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentModalLabel">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="studentForm">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="student_id" id="student_id">
                            <div class="mb-2">
                                <label for="lastName" class="form-label"><small>Last Name</small></label>
                                <input type="text" name="lastName" class="form-control form-control-sm"
                                    id="exampleInputEmail1" aria-describedby="" required>
                            </div>
                            <div class="mb-2">
                                <label for="firstName" class="form-label"><small>First Name</small></label>
                                <input type="text" name="firstName" class="form-control form-control-sm"
                                    id="exampleInputEmail1" aria-describedby="" required>
                            </div>
                            <div class="mb-2">
                                <label for="middleName" class="form-label"><small>Middle Name</small></label>
                                <input type="text" name="middleName" class="form-control form-control-sm"
                                    id="exampleInputEmail1" aria-describedby="" required>
                            </div>
                            <div class="mb-2" id="schoolIDWrapper">
                                <label class="form-label"><small>Student School Id</small></label>
                                <input type="text" name="studentSchoolId" id="studentSchoolId"
                                    class="form-control form-control-sm" required>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-2">
                                    <label for="YearSelect" class="form-label mb-0">
                                        <small>Year</small>
                                    </label>
                                    <select id="YearSelect" name="yearSelect" class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($year_levels as $year_level): ?>
                                        <option value="<?= $year_level['id'] ?>"><?= $year_level['year_level'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2">
                                    <label for="ProgramSelect" class="form-label mb-0">
                                        <small>Program</small>
                                    </label>
                                    <select id="ProgramSelect" name="programSelect" class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($programs as $program): ?>
                                        <option value="<?= $program['id'] ?>"><?= $program['program_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-2">
                                    <label for="SectionSelect" class="form-label mb-0">
                                        <small>Section</small>
                                    </label>
                                    <select id="SectionSelect" name="sectionSelect" class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($sections as $section): ?>
                                        <option value="<?= $section['id'] ?>"><?= $section['section'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2">
                                    <label for="StatusSelect" class="form-label mb-0">
                                        <small>Status</small>
                                    </label>
                                    <select id="StatusSelect" name="statusSelect" class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($student_statuses as $student_status): ?>
                                        <option value="<?= $student_status['id'] ?>"><?= $student_status['status'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
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
                <button id="resetFiltersBtn" class="btn border btn-sm mt-4"><small><i
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
</main>