document.addEventListener('DOMContentLoaded', function() {
	// SIDEBAR DROPDOWN
	const allDropdown = document.querySelectorAll('#sidebar .side-dropdown');
	const sidebar = document.getElementById('sidebar');

	if (allDropdown) {
		allDropdown.forEach(item=> {
			const a = item.parentElement.querySelector('a:first-child');
			if (a) {
				a.addEventListener('click', function (e) {
					e.preventDefault();

					if (allDropdown) {
						allDropdown.forEach(i => {
							if (i !== item) {
								i.classList.remove('show');
							}
						});
					}

					this.classList.toggle('active');
					item.classList.toggle('show');
				})
			}
		})
	}

	// SIDEBAR COLLAPSE
	const toggleSidebar = document.querySelector('nav .toggle-sidebar');
	const allSideDivider = document.querySelectorAll('#sidebar .divider');

	if (sidebar && sidebar.classList.contains('hide')) {
		if (allSideDivider) {
			allSideDivider.forEach(item=> {
				item.textContent = '-'
			})
		}
		if (allDropdown) {
			allDropdown.forEach(item=> {
				const a = item.parentElement.querySelector('a:first-child');
				if (a) {
					a.classList.remove('active');
					item.classList.remove('show');
				}
			})
		}
	} else if (allSideDivider) {
		allSideDivider.forEach(item=> {
			item.textContent = item.dataset.text;
		})
	}

	if (toggleSidebar && sidebar) {
		toggleSidebar.addEventListener('click', function () {
			sidebar.classList.toggle('hide');

			if(sidebar.classList.contains('hide')) {
				if (allSideDivider) {
					allSideDivider.forEach(item=> {
						item.textContent = '-'
					})
				}

				if (allDropdown) {
					allDropdown.forEach(item=> {
						const a = item.parentElement.querySelector('a:first-child');
						if (a) {
							a.classList.remove('active');
							item.classList.remove('show');
						}
					})
				}
			} else if (allSideDivider) {
				allSideDivider.forEach(item=> {
					item.textContent = item.dataset.text;
				})
			}
		})
	}

	if (sidebar) {
		sidebar.addEventListener('mouseleave', function () {
			if(this.classList.contains('hide')) {
				if (allDropdown) {
					allDropdown.forEach(item=> {
						const a = item.parentElement.querySelector('a:first-child');
						if (a) {
							a.classList.remove('active');
							item.classList.remove('show');
						}
					})
				}
				if (allSideDivider) {
					allSideDivider.forEach(item=> {
						item.textContent = '-'
					})
				}
			}
		})

		sidebar.addEventListener('mouseenter', function () {
			if(this.classList.contains('hide')) {
				if (allDropdown) {
					allDropdown.forEach(item=> {
						const a = item.parentElement.querySelector('a:first-child');
						if (a) {
							a.classList.remove('active');
							item.classList.remove('show');
						}
					})
				}
				if (allSideDivider) {
					allSideDivider.forEach(item=> {
						item.textContent = item.dataset.text;
					})
				}
			}
		})
	}

	// PROFILE DROPDOWN
	const profile = document.querySelector('nav .profile');
	const profileTrigger = profile ? (profile.querySelector('img') || profile.querySelector('.profile-initials')) : null;
	const dropdownProfile = profile ? profile.querySelector('.profile-link') : null;

	if (profileTrigger && dropdownProfile) {
		profileTrigger.addEventListener('click', function () {
			dropdownProfile.classList.toggle('show');
		})
	}

	// MENU
	const allMenu = document.querySelectorAll('main .content-data .head .menu');

	if (allMenu) {
		allMenu.forEach(item=> {
			const icon = item.querySelector('.icon');
			const menuLink = item.querySelector('.menu-link');

			if (icon && menuLink) {
				icon.addEventListener('click', function () {
					menuLink.classList.toggle('show');
				})
			}
		})
	}

	window.addEventListener('click', function (e) {
		if (profileTrigger && dropdownProfile) {
			if(e.target !== profileTrigger) {
				if(e.target !== dropdownProfile) {
					if(dropdownProfile.classList.contains('show')) {
						dropdownProfile.classList.remove('show');
					}
				}
			}
		}

		if (allMenu) {
			allMenu.forEach(item=> {
				const icon = item.querySelector('.icon');
				const menuLink = item.querySelector('.menu-link');

				if (icon && menuLink) {
					if(e.target !== icon) {
						if(e.target !== menuLink) {
							if (menuLink.classList.contains('show')) {
								menuLink.classList.remove('show')
							}
						}
					}
				}
			})
		}
	})

	// PROGRESSBAR
	const allProgress = document.querySelectorAll('main .card .progress');

	if (allProgress) {
		allProgress.forEach(item=> {
			item.style.setProperty('--value', item.dataset.value)
		});
	}

	// APEXCHART
	var options = {
		series: [{
			name: 'Arrendamento',
			data: [30, 45, 35, 60, 50, 120, 110, 90, 80, 70, 60, 50] // Dados mensais
		}, {
			name: 'Venda',
			data: [20, 35, 50, 40, 55, 70, 85, 95, 100, 90, 80, 75] // Dados mensais
		}],
		chart: {
			height: 350,
			type: 'area'
		},
		dataLabels: {
			enabled: false
		},
		stroke: {
			curve: 'smooth'
		},
		xaxis: {
			type: 'category',
			categories: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"] // Meses do ano
		},
		tooltip: {
			x: {
				format: 'MMM'
			},
		},
	};

	// Initialize ApexCharts if the container exists
	const chartElement = document.querySelector("#chart");
	if (chartElement) {
		var chart = new ApexCharts(chartElement, options);
		chart.render();
	}
});