<script>
  const GRADING_PERIODS = <?= json_encode($grading_periods) ?>;
</script>

<body>
  <div class="row">
    <div class="mb-3 col-2">
      <label for="filterSubjectSelect" class="form-label mb-0">
        <small>Subject</small>
      </label>
      <select id="filterSubjectSelect" name="filterSubjectSelect" class="form-select form-select-sm">
        <option value="">Choose:</option>
        <?php foreach ($subjects as $subject): ?>
          <option value="<?= $subject['id'] ?>"><?= $subject['subject_code'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col">
      <div class="col-3">
        <button id="resetFiltersBtn" class="btn border btn-sm mt-4"><small><i
              class="fa-solid fa-arrow-rotate-right"></i>
            Reset Filters</small></button>
      </div>
    </div>
  </div>

  <di class="row">
    <div class="col-md-4">
      <table id="vessels" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr>
            <th scope="col">Subjects</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="subjectsData">
        </tbody>
      </table>
    </div>
    <div class="col-md-8">
      <table id="subjects" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr>
            <th scope="col">Class Code</th>
            <th scope="col">Day Schedule</th>
            <th>Time Start - End</th>
            <th>Section</th>
            <th>Year</th>
            <th>Room</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="subjectSchedulesData">
        </tbody>
      </table>
    </div>
  </di>
  <div class="row">
    <div class="col">
      <table id="students" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr>
            <th scope="col">Student ID</th>
            <th scope="col">Student Name</th>
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

</body>