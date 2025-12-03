<main>
    <div class=" m-3">
        <div class="row">
            <div class="col d-flex justify-content-end">
                <!-- Button to trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sectionModal">
                    New Section
                </button>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <div class="modal fade" id="sectionModal" tabindex="-1" aria-labelledby="sectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sectionModalLabel">Add New Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="sectionForm">
                    <input type="hidden" id="editSectionDbId" name="editSectionDbId"> <!-- hidden DB id for editing -->
                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="section" class="form-label"><small>Section</small></label>
                            <input type="text" name="section" class="form-control form-control-sm" id="section"
                                required>
                        </div>
                        <div class="mb-2">
                            <label for="programSelect" class="form-label"><small>Program</small></label>
                            <select id="programSelect" name="programSelect" class="form-select form-select-sm">
                                <option value="">Choose:</option>
                                <?php foreach ($programs as $program): ?>
                                <option value="<?= $program['id'] ?>"><?= $program['program_name'] ?></option>
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
            <label for="filterProgramSelect" class="form-label mb-0">
                <small>Program</small>
            </label>
            <select id="filterProgramSelect" name="filterProgramSelect" class="form-select form-select-sm">
                <option value="">Choose:</option>
                <?php foreach ($programs as $program): ?>
                <option value="<?= $program['id'] ?>"><?= $program['program_name'] ?></option>
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

    <table id="sections" class="table table-hover table-bordered text-center table-responsive">
        <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" id="selectAllToday" class="form-check-input">
                </th>
                <th scope="col">Section</th>
                <th scope="col">Program</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="sectionsData">
        </tbody>
    </table>
</main>