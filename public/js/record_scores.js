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
	let currentGradePeriod = null; // Track which grade period we're working with

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

	function fetchCriteria(scheduleId, gradePeriod) {
		$.ajax({
			url: BASE_URL + "index.php/Api/getCriteria",
			type: "GET",
			data: { schedule_id: scheduleId, grade_period: gradePeriod },
			dataType: "json",
			success: function (response) {
				let html = "";

				if (response && response.length > 0) {
					criteriaColumns = response.map((c) => ({
						id: c.id,
						name: c.criteria,
						weight: parseFloat(c.weight) || 0,
						items: parseInt(c.items) || 0,
					}));

					console.log(
						"Loaded Criteria for " + gradePeriod + ":",
						criteriaColumns
					);

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
																		}" data-criteria-weight="${
							c.weight
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

	$(document).on("click", ".manageBtn", function () {
		currentCriteriaId = $(this).data("criteria-id");

		const selectedCriteria = criteriaColumns.find(
			(c) => c.id == currentCriteriaId
		);
		console.log("Manage clicked for criteria:", selectedCriteria);

		if (!currentCriteriaId || !currentScheduleId) return;

		window.selectedCriteriaWeight = selectedCriteria.weight;
		window.selectedCriteriaName = selectedCriteria.name;

		$.ajax({
			url: BASE_URL + "index.php/Api/getStudents",
			type: "GET",
			data: {
				schedule_id: currentScheduleId,
				criteria_id: currentCriteriaId,
				grade_period: currentGradePeriod,
			},
			dataType: "json",
			success: function (students) {
				console.log("Students loaded for criteria:", selectedCriteria.name);
				renderScoreTable(students, selectedCriteria);
			},
		});
	});

	// ========== RENDERING ==========

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

				let gradingOptions = `<option value="">Choose:</option>`;

				GRADING_PERIODS.forEach((grp) => {
					gradingOptions += `<option value="${grp.id}">${grp.grading_period}</option>`;
				});

				html += `
			<tr class="${rowClass}">
				<td>${schedules.class_code || ""}</td>
				<td>${schedules.days_schedule || ""}</td>
				<td>${schedules.time_start || ""} | ${schedules.time_end || ""}</td>
				<td>${schedules.section || ""}</td>
				<td>${schedules.year_level || ""}</td>
				<td>${schedules.room || ""}</td>
				<td>
					<select
						name="gradingPeriodSelect"  
						class="form-select form-select-sm recordScoreBtn"
						data-id="${schedules.id}">
						
						<option value="">Choose:</option>
						${gradingOptions}
					</select>
				</td>

			</tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No schedules found</td></tr>';
		}

		$("#subjectSchedulesData").html(html);
	}

	function renderScoreTable(students, selectedCriteria) {
		$("#subjectScheduleData").empty();

		let header = "<th>Students</th>";
		header += `<th class="score-column">${selectedCriteria.name}</th>`;
		header += `<th class="summary-column">Total Score</th>`;
		header += `<th class="summary-column average">Average</th>`;
		header += `<th class="summary-column weighted">Weighted Grade (Base on ${(
			selectedCriteria.weight / 100
		).toFixed(2)} criteria weight)</th>`;
		$("#scoresTableHeader").html(header);

		let itemsRow = `<tr class="itemsRow"><td>Items</td>`;
		itemsRow += `
        <td class="score-column">
            <input type="number" class="form-control colItems" data-col-index="0" value="${
							selectedCriteria.items || 0
						}" placeholder="Items">
        </td>`;
		itemsRow += `<td class="summary-column totalItemsHeader">${
			selectedCriteria.items || 0
		}</td>`;
		itemsRow += `<td class="summary-column itemsAverage">1.0</td>`;
		itemsRow += `<td class="summary-column itemsWeighted">0.00</td></tr>`;
		$("#subjectScheduleData").html(itemsRow);

		students.forEach((s) => {
			let row = `<tr data-student-id="${s.id}"><td>${s.fullname}</td>`;

			const earned = s.scores?.[0]?.earned || "";
			row += `
            <td class="score-column">
                <input type="number" 
                    class="form-control studentScoreEarned"
                    data-student-id="${s.id}"
                    data-col-index="0"
                    data-criteria-id="${selectedCriteria.id}"
                    placeholder="Score"
                    value="${earned}">
            </td>`;

			row += `
            <td class="summary-column totalScore text-center">0</td>
            <td class="summary-column average text-center">0.00</td>
            <td class="summary-column weighted text-center">0.00</td>
        </tr>`;

			$("#subjectScheduleData").append(row);
		});

		calculateAverage();
	}
	$(document).on("input", ".studentScoreEarned, .colItems", function () {
		calculateAverage();
	});

	// ========== FORM SUBMISSIONS ==========

	$("#criteriaForm").submit(function (event) {
		event.preventDefault();

		const formData =
			$(this).serialize() + "&gradingPeriodSelect=" + currentGradePeriod;

		$.ajax({
			url: BASE_URL + "index.php/Api/addCriteria",
			type: "POST",
			data: formData,
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Criteria successfully added for " + currentGradePeriod + "!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#criteriaForm")[0].reset();
						fetchCriteria(currentScheduleId, currentGradePeriod);
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

	$(document).on("change", ".recordScoreBtn", function () {
		const scheduleId = $(this).data("id");
		const gradingPeriod = $(this).val();

		if (!scheduleId || !gradingPeriod) {
			$("#criteriaList").html(
				'<div class="text-muted text-center">Please select a grading period</div>'
			);
			return;
		}

		currentScheduleId = scheduleId;
		currentGradePeriod = gradingPeriod;

		$("#recordScoreBtn").val(scheduleId);
		$("#gradingPeriodSelect").val(gradingPeriod);

		$("#criteriaListContainer").show();

		fetchCriteria(currentScheduleId, currentGradePeriod);
	});

	$(document).on("click", "#addScoreColumn", function () {
		const newIndex = criteriaColumns.length;

		let usedWeight = criteriaColumns.reduce(
			(sum, c) => sum + parseFloat(c.weight || 0),
			0
		);
		let remainingWeight = Math.max(0, 100 - usedWeight);

		console.log(
			`Adding Score Column | Used Weight: ${usedWeight}% | Remaining: ${remainingWeight}%`
		);

		criteriaColumns.push({
			id: null,
			name: "Score " + (newIndex + 1),
			weight: remainingWeight,
			items: 0,
			isDynamic: true,
		});

		$("#scoresTableHeader th.summary-column:first").before(
			`<th class="score-column" data-col-index="${newIndex}">Score ${
				newIndex + 1
			}</th>`
		);

		$(".itemsRow td.summary-column:first").before(`
        <td class="score-column">
            <input type="number" 
                class="form-control colItems"
                data-col-index="${newIndex}"
                value="0"
                placeholder="Items">
        </td>
    `);

		$("#subjectScheduleData tr")
			.not(".itemsRow")
			.each(function () {
				$(this).find("td.summary-column:first").before(`
            <td class="score-column">
                <input type="number" class="form-control studentScoreEarned" 
                    data-student-id="${$(this).data("student-id")}" 
                    data-col-index="${newIndex}" 
                    placeholder="Score">
            </td>`);
			});

		calculateAverage();
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
		$("#subjectScheduleData tr")
			.not(".itemsRow")
			.each(function () {
				const studentId = $(this).data("student-id");
				$(this)
					.find("input.studentScoreEarned")
					.each(function () {
						const colIndex = $(this).data("col-index");
						const score = $(this).val();
						if (score !== "") {
							dataToSave.push({
								student_id: studentId,
								col_index: colIndex,
								score: score,
							});
						}
					});
			});

		$.ajax({
			url: BASE_URL + "index.php/Api/saveAllStudentScores",
			type: "POST",
			data: {
				schedule_id: currentScheduleId,
				criteria_id: currentCriteriaId,
				grade_period: currentGradePeriod,
				scores: dataToSave,
			},
			success: function () {
				Swal.fire({
					icon: "success",
					title: "Scores Saved!",
					text:
						"All student scores have been saved for " +
						currentGradePeriod +
						".",
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

	$(document).on("change", ".studentScoreEarned", function () {
		const studentId = $(this).data("student-id");
		const colIndex = $(this).data("col-index");
		const score = $(this).val();

		if (score !== "") {
			$.ajax({
				url: BASE_URL + "index.php/Api/saveStudentScoreColumn",
				type: "POST",
				data: {
					schedule_id: currentScheduleId,
					criteria_id: currentCriteriaId,
					grade_period: currentGradePeriod,
					student_id: studentId,
					col_index: colIndex,
					score,
				},
				success: function () {
					calculateAverage();
				},
			});
		}
	});

	$("#filterSubjectSelect").on("change", function () {
		renderTeacherSubjects(subjectsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterSubjectSelect").val("");
		renderTeacherSubjects(subjectsData);
	});

	// ========== CALCULATIONS ==========

	function calculateAverage() {
		const selectedWeight = window.selectedCriteriaWeight || 0;
		const weightDecimal = selectedWeight / 100;

		let totalItems = 0;
		$(".itemsRow input.colItems").each(function () {
			totalItems += parseFloat($(this).val()) || 0;
		});

		$(".itemsRow .totalItemsHeader").text(totalItems);

		$(".itemsRow .itemsAverage").text("1.0");

		const itemsWeighted = weightDecimal;
		$(".itemsRow .itemsWeighted").text(itemsWeighted.toFixed(2));

		$("#subjectScheduleData tr")
			.not(".itemsRow")
			.each(function () {
				let totalEarned = 0;

				$(this)
					.find("input.studentScoreEarned")
					.each(function () {
						totalEarned += parseFloat($(this).val()) || 0;
					});

				$(this).find("td.totalScore").text(totalEarned);

				const averagePercent =
					totalItems > 0 ? (totalEarned / totalItems) * 100 : 0;
				let averageScale = 5 - (averagePercent / 100) * 4;
				averageScale = Math.min(Math.max(averageScale, 1), 5).toFixed(2);
				$(this).find("td.average").text(averageScale);

				const weightedValue = averageScale * weightDecimal;
				$(this).find("td.weighted").text(weightedValue.toFixed(2));
			});
	}

	$(document).on("input", ".colItems", function () {
		let totalItems = 0;
		$(".itemsRow input.colItems").each(function () {
			totalItems += parseFloat($(this).val()) || 0;
		});
		$(".itemsRow .totalItemsHeader").text(totalItems);
		calculateAverage();
	});
});
