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
					<button class="btn btn-sm btn-success showGradeReportBtn" data-user-id="${
						schedules.id
					}" title="Show schedules subject">
							<i class="fa-solid fa-file-circle-question"></i>
						</button>
						<button class="btn btn-sm btn-warning printReportBtn" data-schedule-id="${
							schedules.id
						}" title="Print Report">
							<i class="fa-solid fa-print"></i>
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

	$(document).on("click", ".showGradeReportBtn", function () {
		const selectedUserId = $(this).data("user-id");
		$.ajax({
			url: BASE_URL + "index.php/Api/get_grade_reports",
			method: "GET",
			data: { user_id: selectedUserId },
			dataType: "json",
			success: function (reports) {
				renderGradeReportTable(reports);
			},
			error: function () {
				$("#gradeReportData").html(
					'<tr><td colspan="10" class="text-center">Failed to load data</td></tr>'
				);
			},
		});
	});

	function renderGradeReportTable(grades) {
		let html = "";

		if (grades.length > 0) {
			$.each(grades, function (i, grade) {
				// Determine badge class for Bootstrap 5
				let badgeClass =
					grade.remarks === "Passed" ? "text-bg-success" : "text-bg-danger";

				html += `
            <tr>
                <td>${i + 1}</td>
                <td>${grade.student_name}</td>
                <td>${parseFloat(grade.midterm_grade).toFixed(2)}</td>
                <td>${parseFloat(grade.final_grade).toFixed(2)}</td>
                <td>${parseFloat(grade.final_rating).toFixed(2)}</td>
                <td><span class="badge ${badgeClass}">${
					grade.remarks
				}</span></td>
            </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="6" class="text-center">No grade report found</td></tr>';
		}

		$("#gradeReportData").html(html);
	}

	// Handle Print Report Button Click
	$(document).on("click", ".printReportBtn", function () {
		const scheduleId = $(this).data("schedule-id");

		// Show loading indicator
		showLoadingOverlay();

		// Create form data
		const formData = new FormData();
		formData.append("schedule_id", scheduleId);

		// FIXED: Correct URL to match CodeIgniter routing
		fetch(BASE_URL + "index.php/Class_list/generate_pdf", {
			method: "POST",
			body: formData,
		})
			.then((response) => {
				if (!response.ok) {
					throw new Error("Network response was not ok");
				}
				return response.blob();
			})
			.then((blob) => {
				// Create a URL for the blob
				const url = window.URL.createObjectURL(blob);

				// Open PDF in new window
				window.open(url, "_blank");

				// Clean up
				setTimeout(() => {
					window.URL.revokeObjectURL(url);
				}, 100);

				hideLoadingOverlay();
			})
			.catch((error) => {
				console.error("Error:", error);
				alert("Error generating PDF. Please try again.");
				hideLoadingOverlay();
			});
	});

	function showLoadingOverlay() {
		// Remove existing overlay if any
		$("#pdfLoadingOverlay").remove();

		// Create loading overlay
		const overlay = `
			<div id="pdfLoadingOverlay" style="
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: rgba(0,0,0,0.7);
				display: flex;
				justify-content: center;
				align-items: center;
				z-index: 9999;
			">
				<div style="
					background: white;
					padding: 30px 40px;
					border-radius: 8px;
					text-align: center;
					box-shadow: 0 4px 6px rgba(0,0,0,0.3);
				">
					<div class="spinner-border text-primary mb-3" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
					<div style="font-size: 16px; font-weight: 500; color: #333;">
						<i class="fa-solid fa-file-pdf"></i> Generating PDF...
					</div>
					<div style="font-size: 12px; color: #666; margin-top: 8px;">
						Please wait
					</div>
				</div>
			</div>
		`;

		$("body").append(overlay);
	}

	function hideLoadingOverlay() {
		$("#pdfLoadingOverlay").fadeOut(300, function () {
			$(this).remove();
		});
	}

	$("#filterSubjectSelect").on("change", function () {
		renderTeacherSubjects(subjectsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterSubjectSelect").val("");
		renderTeacherSubjects(subjectsData);
	});
});
