$(document).ready(function () {
	let subjectsData = [];
	loadSubjects();

	// ========== ADD OR EDIT SUBJECT ==========
	$("#subjectForm").submit(function (event) {
		event.preventDefault();

		let formData = $(this).serialize(); // uses existing field names
		let subjectId = $("#subject_id").val();
		let url = subjectId
			? BASE_URL + "index.php/Api/updateSubject/" + subjectId
			: BASE_URL + "index.php/Api/addSubjects";

		$.ajax({
			url: url,
			type: "POST",
			data: formData,
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: response.message || "Subject successfully saved!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#subjectForm")[0].reset();
						$("#subject_id").val("");
						$("#subjectModalLabel").text("Add New Subject");
						$("#subjectModal").modal("hide");
						loadSubjects();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Error!",
						text: response.message || "Something went wrong!",
						confirmButtonText: "OK",
					});
				}
			},
			error: function () {
				Swal.fire({
					icon: "error",
					title: "AJAX Error!",
					text: "An error occurred while saving the subject.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	// ========== LOAD SUBJECTS ==========
	function loadSubjects() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_subjects",
			type: "GET",
			dataType: "json",
			success: function (response) {
				subjectsData = response || [];
				renderSubjectsTable(subjectsData);
			},
			error: function () {
				$("#subjectsData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	// ========== RENDER TABLE ==========
	function renderSubjectsTable(subjects) {
		const filterProgramSelect = $("#filterProgramSelect").val();
		const filterStatusSelect = $("#filterStatusSelect").val();

		let html = "";
		const filteredSubjects = subjects.filter((sub) => {
			if (filterProgramSelect && sub.program_id != filterProgramSelect)
				return false;
			if (filterStatusSelect && sub.status_id != filterStatusSelect)
				return false;
			return true;
		});

		if (filteredSubjects.length > 0) {
			$.each(filteredSubjects, function (i, sub) {
				const rowClass =
					sub.status && sub.status.toLowerCase() === "active"
						? "highlight-row"
						: "";
				html += `
                <tr class="${rowClass}">
                    <td><input type="checkbox" class="subjectCheckbox" data-id="${
											sub.subject_id
										}" /></td>
                    <td>${sub.subject_code || ""}</td>
                    <td>${sub.subject_name || ""}</td>
                    <td>${sub.program_name || ""}</td>
                    <td>${sub.status || ""}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editSubjects" data-id="${
													sub.subject_id
												}" title="Edit">
                            <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteSubjects" data-id="${
													sub.subject_id
												}" title="Delete">
                            <i class="fa-solid fa-trash"></i>
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

	// ========== FILTERS ==========
	$("#filterProgramSelect, #filterStatusSelect").on("change", function () {
		renderSubjectsTable(subjectsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterProgramSelect")[0].selectedIndex = 0;
		$("#filterStatusSelect")[0].selectedIndex = 0;
		renderSubjectsTable(subjectsData);
	});

	// ========== EDIT SUBJECT ==========
	$(document).on("click", ".editSubjects", function () {
		const subjectId = $(this).data("id");
		const subject = subjectsData.find((sub) => sub.subject_id == subjectId);
		if (subject) {
			$("#subject_id").val(subject.subject_id);
			$("#subject").val(subject.subject_name);
			$("#subjectCode").val(subject.subject_code);
			$("#programSelect").val(subject.program_id);
			$("#statusSelect").val(subject.status_id);
			$("#subjectModalLabel").text("Edit Subject");
			$("#subjectModal").modal("show");
		}
	});

	// ========== DELETE SUBJECT ==========
	$(document).on("click", ".deleteSubjects", function () {
		const subjectId = $(this).data("id");
		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Yes, delete it!",
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: BASE_URL + "index.php/Api/deleteSubject/" + subjectId,
					type: "POST",
					dataType: "json",
					success: function (response) {
						if (response.status === "success") {
							Swal.fire(
								"Deleted!",
								response.message || "Subject deleted.",
								"success"
							);
							loadSubjects();
						} else {
							Swal.fire(
								"Error!",
								response.message || "Something went wrong!",
								"error"
							);
						}
					},
					error: function () {
						Swal.fire("Error!", "AJAX error while deleting.", "error");
					},
				});
			}
		});
	});

	// ========== SELECT ALL CHECKBOX ==========
	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".subjectCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".subjectCheckbox", function () {
		const total = $(".subjectCheckbox").length;
		const checked = $(".subjectCheckbox:checked").length;
		$("#selectAllToday").prop("checked", total === checked);
	});

	// ========== RESET MODAL ON CLOSE ==========
	$("#subjectModal").on("hidden.bs.modal", function () {
		$("#subjectForm")[0].reset();
		$("#subject_id").val("");
		$("#subjectModalLabel").text("Add New Subject");
	});
});
