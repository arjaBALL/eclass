let currentStudentData = null;
let currentSectionData = null;
let registrationStream = null;
let attendanceStream = null;
let capturedImages = [];
let requiredSamples = 5;
let recognizedStudents = [];
let attendanceInterval = null;
let sectionStudents = [];

function loadStudentsTable(students) {
	const tbody = $("#studentsData");
	tbody.empty();

	students.forEach((student) => {
		tbody.append(`
			<tr>
				<td>${student.student_school_id}</td>
				<td>${student.last_name}, ${student.first_name}</td>
				<td>${student.year_level || "N/A"}</td>
				<td>${student.program || "N/A"}</td>
				<td>${student.status || "Active"}</td>
				<td>
					<button class="btn btn-sm btn-danger recordBtn" 
						data-id="${student.student_school_id}" 
						data-name="${student.first_name} ${student.last_name}"
						data-firstname="${student.first_name}"
						data-lastname="${student.last_name}"
						title="Face Registration">
						<i class="fa-solid fa-clipboard-user"></i>
					</button>
					<button class="btn btn-sm btn-success attendanceBtn" 
						data-section-id="${student.section_id || ""}"
						data-subject="${student.subject_name || "Class"}"
						data-section="${student.section_name || "Section"}"
						title="Take Attendance">
						<i class="fa-solid fa-check-to-slot"></i>
					</button>
				</td>
			</tr>
		`);
	});

	bindFaceRecognitionButtons();
}

function bindFaceRecognitionButtons() {
	$(document)
		.off("click", ".recordBtn")
		.on("click", ".recordBtn", function () {
			currentStudentData = {
				student_school_id: $(this).data("id"),
				first_name: $(this).data("firstname"),
				last_name: $(this).data("lastname"),
				full_name: $(this).data("name"),
			};
			openFaceRegistrationModal();
		});

	$(document)
		.off("click", ".attendanceBtn")
		.on("click", ".attendanceBtn", function () {
			const sectionId = $(this).data("section-id");
			const subject = $(this).data("subject");
			const section = $(this).data("section");

			if (!sectionId) {
				Swal.fire({
					icon: "error",
					title: "Section Not Found",
					text: "Section ID not found!",
				});
				return;
			}

			currentSectionData = { section_id: sectionId, subject, section };
			loadSectionStudents(sectionId);
			openAttendanceModal();
		});
}

function resetRegistrationUI() {
	capturedImages = [];
	$("#registrationIdle").show();
	$(
		"#registrationActive, #registrationProcessing, #registrationSuccess, #registrationErrorAlert, #registrationInstructions, #stopRegistrationBtn"
	).hide();
	$("#registrationProgressBar").css("width", "0%").removeClass("bg-success");
	$("#registrationProgressText").text(`Progress: 0/${requiredSamples}`);
	$("#registrationError").text("");
}

$("#startRegistrationBtn, #retryRegistrationBtn").on(
	"click",
	startFaceRegistration
);
$("#stopRegistrationBtn").on("click", stopRegistrationCamera);

async function startFaceRegistration() {
	try {
		capturedImages = [];
		registrationStream = await navigator.mediaDevices.getUserMedia({
			video: { width: { ideal: 640 }, height: { ideal: 480 } },
		});
		const video = document.getElementById("registrationVideo");
		video.srcObject = registrationStream;

		$("#registrationIdle").hide();
		$(
			"#registrationActive, #registrationInstructions, #stopRegistrationBtn"
		).show();
		$("#registrationErrorAlert").hide();

		setTimeout(captureRegistrationSamples, 2000);
	} catch (error) {
		showRegistrationError("Could not access camera. Check permissions.");
	}
}

async function captureRegistrationSamples() {
	const video = document.getElementById("registrationVideo");
	const canvas = document.getElementById("registrationCanvas");
	const context = canvas.getContext("2d");

	canvas.width = video.videoWidth;
	canvas.height = video.videoHeight;

	const interval = setInterval(async () => {
		if (capturedImages.length >= requiredSamples) {
			clearInterval(interval);
			await processRegistration();
			return;
		}

		context.drawImage(video, 0, 0, canvas.width, canvas.height);
		const imageData = canvas.toDataURL("image/jpeg", 0.95);

		const isValid = await validateImage(imageData);
		if (isValid.success) {
			capturedImages.push(imageData);
			updateRegistrationProgress();
			$("#registrationError").text("");
		} else {
			$("#registrationError").text(isValid.message);
		}
	}, 1500);
}

function updateRegistrationProgress() {
	const progress = (capturedImages.length / requiredSamples) * 100;
	$("#registrationProgressBar").css("width", `${progress}%`);
	$("#registrationProgressText").text(
		`Progress: ${capturedImages.length}/${requiredSamples}`
	);
	if (capturedImages.length === requiredSamples)
		$("#registrationProgressBar").addClass("bg-success");
}

async function validateImage(imageData) {
	try {
		const response = await fetch(`${BASE_URL}index.php/validate-face`, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ image_data: imageData }),
		});
		return await response.json();
	} catch (error) {
		return { success: true };
	}
}

async function processRegistration() {
	if (!currentStudentData) {
		showRegistrationError("Student data not found. Please try again.");
		return;
	}

	const studentId = currentStudentData.student_school_id;

	if (!studentId) {
		showRegistrationError("Student ID is missing. Please try again.");
		return;
	}

	if (!capturedImages || capturedImages.length === 0) {
		showRegistrationError("No images captured. Please try again.");
		return;
	}

	stopRegistrationCamera();
	$("#registrationActive").hide();
	$("#registrationProcessing").show();
	$("#registrationInstructions").hide();

	try {
		const response = await fetch(`${BASE_URL}index.php/register-face`, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				student_id: studentId,
				student_name: `${currentStudentData.first_name} ${currentStudentData.last_name}`,
				images: capturedImages,
			}),
		});

		const result = await response.json();
		$("#registrationProcessing").hide();

		if (result.success) {
			$("#registrationSuccess").show();
			setTimeout(() => $("#faceRegistrationModal").modal("hide"), 2000);
		} else {
			showRegistrationError(result.message || "Registration failed");
		}
	} catch (error) {
		$("#registrationProcessing").hide();
		showRegistrationError("Network error. Please try again.");
	}
}

function showRegistrationError(message) {
	$("#registrationErrorMessage").text(message);
	$("#registrationErrorAlert").show();
}

function stopRegistrationCamera() {
	if (registrationStream) {
		registrationStream.getTracks().forEach((track) => track.stop());
		registrationStream = null;
	}
	$("#stopRegistrationBtn").hide();
}

function openAttendanceModal() {
	$("#attendanceSubjectDisplay").text(currentSectionData.subject);
	$("#attendanceSectionDisplay").text(currentSectionData.section);
	resetAttendanceUI();
	$("#attendanceModal").modal("show");
}

function resetAttendanceUI() {
	recognizedStudents = [];
	$("#attendanceIdle").show();
	$(
		"#attendanceActive, #attendanceProcessing, #attendanceSuccess, #attendanceResults, #attendanceInstructions, #stopAttendanceBtn, #saveAttendanceBtn"
	).hide();
	updateAttendanceStats();
}

async function loadSectionStudents(sectionId) {
	try {
		const response = await fetch(
			`${BASE_URL}index.php/FaceRecognition/get_section_students/${sectionId}`
		);
		const result = await response.json();

		if (result.success) {
			sectionStudents = result.students || [];
			updateAttendanceTable();
		} else {
			Swal.fire({
				icon: "error",
				title: "Failed to Load Students",
				text: result.message,
			});
		}
	} catch (error) {
		Swal.fire({
			icon: "error",
			title: "Network Error",
			text: "Failed to load students. Please try again.",
		});
	}
}

$("#startAttendanceBtn").on("click", startAttendanceRecognition);
$("#stopAttendanceBtn").on("click", stopAttendanceCamera);
$("#saveAttendanceBtn").on("click", saveAttendance);

async function startAttendanceRecognition() {
	try {
		attendanceStream = await navigator.mediaDevices.getUserMedia({
			video: { width: { ideal: 640 }, height: { ideal: 480 } },
		});
		const video = document.getElementById("attendanceVideo");
		video.srcObject = attendanceStream;

		$("#attendanceIdle").hide();
		$(
			"#attendanceActive, #attendanceResults, #attendanceInstructions, #stopAttendanceBtn, #saveAttendanceBtn"
		).show();

		attendanceInterval = setInterval(recognizeFaces, 3000);
	} catch (error) {
		Swal.fire({
			icon: "error",
			title: "Camera Access Denied",
			text: "Could not access camera. Please check permissions.",
		});
	}
}

async function recognizeFaces() {
	const video = document.getElementById("attendanceVideo");
	const canvas = document.getElementById("attendanceCanvas");
	const context = canvas.getContext("2d");

	canvas.width = video.videoWidth;
	canvas.height = video.videoHeight;

	context.drawImage(video, 0, 0, canvas.width, canvas.height);
	const imageData = canvas.toDataURL("image/jpeg", 0.95);

	try {
		const response = await fetch(`${BASE_URL}index.php/recognize-faces`, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				image_data: imageData,
				section_id: currentSectionData.section_id,
			}),
		});

		const result = await response.json();
		if (result.success && result.recognized_ids) {
			result.recognized_ids.forEach((id) => {
				if (!recognizedStudents.some((s) => s.id === id))
					recognizedStudents.push({
						id,
						time: new Date().toLocaleTimeString(),
					});
			});
			updateAttendanceStats();
			updateAttendanceTable();
		}
	} catch (error) {
		// Silent fail for recognition
	}
}

function updateAttendanceStats() {
	const total = sectionStudents.length;
	const present = recognizedStudents.length;
	const absent = total - present;
	const progress = total > 0 ? (present / total) * 100 : 0;

	$("#totalCount").text(total);
	$("#presentCount").text(present);
	$("#recognizedCount").text(present);
	$("#absentCount").text(absent);
	$("#attendanceProgressBar").css("width", `${progress}%`);
}

function updateAttendanceTable() {
	const tbody = $("#attendanceResultsData");
	tbody.empty();

	sectionStudents.forEach((student) => {
		const studentId = student.student_school_id;
		const firstName = student.firstname || student.first_name || "";
		const lastName = student.lastname || student.last_name || "";

		const fullName =
			lastName && firstName
				? `${lastName}, ${firstName}`
				: student.fullname || `${firstName} ${lastName}`.trim() || "Unknown";

		const recognized = recognizedStudents.find((s) => s.id === studentId);
		const status = recognized ? "Present" : "Absent";
		const statusClass = recognized ? "bg-success" : "bg-secondary";
		const time = recognized ? recognized.time : "—";

		tbody.append(`
			<tr>
				<td>${studentId}</td>
				<td>${fullName}</td>
				<td><span class="badge ${statusClass}">${status}</span></td>
				<td>${time}</td>
			</tr>
		`);
	});
}

async function saveAttendance() {
	if (!recognizedStudents.length) {
		Swal.fire({
			icon: "warning",
			title: "No Students Recognized",
			text: "Please wait for students to be recognized before saving.",
		});
		return;
	}

	if (!currentSectionData || !currentSectionData.schedule_id) {
		Swal.fire({
			icon: "error",
			title: "Missing Information",
			text: "Schedule information missing. Please try again.",
		});
		return;
	}

	stopAttendanceCamera();
	$("#attendanceActive").hide();
	$("#attendanceProcessing").show();

	try {
		const studentIds = recognizedStudents.map((s) => s.id);

		const requestData = {
			student_ids: studentIds,
			schedule_id: currentSectionData.schedule_id,
		};

		const response = await fetch(
			`${BASE_URL}index.php/FaceRecognition/mark_attendance`,
			{
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify(requestData),
			}
		);

		const result = await response.json();
		$("#attendanceProcessing").hide();

		if (result.success) {
			$("#attendanceSuccess").show();

			let message = `Successfully marked ${result.count} student(s) present`;
			if (result.duplicates > 0) {
				message += `\n${result.duplicates} student(s) already marked today`;
			}
			if (result.failed > 0) {
				message += `\n${result.failed} student(s) failed`;
			}

			Swal.fire({
				icon: "success",
				title: "Attendance Saved",
				text: message,
				timer: 2000,
				showConfirmButton: false,
			});

			setTimeout(() => {
				$("#attendanceModal").modal("hide");
			}, 2000);
		} else {
			Swal.fire({
				icon: "error",
				title: "Save Failed",
				text: result.message || "Failed to save attendance.",
			});
		}
	} catch (error) {
		$("#attendanceProcessing").hide();
		Swal.fire({
			icon: "error",
			title: "Network Error",
			text: "Failed to save attendance. Please try again.",
		});
	}
}

function stopAttendanceCamera() {
	if (attendanceStream)
		attendanceStream.getTracks().forEach((track) => track.stop());
	attendanceStream = null;

	if (attendanceInterval) clearInterval(attendanceInterval);
	attendanceInterval = null;

	$("#stopAttendanceBtn").hide();
}

$("#faceRegistrationModal").on("hidden.bs.modal", stopRegistrationCamera);
$("#attendanceModal").on("hidden.bs.modal", stopAttendanceCamera);
