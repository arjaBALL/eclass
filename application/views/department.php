<main>
    <div class=" m-3">
        <div class="row">
            <div class="col d-flex justify-content-end">
                <!-- Button to trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal">
                    New Department
                </button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="departmentModalLabel">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="departmentForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-2">
                                <label for="department" class="form-label"><small>Department</small></label>
                                <input type="text" name="department" class="form-control form-control-sm"
                                    id="department" aria-describedby="" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
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

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editDepartmentForm">
                    <div class="modal-body">
                        <!-- Hidden field to store DB id -->
                        <input type="hidden" id="editDepartmentDbId" name="id">

                        <div class="mb-2">
                            <label for="editDepartmentName" class="form-label"><small>Department</small></label>
                            <input type="text" name="department" class="form-control form-control-sm"
                                id="editDepartmentName" required>
                        </div>

                        <div class="mb-2">
                            <label for="editStatusSelect" class="form-label"><small>Status</small></label>
                            <select id="editStatusSelect" name="statusSelect" class="form-select form-select-sm">
                                <option value="">Choose:</option>
                                <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status['id'] ?>"><?= $status['status'] ?></option>
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

    <table id="departments" class="table table-hover table-bordered text-center table-responsive">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" id="selectAllToday" class="form-check-input">
                </th>
                <th scope="col">Department</th>
                <th scope="col">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="departmentsData">
        </tbody>
    </table>
</main>