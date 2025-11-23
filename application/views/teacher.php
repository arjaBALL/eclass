<main>
  <div class=" m-3">
    <div class="row">
      <div class="col d-flex justify-content-end">
        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#teacherModal">
          New Teacher
        </button>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="teacherModalLabel">Add New Teacher</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" id="teachersForm">
          <div class="modal-body">
            <div class="row">
              <div class="mb-2">
                <label for="teacherId" class="form-label"><small>Teacher ID</small></label>
                <input type="text" name="teacherSchoolId" class="form-control form-control-sm" id="teacherId"
                  aria-describedby="" required>
              </div>
              <div class="mb-2">
                <label for="lastName" class="form-label"><small>Last Name</small></label>
                <input type="text" name="lastName" class="form-control form-control-sm" id="lastName"
                  aria-describedby="" required>
              </div>
              <div class="mb-2">
                <label for="firstName" class="form-label"><small>First Name</small></label>
                <input type="text" name="firstName" class="form-control form-control-sm" id="" aria-describedby=""
                  required>
              </div>
              <div class="mb-2">
                <label for="middleName" class="form-label"><small>Middle Name</small></label>
                <input type="text" name="middleName" class="form-control form-control-sm" id="middleName"
                  aria-describedby="" required>
              </div>
              <div class="col">
                <div class="mb-2">
                  <label for="departmentSelect" class="form-label mb-0">
                    <small>Department</small>
                  </label>
                  <select id="departmentSelect" name="departmentSelect" class="form-select form-select-sm">
                    <option value="">Choose:</option>
                    <?php foreach ($departments as $department): ?>
                      <option value="<?= $department['id'] ?>"><?= $department['department'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <!-- <div class="mb-2">
                <label for="areaOfExperties" class="form-label"><small>Area of Expertise</small></label>
                <input type="text" name="areaOfExperties" class="form-control form-control-sm" id="areaOfExperties"
                  aria-describedby="" required>
              </div> -->
              <div class="mb-2">
                <label for="password" class="form-label"><small>Password</small></label>
                <input type="password" name="password" class="form-control form-control-sm" id="password"
                  value="abcxyz123" aria-describedby="" required>
              </div>
            </div>
            <div class="row">
              <div class="col">
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
              <div class="col">
                <div class="mb-2">
                  <label for="teacherRoleSelect" class="form-label mb-0">
                    <small>Status</small>
                  </label>
                  <select id="teacherRoleSelect" name="teacherRoleSelect" class="form-select form-select-sm">
                    <option value="">Choose:</option>
                    <?php foreach ($roles as $role): ?>
                      <option value="<?= $role['id'] ?>"><?= $role['role'] ?>
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
      <label for="filterDepartmentSelect" class="form-label mb-0">
        <small>Department</small>
      </label>
      <select id="filterDepartmentSelect" name="filterDepartmentSelect" class="form-select form-select-sm">
        <option value="">Choose:</option>
        <?php foreach ($departments as $department): ?>
          <option value="<?= $department['id'] ?>"><?= $department['department'] ?></option>
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

  <table id="teachers" class="table table-hover table-bordered text-center table-responsive">
    <thead>
      <tr>
        <th scope="col">
          <input type="checkbox" id="selectAllToday" class="form-check-input">
        </th>
        <th scope="col">Teacher ID</th>
        <th scope="col">Name</th>
        <th scope="col">Department</th>
        <!-- <th scope="col">Area of Expertise</th> -->
        <th scope="col">Status</th>
        <th>Role</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="teachersData">
    </tbody>
  </table>
</main>