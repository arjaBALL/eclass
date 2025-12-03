$(document).ready(function () {
	let departmentsData = [];
	loadDepartments();

	$("#departmentForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addDepartments",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Department successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#departmentForm")[0].reset();
						$("#departmentModal").modal("hide");
						loadDepartments(); // reload students
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
					text: "An error occurred while saving the department.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	function loadDepartments() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_departments",
			type: "GET",
			dataType: "json",
			success: function (response) {
				departmentsData = response || [];
				renderDepartmentsTable(departmentsData);
			},
			error: function (xhr, status, error) {
				console.error("Fetch Error:", xhr.responseText);
				$("#departmentsData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	function renderDepartmentsTable(departments) {
		const filterStatusSelect = $("#filterStatusSelect").val();

		let html = "";

		const filteredDepartments = departments.filter((departments) => {
			if (filterStatusSelect && departments.program_id != filterStatusSelect)
				return false;
			return true;
		});

		if (filteredDepartments.length > 0) {
			$.each(filteredDepartments, function (i, departments) {
				const rowClass =
					departments.status &&
					departments.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
        	<tr class="${rowClass}">
            <td>
                <input type="checkbox" class="departmentCheckbox" 
               data-id="${departments.id}" />
            </td>
            <td>${departments.department || ""}</td>
            <td>${departments.status || ""}</td>
            <td>
                <button class="btn btn-sm btn-primary editDepartments" data-id="${
									departments.id
								}" title="Edit">
                    <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                </button>
                <button class="btn btn-sm btn-danger deleteDepartments" data-id="${
									departments.id
								}" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No departments found</td></tr>';
		}

		$("#departmentsData").html(html);
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	// Edit Department
	$(document).on("click", ".editDepartments", function () {
		const deptId = $(this).data("id");
		$.ajax({
			url: BASE_URL + "index.php/Api/get_department/" + deptId,
			type: "GET",
			dataType: "json",
			success: function (data) {
				$("#editDepartmentDbId").val(data.id);
				$("#editDepartmentName").val(data.department);
				$("#editStatusSelect").val(data.status);

				const modal = new bootstrap.Modal(
					document.getElementById("editDepartmentModal")
				);
				modal.show();
			},
			error: function (xhr) {
				console.error("Fetch department error:", xhr.responseText);
				Swal.fire("Error!", "Failed to fetch department data.", "error");
			},
		});
	});

	// Submit Edit Department
	$("#editDepartmentForm").submit(function (e) {
		e.preventDefault();
		const deptId = $("#editDepartmentDbId").val();
		$.ajax({
			url: BASE_URL + "index.php/Api/update_department/" + deptId,
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire("Success!", "Department updated successfully.", "success");
					$("#editDepartmentModal").modal("hide");
					loadDepartments();
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

	// Delete Department
	$(document).on("click", ".deleteDepartments", function () {
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
					url: BASE_URL + "index.php/Api/delete_department/" + deptId,
					type: "POST",
					dataType: "json",
					success: function (response) {
						if (response.status === "success") {
							Swal.fire("Deleted!", response.message, "success");
							loadDepartments();
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
		renderDepartmentsTable(departmentsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterStatusSelect")[0].selectedIndex = 0;

		renderDepartmentsTable(departmentsData);
	});

	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".departmentCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".departmentCheckbox", function () {
		const total = $(".departmentCheckbox").length;
		const checked = $(".departmentCheckbox:checked").length;

		$("#selectAllToday").prop("checked", total === checked);
	});
});
