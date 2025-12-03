$(document).ready(function () {
	let studentsData = [];
	loadStudents();

	$("#studentForm").submit(function (event) {
		event.preventDefault();

		const studentId = $("#student_id").val();
		let url = BASE_URL + "index.php/Api/addStudent";

		if (studentId !== "") {
			url = BASE_URL + "index.php/Api/updateStudent";
		}

		$.ajax({
			url: url,
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: response.message,
						confirmButtonText: "OK",
					}).then(() => {
						$("#studentForm")[0].reset();
						$("#student_id").val("");
						$("#studentModal").modal("hide");
						loadStudents();
					});
				} else {
					Swal.fire("Error", response.message, "error");
				}
			},
		});
	});

	$('[data-bs-target="#studentModal"]').click(function () {
		$("#studentModalLabel").text("Add New Student");
		$("#student_id").val("");
		$("#studentForm")[0].reset();

		// SHOW School ID on ADD
		$("#schoolIDWrapper").show();
		$("#studentSchoolId").prop("required", true);
	});

	$(document).on("click", ".editStudents", function () {
		$("#schoolIDWrapper").hide();
		$("#studentSchoolId").prop("required", false);
		const id = $(this).data("id");

		$.ajax({
			url: BASE_URL + "index.php/Api/get_student_by_id",
			type: "POST",
			data: { id: id },
			dataType: "json",
			success: function (student) {
				if (student) {
					$("#studentModalLabel").text("Edit Student");

					// HIDE School ID on EDIT
					$("#schoolIDWrapper").hide();
					$("#studentSchoolId").prop("required", false);

					$("#student_id").val(student.id);
					$("input[name='lastName']").val(student.lastname);
					$("input[name='firstName']").val(student.firstname);
					$("input[name='middleName']").val(student.middlename);
					$("#YearSelect").val(student.year_level_id);
					$("#ProgramSelect").val(student.program_id);
					$("#SectionSelect").val(student.section_id);
					$("#StatusSelect").val(student.status_id);

					$("#studentModal").modal("show");
				}
			},
		});
	});

	function loadStudents() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_students",
			type: "GET",
			dataType: "json",
			success: function (response) {
				studentsData = response || [];
				renderStudentsTable(studentsData);
			},
			error: function (xhr, status, error) {
				console.error("Fetch Error:", xhr.responseText);
				$("#userData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	function renderStudentsTable(students) {
		const filterYearSelect = $("#filterYearSelect").val();
		const filterSectionSelect = $("#filterSectionSelect").val();
		const filterProgramSelect = $("#filterProgramSelect").val();

		let html = "";

		const filteredStudents = students.filter((students) => {
			if (filterYearSelect && students.year_level_id != filterYearSelect)
				return false;
			if (filterSectionSelect && students.section_id != filterSectionSelect)
				return false;
			if (filterProgramSelect && students.program_id != filterProgramSelect)
				return false;
			return true;
		});

		if (filteredStudents.length > 0) {
			$.each(filteredStudents, function (i, students) {
				const rowClass =
					students.status && students.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
        	<tr class="${rowClass}">
            <td>
                <input type="checkbox" class="studentCheckbox" 
               data-id="${students.id}" />
            </td>
            <td>${students.school_id || ""}</td>
            <td>${students.fullname || ""}</td>
            <td>${students.section || ""}</td>
            <td>${students.year_level || ""}</td>
            <td>${students.program_name || ""}</td>
            <td>${students.status || ""}</td>
            <td>
                <button class="btn btn-sm btn-primary editStudents" data-id="${
									students.id
								}" title="Edit">
                    <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                </button>
                <button class="btn btn-sm btn-danger deleteStudents" data-id="${
									students.id
								}" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No students found</td></tr>';
		}

		$("#studentsData").html(html);
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	$(document).on("click", ".deleteStudents", function () {
		const id = $(this).data("id");

		Swal.fire({
			title: "Are you sure?",
			text: "This student will be permanently deleted!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			confirmButtonText: "Yes, delete it!",
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: BASE_URL + "index.php/Api/deleteStudent",
					type: "POST",
					data: { id: id },
					dataType: "json",
					success: function (response) {
						if (response.status === "success") {
							Swal.fire("Deleted!", response.message, "success");
							loadStudents();
						} else {
							Swal.fire("Error", response.message, "error");
						}
					},
				});
			}
		});
	});

	$("#filterYearSelect, #filterSectionSelect, #filterProgramSelect").on(
		"change",
		function () {
			renderStudentsTable(studentsData);
		}
	);

	$("#resetFiltersBtn").on("click", function () {
		$("#filterYearSelect")[0].selectedIndex = 0;
		$("#filterSectionSelect")[0].selectedIndex = 0;
		$("#filterProgramSelect")[0].selectedIndex = 0;

		renderStudentsTable(studentsData);
	});

	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".studentCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".studentCheckbox", function () {
		const total = $(".studentCheckbox").length;
		const checked = $(".studentCheckbox:checked").length;

		$("#selectAllToday").prop("checked", total === checked);
	});
});
