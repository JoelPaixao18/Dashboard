// Seletores de elementos
const tbody = document.querySelector('.listar-residencias');
const cadForm = document.getElementById("cad-residencia-form");
const editForm = document.getElementById("edit-residencia-form");
const msgAlertaErroCad = document.getElementById("msgAlertaErroCad");
const msgAlertaErroEdit = document.getElementById("msgAlertaErroEdit");
const msgAlerta = document.getElementById("msgAlerta");
const cadModal = new bootstrap.Modal(document.getElementById("cadResidenciaModal"));
const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("searchInput");

// Variável global para armazenar o termo de pesquisa
let currentSearchTerm = '';

// Função principal para listar residencias
const listarResidencias = async (pagina) => {
    try {
        const url = `../Controllers/list-resi.php?pagina=${pagina}${currentSearchTerm ? `&search=${encodeURIComponent(currentSearchTerm)}` : ''}`;
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error('Erro ao carregar dados');
        }
        
        const resposta = await response.text();
        tbody.innerHTML = resposta;
        
        // Reaplica os event listeners
        reapplynEventListeners();
    } catch (error) {
        console.error('Erro:', error);
        msgAlerta.innerHTML = `<div class="alert alert-danger">Erro ao carregar dados: ${error.message}</div>`;
    }
}

// Função para reaplicar event listeners
function reapplynEventListeners() {
    // Botões de ação
    document.querySelectorAll('[onclick^="visResidencia"]').forEach(btn => {
        const id = btn.id;
        btn.onclick = (e) => {
            e.preventDefault();
            visResidencia(id);
        };
    });
    
    document.querySelectorAll('[onclick^="editResidenciaDados"]').forEach(btn => {
        const id = btn.id;
        btn.onclick = (e) => {
            e.preventDefault();
            editResidenciaDados(id);
        };
    });
    
    document.querySelectorAll('[onclick^="apagarResidenciaDados"]').forEach(btn => {
        const id = btn.id;
        btn.onclick = (e) => {
            e.preventDefault();
            apagarResidenciaDados(id);
        };
    });
    
    // Links de paginação
    document.querySelectorAll('.page-link').forEach(link => {
        if (link.getAttribute('onclick')) {
            const pageNum = link.getAttribute('onclick').match(/listarResidencias\((\d+)\)/)[1];
            link.onclick = null;
            link.addEventListener('click', (e) => {
                e.preventDefault();
                listarResidencias(pageNum);
            });
        }
    });
}

// Função de filtro
async function filterUsers() {
    currentSearchTerm = searchInput.value.trim();
    await listarResidencias(1);
}

// Event Listeners
searchForm.addEventListener('submit', filterUsers);

searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        filterUsers();
    }
});

// CRUD - Cadastrar 
document.getElementById('typeResi')?.addEventListener('change', function() {
    const type = this.value;
    const dynamicFields = document.getElementById('dynamic-fields');
    const featuresContainer = document.getElementById('features-container');
    
    // Limpar campos anteriores
    dynamicFields.innerHTML = '';
    featuresContainer.innerHTML = '';
    
    if (type === 'Apartamento') {
        dynamicFields.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Varanda</label>
                <div class="form-check form-switch">
                    <label class="form-check-label" for="varanda">Possui varanda!</label>
                    <input class="form-check-input" type="checkbox" name="varanda" id="varanda" value="1">
                </div>
            </div>
        `;
        
        featuresContainer.innerHTML = `
            <div class="col-md-4">
                <label class="form-label required">Salas de Estar</label>
                <select name="livingRoomCount" id="livingRoomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="0">Nenhuma</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label required">Banheiros</label>
                <select name="bathroomCount" id="bathroomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label required">Cozinhas</label>
                <select name="kitchenCount" id="kitchenCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                </select>
            </div>
        `;
    } 
    else if (type === 'Vivenda' || type === 'Moradia') {
        dynamicFields.innerHTML = `
            <div class="mb-3">
                <label class="form-label required">Quintal</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="quintal" id="quintalSim" value="Sim" required>
                    <label class="form-check-label" for="quintalSim">Sim</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="quintal" id="quintalNao" value="Não" required>
                    <label class="form-check-label" for="quintalNao">Não</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label required">Garagem</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="garagem" id="garagemSim" value="Sim" required>
                    <label class="form-check-label" for="garagemSim">Sim</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="garagem" id="garagemNao" value="Não" required>
                    <label class="form-check-label" for="garagemNao">Não</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label required">Água</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="hasWater" id="hasWaterSim" value="Sim" required>
                    <label class="form-check-label" for="hasWaterSim">Sim</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="hasWater" id="hasWaterNao" value="Não" required>
                    <label class="form-check-label" for="hasWaterNao">Não</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label required">Energia Elétrica</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="hasElectricity" id="hasElectricitySim" value="Sim" required>
                    <label class="form-check-label" for="hasElectricitySim">Sim</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="hasElectricity" id="hasElectricityNao" value="Não" required>
                    <label class="form-check-label" for="hasElectricityNao">Não</label>
                </div>
            </div>
        `;
        
        featuresContainer.innerHTML = `
            <div class="col-md-3">
                <label class="form-label required">Salas de Estar</label>
                <select name="livingRoomCount" id="livingRoomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Banheiros</label>
                <select name="bathroomCount" id="bathroomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Cozinhas</label>
                <select name="kitchenCount" id="kitchenCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="0">Nenhuma</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Andares</label>
                <select name="andares" id="andares" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="Nenhum">Nenhum</option>
                    <option value="1 - Andar">1 Andar</option>
                    <option value="2 - Andar(es)">2 Andares</option>
                    <option value="3 - Andar(es)">3 Andares</option>
                </select>
            </div>
        `;

        // Adicionar event listeners para os radio buttons
        const camposBinarios = ['quintal', 'garagem', 'hasWater', 'hasElectricity'];
        camposBinarios.forEach(campo => {
            document.querySelectorAll(`input[name="${campo}"]`).forEach(radio => {
                radio.addEventListener('change', function() {
                    // Garantir que o valor seja exatamente 'Sim' ou 'Não'
                    if (this.checked) {
                        this.value = this.value === 'Sim' ? 'Sim' : 'Não';
                    }
                });
            });
        });
    }
});

// Configurar autocomplete para localização
function setupLocationAutocomplete(inputId, suggestionsId) {
    const input = document.getElementById(inputId);
    const suggestions = document.getElementById(suggestionsId);
    
    let timeout;
    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value.trim();
        
        if (query.length < 3) {
            suggestions.classList.add('d-none');
            return;
        }
        
        timeout = setTimeout(async () => {
            try {
                const response = await fetch(`https://api.opencagedata.com/geocode/v1/json?q=${encodeURIComponent(query + ', Angola')}&key=45f8077e-cd8f-4919-be26-31ce1a691183`);
                const data = await response.json();
                
                suggestions.innerHTML = '';
                suggestions.classList.remove('d-none');
                
                if (data.results && data.results.length > 0) {
                    data.results.forEach(result => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = result.formatted;
                        item.addEventListener('click', function() {
                            input.value = result.formatted;
                            suggestions.classList.add('d-none');
                            
                            // Armazenar coordenadas se necessário
                            if (document.getElementById('latitude')) {
                                document.getElementById('latitude').value = result.geometry.lat;
                            }
                            if (document.getElementById('longitude')) {
                                document.getElementById('longitude').value = result.geometry.lng;
                            }
                        });
                        suggestions.appendChild(item);
                    });
                } else {
                    const item = document.createElement('div');
                    item.className = 'list-group-item';
                    item.textContent = 'Nenhum resultado encontrado';
                    suggestions.appendChild(item);
                }
            } catch (error) {
                console.error('Erro ao buscar localizações:', error);
            }
        }, 500);
    });
    
    // Esconder sugestões ao clicar fora
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target)) {
            suggestions.classList.add('d-none');
        }
    });
}

// Configurar autocomplete para o campo de localização
if (document.getElementById('location')) {
    setupLocationAutocomplete('location', 'location-suggestions');
}

// Upload de imagens
document.getElementById('add-images-btn')?.addEventListener('click', () => {
    document.getElementById('image-upload').click();
});

document.getElementById('image-upload')?.addEventListener('change', function(e) {
    const files = e.target.files;
    const container = document.getElementById('cad-images-container');
    container.innerHTML = '';
    
    // Limitar a 5 imagens
    const filesToShow = Array.from(files).slice(0, 5);
    
    if (filesToShow.length === 0) {
        container.innerHTML = '<div class="col-12 text-muted">Nenhuma imagem selecionada</div>';
        return;
    }
    
    filesToShow.forEach((file, index) => {
        if (!file.type.match('image.*')) return;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-3';
            col.innerHTML = `
                <div class="image-thumbnail position-relative">
                    <img src="${e.target.result}" class="img-thumbnail" alt="Pré-visualização ${index + 1}" style="height: 150px; margin-right="2rem"; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});

// CRUD - Cadastrar 
// Função para configurar autocomplete de localização
function setupLocationAutocomplete(inputId, suggestionsId) {
    const input = document.getElementById(inputId);
    const suggestions = document.getElementById(suggestionsId);
    
    let timeout;
    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value.trim();
        
        if (query.length < 3) {
            suggestions.classList.add('d-none');
            return;
        }
        
        timeout = setTimeout(async () => {
            try {
                const response = await fetch(`https://api.opencagedata.com/geocode/v1/json?q=${encodeURIComponent(query + ', Angola')}&key=45f8077e-cd8f-4919-be26-31ce1a691183`);
                const data = await response.json();
                
                suggestions.innerHTML = '';
                suggestions.classList.remove('d-none');
                
                if (data.results && data.results.length > 0) {
                    data.results.forEach(result => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = result.formatted;
                        item.addEventListener('click', function() {
                            input.value = result.formatted;
                            suggestions.classList.add('d-none');
                            
                            // Armazenar coordenadas se necessário
                            if (document.getElementById('latitude')) {
                                document.getElementById('latitude').value = result.geometry.lat;
                            }
                            if (document.getElementById('longitude')) {
                                document.getElementById('longitude').value = result.geometry.lng;
                            }
                        });
                        suggestions.appendChild(item);
                    });
                } else {
                    const item = document.createElement('div');
                    item.className = 'list-group-item';
                    item.textContent = 'Nenhum resultado encontrado';
                    suggestions.appendChild(item);
                }
            } catch (error) {
                console.error('Erro ao buscar localizações:', error);
            }
        }, 500);
    });
    
    // Esconder sugestões ao clicar fora
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target)) {
            suggestions.classList.add('d-none');
        }
    });
}

// Configurar autocomplete para o campo de localização
if (document.getElementById('location')) {
    setupLocationAutocomplete('location', 'location-suggestions');
}

// Upload de imagens
document.getElementById('add-images-btn')?.addEventListener('click', () => {
    document.getElementById('image-upload').click();
});

document.getElementById('image-upload')?.addEventListener('change', function(e) {
    const files = e.target.files;
    const container = document.getElementById('cad-images-container');
    container.innerHTML = '';
    
    // Limitar a 5 imagens
    const filesToShow = Array.from(files).slice(0, 5);
    
    if (filesToShow.length === 0) {
        container.innerHTML = '<div class="col-12 text-muted">Nenhuma imagem selecionada</div>';
        return;
    }
    
    filesToShow.forEach((file, index) => {
        if (!file.type.match('image.*')) return;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-3';
            col.innerHTML = `
                <div class="image-thumbnail position-relative">
                    <img src="${e.target.result}" class="img-thumbnail" alt="Pré-visualização ${index + 1}" style="height: 150px; margin-right="2rem"; object-fit: cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});

// Configurar campos dinâmicos baseados no tipo de imóvel
document.getElementById('typeResi')?.addEventListener('change', function() {
    const type = this.value;
    const dynamicFields = document.getElementById('dynamic-fields');
    const featuresContainer = document.getElementById('features-container');
    
    // Limpar campos anteriores
    dynamicFields.innerHTML = '';
    featuresContainer.innerHTML = '';
    
    if (type === 'Apartamento') {
        dynamicFields.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Varanda</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="varanda" id="varanda" value="1">
                    <label class="form-check-label" for="varanda">Possui varanda</label>
                </div>
            </div>
        `;
        
        featuresContainer.innerHTML = `
            <div class="col-md-4">
                <label class="form-label required">Salas de Estar</label>
                <select name="livingRoomCount" id="livingRoomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label required">Banheiros</label>
                <select name="bathroomCount" id="bathroomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label required">Cozinhas</label>
                <select name="kitchenCount" id="kitchenCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                </select>
            </div>
        `;
    } 
    else if (type === 'Vivenda' || type === 'Moradia') {
        featuresContainer.innerHTML = `
            <div class="col-md-3">
                <label class="form-label required">Salas de Estar</label>
                <select name="livingRoomCount" id="livingRoomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Banheiros</label>
                <select name="bathroomCount" id="bathroomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Cozinhas</label>
                <select name="kitchenCount" id="kitchenCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="0">Nenhuma</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Andares</label>
                <select name="andares" id="andares" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="0">Nenhum</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3+</option>
                </select>
            </div>
        `;
    }
});

// Submissão do formulário de cadastro
if (document.getElementById('cad-residencia-form')) {
    document.getElementById('cad-residencia-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const cadBtn = document.getElementById("cad-residencia-btn");
        if (cadBtn) cadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

        // Resetar mensagens de erro
        const msgAlertaErroCad = document.getElementById("msgAlertaErroCad");
        if (msgAlertaErroCad) msgAlertaErroCad.innerHTML = '';
        
        try {
            const dadosForm = new FormData(document.getElementById('cad-residencia-form'));
            const typeResi = dadosForm.get('typeResi');
            
            // Validações específicas para Vivenda
            if (typeResi === 'Vivenda') {
                // Verificar campos booleanos
                const camposBinarios = ['quintal', 'garagem', 'hasWater', 'hasElectricity'];
                camposBinarios.forEach(campo => {
                    const elementos = document.querySelectorAll(`input[name="${campo}"]`);
                    let valor = null;
                    elementos.forEach(el => {
                        if (el.checked) {
                            valor = el.value;
                        }
                    });
                    if (valor === null) {
                        throw new Error(`Por favor, selecione uma opção para ${campo}`);
                    }
                    if (valor !== 'Sim' && valor !== 'Não') {
                        throw new Error(`Valor inválido para ${campo}. Deve ser 'Sim' ou 'Não'`);
                    }
                    dadosForm.set(campo, valor);
                });

                // Validar campos obrigatórios
                const requiredFields = ['andares', 'livingRoomCount', 'bathroomCount', 'kitchenCount'];
                const errors = [];
                
                requiredFields.forEach(field => {
                    if (!dadosForm.get(field)) {
                        errors.push(`O campo ${field} é obrigatório`);
                    }
                });

                if (errors.length > 0) {
                    throw new Error(errors.join('<br>'));
                }
            }
            
            dadosForm.append('add', 1);
            
            // Adicionar todas as imagens selecionadas
            const images = document.getElementById("image-upload").files;
            for (let i = 0; i < images.length; i++) {
                dadosForm.append('images[]', images[i]);
            }
            
            const response = await fetch('../Controllers/cadastro-resi.php', {
                method: 'POST',
                body: dadosForm,
            });

            if (!response.ok) throw new Error('Erro na requisição');

            const resposta = await response.json();
            
            if (resposta.erro) {
                throw new Error(resposta.msg);
            } else {
                const msgAlerta = document.getElementById("msgAlerta");
                if (msgAlerta) {
                    msgAlerta.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong>Sucesso!</strong> ${resposta.msg}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                }
                document.getElementById('cad-residencia-form').reset();
                document.getElementById('cad-images-container').innerHTML = '<div class="col-12 text-muted">Nenhuma imagem selecionada</div>';
                bootstrap.Modal.getInstance(document.getElementById('cadResidenciaModal')).hide();
                listarResidencias(1);
            }
        } catch (error) {
            console.error('Erro ao cadastrar residência:', error);
            if (msgAlertaErroCad) {
                msgAlertaErroCad.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Erro!</strong> ${error.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
            }
        } finally {
            if (cadBtn) cadBtn.innerHTML = '<i class="fas fa-save me-1"></i> Cadastrar Imóvel';
        }
    });
}
// CRUD - Visualizar
async function visResidencia(id) {
    try {
        const response = await fetch(`../Models/visualizar-resi.php?id=${id}`);
        if (!response.ok) throw new Error('Erro na requisição');
        
        const resposta = await response.json();

        if (resposta.erro) {
            throw new Error(resposta.msg);
        }

        const visModal = new bootstrap.Modal(document.getElementById("visResidenciaModal"));
        visModal.show();

        // Carregar imagens
        const imagesContainer = document.getElementById('vis-images-container');
        imagesContainer.innerHTML = '';
        
        // Verificar se existem imagens
        if (resposta.dados.images && resposta.dados.images.length > 0) {
            // Criar um carrossel de imagens
            const carouselContainer = document.createElement('div');
            carouselContainer.id = 'propertyCarousel';
            carouselContainer.className = 'carousel slide';
            carouselContainer.setAttribute('data-bs-ride', 'carousel');

            // Adicionar estilo para ocupar todo o espaço
            carouselContainer.style.width = '100%';
            carouselContainer.style.height = '500px';

            // Indicadores do carrossel
            const indicators = document.createElement('div');
            indicators.className = 'carousel-indicators';

            // Inner do carrossel
            const carouselInner = document.createElement('div');
            carouselInner.className = 'carousel-inner h-100';

            resposta.dados.images.forEach((image, index) => {
                // Criar indicador
                const indicator = document.createElement('button');
                indicator.type = 'button';
                indicator.setAttribute('data-bs-target', '#propertyCarousel');
                indicator.setAttribute('data-bs-slide-to', index);
                indicator.className = index === 0 ? 'active' : '';
                indicator.setAttribute('aria-current', index === 0 ? 'true' : 'false');
                indicators.appendChild(indicator);
                
                // Criar item do carrossel
                const carouselItem = document.createElement('div');
                carouselItem.className = `carousel-item h-100 ${index === 0 ? 'active' : ''}`;
                
                // Criar imagem primeiro
                const img = document.createElement('img');
                img.className = 'd-block mx-auto h-100 w-auto';
                img.alt = `Imagem ${index + 1} do imóvel`;
                img.style.objectFit = 'contain';

                // Determinar o caminho da imagem
                let imagePath;
                if (image.startsWith('http')) {
                    imagePath = image;
                } else {
                    // Usar o caminho completo da imagem
                    imagePath = `/RESINGOLA-main/${image}`;
                }

                // Configurar manipulador de erro
                img.onerror = function() {
                    this.src = '/RESINGOLA-main/uploads/no-image.svg';
                };

                // Definir o src da imagem depois de configurar o handler de erro
                img.src = imagePath;
                
                carouselItem.appendChild(img);
                carouselInner.appendChild(carouselItem);
            });
            
            // Botões de controle
            const prevButton = document.createElement('button');
            prevButton.className = 'carousel-control-prev';
            prevButton.type = 'button';
            prevButton.setAttribute('data-bs-target', '#propertyCarousel');
            prevButton.setAttribute('data-bs-slide', 'prev');
            prevButton.innerHTML = `
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            `;
            
            const nextButton = document.createElement('button');
            nextButton.className = 'carousel-control-next';
            nextButton.type = 'button';
            nextButton.setAttribute('data-bs-target', '#propertyCarousel');
            nextButton.setAttribute('data-bs-slide', 'next');
            nextButton.innerHTML = `
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            `;
            
            // Montar o carrossel
            carouselContainer.appendChild(indicators);
            carouselContainer.appendChild(carouselInner);
            carouselContainer.appendChild(prevButton);
            carouselContainer.appendChild(nextButton);
            
            // Adicionar ao container
            imagesContainer.appendChild(carouselContainer);
            
            // Inicializar o carrossel
            new bootstrap.Carousel(carouselContainer);
        } else {
            // Caso não haja imagens
            imagesContainer.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="fas fa-image fa-2x mb-2"></i>
                    <p>Nenhuma imagem disponível para este imóvel</p>
                    <img src="/RESINGOLA-main/uploads/no-image.svg" class="img-fluid rounded" alt="Sem imagem" style="max-height: 300px;">
                </div>
            `;
        }

        // Formatadores auxiliares
        const formatCurrency = (value) => {
            return value ? 'Kz ' + parseFloat(value).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : 'N/A';
        };

        const formatArea = (value) => {
            return value ? parseFloat(value).toLocaleString('pt-BR', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            }) + ' m²' : 'N/A';
        };

        const formatBoolean = (value) => {
            // Verifica vários formatos possíveis de "verdadeiro"
            const isTrue = value === '1' || value === 1 || value === true || value === 'Sim' || value === 'sim';
            return isTrue ? 
                '<i class="fas fa-check-circle text-success"></i> Sim' : 
                '<i class="fas fa-times-circle text-danger"></i> Não';
        };

        // Preencher os dados básicos
        document.getElementById("idResidencia").textContent = resposta.dados.id || 'N/A';
        document.getElementById("typeResiResidencia").textContent = resposta.dados.typeResi || 'N/A';
        document.getElementById("typologyResidencia").textContent = resposta.dados.typology || 'N/A';
        document.getElementById("locationResidencia").textContent = resposta.dados.location || 'N/A';
        document.getElementById("priceResidencia").innerHTML = formatCurrency(resposta.dados.price);
        
        // Status com badge colorido
        const statusElement = document.getElementById("statusResidencia");
        statusElement.textContent = resposta.dados.status || 'N/A';
        statusElement.className = 'badge';
        if (resposta.dados.status === 'Venda') {
            statusElement.classList.add('bg-warning', 'text-dark');
        } else if (resposta.dados.status === 'Arrendamento') {
            statusElement.classList.add('bg-success');
        } else {
            statusElement.classList.add('bg-secondary');
        }

        // Preencher características
        document.getElementById("houseSizeResidencia").innerHTML = formatArea(resposta.dados.houseSize);
        document.getElementById("livingRoomCountResidencia").textContent = resposta.dados.livingRoomCount || 'N/A';
        document.getElementById("bathroomCountResidencia").textContent = resposta.dados.bathroomCount || 'N/A';
        document.getElementById("kitchenCountResidencia").textContent = resposta.dados.kitchenCount || 'N/A';
        document.getElementById("quintalResidencia").innerHTML = formatBoolean(resposta.dados.quintal);
        document.getElementById("andaresResidencia").textContent = resposta.dados.andares || 'N/A';

        // Infraestrutura
        const garagemElement = document.getElementById("garagemResidencia");
        const aguaElement = document.getElementById("hasWaterResidencia");
        const energiaElement = document.getElementById("hasElectricityResidencia");
        const quintalElement = document.getElementById("quintalResidencia");

        if (garagemElement) garagemElement.innerHTML = formatBoolean(resposta.dados.garagem);
        if (aguaElement) aguaElement.innerHTML = formatBoolean(resposta.dados.hasWater);
        if (energiaElement) energiaElement.innerHTML = formatBoolean(resposta.dados.hasElectricity);
        if (quintalElement) quintalElement.innerHTML = formatBoolean(resposta.dados.quintal);

        // Botão de impressão
        document.getElementById("printResidenciaBtn").onclick = () => {
            window.print();
        };
    } catch (error) {
        console.error('Erro ao visualizar residência:', error);
        if (msgAlerta) {
            msgAlerta.innerHTML = `<div class="alert alert-danger">Erro ao carregar dados: ${error.message}</div>`;
        }
    }
}

//imagem

async function uploadImage(file) {
    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch('../uploads/upload_image.php', {
            method: 'POST',
            body: formData
        });
        
        return await response.json();
    } catch (error) {
        console.error('Erro no upload:', error);
        return { status: 'error', message: error.message };
    }
}

// Função para editar residência
async function editResidenciaDados(id) {
    try {
        // Mostrar loader
        document.getElementById('edit-residencia-btn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...';
        
        const response = await fetch(`../Models/visualizar-resi.php?id=${id}`);
        if (!response.ok) throw new Error('Erro na requisição');
        
        const resposta = await response.json();

        if (resposta.erro) {
            throw new Error(resposta.msg);
        }

        const dados = resposta.dados;
        const editModal = new bootstrap.Modal(document.getElementById("editResidenciaModal"));
        
        // Preencher campos básicos
        document.getElementById("editid").value = dados.id;
        document.getElementById("edittypeResi").value = dados.typeResi || '';
        document.getElementById("editlocation").value = dados.location || '';
        document.getElementById("editprice").value = dados.price || '';
        document.getElementById("editstatus").value = dados.status || '';
        document.getElementById("edithouseSize").value = dados.houseSize || '';
        
        // Preencher imagens - CORREÇÃO DO ERRO
        const imagesContainer = document.getElementById('edit-images-container');
        imagesContainer.innerHTML = '';
        
        // Verificar se existem imagens e converter para array se necessário
        let imagens = [];
        if (dados.images) {
            // Se images é uma string (caminho único), converter para array
            if (typeof dados.images === 'string') {
                imagens = [dados.images];
            } 
            // Se já for array, usar diretamente
            else if (Array.isArray(dados.images)) {
                imagens = dados.images;
            }
        }
        
        // Exibir imagens
        if (imagens.length > 0) {
            imagens.forEach((image, index) => {
                // Verificar se a imagem é um caminho completo ou apenas o nome do arquivo
                const imagePath = image.startsWith('http') || image.startsWith('/') ? 
                    image : 
                    `../uploads/${image}`;
                
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3';
                col.innerHTML = `
                    <div class="image-thumbnail position-relative">
                        <img src="${imagePath}" class="img-thumbnail" alt="Imagem ${index + 1}" style="height: 150px; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="removeEditImage(this, '${image}')">
                            <i class="fas fa-times"></i>
                        </button>
                        <input type="hidden" name="existing_images[]" value="${image}">
                    </div>
                `;
                imagesContainer.appendChild(col);
            });
        } else {
            imagesContainer.innerHTML = '<div class="col-12 text-muted">Nenhuma imagem disponível</div>';
        }
        
        // Configurar tipologia (radio buttons)
        const typologyOptions = ['T1', 'T2', 'T3', 'T4', 'Outro'];
        const typologyContainer = document.getElementById('edit-typology-options');
        typologyContainer.innerHTML = typologyOptions.map(opt => `
            <input type="radio" class="btn-check" name="typology" id="edittypology${opt}" value="${opt}" ${dados.typology === opt ? 'checked' : ''}>
            <label class="btn btn-outline-primary" for="edittypology${opt}">${opt}</label>
        `).join('');
        
        // Configurar campos dinâmicos baseados no tipo de imóvel
        const dynamicFields = document.getElementById('edit-dynamic-fields');
        const featuresContainer = document.getElementById('edit-features-container');
        
        if (dados.typeResi === 'Apartamento') {
            dynamicFields.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Varanda</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="varanda" id="editvaranda" value="1" ${dados.varanda ? 'checked' : ''}>
                        <label class="form-check-label" for="editvaranda">Possui varanda</label>
                    </div>
                </div>
            `;
            
            featuresContainer.innerHTML = `
                <div class="col-md-4">
                    <label class="form-label required">Salas de Estar</label>
                    <select name="livingRoomCount" id="editlivingRoomCount" class="form-select" required>
                        <option value="1" ${dados.livingRoomCount == '1' ? 'selected' : ''}>1</option>
                        <option value="2" ${dados.livingRoomCount == '2' ? 'selected' : ''}>2</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Banheiros</label>
                    <select name="bathroomCount" id="editbathroomCount" class="form-select" required>
                        <option value="1" ${dados.bathroomCount == '1' ? 'selected' : ''}>1</option>
                        <option value="2" ${dados.bathroomCount == '2' ? 'selected' : ''}>2</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Cozinhas</label>
                    <select name="kitchenCount" id="editkitchenCount" class="form-select" required>
                        <option value="1" ${dados.kitchenCount == '1' ? 'selected' : ''}>1</option>
                    </select>
                </div>
            `;
        } 
        else if (dados.typeResi === 'Vivenda' || dados.typeResi === 'Moradia') {
            dynamicFields.innerHTML = '';
            
            featuresContainer.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label required">Salas de Estar</label>
                    <select name="livingRoomCount" id="editlivingRoomCount" class="form-select" required>
                        <option value="1" ${dados.livingRoomCount == '1' ? 'selected' : ''}>1</option>
                        <option value="2" ${dados.livingRoomCount == '2' ? 'selected' : ''}>2</option>
                        <option value="3" ${dados.livingRoomCount == '3' ? 'selected' : ''}>3</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Banheiros</label>
                    <select name="bathroomCount" id="editbathroomCount" class="form-select" required>
                        <option value="1" ${dados.bathroomCount == '1' ? 'selected' : ''}>1</option>
                        <option value="2" ${dados.bathroomCount == '2' ? 'selected' : ''}>2</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Cozinhas</label>
                    <select name="kitchenCount" id="editkitchenCount" class="form-select" required>
                        <option value="1" ${dados.kitchenCount == '1' ? 'selected' : ''}>1</option>
                        <option value="2" ${dados.kitchenCount == '2' ? 'selected' : ''}>2</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Andares</label>
                    <select name="andares" id="editandares" class="form-select" required>
                        <option value="0" ${dados.andares == '0' ? 'selected' : ''}>Nenhum</option>
                        <option value="1" ${dados.andares == '1' ? 'selected' : ''}>1</option>
                        <option value="2" ${dados.andares == '2' ? 'selected' : ''}>2</option>
                        <option value="3" ${dados.andares == '3' ? 'selected' : ''}>3+</option>
                    </select>
                </div>
            `;
        }
        
        const setCheckbox = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                // Verifica vários formatos possíveis de "verdadeiro"
                const isChecked = value === '1' || value === 1 || value === true || value === 'Sim' || value === 'sim';
                element.checked = isChecked;
                // Garante que o valor seja '1' ou '0' para o formulário
                element.value = isChecked ? '1' : '0';
            }
        };

        // Configura os checkboxes com tratamento robusto
        setCheckbox('editquintal', dados.quintal);
        setCheckbox('editgaragem', dados.garagem);
        setCheckbox('edithasWater', dados.hasWater);
        setCheckbox('edithasElectricity', dados.hasElectricity);

        // Adiciona event listeners para atualizar os valores quando os checkboxes mudam
        document.querySelectorAll('.form-check-input').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                this.value = this.checked ? '1' : '0';
            });
        });
        
        // Configurar evento para adicionar imagens
        document.getElementById('edit-add-images-btn').addEventListener('click', () => {
            document.getElementById('edit-image-upload').click();
        });
        
        document.getElementById('edit-image-upload').addEventListener('change', function(e) {
            handleImageUpload(e, 'edit-images-container');
        });
        
        // Configurar autocomplete para localização
        setupLocationAutocomplete('editlocation', 'edit-location-suggestions');
        
        // Mostrar modal
        editModal.show();
        
    } catch (error) {
        console.error('Erro ao editar residência:', error);
        if (msgAlerta) {
            msgAlerta.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Erro!</strong> ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    } finally {
        document.getElementById('edit-residencia-btn').innerHTML = '<i class="fas fa-save me-1"></i> Salvar Alterações';
    }
}

// Função auxiliar para remover imagem no modal de edição
function removeEditImage(button, imageName) {
    if (confirm('Tem certeza que deseja remover esta imagem?')) {
        const imageContainer = button.closest('.col-md-3');
        imageContainer.remove();
        
        // Aqui você pode adicionar lógica para remover a imagem do servidor se necessário
        // Exemplo: fazer uma requisição AJAX para remover a imagem
    }
}

// Função para lidar com upload de imagens
function handleImageUpload(event, containerId) {
    const files = event.target.files;
    const container = document.getElementById(containerId);
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!file.type.match('image.*')) continue;
        
        const reader = new FileReader();
        reader.onload = (function(file) {
            return function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3';
                col.innerHTML = `
                    <div class="image-thumbnail position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" alt="${file.name}">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                        <input type="hidden" name="new_images[]" value="${file.name}">
                    </div>
                `;
                container.appendChild(col);
            };
        })(file);
        reader.readAsDataURL(file);
    }
    
    // Resetar o input para permitir novos uploads
    event.target.value = '';
}

// Função para configurar autocomplete de localização
function setupLocationAutocomplete(inputId, suggestionsId) {
    const input = document.getElementById(inputId);
    const suggestions = document.getElementById(suggestionsId);
    
    let timeout;
    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value.trim();
        
        if (query.length < 3) {
            suggestions.classList.add('d-none');
            return;
        }
        
        timeout = setTimeout(async () => {
            try {
                const response = await fetch(`https://api.opencagedata.com/geocode/v1/json?q=${encodeURIComponent(query + ', Angola')}&key=45f8077e-cd8f-4919-be26-31ce1a691183`);
                const data = await response.json();
                
                suggestions.innerHTML = '';
                suggestions.classList.remove('d-none');
                
                if (data.results && data.results.length > 0) {
                    data.results.forEach(result => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = result.formatted;
                        item.addEventListener('click', function() {
                            input.value = result.formatted;
                            suggestions.classList.add('d-none');
                            
                            // Aqui você pode armazenar as coordenadas se necessário
                            document.getElementById('edit-latitude').value = result.geometry.lat;
                            document.getElementById('edit-longitude').value = result.geometry.lng;
                        });
                        suggestions.appendChild(item);
                    });
                } else {
                    const item = document.createElement('div');
                    item.className = 'list-group-item';
                    item.textContent = 'Nenhum resultado encontrado';
                    suggestions.appendChild(item);
                }
            } catch (error) {
                console.error('Erro ao buscar localizações:', error);
            }
        }, 500);
    });
    
    // Esconder sugestões ao clicar fora
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target)) {
            suggestions.classList.add('d-none');
        }
    });
}

// Event listener para o formulário de edição
if (document.getElementById('edit-residencia-form')) {
    document.getElementById('edit-residencia-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('edit-residencia-btn');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';
        submitBtn.disabled = true;
        
        try {
            const formData = new FormData(this);
            
            // Validação dos campos
            if (!formData.get('typeResi')) {
                throw new Error('Selecione o tipo de imóvel');
            }
            if (!formData.get('typology')) {
                throw new Error('Selecione a tipologia');
            }
            if (!formData.get('location')) {
                throw new Error('Informe a localização');
            }
            if (!formData.get('houseSize') || parseFloat(formData.get('houseSize')) <= 0) {
                throw new Error('Informe um tamanho válido para a casa');
            }
            if (!formData.get('status')) {
                throw new Error('Selecione o status');
            }
            if (!formData.get('price') || parseFloat(formData.get('price')) <= 0) {
                throw new Error('Informe um preço válido');
            }
            
            // Validações específicas por tipo de imóvel
            if (formData.get('typeResi') === 'Apartamento') {
                if (!formData.get('livingRoomCount')) throw new Error('Selecione o número de salas');
                if (!formData.get('bathroomCount')) throw new Error('Selecione o número de banheiros');
                if (!formData.get('kitchenCount')) throw new Error('Selecione o número de cozinhas');
            } else {
                if (!formData.get('livingRoomCount')) throw new Error('Selecione o número de salas');
                if (!formData.get('bathroomCount')) throw new Error('Selecione o número de banheiros');
                if (!formData.get('kitchenCount')) throw new Error('Selecione o número de cozinhas');
                if (!formData.get('andares')) throw new Error('Selecione o número de andares');
            }
            
            const response = await fetch('../Models/editar-resi.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) throw new Error('Erro na requisição');
            
            const result = await response.json();
            
            if (result.erro) {
                throw new Error(result.msg);
            }
            
            // Sucesso
            if (msgAlerta) {
                msgAlerta.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>Sucesso!</strong> ${result.msg}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
            }
            
            // Fechar modal e atualizar lista
            bootstrap.Modal.getInstance(document.getElementById('editResidenciaModal')).hide();
            listarResidencias(1);
            
        } catch (error) {
            console.error('Erro ao salvar edição:', error);
            if (msgAlertaErroEdit) {
                msgAlertaErroEdit.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Erro!</strong> ${error.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
            }
        } finally {
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Alterações';
            submitBtn.disabled = false;
        }
    });
}

// CRUD - Apagar
async function apagarResidenciaDados(id) {
    console.log('Apagando residência ID:', id);
    try {
        const confirmar = confirm("Tem certeza que deseja excluir o registro selecionado?");
        if (!confirmar) return;

        const response = await fetch('../Models/apagar-resi.php?id=' + id);
        if (!response.ok) throw new Error('Erro na requisição');

        const resposta = await response.json();
        console.log('Resposta da exclusão:', resposta);
        
        if (resposta.erro) {
            if (msgAlerta) msgAlerta.innerHTML = resposta.msg;
        } else {
            if (msgAlerta) msgAlerta.innerHTML = resposta.msg;
            await listarResidencias(1);
        }
    } catch (error) {
        console.error('Erro ao apagar residência:', error);
        if (msgAlerta) {
            msgAlerta.innerHTML = `<div class="alert alert-danger">Erro ao apagar: ${error.message}</div>`;
        }
    }
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM carregado, iniciando listagem...');
    listarResidencias(1);
});