$(document).ready(function () {
	$("#subjectSchedulesData").html(
		'<tr><td colspan="10" class="text-center">No data available (Please click this button <i class="fa-solid fa-expand"></i> in the subject)</td></tr>'
	);
	loadSubjects();
	function loadSubjects() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_teacher_subjects",
			type: "GET",
			dataType: "json",
			success: function (response) {
				subjectsData = response || [];
				renderTeacherSubjects(subjectsData);
			},
			error: function (xhr, status, error) {
				console.error("Fetch Error:", xhr.responseText);
				$("#subjectsData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	function renderTeacherSubjects(teachers) {
		const filterSubjectSelect = $("#filterSubjectSelect").val();
		let html = "";

		const filteredSubjects = teachers.filter((t) => {
			if (filterSubjectSelect && t.subject_id != filterSubjectSelect)
				return false;
			return true;
		});

		if (filteredSubjects.length > 0) {
			$.each(filteredSubjects, function (i, teacher) {
				const rowClass =
					teacher.status && teacher.status.trim().toLowerCase() === ""
						? "highlight-row"
						: "";

				html += `
				<tr class="${rowClass}">
					<td>${teacher.subject_code || ""}</td>
					<td>
						<button class="btn btn-sm btn-primary showSchedulesbtn" data-user-id="${
							teacher.id
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

		$("#subjectsData").html(html);
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
				<td>${schedules.section || ""}</td>
				<td>${schedules.year_level || ""}</td>
				<td>${schedules.room || ""}</td>
				<td>
					<button class="btn btn-sm btn-success showAttendanceBtn" data-user-id="${
						schedules.id
					}" title="Show schedules subject">
							<i class="fa-solid fa-user-check"></i>
						</button>
				</td>
			</tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No schedules found</td></tr>';
		}

		$("#subjectSchedulesData").html(html);
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
                    <td>${students.year_level || ""}</td>
                    <td>${students.program_name || ""}</td>
                    <td>${students.status || ""}</td>
                    <td>
                        <button class="btn btn-sm btn-danger recordBtn" 
                            data-id="${students.id}" 
                            title="Delete">
                            <i class="fa-solid fa-clipboard-user"></i>
                        </button>
                        <button class="btn btn-sm btn-success attendanceBtn" 
                            data-id="${students.id}" 
                            title="Restore">
                            <i class="fa-solid fa-check-to-slot"></i>
                        </button>
                    </td>
                </tr>
            `;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No students found</td></tr>';
		}

		$("#studentsData").html(html);
	}

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

	$(document).on("click", ".showAttendanceBtn", function () {
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
				$("#studentsData").html(
					'<tr><td colspan="10" class="text-center">Failed to load data</td></tr>'
				);
			},
		});
	});
});
