document.addEventListener('DOMContentLoaded', function () {
    // Gráfico de Pizza (Status das Residências: Venda e Arrendamento)
    if (document.getElementById('myChart')) {
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Venda', 'Arrendamento'],
                datasets: [{
                    data: [
                        window.residenciaStatus.venda || 0,
                        window.residenciaStatus.arrendamento || 0
                    ],
                    backgroundColor: [
                        '#4e73df', // Azul para Venda
                        '#1cc88a'  // Verde para Arrendamento
                    ],
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribuição de Imóveis: Venda vs Arrendamento',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        // Atualiza os valores da legenda personalizada
        document.getElementById('venda-count').textContent = window.residenciaStatus.venda || 0;
        document.getElementById('arrendamento-count').textContent = window.residenciaStatus.arrendamento || 0;
    }
    

    // Gráfico Doughnut (Usuários vs Residências)
    if (document.getElementById('earning')) {
        const earning = document.getElementById("earning").getContext("2d");
        new Chart(earning, {
            type: "doughnut",
            data: {
                labels: ["Usuários", "Residências"],
                datasets: [{
                    data: [window.totalUsuarios || 0, window.totalResidencias || 0],
                    backgroundColor: ["#36b9cc", "#f6c23e"],
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Usuários vs Residências',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
    }
});
