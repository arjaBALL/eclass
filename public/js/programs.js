$(document).ready(function () {
	let programsData = [];
	loadPrograms();

	$("#programForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addPrograms",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Program successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#programForm")[0].reset();
						$("#programModal").modal("hide");
						loadPrograms(); // reload students
					});
				} else if (response.status === "error") {
					Swal.fire({
						icon: "error",
						title: "Error!",
						text: response.message || "Something went wrong!",
						confirmButtonText: "OK",
					});
				}
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", xhr.responseText);
				Swal.fire({
					icon: "error",
					title: "AJAX Error!",
					text: "An error occurred while saving the program.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	function loadPrograms() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_programs",
			type: "GET",
			dataType: "json",
			success: function (response) {
				programsData = response || [];
				renderProgramsTable(programsData);
			},
			error: function (xhr, status, error) {
				console.error("Fetch Error:", xhr.responseText);
				$("#programsData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	function renderProgramsTable(programs) {
		const filterStatusSelect = $("#filterStatusSelect").val();

		let html = "";

		const filteredPrograms = programs.filter((programs) => {
			if (filterStatusSelect && programs.program_id != filterStatusSelect)
				return false;
			return true;
		});

		if (filteredPrograms.length > 0) {
			$.each(filteredPrograms, function (i, programs) {
				const rowClass =
					programs.status && programs.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
        	<tr class="${rowClass}">
          
            <td>${programs.program_name || ""}</td>
             <td>${programs.program_details || ""}</td>
            <td>${programs.department || ""}</td>
            <td>
                <button class="btn btn-sm btn-primary editPrograms" data-id="${
									programs.id
								}" title="Edit">
                    <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                </button>
                <button class="btn btn-sm btn-danger deletePrograms" data-id="${
									programs.id
								}" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No programs found</td></tr>';
		}

		$("#programsData").html(html);
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	// Edit Program
	$(document).on("click", ".editPrograms", function () {
		const deptId = $(this).data("id");
		$.ajax({
			url: BASE_URL + "index.php/Api/get_program/" + deptId,
			type: "GET",
			dataType: "json",
			success: function (data) {
				// FIX 1: Use correct hidden field ID from HTML
				$("#editProgramId").val(data.id);

				// FIX 2: Map to correct field names from database
				$("#editProgramName").val(data.program_name);
				$("#editProgramDetails").val(data.program_details);
				$("#editDepartmentSelect").val(data.department_id);

				const modal = new bootstrap.Modal(
					document.getElementById("editProgramModal")
				);
				modal.show();
			},
			error: function (xhr) {
				console.error("Fetch program error:", xhr.responseText);
				Swal.fire("Error!", "Failed to fetch program data.", "error");
			},
		});
	});

	// Submit Edit Program
	$("#editProgramForm").submit(function (e) {
		e.preventDefault();
		const deptId = $("#editProgramDbId").val();
		$.ajax({
			url: BASE_URL + "index.php/Api/update_program/" + deptId,
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire("Success!", "Program updated successfully.", "success");
					$("#editProgramModal").modal("hide");
					loadPrograms();
				} else {
					Swal.fire("Error!", response.message || "Update failed.", "error");
				}
			},
			error: function (xhr) {
				console.error(xhr.responseText);
				Swal.fire("Error!", "AJAX error occurred.", "error");
			},
		});
	});

	// Delete Program
	$(document).on("click", ".deletePrograms", function () {
		const deptId = $(this).data("id");
		Swal.fire({
			title: "Are you sure?",
			text: "This action cannot be undone!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, delete it!",
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: BASE_URL + "index.php/Api/delete_program/" + deptId,
					type: "POST",
					dataType: "json",
					success: function (response) {
						if (response.status === "success") {
							Swal.fire("Deleted!", response.message, "success");
							loadPrograms();
						} else {
							Swal.fire("Error!", response.message, "error");
						}
					},
					error: function (xhr) {
						console.error(xhr.responseText);
						Swal.fire("Error!", "AJAX error occurred.", "error");
					},
				});
			}
		});
	});

	$("#filterStatusSelect").on("change", function () {
		renderProgramsTable(programsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterStatusSelect")[0].selectedIndex = 0;

		renderProgramsTable(programsData);
	});

	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".programCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".programCheckbox", function () {
		const total = $(".programCheckbox").length;
		const checked = $(".programCheckbox:checked").length;

		$("#selectAllToday").prop("checked", total === checked);
	});
});
