// Elementos DOM
const tbody = document.querySelector('.listar-geral');
const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("searchInput");
const msgAlerta = document.getElementById("msgAlerta");

// Variável global para armazenar o termo de pesquisa
let currentSearchTerm = '';

// Função principal para listar dados gerais
const listarGeral = async (pagina) => {
    try {
        tbody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando dados...</p></div>';
        
        const url = `../Controllers/geral-list.php?pagina=${pagina}${currentSearchTerm ? `&search=${encodeURIComponent(currentSearchTerm)}` : ''}`;
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error('Erro ao carregar dados');
        }
        
        const resposta = await response.text();
        
        if (resposta.includes('Nenhum dado encontrado')) {
            msgAlerta.innerHTML = '<div class="alert alert-info">Nenhum resultado encontrado para sua pesquisa.</div>';
        } else {
            msgAlerta.innerHTML = '';
        }
        
        tbody.innerHTML = resposta;
        
        // Inicializar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                boundary: document.body
            });
        });
        
    } catch (error) {
        console.error('Erro:', error);
        msgAlerta.innerHTML = `<div class="alert alert-danger alert-dismissible fade show">
            <strong>Erro!</strong> ${error.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
    }
}

function viewOnMap(latitude, longitude) {
    if (latitude && longitude) {
        // Armazenar as coordenadas no localStorage
        localStorage.setItem('selectedLocation', JSON.stringify({
            lat: parseFloat(latitude),
            lng: parseFloat(longitude)
        }));
        
        // Abrir o mapa em uma nova aba
        window.open('../Views/map/map.php?focus=true', '_blank');
    } else {
        alert('Localização não disponível para este imóvel');
    }
}

// Função para reaplicar event listeners
function reapplynEventListeners() {
    // Links de paginação
    document.querySelectorAll('.page-link').forEach(link => {
        if (link.getAttribute('onclick')) {
            const pageNum = link.getAttribute('onclick').match(/listarGeral\((\d+)\)/)[1];
            link.onclick = null;
            link.addEventListener('click', (e) => {
                e.preventDefault();
                listarGeral(pageNum);
            });
        }
    });
}

// Função de filtro
async function filterGeral() {
    currentSearchTerm = searchInput.value.trim();
    await listarGeral(1);
}

// Event Listeners
searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    filterGeral();
});

searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        filterGeral();
    }
});

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    listarGeral(1);
});