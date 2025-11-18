<main>
  <div class=" mt-3 border">
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
          <h5 class="modal-title" id="studentModalLabel">Modal Title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          This is the content of the modal.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>
  
  <table id="vessels" class="table table-hover table-bordered text-center table-responsive">
        <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col">Vessel Name</th>
                <th scope="col">MMSI No.</th>
                <th scope="col">Vessels Owner Name</th>
                <th scope="col">Owner Address</th>
                <th scope="col">Callsign</th>
                <th scope="col">Registration Number</th>
                <th scope="col">Vessel Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="vesselData">  
        </tbody>
    </table>
</main>
