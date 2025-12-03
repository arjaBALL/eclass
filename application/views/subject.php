<main>
    <div class=" m-3">
        <div class="row">
            <div class="col d-flex justify-content-end">
                <!-- Button to trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subjectModal">
                    New Subject
                </button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subjectModalLabel">Add New Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="subjectForm">
                    <div class="modal-body">
                        <input type="hidden" id="subject_id" name="subject_id">

                        <div class="row">
                            <div class="mb-2">
                                <label for="subject" class="form-label"><small>Subject</small></label>
                                <input type="text" name="subject" class="form-control form-control-sm" id="subject"
                                    aria-describedby="" required>
                            </div>
                            <div class="mb-2">
                                <label for="subjectCode" class="form-label"><small>Subject Code</small></label>
                                <input type="text" name="subjectCode" class="form-control form-control-sm"
                                    id="subjectCode" aria-describedby="" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="mb-2">
                                    <label for="programSelect" class="form-label mb-0">
                                        <small>Program</small>
                                    </label>
                                    <select id="programSelect" name="programSelect" class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($programs as $program): ?>
                                        <option value="<?= $program['id'] ?>"><?= $program['program_name'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <label for="statusSelect" class="form-label mb-0">
                                        <small>Status</small>
                                    </label>
                                    <select id="statusSelect" name="statusSelect" class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($statuses as $status): ?>
                                        <option value="<?= $status['id'] ?>"><?= $status['status'] ?>
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
            <label for="filterProgramSelect" class="form-label mb-0">
                <small>Program</small>
            </label>
            <select id="filterProgramSelect" name="filterProgramSelect" class="form-select form-select-sm">
                <option value="">Choose:</option>
                <?php foreach ($programs as $program): ?>
                <option value="<?= $program['id'] ?>"><?= $program['program_name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3 col-2">
            <label for="filterStatusSelect" class="form-label mb-0">
                <small>Status</small>
            </label>
            <select id="filterStatusSelect" name="filterStatusSelect" class="form-select form-select-sm">
                <option value="">Choose:</option>
                <?php foreach ($statuses as $status): ?>
                <option value="<?= $status['id'] ?>"><?= $status['status'] ?>
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
                <th scope="col">Subject Code</th>
                <th scope="col">Subject Name</th>
                <th scope="col">Program</th>
                <th scope="col">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="subjectsData">
        </tbody>
    </table>
</main>