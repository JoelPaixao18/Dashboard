// SIDEBAR DROPDOWN
const allDropdown = document.querySelectorAll('#sidebar .side-dropdown');
const sidebar = document.getElementById('sidebar');

allDropdown.forEach(item=> {
	const a = item.parentElement.querySelector('a:first-child');
	a.addEventListener('click', function (e) {
		e.preventDefault();

		if(!this.classList.contains('active')) {
			allDropdown.forEach(i=> {
				const aLink = i.parentElement.querySelector('a:first-child');

				aLink.classList.remove('active');
				i.classList.remove('show');
			})
		}

		this.classList.toggle('active');
		item.classList.toggle('show');
	})
})





// SIDEBAR COLLAPSE
const toggleSidebar = document.querySelector('nav .toggle-sidebar');
const allSideDivider = document.querySelectorAll('#sidebar .divider');

if(sidebar.classList.contains('hide')) {
	allSideDivider.forEach(item=> {
		item.textContent = '-'
	})
	allDropdown.forEach(item=> {
		const a = item.parentElement.querySelector('a:first-child');
		a.classList.remove('active');
		item.classList.remove('show');
	})
} else {
	allSideDivider.forEach(item=> {
		item.textContent = item.dataset.text;
	})
}

toggleSidebar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');

	if(sidebar.classList.contains('hide')) {
		allSideDivider.forEach(item=> {
			item.textContent = '-'
		})

		allDropdown.forEach(item=> {
			const a = item.parentElement.querySelector('a:first-child');
			a.classList.remove('active');
			item.classList.remove('show');
		})
	} else {
		allSideDivider.forEach(item=> {
			item.textContent = item.dataset.text;
		})
	}
})




sidebar.addEventListener('mouseleave', function () {
	if(this.classList.contains('hide')) {
		allDropdown.forEach(item=> {
			const a = item.parentElement.querySelector('a:first-child');
			a.classList.remove('active');
			item.classList.remove('show');
		})
		allSideDivider.forEach(item=> {
			item.textContent = '-'
		})
	}
})



sidebar.addEventListener('mouseenter', function () {
	if(this.classList.contains('hide')) {
		allDropdown.forEach(item=> {
			const a = item.parentElement.querySelector('a:first-child');
			a.classList.remove('active');
			item.classList.remove('show');
		})
		allSideDivider.forEach(item=> {
			item.textContent = item.dataset.text;
		})
	}
})




// PROFILE DROPDOWN
const profile = document.querySelector('nav .profile');
const imgProfile = profile.querySelector('img');
const dropdownProfile = profile.querySelector('.profile-link');

imgProfile.addEventListener('click', function () {
	dropdownProfile.classList.toggle('show');
})




// MENU
const allMenu = document.querySelectorAll('main .content-data .head .menu');

allMenu.forEach(item=> {
	const icon = item.querySelector('.icon');
	const menuLink = item.querySelector('.menu-link');

	icon.addEventListener('click', function () {
		menuLink.classList.toggle('show');
	})
})



window.addEventListener('click', function (e) {
	if(e.target !== imgProfile) {
		if(e.target !== dropdownProfile) {
			if(dropdownProfile.classList.contains('show')) {
				dropdownProfile.classList.remove('show');
			}
		}
	}

	allMenu.forEach(item=> {
		const icon = item.querySelector('.icon');
		const menuLink = item.querySelector('.menu-link');

		if(e.target !== icon) {
			if(e.target !== menuLink) {
				if (menuLink.classList.contains('show')) {
					menuLink.classList.remove('show')
				}
			}
		}
	})
})





// PROGRESSBAR
const allProgress = document.querySelectorAll('main .card .progress');

allProgress.forEach(item=> {
	item.style.setProperty('--value', item.dataset.value)
});

// APEXCHART
var options = {
  series: [{
    name: 'Aluguer',
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

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();


var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();

var ctx = document.getElementById('myChart').getContext('2d');
var earning = document.getElementById('earning').getContext('2d');
var myChart = new Chart(ctx, {

    type: 'polarArea',
    data: {
      labels: [ 'Residência em Rendas', 'Residência Pendentes', 'Residencias Vendidas'],
      datasets: [{
        label: '# of Votes',
        data: [1200, 1900, 3000],
        borderWidth: [
            'rgba (0, 0, 255)',
            'rgba (255, 240, 32)',
            'rgba (0, 128, 0)'
        ],
        
      }]
    },
    options: {
        response: true,
    }
  });

var myChart = new Chart(earning, {
 type: 'bar',
 data: {
   labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 
    'September', 'October', 'November', 'December'],
   datasets: [{
    label: 'Earning',
     data: [1230, 1209, 3000, 5324, 2200, 3456, 1999, 2309, 1200, 3000, 5324, 2200],
         borderWidth: [
         'rgba (255, 99, 132, 1)',
         'rgba (54, 162, 235, 1)',
         'rgba (255, 206, 86, 1)',
         'rgba (75, 192, 192, 1)',
         'rgba (153, 102, 255, 1)',
         'rgba (255, 159, 64, 1)',
         'rgba (255, 99, 132, 1)',
         'rgba (54, 162, 235, 1)',
         'rgba (255, 206, 86, 1)',
         'rgba (75, 192, 192, 1)',
         'rgba (153, 102, 255, 1)',
         'rgba (255, 159, 64, 1)'
        ],
        
      }]
 },
 options: {
     response: true,
 }
});