$(document).ready(function () {
	let teachersData = [];
	loadTeachers();

	// Add Teacher Form
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
						text: "Teacher successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#teachersForm")[0].reset();
						$("#teacherModal").modal("hide");
						loadTeachers();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Error!",
						text: response.message || "Something went wrong!",
						confirmButtonText: "OK",
					});
				}
			},
			error: function (xhr) {
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

	// Load teachers and render table
	function loadTeachers() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_teachers",
			type: "GET",
			dataType: "json",
			success: function (response) {
				teachersData = response || [];
				renderTeachersTable(teachersData);
			},
			error: function (xhr) {
				console.error("Fetch Error:", xhr.responseText);
				$("#teachersData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	// Render teachers table
	function renderTeachersTable(teachers) {
		const filterDepartmentSelect = $("#filterDepartmentSelect").val();
		const filterStatusSelect = $("#filterStatusSelect").val();

		let html = "";

		const filteredTeachers = teachers.filter((teacher) => {
			if (
				filterDepartmentSelect &&
				teacher.department_id != filterDepartmentSelect
			)
				return false;
			if (filterStatusSelect && teacher.status_id != filterStatusSelect)
				return false;
			return true;
		});

		if (filteredTeachers.length > 0) {
			$.each(filteredTeachers, function (i, teacher) {
				const rowClass =
					teacher.status && teacher.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
                <tr class="${rowClass}">
                    <td>${i + 1}</td>
                    <td>${teacher.teacher_school_id || ""}</td>
                    <td>${teacher.fullname || ""}</td>
                    <td>${teacher.department || ""}</td>
                    <td>${teacher.status || ""}</td>
                    <td>${teacher.role || ""}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editTeachers" data-id="${
													teacher.id
												}" title="Edit">
                            <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteTeachers" data-id="${
													teacher.id
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
	}

	// --- EDIT TEACHER ---
	$(document).on("click", ".editTeachers", function () {
		const teacherId = $(this).data("id"); // this is the DB id

		$.ajax({
			url: BASE_URL + "index.php/Api/get_teacher/" + teacherId,
			type: "GET",
			dataType: "json",
			success: function (data) {
				$("#editTeacherDbId").val(data.id); // <-- set DB id
				$("#editTeacherId").val(data.teacher_school_id);
				$("#editLastName").val(data.lastname);
				$("#editFirstName").val(data.firstname);
				$("#editMiddleName").val(data.middlename);
				$("#editDepartmentSelect").val(data.department_id);
				$("#editPassword").val(data.password);
				$("#editStatusSelect").val(data.status_id);
				$("#editTeacherRoleSelect").val(data.role_id);

				const modal = new bootstrap.Modal(
					document.getElementById("editTeacherModal")
				);
				modal.show();
			},
			error: function (xhr) {
				console.error("Fetch teacher error:", xhr.responseText);
				Swal.fire("Error!", "Failed to fetch teacher data.", "error");
			},
		});
	});

	// Submit Edit Teacher Form
	$("#editTeachersForm").submit(function (e) {
		e.preventDefault();
		const formData = $(this).serialize();
		const teacherDbId = $("#editTeacherDbId").val(); // DB id

		$.ajax({
			url: BASE_URL + "index.php/Api/update_teacher/" + teacherDbId,
			type: "POST",
			data: formData,
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire("Success!", "Teacher updated successfully.", "success");
					$("#editTeacherModal").modal("hide");
					loadTeachers();
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

	// Delete Teacher
	$(document).on("click", ".deleteTeachers", function () {
		let id = $(this).data("id");

		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Yes, delete it!",
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: BASE_URL + "index.php/Api/delete_teacher/" + id,
					type: "POST",
					dataType: "json",
					success: function (response) {
						if (response.status === "success") {
							Swal.fire("Deleted!", response.message, "success");
							loadTeachers();
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

	// Filters
	$("#filterDepartmentSelect, #filterStatusSelect").on("change", function () {
		renderTeachersTable(teachersData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterDepartmentSelect")[0].selectedIndex = 0;
		$("#filterStatusSelect")[0].selectedIndex = 0;
		renderTeachersTable(teachersData);
	});

	// Select All Checkbox
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
