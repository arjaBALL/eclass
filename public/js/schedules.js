$(document).ready(function () {
	$("#teacherSubjectData").html(
		'<tr><td colspan="10" class="text-center">No data available (Please click this button <i class="fa-solid fa-expand"></i> in the teacher)</td></tr>'
	);
	$("#subjectSchedulesData").html(
		'<tr><td colspan="10" class="text-center">No data available (Please click this button <i class="fa-solid fa-expand"></i> in the teacher)</td></tr>'
	);

	let schedulesData = [];
	let teachersData = [];
	// let teacherSubjectData = [];
	// loadSchedules();
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
						$("#scheduleModal").modal("hide");
						loadSchedules(); // reload students
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

	function renderTeachersTable(teachers) {
		const filterDepartmentSelect = $("#filterDepartmentSelect").val();

		let html = "";

		const filteredTeachers = teachers.filter((teachers) => {
			if (
				filterDepartmentSelect &&
				teachers.department_id != filterDepartmentSelect
			)
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
	            <button class="btn btn-sm btn-success editSchedules" data-id="${
								schedules.id
							}" title="Edit">
	                <i class="fa-solid fa-user-plus"></i>
	            </button>
                <button class="btn btn-sm btn-primary addSchedulebtn" 
                data-user-id="${subjects.id}" 
                title="Add schedule"
                data-bs-toggle="modal"
                data-bs-target="#subjectScheduleModal">
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

	$(document).on("click", ".addSchedulebtn", function () {
		let teacherId = $(this).data("user-id");

		// set the hidden input value
		console.log(teacherId);
		$("#subjectTeacherId").val(teacherId);
	});

	$("#filterDepartmentSelect").on("change", function () {
		enderTeachersTable(schedulesData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterDepartmentSelect")[0].selectedIndex = 0;

		enderTeachersTable(schedulesData);
	});

	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".scheduleCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".scheduleCheckbox", function () {
		const total = $(".scheduleCheckbox").length;
		const checked = $(".scheduleCheckbox:checked").length;

		$("#selectAllToday").prop("checked", total === checked);
	});
});
