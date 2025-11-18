document.addEventListener("DOMContentLoaded", () => {
	const toggleBtn = document.getElementById("toggleSidebar");
	const sidebar = document.getElementById("sidebar");

	if (toggleBtn && sidebar) {
		toggleBtn.addEventListener("click", () => {
			sidebar.classList.toggle("minimized");
		});
	}

	// Dropdown positioning
	sidebar?.querySelectorAll(".dropdown").forEach((drop) => {
		const toggle = drop.querySelector(".dropdown-toggle");
		const menu = drop.querySelector(".dropdown-menu");

		if (toggle && menu) {
			toggle.addEventListener("mouseenter", () => {
				if (sidebar.classList.contains("minimized")) {
					const rect = toggle.getBoundingClientRect();
					menu.style.top = `${rect.top}px`;
					menu.style.left = `${rect.right}px`;
				} else {
					menu.style.top = "";
					menu.style.left = "";
				}
			});
		}
	});

	initAlertTable(); // run once on initial page load
	// initSidebarLinks(); // handle dynamic view loading

	// Initialize the default loaded module (user)
	if (typeof modules !== "undefined" && modules.user?.init) {
		modules.user.init();
	}
});
