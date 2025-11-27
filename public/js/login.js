const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("password");

togglePassword.addEventListener("click", function () {
	// Toggle the type attribute
	const type =
		password.getAttribute("type") === "password" ? "text" : "password";
	password.setAttribute("type", type);

	// Toggle the eye icon
	this.classList.toggle("fa-eye");
	this.classList.toggle("fa-eye-slash");
});
$(document).ready(function () {
	$("#loginForm").on("submit", function (e) {
		e.preventDefault();

		// Show loading SweetAlert
		Swal.fire({
			title: "Logging in...",
			text: "Please wait a moment.",
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			},
		});

		$.ajax({
			url: BASE_URL + "index.php/Auth/login",
			method: "POST",
			data: $(this).serialize(),
			dataType: "json",
			success: function (res) {
				if (res.status === "success") {
					Swal.fire({
						icon: "success",
						title: "Login Successful!",
						text: "Redirecting to your dashboard...",
						showConfirmButton: false,
						timer: 1500,
					});

					setTimeout(() => {
						if (res.role && res.role.toLowerCase() === "Admin") {
							window.location.href = BASE_URL + "index.php/students";
						} else {
							window.location.href = BASE_URL + "index.php/schedules";
						}
					}, 1500);
				} else {
					Swal.fire({
						icon: "error",
						title: "Login Failed",
						text: res.message || "Invalid username or password.",
					});
					$("#loginForm")[0].reset();
					$("input[name='username']").focus();
				}
			},
			error: function (err) {
				Swal.fire({
					icon: "error",
					title: "Server Error",
					text: "An error occurred while trying to log in.",
				});
				console.log(err.responseText);
			},
		});
	});
});
