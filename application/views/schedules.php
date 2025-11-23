<main>
  <div class=" m-3">
    <div class="row">
      <div class="col d-flex justify-content-end">
        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subjectAssignmentModal">
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
        <div class="modal-body">
          <div class="row">
            <div class="mb-2">
              <label for="subject" class="form-label"><small>Subject</small></label>
              <input type="text" name="subject" class="form-control form-control-sm" id="subject" aria-describedby=""
                required>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="mb-2">
                <label for="filterTeacherSelect" class="form-label mb-0">
                  <small>Teacher</small>
                </label>
                <select id="filterTeacherSelect" name="filterTeacherSelect" class="form-select form-select-sm">
                  <option value="">Choose:</option>
                  <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
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
        <?php foreach ($teachers as $status): ?>
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

  <di class="row">
    <div class="col-md-4">
      <table id="vessels" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr>
            <th scope="col">
              <input type="checkbox" id="selectAllToday" class="form-check-input">
            </th>
            <th scope="col">Teacher</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="departmentData">
        </tbody>
      </table>
    </div>
    <div class="col-md-8">
      <table id="vessels" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr>
            <th scope="col">
              <input type="checkbox" id="selectAllToday" class="form-check-input">
            </th>
            <th scope="col">Subjects</th>
            <th scope="col">Class Code</th>
            <th scope="col">Section</th>
            <th scope="col">Schedule</th>
            <th scope="col">Year</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="departmentData">
        </tbody>
      </table>
    </div>
  </di>
</main>