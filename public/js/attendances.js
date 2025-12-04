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
                <button class="btn btn-sm btn-success showAttendanceBtn" 
                    data-user-id="${schedules.id}" 
                    data-schedule-id="${schedules.id}"
                    data-section-id="${schedules.section_id || ""}"
                    data-subject="${
											schedules.subject_name || schedules.class_code || "Class"
										}"
                    data-section="${schedules.section || "Section"}"
                    title="Show schedules subject">
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

	function renderViewStudentTable(students) {
		console.log("=== Rendering Student Table ===");
		console.log("Students data:", students);
		console.log("Current section data:", currentSectionData);

		let html = "";

		if (students.length > 0) {
			$.each(students, function (i, student) {
				const studentSchoolId = student.student_school_id || student.id || "";
				const firstName = student.firstname || student.first_name || "";
				const lastName = student.lastname || student.last_name || "";
				const fullName =
					student.fullname ||
					`${firstName} ${lastName}`.trim() ||
					student.full_name ||
					"";

				const rowClass =
					student.status === "Active" ||
					student.status === 1 ||
					student.status === "Regular"
						? "highlight-row"
						: "";

				html += `
            <tr class="${rowClass}" data-student-id="${studentSchoolId}">
                <td class="student-school-id">${studentSchoolId}</td>
                <td class="student-fullname">${fullName}</td>
                <td>${student.section || ""}</td>
                <td>${student.year_level || ""}</td>
                <td>${student.program_name || ""}</td>
                <td>${student.status || ""}</td>
                <td>
                    <button class="btn btn-sm btn-danger recordBtn" 
                        data-student-id="${studentSchoolId}"
                        data-first-name="${firstName}"
                        data-last-name="${lastName}"
                        data-full-name="${fullName}"
                        title="Face Registration">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </button>
                    <button class="btn btn-sm btn-success attendanceBtn" 
                        data-student-id="${studentSchoolId}"
                        data-section-id="${
													currentSectionData?.section_id || ""
												}"
                        data-schedule-id="${
													currentSectionData?.schedule_id || ""
												}"
                        data-subject="${currentSectionData?.subject || "Class"}"
                        data-section="${
													currentSectionData?.section || "Section"
												}"
                        title="Take Attendance">
                        <i class="fa-solid fa-check-to-slot"></i>
                    </button>
                </td>
            </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No students found</td></tr>';
		}

		$("#studentsData").html(html);
	}
	$(document).on("click", ".recordBtn", function () {
		console.log("=== Face Registration Button Clicked ===");

		// Get data attributes
		const studentId = $(this).data("student-id");
		const firstName = $(this).data("first-name");
		const lastName = $(this).data("last-name");
		const fullName = $(this).data("full-name");

		console.log("Student ID:", studentId);
		console.log("Full Name:", fullName);

		// Validate required data
		if (!studentId) {
			alert("Student ID not found! Please refresh and try again.");
			return;
		}

		if (!fullName || fullName === "undefined") {
			alert("Student name not found! Please refresh and try again.");
			return;
		}

		// Set current student data
		currentStudentData = {
			student_school_id: studentId,
			first_name: firstName || "",
			last_name: lastName || "",
			full_name: fullName,
		};

		console.log("Current student data set:", currentStudentData);

		// Display in modal
		$("#studentNameDisplay").text(fullName);
		$("#studentIdDisplay").text(studentId);

		// Reset registration UI
		resetRegistrationUI();

		// Open modal
		$("#faceRegistrationModal").modal("show");
	});
	$(document).on("click", ".showAttendanceBtn", function () {
		const selectedUserId = $(this).data("user-id");
		const scheduleId = $(this).data("schedule-id"); // ✅ GET SCHEDULE ID
		const sectionId = $(this).data("section-id");
		const subject = $(this).data("subject");
		const section = $(this).data("section");

		console.log("=== Show Attendance Button Clicked ===");
		console.log("Schedule ID:", scheduleId);
		console.log("Section ID:", sectionId);

		// ✅ STORE SCHEDULE DATA BEFORE LOADING STUDENTS
		currentSectionData = {
			schedule_id: scheduleId,
			section_id: sectionId,
			subject: subject || "Class",
			section: section || "Section",
		};

		console.log("✅ currentSectionData set:", currentSectionData);

		// Load students for this schedule
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

	$(document).on("click", ".recordBtn", function () {
		console.log("=== DEBUG CLICK ===");
		console.log("Button clicked:", this);
		console.log("Button HTML:", $(this).html());
		console.log("All data attributes:", $(this).data());

		const studentId = $(this).data("id");
		const firstName = $(this).data("firstname");
		const lastName = $(this).data("lastname");

		console.log("studentId from data-id:", studentId);
		console.log("firstName from data-firstname:", firstName);
		console.log("lastName from data-lastname:", lastName);

		// Alternative: get from parent row
		const row = $(this).closest("tr");
		const studentIdFromRow = row.find("td:first-child").text().trim();
		const studentNameFromRow = row.find("td:nth-child(2)").text().trim();

		console.log("studentId from table cell:", studentIdFromRow);
		console.log("studentName from table cell:", studentNameFromRow);

		if (!studentId && studentIdFromRow) {
			console.log("Using studentId from table cell instead");
			currentStudentData = {
				student_school_id: studentIdFromRow,
				first_name: firstName || "",
				last_name: lastName || "",
				full_name: studentNameFromRow,
			};
		} else if (studentId) {
			currentStudentData = {
				student_school_id: studentId,
				first_name: firstName || "",
				last_name: lastName || "",
				full_name: `${firstName || ""} ${lastName || ""}`.trim(),
			};
		} else {
			console.error("No student ID found!");
			alert("Student ID not found. Please try again.");
			return;
		}

		console.log("Current student data set:", currentStudentData);

		// Display in modal
		$("#studentNameDisplay").text(currentStudentData.full_name);
		$("#studentIdDisplay").text(currentStudentData.student_school_id);

		// Reset registration UI
		resetRegistrationUI();

		// Open modal
		$("#faceRegistrationModal").modal("show");
	});

	$(document).on("click", ".attendanceBtn", function () {
		console.log("=== Individual Attendance Button Clicked ===");

		const studentId = $(this).data("student-id");
		const sectionId = $(this).data("section-id");
		const scheduleId = $(this).data("schedule-id"); // ✅ NOW THIS WORKS
		const subject = $(this).data("subject");
		const section = $(this).data("section");

		console.log("Student ID:", studentId);
		console.log("Section ID:", sectionId);
		console.log("Schedule ID:", scheduleId);
		console.log("Subject:", subject);
		console.log("Section:", section);

		// ✅ VALIDATION
		if (!scheduleId) {
			alert("Schedule ID not found! Please click the expand button again.");
			console.error("No schedule_id available");
			return;
		}

		if (!sectionId) {
			alert("Section ID not found! Cannot take attendance.");
			return;
		}

		// ✅ UPDATE currentSectionData with button data
		currentSectionData = {
			schedule_id: scheduleId,
			section_id: sectionId,
			subject: subject || "Class",
			section: section || "Section",
		};

		console.log("✅ Current section data updated:", currentSectionData);

		// Load students for this section
		loadSectionStudents(sectionId);

		// Open attendance modal
		openAttendanceModal();
	});
	function openAttendanceModal() {
		console.log("=== Opening Attendance Modal ===");
		console.log("Current section data:", currentSectionData);

		if (!currentSectionData) {
			console.error("currentSectionData is null!");
			alert("Section data not loaded. Please try again.");
			return;
		}

		if (!currentSectionData.schedule_id) {
			console.error("schedule_id is missing!", currentSectionData);
			alert(
				"Schedule information missing. Please click the expand button (📊) first, then try again."
			);
			return;
		}

		$("#attendanceSubjectDisplay").text(currentSectionData.subject);
		$("#attendanceSectionDisplay").text(currentSectionData.section);

		resetAttendanceUI();
		$("#attendanceModal").modal("show");
	}
});
