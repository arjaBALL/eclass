$(document).ready(function () {
	let sectionsData = [];
	loadSections();

	$("#sectionForm").submit(function (event) {
		event.preventDefault();

		$.ajax({
			url: BASE_URL + "index.php/Api/addSections",
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Section successfully added!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#sectionForm")[0].reset();
						$("#sectionModal").modal("hide");
						loadSections(); // reload students
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
					text: "An error occurred while saving the section.",
					confirmButtonText: "OK",
				});
			},
		});
	});

	function loadSections() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_sections",
			type: "GET",
			dataType: "json",
			success: function (response) {
				sectionsData = response || [];
				renderSectionsTable(sectionsData);
			},
			error: function (xhr, status, error) {
				console.error("Fetch Error:", xhr.responseText);
				$("#sectionsData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	function renderSectionsTable(sections) {
		const filterProgramSelect = $("#filterProgramSelect").val();

		let html = "";

		const filteredSections = sections.filter((sections) => {
			if (filterProgramSelect && sections.program_id != filterProgramSelect)
				return false;
			return true;
		});

		if (filteredSections.length > 0) {
			$.each(filteredSections, function (i, sections) {
				const rowClass =
					sections.status && sections.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";

				html += `
        	<tr class="${rowClass}">
            <td>
                <input type="checkbox" class="sectionCheckbox" 
               data-id="${sections.id}" />
            </td>
            <td>${sections.section || ""}</td>
            <td>${sections.program_name || ""}</td>
            <td>
                <button class="btn btn-sm btn-primary editSections" data-id="${
									sections.id
								}" title="Edit">
                    <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                </button>
                <button class="btn btn-sm btn-danger deleteSections" data-id="${
									sections.id
								}" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>`;
			});
		} else {
			html =
				'<tr><td colspan="7" class="text-center">No sections found</td></tr>';
		}

		$("#sectionsData").html(html);
		// setupTable("users", "searchUsers", [10, 25, 50, 100], 10);
	}

	$("#filterProgramSelect").on("change", function () {
		renderSectionsTable(sectionsData);
	});

	$("#resetFiltersBtn").on("click", function () {
		$("#filterProgramSelect")[0].selectedIndex = 0;

		renderTeachersTable(sectionsData);
	});

	$(document).on("change", "#selectAllToday", function () {
		const checked = $(this).is(":checked");
		$(".sectionCheckbox").prop("checked", checked);
	});

	$(document).on("change", ".sectionCheckbox", function () {
		const total = $(".sectionCheckbox").length;
		const checked = $(".sectionCheckbox:checked").length;

		$("#selectAllToday").prop("checked", total === checked);
	});
});
