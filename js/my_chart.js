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