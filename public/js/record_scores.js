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
	let currentGradePeriod = null;

	// Hide criteria form and list initially
	if ($("#criteriaForm").length > 0) {
		$("#criteriaForm")
			.closest('.border, .card, .container, [class*="col"]')
			.hide();
	}
	$("#criteriaListContainer, #criteriaList")
		.closest('.border, .card, .container, [class*="col"]')
		.hide();

	// Hide score table initially
	$(".col-9.border").hide();

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
                                    <button type="button" class="btn btn-primary btn-sm manageBtn" 
                                        data-criteria-id="${c.id}" 
                                        data-criteria-weight="${
																					c.weight
																				}">Manage</button>
                                </div>
                            </div>
                        </div>
                    `;
					});
				} else {
					// Reset criteriaColumns to empty array when no criteria found
					criteriaColumns = [];
					html = '<div class="text-center text-muted">No criteria found</div>';
				}

				$("#criteriaList").html(html);

				// Update weight display and check if can add more criteria
				updateWeightDisplay();
			},
			error: function (xhr, status, error) {
				console.error("Failed to fetch criteria:", error);
				criteriaColumns = []; // Reset on error too
				$("#criteriaList").html(
					'<div class="text-center text-danger">Error loading criteria</div>'
				);
			},
		});
	}

	// New function to calculate total weight for current grading period
	function calculateTotalWeight() {
		let totalWeight = 0;
		criteriaColumns.forEach((c) => {
			totalWeight += parseFloat(c.weight) || 0;
		});
		return totalWeight;
	}

	// New function to update weight display and disable form if needed
	function updateWeightDisplay() {
		const totalWeight = calculateTotalWeight();
		const remainingWeight = 100 - totalWeight;

		// Update or create weight display
		let weightDisplay = $("#weightDisplay");
		if (weightDisplay.length === 0) {
			$("#criteriaList").before(`
				<div id="weightDisplay" class="alert mb-2 py-1 px-2" role="alert" style="font-size: 0.75rem;">
					<span><strong>Total:</strong> <span id="totalWeightValue">0</span>%</span>
					<span class="mx-2">|</span>
					<span><strong>Remaining:</strong> <span id="remainingWeightValue">100</span>%</span>
				</div>
			`);
			weightDisplay = $("#weightDisplay");
		}

		$("#totalWeightValue").text(totalWeight.toFixed(2));
		$("#remainingWeightValue").text(remainingWeight.toFixed(2));

		// Change alert color based on weight
		weightDisplay.removeClass(
			"alert-info alert-warning alert-danger alert-success"
		);
		if (totalWeight >= 100) {
			weightDisplay.addClass("alert-danger");
			// Disable the criteria form
			$("#criteriaForm :input").prop("disabled", true);
			$("#criteriaForm button[type='submit']").prop("disabled", true);

			// Show message
			if ($("#weightLimitMessage").length === 0) {
				$("#criteriaForm").before(`
					<div id="weightLimitMessage" class="alert alert-warning mb-2 py-1 px-2" style="font-size: 0.75rem;">
						<i class="fa-solid fa-exclamation-triangle"></i> 
						Cannot add more criteria. Total weight has reached 100% for ${currentGradePeriod}.
					</div>
				`);
			}
		} else {
			weightDisplay.addClass("alert-info");
			// Enable the criteria form - make sure form exists first
			if ($("#criteriaForm").length > 0) {
				$("#criteriaForm :input").prop("disabled", false);
				$("#criteriaForm button[type='submit']").prop("disabled", false);
			}
			$("#weightLimitMessage").remove();
		}
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

		// Show the score table section
		$(".col-9.border").show();

		$.ajax({
			url: BASE_URL + "index.php/Api/getStudentScore",
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

		let allColIndexes = new Set();
		students.forEach((s) => {
			if (s.scores && s.scores.length > 0) {
				s.scores.forEach((score) => {
					allColIndexes.add(score.col_index);
				});
			}
		});

		let colIndexArray = Array.from(allColIndexes).sort((a, b) => a - b);

		let header = "<th>Students</th>";
		colIndexArray.forEach((colIndex, i) => {
			header += `<th class="score-column" data-col-index="${colIndex}">Score ${
				colIndex + 1
			}</th>`;
		});
		header += `<th class="summary-column">Total Score</th>`;
		header += `<th class="summary-column average">Average</th>`;
		header += `<th class="summary-column weighted">Weighted Grade (Base on ${(
			selectedCriteria.weight / 100
		).toFixed(2)} criteria weight)</th>`;
		$("#scoresTableHeader").html(header);

		let itemsRow = `<tr class="itemsRow"><td>Items</td>`;
		colIndexArray.forEach((colIndex) => {
			itemsRow += `
            <td class="score-column">
                <input type="number" class="form-control colItems" 
                    data-col-index="${colIndex}" 
                    value="${selectedCriteria.items || 0}" 
                    placeholder="Items">
            </td>`;
		});

		let totalItems = (selectedCriteria.items || 0) * colIndexArray.length;
		itemsRow += `<td class="summary-column totalItemsHeader">${totalItems}</td>`;
		itemsRow += `<td class="summary-column itemsAverage">1.0</td>`;
		itemsRow += `<td class="summary-column itemsWeighted">0.00</td></tr>`;
		$("#subjectScheduleData").html(itemsRow);

		students.forEach((s) => {
			let row = `<tr data-student-id="${s.id}"><td>${s.fullname}</td>`;

			colIndexArray.forEach((colIndex) => {
				const scoreData =
					s.scores?.find((score) => score.col_index == colIndex) || {};
				const earned = scoreData.score || "";

				row += `
                <td class="score-column">
                    <input type="number" 
                        class="form-control studentScoreEarned"
                        data-student-id="${s.id}"
                        data-col-index="${colIndex}"
                        data-criteria-id="${selectedCriteria.id}"
                        placeholder="Score"
                        value="${earned}">
                </td>`;
			});

			const gradeReport = s.grade_report || {};

			row += `
            <td class="summary-column totalScore text-center">${
							gradeReport.total_score || 0
						}</td>
            <td class="summary-column average text-center">${
							gradeReport.average || "0.00"
						}</td>
            <td class="summary-column weighted text-center">${
							gradeReport.weighted_grade || "0.00"
						}</td>
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

		// Check if total weight would exceed 100%
		const newWeight = parseFloat($("#criteriaWeight").val()) || 0;
		const currentTotal = calculateTotalWeight();

		if (currentTotal + newWeight > 100) {
			Swal.fire({
				icon: "error",
				title: "Weight Limit Exceeded!",
				text: `Cannot add criteria. Current total weight is ${currentTotal.toFixed(
					2
				)}%. Adding ${newWeight}% would exceed 100%.`,
				confirmButtonText: "OK",
			});
			return;
		}

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
			// Hide everything when no grading period is selected
			if ($("#criteriaForm").length > 0) {
				$("#criteriaForm")
					.closest('.border, .card, .container, [class*="col"]')
					.hide();
			}
			$("#criteriaListContainer, #criteriaList")
				.closest('.border, .card, .container, [class*="col"]')
				.hide();
			$(".col-9.border").hide();
			$("#weightDisplay").remove();
			$("#weightLimitMessage").remove();

			$("#criteriaList").html(
				'<div class="text-muted text-center">Please select a grading period</div>'
			);
			return;
		}

		currentScheduleId = scheduleId;
		currentGradePeriod = gradingPeriod;

		$("#recordScoreBtn").val(scheduleId);
		$("#gradingPeriodSelect").val(gradingPeriod);

		// Show criteria form and list
		if ($("#criteriaForm").length > 0) {
			$("#criteriaForm")
				.closest('.border, .card, .container, [class*="col"]')
				.show();
		}
		$("#criteriaListContainer").show();
		$("#criteriaList")
			.closest('.border, .card, .container, [class*="col"]')
			.show();

		fetchCriteria(currentScheduleId, currentGradePeriod);
	});

	$(document).on("click", "#addScoreColumn", function () {
		const newIndex = $(".score-column").length - 1; // -1 for items row

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
                    data-criteria-id="${currentCriteriaId}"
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
				const totalScore =
					parseFloat($(this).find("td.totalScore").text()) || 0;
				const average = parseFloat($(this).find("td.average").text()) || 0;
				const weighted = parseFloat($(this).find("td.weighted").text()) || 0;

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
								total_score: totalScore,
								average: average,
								weighted_grade: weighted,
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
		const $row = $(this).closest("tr");
		const studentId = $(this).data("student-id");
		const colIndex = $(this).data("col-index");
		const score = $(this).val();

		// Calculate immediately
		calculateAverage();

		// Get calculated values
		const totalScore = parseFloat($row.find("td.totalScore").text()) || 0;
		const average = parseFloat($row.find("td.average").text()) || 0;
		const weighted = parseFloat($row.find("td.weighted").text()) || 0;

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
					score: score,
					total_score: totalScore,
					average: average,
					weighted_grade: weighted,
				},
				success: function () {
					console.log("Score saved successfully");
				},
				error: function () {
					console.error("Failed to save score");
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
