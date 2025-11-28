$(document).ready(function () {
	// ========== INITIALIZATION ==========
	$("#subjectScheduleData").html(
		'<tr><td colspan="10" class="text-center">No data available (Please click this button <i class="fa-solid fa-expand"></i> in the subject)</td></tr>'
	);

	$("#subjectSchedulesData").html(
		'<tr><td colspan="10" class="text-center">No data available (Please click this button <i class="fa-solid fa-expand"></i> in the subject)</td></tr>'
	);

	loadSubjects();
	let criteriaColumns = [];
	let currentCriteriaId = null;
	let currentScheduleId = null;

	// ========== DATA LOADING ==========

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

	function fetchCriteria(scheduleId) {
		$.ajax({
			url: BASE_URL + "index.php/Api/getCriteria",
			type: "GET",
			data: { schedule_id: scheduleId },
			dataType: "json",
			success: function (response) {
				let html = "";

				if (response && response.length > 0) {
					response.forEach((c) => {
						html += `
                        <div class="border rounded p-2 px-2 mb-2">
                            <div class="row">
                                <div class="col-7">
                                    <div class="row">
                                        <div>${c.criteria}</div>
                                        <div class="col" style="font-size:10px;">
                                            Weight: ${c.weight}% • Items: ${
							c.items || 0
						}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5 d-flex justify-content-center align-items-center">
                                    <button type="button" class="btn btn-primary btn-sm manageBtn" data-criteria-id="${
																			c.id
																		}">Manage</button>
                                </div>
                            </div>
                        </div>
                    `;
					});
				} else {
					html = '<div class="text-center text-muted">No criteria found</div>';
				}

				$("#criteriaList").html(html);
			},
			error: function (xhr, status, error) {
				console.error("Failed to fetch criteria:", error);
				$("#criteriaList").html(
					'<div class="text-center text-danger">Error loading criteria</div>'
				);
			},
		});
	}

	// ========== RENDERING ==========

	function renderTeacherSubjects(teachers) {
		const filterSubjectSelect = $("#filterSubjectSelect").val();
		let html = "";

		const filteredTeachers = teachers.filter((t) => {
			if (filterSubjectSelect && t.subject_id != filterSubjectSelect)
				return false;
			return true;
		});

		if (filteredTeachers.length > 0) {
			$.each(filteredTeachers, function (i, teacher) {
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
	            <button class="btn btn-sm btn-warning recordScoreBtn" data-id="${
								schedules.id
							}" title="Delete">
	               <i class="fa-solid fa-file-pen"></i> Record Score
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

	function renderScoreTable(students) {
		// Header row
		let header = "<th>Students</th>";
		criteriaColumns.forEach((c, i) => {
			header += `<th data-col-index="${i}">${
				c.name || "Score " + (i + 1)
			}</th>`;
		});
		header += `<th>Total Earned</th><th>Total Items</th><th>Average %</th><th>Weighted (%)</th>`;
		$("#scoresTableHeader").html(header);

		// --- Add items row ---
		let itemsRow = '<tr class="itemsRow"><td>Items</td>';
		criteriaColumns.forEach((c, i) => {
			itemsRow += `<td class="criteriaItems">${c.items || 0}</td>`;
		});
		itemsRow += '<td colspan="4"></td></tr>';

		$("#subjectScheduleData").html(itemsRow);

		// Student rows
		students.forEach((s) => {
			let studentRow = `<tr data-student-id="${s.id}"><td>${s.fullname}</td>`;
			criteriaColumns.forEach((c, i) => {
				const earned = s.scores?.[i]?.earned || "";
				const total = s.scores?.[i]?.total || "";
				studentRow += `<td>
                <input type="number" class="form-control studentScoreEarned" 
                    data-student-id="${s.id}" data-col-index="${i}" placeholder="Earned" value="${earned}" style="width:45%; display:inline-block;">
                /
                <input type="number" class="form-control studentScoreTotal" 
                    data-student-id="${s.id}" data-col-index="${i}" placeholder="Total" value="${total}" style="width:45%; display:inline-block;">
            </td>`;
			});
			studentRow += `<td class="totalEarned">0</td><td class="totalItems">0</td><td class="average">0</td><td class="weighted">0</td></tr>`;
			$("#subjectScheduleData").append(studentRow);
		});

		calculateAverage();
	}

	// Listen to changes in earned/total inputs
	$(document).on(
		"input",
		".studentScoreEarned, .studentScoreTotal",
		function () {
			calculateAverage();
		}
	);

	function calculateAverage() {
		$("#subjectScheduleData tr").each(function () {
			let totalEarned = 0,
				totalItems = 0;

			$(this)
				.find("td")
				.each(function (index) {
					const earnedInput = $(this).find(".studentScoreEarned");
					const totalInput = $(this).find(".studentScoreTotal");

					if (earnedInput.length && totalInput.length) {
						const earned = parseFloat(earnedInput.val()) || 0;
						const total = parseFloat(totalInput.val()) || 0;

						totalEarned += earned;
						totalItems += total;
					}
				});

			const average =
				totalItems > 0 ? ((totalEarned / totalItems) * 100).toFixed(2) : 0;
			$(this).find("td.totalEarned").text(totalEarned);
			$(this).find("td.totalItems").text(totalItems);
			$(this).find("td.average").text(average);

			// Weighted = average * sum of criteria weights / 100
			let weighted = 0;
			criteriaColumns.forEach((c, i) => {
				const weight = parseFloat(c.weight) || 0;
				const earned =
					parseFloat(
						$(this)
							.find(`input.studentScoreEarned[data-col-index='${i}']`)
							.val()
					) || 0;
				const total =
					parseFloat(
						$(this).find(`input.studentScoreTotal[data-col-index='${i}']`).val()
					) || 0;
				if (total > 0) {
					weighted += (earned / total) * 100 * (weight / 100);
				}
			});
			$(this).find("td.weighted").text(weighted.toFixed(2));
		});
	}

	// ========== FORM SUBMISSIONS ==========

	$("#criteriaForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addCriteria",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Criteria successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#criteriasForm")[0].reset();
						fetchCriteria();
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
					text: "An error occurred while saving the criteria.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	// ========== EVENT HANDLERS ==========

	$(document).on("click", ".recordScoreBtn", function () {
		let scheduleId = $(this).data("id");
		$("#criteriaListContainer").show();
		fetchCriteria(scheduleId);
		$("#recordScoreBtn").val(scheduleId);
	});

	$(document).on("click", ".manageBtn", function () {
		currentCriteriaId = $(this).data("criteria-id");
		currentScheduleId = $("#recordScoreBtn").val();

		if (!currentCriteriaId || !currentScheduleId) return;

		$.ajax({
			url: BASE_URL + "index.php/Api/getStudents",
			type: "GET",
			data: { schedule_id: currentScheduleId, criteria_id: currentCriteriaId },
			dataType: "json",
			success: function (students) {
				criteriaColumns = [];
				renderScoreTable(students);
			},
		});
	});

	$(document).on("click", "#addScoreColumn", function () {
		const newIndex = criteriaColumns.length;
		criteriaColumns.push({});

		// Insert new header after "Students" column
		$("#scoresTableHeader th:nth-child(1)").after(
			`<th data-col-index="${newIndex}">Score ${newIndex + 1}</th>`
		);

		// Add input to each student row after the first td (Students)
		$("#subjectScheduleData tr").each(function () {
			$(this).find("td:first").after(`<td>
            <input type="number" class="form-control studentScore" 
                data-student-id="${$(this).data("student-id")}" 
                data-col-index="${newIndex}" value="">
        </td>`);
		});
	});

	if ($("#saveScoresBtn").length === 0) {
		$(".col-9.border").append(`
        <div class="d-flex justify-content-end mt-2">
            <button id="saveScoresBtn" class="btn btn-success btn-sm">Save Scores</button>
        </div>
    `);
	}

	$(document).on("click", "#saveScoresBtn", function () {
		const dataToSave = [];
		$("#subjectScheduleData tr").each(function () {
			const studentId = $(this).data("student-id");
			$(this)
				.find("input.studentScore")
				.each(function () {
					const colIndex = $(this).data("col-index");
					const score = $(this).val();
					dataToSave.push({
						student_id: studentId,
						col_index: colIndex,
						score: score,
					});
				});
		});

		$.ajax({
			url: BASE_URL + "index.php/Api/saveAllStudentScores",
			type: "POST",
			data: {
				schedule_id: currentScheduleId,
				criteria_id: currentCriteriaId,
				scores: dataToSave,
			},
			success: function () {
				Swal.fire({
					icon: "success",
					title: "Scores Saved!",
					text: "All student scores have been saved.",
					confirmButtonText: "OK",
				});
				calculateAverage();
			},
			error: function () {
				Swal.fire({
					icon: "error",
					title: "Error",
					text: "Failed to save scores.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	$(document).on("change", ".studentScore", function () {
		const studentId = $(this).data("student-id");
		const colIndex = $(this).data("col-index");
		const score = $(this).val();

		$.ajax({
			url: BASE_URL + "index.php/Api/saveStudentScoreColumn",
			type: "POST",
			data: {
				schedule_id: currentScheduleId,
				criteria_id: currentCriteriaId,
				student_id: studentId,
				col_index: colIndex,
				score,
			},
			success: function () {
				calculateAverage();
			},
		});
	});

	$("#filterSubjectSelect").on("change", function () {
		renderTeacherSubjects(subjectsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterSubjectSelect").val("");
		renderTeacherSubjects(subjectsData);
	});

	// ========== CALCULATIONS ==========

	$(document).on(
		"input",
		".studentScoreEarned, .studentScoreTotal",
		function () {
			calculateAverage();
		}
	);

	function calculateAverage() {
		$("#subjectScheduleData tr").each(function () {
			let totalEarned = 0,
				totalItems = 0;

			$(this)
				.find("td")
				.each(function (index) {
					const earnedInput = $(this).find(".studentScoreEarned");
					const totalInput = $(this).find(".studentScoreTotal");

					if (earnedInput.length && totalInput.length) {
						const earned = parseFloat(earnedInput.val()) || 0;
						const total = parseFloat(totalInput.val()) || 0;

						totalEarned += earned;
						totalItems += total;
					}
				});

			const average =
				totalItems > 0 ? ((totalEarned / totalItems) * 100).toFixed(2) : 0;
			$(this).find("td.totalEarned").text(totalEarned);
			$(this).find("td.totalItems").text(totalItems);
			$(this).find("td.average").text(average);

			// Weighted = average * sum of criteria weights / 100
			let weighted = 0;
			criteriaColumns.forEach((c, i) => {
				const weight = parseFloat(c.weight) || 0;
				const earned =
					parseFloat(
						$(this)
							.find(`input.studentScoreEarned[data-col-index='${i}']`)
							.val()
					) || 0;
				const total =
					parseFloat(
						$(this).find(`input.studentScoreTotal[data-col-index='${i}']`).val()
					) || 0;
				if (total > 0) {
					weighted += (earned / total) * 100 * (weight / 100);
				}
			});
			$(this).find("td.weighted").text(weighted.toFixed(2));
		});
	}
});
