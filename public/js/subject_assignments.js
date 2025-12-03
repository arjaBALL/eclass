$(document).ready(function () {
	let subjectAssignmentsData = [];
	loadSubjectAssignments();

	$("#subjectAssignmentForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addSubjectAssignments",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "SubjectAssignment successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#subjectAssignmentForm")[0].reset();
						$("#subjectAssignmentModal").modal("hide");
						loadSubjectAssignments(); // reload students
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
					text: "An error occurred while saving the subjectAssignment.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	function loadSubjectAssignments() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_subjectAssignments",
			type: "GET",
			dataType: "json",
			success: function (response) {
				subjectAssignmentsData = response || [];
				renderSubjectAssignmentsTable(subjectAssignmentsData);
			},
			error: function (xhr, status, error) {
				console.error("Fetch Error:", xhr.responseText);
				$("#subjectAssignmentsData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	function renderSubjectAssignmentsTable(subjectAssignments) {
		const filterTeacherSelect = $("#filterTeacherSelect").val();
		const filterSubjectSelect = $("#filterSubjectSelect").val();

		let html = "";

		const filteredSubjectAssignments = subjectAssignments.filter(
			(assignment) => {
				if (
					filterTeacherSelect &&
					assignment.teacher_id.toString() !== filterTeacherSelect
				)
					return false;
				if (
					filterSubjectSelect &&
					assignment.subject_id.toString() !== filterSubjectSelect
				)
					return false;
				return true;
			}
		);

		if (filteredSubjectAssignments.length > 0) {
			$.each(filteredSubjectAssignments, function (i, subjectAssignments) {
				const rowClass =
					subjectAssignments.status &&
					subjectAssignments.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
        	<tr class="${rowClass}">
            <td>
                <input type="checkbox" class="subjectAssignmentCheckbox" 
               data-id="${subjectAssignments.id}" />
            </td>
            <td>${subjectAssignments.fullname || ""}</td>
            <td>${subjectAssignments.subject_code || ""}</td>
            <td>${subjectAssignments.semester || ""}</td>
            <td>
                <button class="btn btn-sm btn-primary editSubjectAssignments" data-id="${
									subjectAssignments.id
								}" title="Edit">
                    <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                </button>
                <button class="btn btn-sm btn-danger deleteSubjectAssignments" data-id="${
									subjectAssignments.id
								}" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No subjectAssignments found</td></tr>';
		}

		$("#subjectAssignmentsData").html(html);
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	// Open Edit Modal
	$(document).on("click", ".editSubjectAssignments", function () {
		const assignmentId = $(this).data("id");

		$.ajax({
			url: BASE_URL + "index.php/Api/get_subjectAssignment/" + assignmentId,
			type: "GET",
			dataType: "json",
			success: function (data) {
				if (data.status === "error") {
					Swal.fire("Error!", data.message, "error");
					return;
				}

				$("#editSubjectAssignmentId").val(data.id);
				$("#editSubjectSelect").val(data.subject_id);
				$("#editTeacherSelect").val(data.teacher_id);
				$("#editSemesterSelect").val(data.semester_id);

				const modal = new bootstrap.Modal(
					document.getElementById("editSubjectAssignmentModal")
				);
				modal.show();
			},
			error: function (xhr) {
				console.error(xhr.responseText);
				Swal.fire("Error!", "Failed to fetch assignment data.", "error");
			},
		});
	});

	// Submit Edit Form
	$("#editSubjectAssignmentForm").submit(function (e) {
		e.preventDefault();
		const assignmentId = $("#editSubjectAssignmentId").val();
		const formData = $(this).serialize();

		$.ajax({
			url: BASE_URL + "index.php/Api/update_subjectAssignment/" + assignmentId,
			type: "POST",
			data: formData,
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire("Success!", response.message, "success").then(() => {
						$("#editSubjectAssignmentModal").modal("hide");
						loadSubjectAssignments(); // reload table
					});
				} else {
					Swal.fire("Error!", response.message, "error");
				}
			},
			error: function (xhr) {
				console.error(xhr.responseText);
				Swal.fire("Error!", "AJAX error occurred.", "error");
			},
		});
	});

	$("#filterTeacherSelect, #filterSubjectSelect").on("change", function () {
		renderSubjectAssignmentsTable(subjectAssignmentsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterTeacherSelect")[0].selectedIndex = 0;
		$("#filterSubjectSelect")[0].selectedIndex = 0;

		renderSubjectAssignmentsTable(subjectAssignmentsData);
	});

	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".subjectAssignmentCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".subjectAssignmentCheckbox", function () {
		const total = $(".subjectAssignmentCheckbox").length;
		const checked = $(".subjectAssignmentCheckbox:checked").length;

		$("#selectAllToday").prop("checked", total === checked);
	});
});
