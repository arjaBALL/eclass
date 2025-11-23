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
