$(document).ready(function () {
	let subjectsData = [];
	loadSubjects();

	$("#subjectForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addSubjects",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Subject successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#subjectForm")[0].reset();
						$("#subjectModal").modal("hide");
						loadSubjects(); // reload students
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
					text: "An error occurred while saving the subject.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	function loadSubjects() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_subjects",
			type: "GET",
			dataType: "json",
			success: function (response) {
				subjectsData = response || [];
				renderSubjectsTable(subjectsData);
			},
			error: function (xhr, status, error) {
				console.error("Fetch Error:", xhr.responseText);
				$("#subjectsData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	function renderSubjectsTable(subjects) {
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
            <td>
                <input type="checkbox" class="subjectCheckbox" 
               data-id="${subjects.id}" />
            </td>
            <td>${subjects.subject_code || ""}</td>
            <td>${subjects.subject_name || ""}</td>
            <td>${subjects.program_name || ""}</td>
            <td>${subjects.status || ""}</td>
            <td>
                <button class="btn btn-sm btn-primary editSubjects" data-id="${
									subjects.id
								}" title="Edit">
                    <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                </button>
                <button class="btn btn-sm btn-danger deleteSubjects" data-id="${
									subjects.id
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
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	$("#filterProgramSelect, #filterStatusSelect").on("change", function () {
		renderSubjectsTable(subjectsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterProgramSelect")[0].selectedIndex = 0;
		$("#filterStatusSelect")[0].selectedIndex = 0;

		renderTeachersTable(teachersData);
	});

	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".subjectCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".subjectCheckbox", function () {
		const total = $(".subjectCheckbox").length;
		const checked = $(".subjectCheckbox:checked").length;

		$("#selectAllToday").prop("checked", total === checked);
	});
});
