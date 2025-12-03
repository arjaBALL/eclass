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

    <div class="row">
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
    </div>

    <div class="row">
        <div class="container"></div>
        <div class="">
            <table id="subjects" class="table table-hover table-bordered text-center table-responsive">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th scope="col">Name</th>
                        <th scope="col">Midterm Grade</th>
                        <th>Pre-final Grade</th>
                        <th>Final Grade</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="gradeReportData">
                </tbody>
            </table>
        </div>
    </div>

</body>