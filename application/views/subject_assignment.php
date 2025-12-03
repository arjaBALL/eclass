<main>
    <div class=" m-3">
        <div class="row">
            <div class="col d-flex justify-content-end">
                <!-- Button to trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#subjectAssignmentModal">
                    New Subject Assignment
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="subjectAssignmentModal" tabindex="-1" aria-labelledby="subjectAssignmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subjectAssignmentModalLabel">Add New Subject Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="subjectAssignmentForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-2">
                                <label for="subjectAssignmentSelect" class="form-label mb-0">
                                    <small>Subject</small>
                                </label>
                                <select id="subjectAssignmentSelect" name="subjectAssignmentSelect"
                                    class="form-select form-select-sm">
                                    <option value="">Choose:</option>
                                    <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['id'] ?>"><?= $subject['subject_code'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-2">
                                    <label for="teacherSelect" class="form-label mb-0">
                                        <small>Teacher</small>
                                    </label>
                                    <select id="teacherSelect" name="teacherSelect" class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>">
                                            <?= $teacher['lastname'] . ', ' . $teacher['firstname'] . $teacher['middlename'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2">
                                    <label for="semesterSelect" class="form-label mb-0">
                                        <small>Semester</small>
                                    </label>
                                    <select id="semesterSelect" name="semesterSelect"
                                        class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($semesters as $semester): ?>
                                        <option value="<?= $semester['id'] ?>"><?= $semester['semester'] ?>
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

    <!-- Edit Subject Assignment Modal -->
    <div class="modal fade" id="editSubjectAssignmentModal" tabindex="-1"
        aria-labelledby="editSubjectAssignmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubjectAssignmentModalLabel">Edit Subject Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSubjectAssignmentForm">
                    <div class="modal-body">
                        <input type="hidden" id="editSubjectAssignmentId" name="editSubjectAssignmentId">
                        <div class="mb-2">
                            <label for="editSubjectSelect" class="form-label"><small>Subject</small></label>
                            <select id="editSubjectSelect" name="subjectAssignmentSelect"
                                class="form-select form-select-sm">
                                <option value="">Choose:</option>
                                <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>"><?= $subject['subject_code'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="editTeacherSelect" class="form-label"><small>Teacher</small></label>
                            <select id="editTeacherSelect" name="teacherSelect" class="form-select form-select-sm">
                                <option value="">Choose:</option>
                                <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>">
                                    <?= $teacher['lastname'] . ', ' . $teacher['firstname'] . ' ' . $teacher['middlename'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="editSemesterSelect" class="form-label"><small>Semester</small></label>
                            <select id="editSemesterSelect" name="semesterSelect" class="form-select form-select-sm">
                                <option value="">Choose:</option>
                                <?php foreach ($semesters as $semester): ?>
                                <option value="<?= $semester['id'] ?>"><?= $semester['semester'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="mb-3 col-2">
            <label for="filterTeacherSelect" class="form-label mb-0">
                <small>Teacher</small>
            </label>
            <select id="filterTeacherSelect" name="filterTeacherSelect" class="form-select form-select-sm">
                <option value="">Choose:</option>
                <?php foreach ($teachers as $teacher): ?>
                <option value="<?= $teacher['id'] ?>">
                    <?= $teacher['lastname'] . ', ' . $teacher['firstname'] . $teacher['middlename'] ?>
                </option>
                <?php endforeach; ?>

            </select>
        </div>
        <div class="mb-3 col-2">
            <label for="filterSubjectSelect" class="form-label mb-0">
                <small>Subject</small>
            </label>
            <select id="filterSubjectSelect" name="filterSubjectSelect" class="form-select form-select-sm">
                <option value="">Choose:</option>
                <?php foreach ($subjects as $subject): ?>
                <option value="<?= $subject['id'] ?>">
                    <?= $subject['subject_code'] ?>
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

    <table id="vessels" class="table table-hover table-bordered text-center table-responsive">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" id="selectAllToday" class="form-check-input">
                </th>
                <th scope="col">Teacher</th>
                <th scope="col">Subject</th>
                <th scope="col">Semester</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="subjectAssignmentsData">
        </tbody>
    </table>
</main>