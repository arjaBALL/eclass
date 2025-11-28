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
  <div class="p-2 py-2 rounded border mb-3">

    <form action="" id="criteriaForm" class="d-flex justify-content-start align-items-end gap-3">
      <input type="hidden" id="recordScoreBtn" name="recordScoreBtn">

      <div>
        <label for="criteria" class="label"><small>Criteria</small></label>
        <input type="text" name="criteria" class="form-control form-control-sm" id="criteria" required>
      </div>

      <div>
        <label for="weight" class="label"><small>Weight %</small></label>
        <input type="number" name="weight" class="form-control form-control-sm" id="weight" required>
      </div>

      <div class="pb-1">
        <button type="submit" class="btn btn-primary btn-sm">
          Add Criteria
        </button>
      </div>

    </form>

  </div>


  <div class="row">
    <div class="col-3">
      <div class="card-body border rounded p-3" id="criteriaListContainer" style="display:none;">
        <h5>Criteria</h5>
        <div class="row gap-2 p-2" id="criteriaList">
          <!-- Criteria will be appended here -->
        </div>
      </div>

    </div>
    <div class="col-9 border rounded p-3">
      <div class="d-flex justify-content-end gap-2 p-2">
        <button type="button" id="addScoreColumn" class="btn btn-primary btn-sm">
          Add New Score
        </button>
        <button type="button" class="btn btn-danger btn-sm">
          <i class="fa-solid fa-circle-arrow-left"></i>
        </button>
      </div>
      <table id="scoresTable" class="table table-hover table-bordered text-center table-responsive">
        <thead>
          <tr id="scoresTableHeader">
            <th>Students</th>
            <!-- Score columns will be appended dynamically -->
            <th>Average %</th>
            <th>Weighted (%)</th>
          </tr>
        </thead>
        <tbody id="subjectScheduleData">
          <!-- Student rows will be populated dynamically -->
        </tbody>
      </table>
    </div>

  </div>
</body>