$(document).ready(function () {
	let studentsData = [];
	loadStudents();

	$("#studentForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addStudent",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Student successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#studentForm")[0].reset();
						$("#studentModal").modal("hide");
						loadStudents(); // reload students
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
					text: "An error occurred while saving the student.",
					confirmButtonText: "OK",
				});
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
