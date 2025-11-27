$(document).ready(function () {
	$("#teacherSubjectData").html(
		'<tr><td colspan="10" class="text-center">No data available (Please click this button <i class="fa-solid fa-expand"></i> in the teacher)</td></tr>'
	);
	$("#subjectSchedulesData").html(
		'<tr><td colspan="10" class="text-center">No data available (Please click this button <i class="fa-solid fa-expand"></i> in the subject)</td></tr>'
	);

	let schedulesData = [];
	let currentScheduleId = null;
	let teachersData = [];
	let studentsData = [];
	loadStudents();
	loadTeachers();

	$("#schedulesForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addSchedules",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "schedule successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#schedulesForm")[0].reset();
						$("#subjectScheduleModal").modal("hide");
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
					text: "An error occurred while saving the schedule.",
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

	$(document).on("click", ".showSubjectbtn", function () {
		const selectedUserId = $(this).data("user-id");
		$.ajax({
			url: BASE_URL + "index.php/Api/get_teacher_subjects",
			method: "GET",
			data: { user_id: selectedUserId },
			dataType: "json",
			success: function (reports) {
				renderTeacherSubjectTable(reports);
			},
			error: function () {
				$("#subjectAssignmentsData").html(
					'<tr><td colspan="10" class="text-center">Failed to load data</td></tr>'
				);
			},
		});
	});

	$(document).on("click", ".showSchedulesbtn", function () {
		const selectedUserId = $(this).data("user-id");
		$.ajax({
			url: BASE_URL + "index.php/Api/get_subject_schedules",
			method: "GET",
			data: { user_id: selectedUserId },
			dataType: "json",
			success: function (reports) {
				renderSubjectSchedulesTable(reports);
			},
			error: function () {
				$("#subjectSchedulesData").html(
					'<tr><td colspan="10" class="text-center">Failed to load data</td></tr>'
				);
			},
		});
	});

	$(document).on("click", ".viewStudentsbtn", function () {
		const selectedUserId = $(this).data("user-id");
		$.ajax({
			url: BASE_URL + "index.php/Api/get_schedule_students",
			method: "GET",
			data: { user_id: selectedUserId },
			dataType: "json",
			success: function (reports) {
				renderViewStudentTable(reports);
			},
			error: function () {
				$("#viewStudentData").html(
					'<tr><td colspan="10" class="text-center">Failed to load data</td></tr>'
				);
			},
		});
	});

	$(document).on("click", ".addStudentToSchedule", function () {
		const studentId = $(this).data("student-id");

		if (!currentScheduleId) {
			Swal.fire({
				icon: "error",
				title: "Error",
				text: "No schedule selected.",
			});
			return;
		}

		$.ajax({
			url: BASE_URL + "index.php/Api/addStudentToSchedule",
			type: "POST",
			data: {
				student_id: studentId,
				schedule_id: currentScheduleId,
				status_id: 1, // default active
			},
			dataType: "json",
			success: function (res) {
				if (res.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Student Added",
						text: res.message, // success message
					});
				} else if (res.status === "error") {
					Swal.fire({
						icon: "warning",
						title: "Already Assigned",
						text: res.message, // "This student is already assigned..."
					});
				}
			},
			error: function (err) {
				Swal.fire({
					icon: "error",
					title: "Error",
					text: "Unable to add student due to server error.",
				});
				console.error(err.responseText);
			},
		});
	});

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
					teachers.status && teachers.status.trim().toLowerCase() === ""
						? "highlight-row"
						: "";

				html += `
	    	<tr class="${rowClass}">
	        <td>${teachers.fullname || ""}</td>
	        <td>
	            <button class="btn btn-sm btn-primary showSubjectbtn" data-user-id="${
								teachers.id
							}" title="Show teacher subject">
	                <i class="fa-solid fa-expand"></i>
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

	function renderSubjectSchedulesTable(schedules) {
		const filterDepartmentSelect = $("#filterDepartmentSelect").val();
		const filterStatusSelect = $("#filterStatusSelect").val();

		let html = "";

		const filteredSchedules = schedules.filter((schedules) => {
			if (
				filterDepartmentSelect &&
				schedules.department_id != filterDepartmentSelect
			)
				return false;
			if (filterStatusSelect && schedules.status_id != filterStatusSelect)
				return false;
			return true;
		});

		if (filteredSchedules.length > 0) {
			$.each(filteredSchedules, function (i, schedules) {
				const rowClass =
					schedules.status && schedules.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
	    	<tr class="${rowClass}">
	        <td>${schedules.class_code || ""}</td>
	        <td>${schedules.days_schedule || ""}</td>
	        <td>${schedules.time_start || ""} | ${schedules.time_end || ""}</td>
            <td>${schedules.room || ""}</td>
	        <td>
			 	<button class="btn btn-sm btn-success addStudentbtn" 
					data-user-id="${schedules.id}" 
					title="Add schedule"
					data-bs-toggle="modal"
					data-bs-target="#scheduleModal">
				<i class="fa-solid fa-user-plus"></i>
                </button>
	            <button class="btn btn-sm btn-primary viewStudentsbtn" 
					data-user-id="${schedules.id}" 
					title="Add schedule"
					data-bs-toggle="modal"
					data-bs-target="#viewStudentModal">
				<i class="fa-solid fa-users-viewfinder"></i>
                </button>
	            <button class="btn btn-sm btn-danger deleteSchedules" data-id="${
								schedules.id
							}" title="Delete">
	                <i class="fa-solid fa-trash"></i>
	            </button>
	        </td>
	    </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No schedules found</td></tr>';
		}

		$("#subjectSchedulesData").html(html);
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	function renderTeacherSubjectTable(subjects) {
		const filterProgramSelect = $("#filterProgramSelect").val();
		const filterStatusSelect = $("#filterStatusSelect").val();

		let html = "";

		const filteredSubjects = subjects.filter((subjects) => {
			if (filterProgramSelect && subjects.program_id != filterProgramSelect)
				return false;
			if (filterStatusSelect && subjects.status_id != filterStatusSelect)
				return false;
			return true;
		});

		if (filteredSubjects.length > 0) {
			$.each(filteredSubjects, function (i, subjects) {
				const rowClass =
					subjects.status && subjects.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
        	<tr class="${rowClass}">
            <td>${subjects.subject_name || ""}</td>
            <td>${subjects.subject_code || ""}</td>
            <td>${subjects.semester || ""}</td>
            <td>
                <button class="btn btn-sm btn-success addSchedulebtn" 
                data-user-id="${subjects.id}" 
                title="Add schedule"
                data-bs-toggle="modal"
                data-bs-target="#subjectScheduleModal">
                <i class="fa-solid fa-calendar-plus"></i>
                </button>
               <button class="btn btn-sm btn-primary showSchedulesbtn" data-user-id="${
									subjects.id
								}" title="Show teacher subject">
	                <i class="fa-solid fa-expand"></i>
	            </button>
            </td>
        </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No subjects found</td></tr>';
		}

		$("#teacherSubjectData").html(html);
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	function renderViewStudentTable(students) {
		let html = "";

		if (students.length > 0) {
			$.each(students, function (i, students) {
				const rowClass =
					students.status && students.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
                <tr class="${rowClass}">
                    <td>${students.fullname || ""}</td>
                    <td>${students.section || ""}</td>
                    <td>${students.status || ""}</td>
                    <td>
                        <button class="btn btn-sm btn-danger dropStudentbtn" 
                            data-id="${students.id}" 
                            title="Delete">
                            <i class="fa-solid fa-circle-chevron-down"></i>
                        </button>
                        <button class="btn btn-sm btn-success inlistbtn" 
                            data-id="${students.id}" 
                            title="Restore">
                            <i class="fa-solid fa-circle-chevron-up"></i>
                        </button>
                    </td>
                </tr>
            `;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No students found</td></tr>';
		}

		$("#viewStudentData").html(html);
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
               <button class="btn btn-sm btn-success addStudentToSchedule" 
						data-student-id="${students.id}" 
						title="Add Student">
					<i class="fa-solid fa-circle-plus"></i>
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

	$(document).on("click", ".addStudentbtn", function () {
		currentScheduleId = $(this).data("user-id"); // schedule_id
		console.log("Selected Schedule ID:", currentScheduleId);
		renderStudentsTable(studentsData);
	});

	$(document).on("click", ".dropStudentbtn", function () {
		const studentScheduleId = $(this).data("id");
		const button = $(this);

		if (!studentScheduleId) {
			Swal.fire("Error", "Invalid student ID", "error");
			return;
		}

		// Show confirmation dialog
		Swal.fire({
			title: "Drop Student",
			text: "Are you sure you want to drop this student from the schedule?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Yes, Drop",
			cancelButtonText: "Cancel",
		}).then((result) => {
			if (result.isConfirmed) {
				dropStudent(studentScheduleId, button);
			}
		});
	});

	$(document).on("click", ".inlistbtn", function () {
		const studentScheduleId = $(this).data("id");
		const button = $(this);

		if (!studentScheduleId) {
			Swal.fire("Error", "Invalid student ID", "error");
			return;
		}

		// Show confirmation dialog
		Swal.fire({
			title: "Inlist Student",
			text: "Are you sure you want to inlist this student from the schedule?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Yes, Inlist",
			cancelButtonText: "Cancel",
		}).then((result) => {
			if (result.isConfirmed) {
				inlistStudent(studentScheduleId, button);
			}
		});
	});

	function inlistStudent(studentScheduleId, button) {
		$.ajax({
			url: BASE_URL + "index.php/Api/inlist_student",
			type: "POST",
			dataType: "json",
			data: {
				student_schedule_id: studentScheduleId,
			},
			success: function (response) {
				if (response.status === "success") {
					Swal.fire("Success", response.message, "success").then(() => {
						// Remove the row from the table
						button.closest("tr").fadeOut(300, function () {
							$(this).remove();
						});
					});
				} else {
					Swal.fire("Error", response.message, "error");
				}
			},
			error: function (xhr) {
				Swal.fire("Error", "Failed to drop student", "error");
				console.error(xhr);
			},
		});
	}

	$(document).on("click", ".addSchedulebtn", function () {
		let teacherId = $(this).data("user-id");

		// set the hidden input value
		console.log(teacherId);
		$("#subjectTeacherId").val(teacherId);
	});

	$(document).on("click", ".addStudentbtn", function () {
		let teacherId = $(this).data("user-id");
		console.log(teacherId);
		$("#subjectStudentId").val(teacherId);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterDepartmentSelect")[0].selectedIndex = 0;
		$("#filterStatusSelect")[0].selectedIndex = 0;

		renderTeachersTable(teachersData);
	});

	$("#filterYearSelect, #filterSectionSelect, #filterProgramSelect").on(
		"change",
		function () {
			renderStudentsTable(studentsData);
		}
	);

	$("#resetFiltersBtn1").on("click", function () {
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
