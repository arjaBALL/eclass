<main>
    <div class=" m-3">
        <div class="row">
            <div class="col d-flex justify-content-end">
                <!-- Button to trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#programModal">
                    New Program
                </button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="programModalLabel">Add New Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="programForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-2">
                                <label for="programs" class="form-label"><small>Program</small></label>
                                <input type="text" name="programs" class="form-control form-control-sm" id="programs"
                                    aria-describedby="" required>
                            </div>
                            <div class="mb-2">
                                <label for="programDetails" class="form-label"><small>Program Details</small></label>
                                <input type="text" name="programDetails" class="form-control form-control-sm"
                                    id="programDetails" aria-describedby="" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-2">
                                    <label for="departmentSelect" class="form-label mb-0">
                                        <small>Department</small>
                                    </label>
                                    <select id="departmentSelect" name="departmentSelect"
                                        class="form-select form-select-sm">
                                        <option value="">Choose:</option>
                                        <?php foreach ($departments as $department): ?>
                                        <option value="<?= $department['id'] ?>"><?= $department['department'] ?>
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

    <!-- Edit Program Modal -->
    <div class="modal fade" id="editProgramModal" tabindex="-1" aria-labelledby="editProgramModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProgramModalLabel">Edit Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editProgramForm">
                    <div class="modal-body">
                        <!-- Hidden field to store DB id -->
                        <input type="hidden" id="editProgramId" name="id">

                        <div class="mb-2">
                            <label for="editProgramName" class="form-label"><small>Program</small></label>
                            <input type="text" name="editProgramName" class="form-control form-control-sm"
                                id="editProgramName" required>
                        </div>
                        <div class="mb-2">
                            <label for="editProgramDetails" class="form-label"><small>Program Details</small></label>
                            <input type="text" name="editProgramDetails" class="form-control form-control-sm"
                                id="editProgramDetails" aria-describedby="" required>
                        </div>
                        <div class="mb-2">
                            <label for="editDepartmentSelect" class="form-label"><small>Department</small></label>
                            <select id="editDepartmentSelect" name="statusSelect" class="form-select form-select-sm">
                                <option value="">Choose:</option>
                                <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['id'] ?>"><?= $department['department'] ?></option>
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


    <div class="row">
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

    <table id="programs" class="table table-hover table-bordered text-center table-responsive">
        <thead>
            <tr>

                <th scope="col">Program</th>
                <th scope="col">Program Details</th>
                <th scope="col">Department</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="programsData">
        </tbody>
    </table>
</main>