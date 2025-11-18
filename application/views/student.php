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
        <div class="modal-body">
         <div class="row">
            <div class="mb-2">
                <label for="lastName" class="form-label"><small>Last Name</small></label>
                <input type="text" name="lastName" class="form-control form-control-sm" id="exampleInputEmail1" aria-describedby="" required>
            </div>
            <div class="mb-2">
                <label for="firstName" class="form-label"><small>First Name</small></label>
                <input type="text" name="firstName" class="form-control form-control-sm" id="exampleInputEmail1" aria-describedby="" required>
            </div>
            <div class="mb-2">
                <label for="middleName" class="form-label"><small>Middle Name</small></label>
                <input type="text" name="middleName" class="form-control form-control-sm" id="exampleInputEmail1" aria-describedby="" required>
            </div>
            <div class="mb-2">
                <label for="studentSchoolId" class="form-label"><small>Student School Id</small></label>
                <input type="text" name="studentSchoolId" class="form-control form-control-sm" id="exampleInputEmail1" aria-describedby="" required>
            </div>
         </div>
            <div class="row">
                  <div class="col-6">
            <div class="mb-2">
               <label for="filterYearSelect" class="form-label mb-0">
                <small>Year</small>
            </label>
                <select id="filterYearSelect" name="filterYearSelect" class="form-select form-select-sm">
            <option value="">Choose:</option>
            <?php foreach($year_levels as $year_level): ?>
                <option value="<?= $year_level['id'] ?>"><?= $year_level['year_level'] ?></option>
            <?php endforeach; ?>
          </select>
            </div>
         </div>
         <div class="col-6">
            <div class="mb-2">
                <label for="filterProgramSelect" class="form-label mb-0">
                <small>Program</small>
            </label>
                <select id="filterProgramSelect" name="filterProgramSelect" class="form-select form-select-sm">
            <option value="">Choose:</option>
            <?php foreach($programs as $program): ?>
                <option value="<?= $program['id'] ?>"><?= $program['program'] ?></option>
            <?php endforeach; ?>
          </select>
            </div>
         </div>
            </div>
            <div class="row">
                  <div class="col-6">
            <div class="mb-2">
               <label for="filterSectionSelect" class="form-label mb-0">
                <small>Section</small>
            </label>
                <select id="filterSectionSelect" name="filterSectionSelect" class="form-select form-select-sm">
            <option value="">Choose:</option>
            <?php foreach($sections as $section): ?>
                <option value="<?= $section['id'] ?>"><?= $section['section'] ?></option>
            <?php endforeach; ?>
          </select>
            </div>
         </div>
         <div class="col-6">
            <div class="mb-2">
                <label for="filterStatusSelect" class="form-label mb-0">
                <small>Status</small>
            </label>
                <select id="filterStatusSelect" name="filterStatusSelect" class="form-select form-select-sm">
            <option value="">Choose:</option>
            <?php foreach($student_statuses as $student_status): ?>
                <option value="<?= $student_status['id'] ?>"><?= $student_status['status'] ?></option>
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
            <label for="filterYearSelect" class="form-label mb-0">
                <small>Year</small>
            </label>
                <select id="filterYearSelect" name="filterYearSelect" class="form-select form-select-sm">
            <option value="">Choose:</option>
            <?php foreach($year_levels as $year_level): ?>
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
            <?php foreach($programs as $program): ?>
                <option value="<?= $program['id'] ?>"><?= $program['program'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>

         <div class="mb-3 col-2">
            <label for="filterSectionSelect" class="form-label mb-0">
                <small>Section</small>
            </label>
                <select id="filterSectionSelect" name="filterSectionSelect" class="form-select form-select-sm">
            <option value="">Choose:</option>
            <?php foreach($sections as $section): ?>
                <option value="<?= $section['id'] ?>"><?= $section['section'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="col">
          <div class="col-2">
            <button id="resetFiltersBtn" class="btn border btn-sm mt-4"><small><i class="fa-solid fa-arrow-rotate-right"></i> Reset Filters</small></button>
          </div>
        </div>
      </div>

    <table id="vessels" class="table table-hover table-bordered text-center table-responsive">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" id="selectAllToday" class="form-check-input">
                </th>
                <th scope="col">Student ID</th>
                <th scope="col">Student Name</th>
                <th scope="col">Section</th>
                <th scope="col">Year</th>
                <th scope="col">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="vesselData">  
        </tbody>
    </table>
</main>
