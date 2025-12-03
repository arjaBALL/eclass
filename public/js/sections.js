$(document).ready(function () {
	let sectionsData = [];
	loadSections();

	// Submit Add/Edit Section
	$("#sectionForm").submit(function (e) {
		e.preventDefault();
		const sectionDbId = $("#editSectionDbId").val(); // if exists, it's edit
		const url = sectionDbId
			? BASE_URL + "index.php/Api/update_section/" + sectionDbId
			: BASE_URL + "index.php/Api/addSections";

		$.ajax({
			url: url,
			type: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: sectionDbId
							? "Section updated successfully!"
							: "Section added successfully!",
						confirmButtonText: "OK",
					}).then(() => {
						$("#sectionForm")[0].reset();
						$("#editSectionDbId").val("");
						$("#sectionModal").modal("hide");
						loadSections();
					});
				} else {
					Swal.fire("Error!", response.message || "Operation failed!", "error");
				}
			},
			error: function (xhr) {
				console.error("AJAX error:", xhr.responseText);
				Swal.fire("Error!", "AJAX error occurred.", "error");
			},
		});
	});

	// Load sections
	function loadSections() {
		$.ajax({
			url: BASE_URL + "index.php/Api/get_sections",
			type: "GET",
			dataType: "json",
			success: function (response) {
				sectionsData = response || [];
				renderSectionsTable(sectionsData);
			},
			error: function (xhr) {
				console.error("Fetch Error:", xhr.responseText);
				$("#sectionsData").html(
					'<tr><td colspan="7" class="text-center text-danger">Error fetching data</td></tr>'
				);
			},
		});
	}

	// Render sections table
	function renderSectionsTable(sections) {
		const filterProgramSelect = $("#filterProgramSelect").val();
		let html = "";

		const filteredSections = sections.filter(
			(s) => !filterProgramSelect || s.program_id == filterProgramSelect
		);

		if (filteredSections.length > 0) {
			$.each(filteredSections, function (i, s) {
				const rowClass =
					s.status && s.status.trim().toLowerCase() === "active"
						? "highlight-row"
						: "";
				html += `
                <tr class="${rowClass}">
                    <td><input type="checkbox" class="sectionCheckbox" data-id="${
											s.id
										}"></td>
                    <td>${s.section || ""}</td>
                    <td>${s.program_name || ""}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editSections" data-id="${
													s.section_id
												}" title="Edit">
                            <i class="fa-solid fa-user-pen" style="color: #ffffff;"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteSections" data-id="${
													s.section_id
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
	}

	// Edit section click
	$(document).on("click", ".editSections", function () {
		const sectionId = $(this).data("id");
		$.ajax({
			url: BASE_URL + "index.php/Api/get_section/" + sectionId,
			type: "GET",
			dataType: "json",
			success: function (data) {
				$("#editSectionDbId").val(data.id);
				$("#section").val(data.section);
				$("#programSelect").val(data.program_id);
				$("#sectionModalLabel").text("Edit Section");

				const modal = new bootstrap.Modal(
					document.getElementById("sectionModal")
				);
				modal.show();
			},
			error: function (xhr) {
				console.error("Fetch section error:", xhr.responseText);
				Swal.fire("Error!", "Failed to fetch section data.", "error");
			},
		});
	});

	// Delete section
	$(document).on("click", ".deleteSections", function () {
		const id = $(this).data("id");
		Swal.fire({
			title: "Are you sure?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, delete it!",
		}).then((result) => {
			if (result.isConfirmed) {
				$.post(
					BASE_URL + "index.php/Api/delete_section/" + id,
					function (res) {
						if (res.status === "success")
							Swal.fire("Deleted!", res.message, "success");
						else Swal.fire("Error!", res.message, "error");
						loadSections();
					},
					"json"
				);
			}
		});
	});

	// Filters
	$("#filterProgramSelect").on("change", function () {
		renderSectionsTable(sectionsData);
	});
	$("#resetFiltersBtn").on("click", function () {
		$("#filterProgramSelect")[0].selectedIndex = 0;
		renderSectionsTable(sectionsData);
	});

	// Select all checkboxes
	$(document).on("change", "#selectAllToday", function () {
		$(".sectionCheckbox").prop("checked", $(this).is(":checked"));
	});
	$(document).on("change", ".sectionCheckbox", function () {
		$("#selectAllToday").prop(
			"checked",
			$(".sectionCheckbox:checked").length === $(".sectionCheckbox").length
		);
	});
});
