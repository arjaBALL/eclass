$(document).ready(function () {
	let teachersData = [];
	loadTeachers();

	$("#teachersForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addTeachers",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Subject successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#teachersForm")[0].reset();
						$("#teacherModal").modal("hide");
						loadTeachers(); // reload students
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
					text: "An error occurred while saving the teacher.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	function loadTeachers() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_teachers",
			type: "GET",
			dataType: "json",
			success: function (response) {
				teachersData = response || [];
				renderTeachersTable(teachersData);
			},
			error: function (xhr, status, error) {
				console.error("Fetch Error:", xhr.responseText);
				$("#teachersData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	function renderTeachersTable(teachers) {
		const filterDepartmentSelect = $("#filterDepartmentSelect").val();
		const filterStatusSelect = $("#filterStatusSelect").val();

		let html = "";

		const filteredTeachers = teachers.filter((teachers) => {
			if (
				filterDepartmentSelect &&
				teachers.department_id != filterDepartmentSelect
			)
				return false;
			if (filterStatusSelect && teachers.status_id != filterStatusSelect)
				return false;
			return true;
		});

		if (filteredTeachers.length > 0) {
			$.each(filteredTeachers, function (i, teachers) {
				const rowClass =
					teachers.status && teachers.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
        	<tr class="${rowClass}">
            <td>
                <input type="checkbox" class="teacherCheckbox" 
               data-id="${teachers.id}" />
            </td>
            <td>${teachers.teacher_school_id || ""}</td>
            <td>${teachers.fullname || ""}</td>
            <td>${teachers.department || ""}</td>
            <td>${teachers.status || ""}</td>
            <td>${teachers.role || ""}</td>
            <td>
                <button class="btn btn-sm btn-primary editTeachers" data-id="${
									teachers.id
								}" title="Edit">
                    <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                </button>
                <button class="btn btn-sm btn-danger deleteTeachers" data-id="${
									teachers.id
								}" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No teachers found</td></tr>';
		}

		$("#teachersData").html(html);
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	$("#filterDepartmentSelect, #filterStatusSelect").on("change", function () {
		renderTeachersTable(teachersData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterDepartmentSelect")[0].selectedIndex = 0;
		$("#filterStatusSelect")[0].selectedIndex = 0;

		renderTeachersTable(teachersData);
	});

	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".teacherCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".teacherCheckbox", function () {
		const total = $(".teacherCheckbox").length;
		const checked = $(".teacherCheckbox:checked").length;

		$("#selectAllToday").prop("checked", total === checked);
	});
});
