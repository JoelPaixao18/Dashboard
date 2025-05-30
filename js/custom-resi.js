document.addEventListener('DOMContentLoaded', function () {
    // Função para carregar a lista de residências
    function carregarResidencias(pagina = 1, searchTerm = '') {
        const url = `../Controllers/list-resi.php?pagina=${pagina}${searchTerm ? '&search=' + encodeURIComponent(searchTerm) : ''}`;
        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.querySelector('.listar-residencias').innerHTML = data;
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipTriggerList.forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            })
            .catch(error => {
                document.querySelector('.listar-residencias').innerHTML = `<div class="alert alert-danger">Erro ao carregar residências: ${error.message}</div>`;
            });
    }

    // Função para visualizar residência
    window.visResidencia = function (id) {
        fetch(`../Models/visualizar-resi.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    document.getElementById('msgAlertaErroVis').innerHTML = `<div class="alert alert-danger">${data.msg}</div>`;
                    return;
                }
                const residencia = data.dados;
                document.getElementById('idResidencia').textContent = residencia.id;
                document.getElementById('typeResiResidencia').textContent = residencia.typeResi;
                document.getElementById('typologyResidencia').textContent = residencia.typology;
                document.getElementById('locationResidencia').textContent = residencia.location;
                document.getElementById('priceResidencia').textContent = parseFloat(residencia.price).toLocaleString('pt-AO', { style: 'currency', currency: 'AOA' });
                document.getElementById('statusResidencia').textContent = residencia.status;
                document.getElementById('statusResidencia').className = `badge ${residencia.status === 'Venda' ? 'bg-success' : residencia.status === 'Arrendamento' ? 'bg-primary' : 'bg-secondary'}`;
                document.getElementById('houseSizeResidencia').textContent = residencia.houseSize ? `${residencia.houseSize} m²` : 'N/A';
                document.getElementById('livingRoomCountResidencia').textContent = residencia.livingRoomCount || 'N/A';
                document.getElementById('bathroomCountResidencia').textContent = residencia.bathroomCount || 'N/A';
                document.getElementById('kitchenCountResidencia').textContent = residencia.kitchenCount || 'N/A';
                document.getElementById('quintalResidencia').textContent = residencia.quintal ? 'Sim' : 'Não';
                document.getElementById('andaresResidencia').textContent = residencia.andares || 'N/A';
                document.getElementById('garagemResidencia').textContent = residencia.garagem ? 'Sim' : 'Não';
                document.getElementById('hasWaterResidencia').textContent = residencia.hasWater ? 'Sim' : 'Não';
                document.getElementById('hasElectricityResidencia').textContent = residencia.hasElectricity ? 'Sim' : 'Não';
                document.getElementById('descriptionResidencia').textContent = residencia.description || 'N/A';
                const imagesContainer = document.getElementById('vis-images-container');
                imagesContainer.innerHTML = '';
                if (residencia.images && residencia.images.length > 0) {
                    residencia.images.forEach(img => {
                        imagesContainer.innerHTML += `
                            <div class="col-md-4 mb-2">
                                <img src="/RESINGOLA-main/${img}" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;" alt="Imagem do imóvel">
                            </div>
                        `;
                    });
                } else {
                    imagesContainer.innerHTML = '<div class="col-12 text-muted">Nenhuma imagem disponível</div>';
                }
                const modal = new bootstrap.Modal(document.getElementById('visResidenciaModal'));
                modal.show();
            })
            .catch(error => {
                document.getElementById('msgAlertaErroVis').innerHTML = `<div class="alert alert-danger">Erro ao visualizar residência: ${error.message}</div>`;
            });
    };

    // Função para abrir o modal de edição
    window.editResidenciaDados = function (id) {
        fetch(`../Models/visualizar-resi.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    document.getElementById('msgAlertaErroEdit').innerHTML = `<div class="alert alert-danger">${data.msg}</div>`;
                    return;
                }
                const residencia = data.dados;
                document.getElementById('editid').value = residencia.id;
                document.getElementById('edittypeResi').value = residencia.typeResi;
                document.getElementById('editlocation').value = residencia.location;
                document.getElementById('edithouseSize').value = residencia.houseSize;
                document.getElementById('editprice').value = residencia.price;
                document.getElementById('editstatus').value = residencia.status;
                document.getElementById('editquintal').checked = residencia.quintal == 1;
                document.getElementById('editgaragem').checked = residencia.garagem == 1;
                document.getElementById('edithasWater').checked = residencia.hasWater == 1;
                document.getElementById('edithasElectricity').checked = residencia.hasElectricity == 1;
                const typologyOptions = document.getElementById('edit-typology-options');
                typologyOptions.innerHTML = `
                    <input type="radio" class="btn-check" name="typology" id="edittypologyT1" value="T1" ${residencia.typology === 'T1' ? 'checked' : ''}>
                    <label class="btn btn-outline-primary" for="edittypologyT1">T1</label>
                    <input type="radio" class="btn-check" name="typology" id="edittypologyT2" value="T2" ${residencia.typology === 'T2' ? 'checked' : ''}>
                    <label class="btn btn-outline-primary" for="edittypologyT2">T2</label>
                    <input type="radio" class="btn-check" name="typology" id="edittypologyT3" value="T3" ${residencia.typology === 'T3' ? 'checked' : ''}>
                    <label class="btn btn-outline-primary" for="edittypologyT3">T3</label>
                    <input type="radio" class="btn-check" name="typology" id="edittypologyT4" value="T4" ${residencia.typology === 'T4' ? 'checked' : ''}>
                    <label class="btn btn-outline-primary" for="edittypologyT4">T4</label>
                    <input type="radio" class="btn-check" name="typology" id="edittypologyT5" value="T5" ${residencia.typology === 'T5' ? 'checked' : ''}>
                    <label class="btn btn-outline-primary" for="edittypologyT5">T5</label>
                    <input type="radio" class="btn-check" name="typology" id="edittypologyT6" value="T6" ${residencia.typology === 'T6' ? 'checked' : ''}>
                    <label class="btn btn-outline-primary" for="edittypologyT6">T6</label>
                `;
                const featuresContainer = document.getElementById('edit-features-container');
                featuresContainer.innerHTML = `
                    <div class="col-md-4 mb-3">
                        <label for="editlivingRoomCount" class="form-label">Salas de Estar</label>
                        <input type="number" name="livingRoomCount" id="editlivingRoomCount" class="form-control" value="${residencia.livingRoomCount || ''}" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="editbathroomCount" class="form-label">Banheiros</label>
                        <input type="number" name="bathroomCount" id="editbathroomCount" class="form-control" value="${residencia.bathroomCount || ''}" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="editkitchenCount" class="form-label">Cozinhas</label>
                        <input type="number" name="kitchenCount" id="editkitchenCount" class="form-control" value="${residencia.kitchenCount || ''}" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="editandares" class="form-label">Número de Andares</label>
                        <input type="number" name="andares" id="editandares" class="form-control" value="${residencia.andares || ''}" min="0">
                    </div>
                `;
                const imagesContainer = document.getElementById('edit-images-container');
                imagesContainer.innerHTML = '';
                if (residencia.images && residencia.images.length > 0) {
                    residencia.images.forEach(img => {
                        imagesContainer.innerHTML += `
                            <div class="col-md-4 mb-2">
                                <img src="/RESINGOLA-main/${img}" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;" alt="Imagem do imóvel">
                                <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removerImagem(this, '${img}')">Remover</button>
                            </div>
                        `;
                    });
                } else {
                    imagesContainer.innerHTML = '<div class="col-12 text-muted">Nenhuma imagem disponível</div>';
                }
                const modal = new bootstrap.Modal(document.getElementById('editResidenciaModal'));
                modal.show();
            })
            .catch(error => {
                document.getElementById('msgAlertaErroEdit').innerHTML = `<div class="alert alert-danger">Erro ao carregar dados para edição: ${error.message}</div>`;
            });
    };

    // Função para confirmar exclusão
    window.apagarResidenciaDados = function (id) {
        if (confirm('Tem certeza que deseja excluir esta residência?')) {
            fetch(`../Models/apagar-resi.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('msgAlerta').innerHTML = data.msg;
                    carregarResidencias();
                })
                .catch(error => {
                    document.getElementById('msgAlerta').innerHTML = `<div class="alert alert-danger">Erro ao excluir residência: ${error.message}</div>`;
                });
        }
    };

    // Manipular o formulário de edição
    document.getElementById('edit-residencia-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('../Models/editar-resi.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('msgAlertaErroEdit').innerHTML = data.msg;
                if (!data.erro) {
                    carregarResidencias();
                    bootstrap.Modal.getInstance(document.getElementById('editResidenciaModal')).hide();
                }
            })
            .catch(error => {
                document.getElementById('msgAlertaErroEdit').innerHTML = `<div class="alert alert-danger">Erro ao editar residência: ${error.message}</div>`;
            });
    });

    // Função para remover imagem (frontend apenas)
    window.removerImagem = function (button, imagem) {
        if (confirm('Deseja remover esta imagem?')) {
            button.parentElement.remove();
        }
    };

    // Função para listar residências com paginação
    window.listarResidencias = function (pagina) {
        const searchTerm = document.getElementById('searchInput').value;
        carregarResidencias(pagina, searchTerm);
    };

    // Função de busca
    window.filterUsers = function () {
        const searchTerm = document.getElementById('searchInput').value;
        carregarResidencias(1, searchTerm);
    };

    // *** Funcionalidades do Modal de Cadastro ***
    const cadTypeResi = document.getElementById('cad-typeResi');
    const cadTypologyOptions = document.getElementById('cad-typology-options');
    const cadLivingRoomCountOptions = document.getElementById('cad-livingRoomCount-options');
    const cadBathroomCountOptions = document.getElementById('cad-bathroomCount-options');
    const cadKitchenCountOptions = document.getElementById('cad-kitchenCount-options');
    const cadAndaresOptions = document.getElementById('cad-andares-options');
    const cadImagesInput = document.getElementById('cad-images');
    const cadImagesPreview = document.getElementById('cad-images-preview');
    const cadLocationInput = document.getElementById('cad-location');
    const cadLocationSuggestions = document.getElementById('cad-location-suggestions');
    const cadForm = document.getElementById('cadastro-residencia-form');
    const cadUserIdInput = document.getElementById('cad-user_id');

    if (!cadUserIdInput.value) {
        document.getElementById('msgAlertaErroCad').classList.remove('d-none');
        document.getElementById('msgAlertaErroCad').innerHTML = '<div class="alert alert-danger">Erro: Usuário não autenticado. Faça login novamente.</div>';
    }

    // Função para validar o formulário
    function isFormValid() {
        const errors = [];
        if (!cadImagesInput.files.length) errors.push('Adicione pelo menos uma imagem.');
        const houseSize = parseFloat(document.getElementById('cad-houseSize').value);
        if (isNaN(houseSize) || houseSize <= 0) errors.push('Informe um tamanho válido para a casa (em m²).');
        if (!document.getElementById('cad-status').value) errors.push('Selecione se o imóvel é para Venda ou Arrendamento.');
        const typeResi = cadTypeResi.value;
        if (!typeResi) errors.push('Selecione o tipo de imóvel.');
        const typology = document.querySelector('input[name="cad-typology"]:checked');
        if (!typology) errors.push(`Selecione a tipologia do ${typeResi}.`);
        const livingRoomCount = document.querySelector('input[name="cad-livingRoomCount"]:checked');
        if (!livingRoomCount) errors.push('Selecione o número de salas.');
        const bathroomCount = document.querySelector('input[name="cad-bathroomCount"]:checked');
        if (!bathroomCount) errors.push('Selecione o número de banheiros.');
        const kitchenCount = document.querySelector('input[name="cad-kitchenCount"]:checked');
        if (!kitchenCount) errors.push('Selecione o número de cozinhas.');
        if (typeResi === 'Vivenda' || typeResi === 'Moradia') {
            const andares = document.querySelector('input[name="cad-andares"]:checked');
            if (!andares) errors.push('Selecione o número de andares.');
        }
        const location = cadLocationInput.value;
        const latitude = document.getElementById('cad-latitude').value;
        const longitude = document.getElementById('cad-longitude').value;
        if (!location || !latitude || !longitude) errors.push('Selecione uma localização válida das sugestões.');
        const price = parseFloat(document.getElementById('cad-price').value);
        if (isNaN(price) || price <= 0) errors.push('Informe um preço válido.');
        const description = document.getElementById('cad-description').value;
        if (description.length > 500) errors.push('A descrição não pode exceder 500 caracteres.');
        if (!cadUserIdInput.value) errors.push('Você precisa estar logado para cadastrar um imóvel.');
        if (errors.length) {
            document.getElementById('msgAlertaErroCad').classList.remove('d-none');
            document.getElementById('msgAlertaErroCad').innerHTML = errors.map(err => `<p>${err}</p>`).join('');
            return false;
        }
        return true;
    }

    // Atualizar campos dinâmicos com base no tipo de imóvel
    function updateFormFields() {
        const typeResi = cadTypeResi.value;
        cadTypologyOptions.innerHTML = '';
        cadLivingRoomCountOptions.innerHTML = '';
        cadBathroomCountOptions.innerHTML = '';
        cadKitchenCountOptions.innerHTML = '';
        cadAndaresOptions.innerHTML = '';
        if (typeResi === 'Apartamento') {
            cadTypologyOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT2" value="T2">
                <label class="btn btn-outline-primary" for="cad-typologyT2">T2</label>
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT3" value="T3">
                <label class="btn btn-outline-primary" for="cad-typologyT3">T3</label>
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT4" value="T4">
                <label class="btn btn-outline-primary" for="cad-typologyT4">T4</label>
            `;
            cadLivingRoomCountOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-livingRoomCount" id="cad-livingRoomCount1" value="1">
                <label class="btn btn-outline-primary" for="cad-livingRoomCount1">1</label>
                <input type="radio" class="btn-check" name="cad-livingRoomCount" id="cad-livingRoomCount2" value="2">
                <label class="btn btn-outline-primary" for="cad-livingRoomCount2">2</label>
            `;
            cadBathroomCountOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-bathroomCount" id="cad-bathroomCount1" value="1">
                <label class="btn btn-outline-primary" for="cad-bathroomCount1">1</label>
                <input type="radio" class="btn-check" name="cad-bathroomCount" id="cad-bathroomCount2" value="2">
                <label class="btn btn-outline-primary" for="cad-bathroomCount2">2</label>
            `;
            cadKitchenCountOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-kitchenCount" id="cad-kitchenCount1" value="1">
                <label class="btn btn-outline-primary" for="cad-kitchenCount1">1</label>
            `;
            document.getElementById('cad-andares-container').style.display = 'none';
        } else if (typeResi === 'Vivenda' || typeResi === 'Moradia') {
            cadTypologyOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT1" value="T1">
                <label class="btn btn-outline-primary" for="cad-typologyT1">T1</label>
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT2" value="T2">
                <label class="btn btn-outline-primary" for="cad-typologyT2">T2</label>
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT3" value="T3">
                <label class="btn btn-outline-primary" for="cad-typologyT3">T3</label>
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT4" value="T4">
                <label class="btn btn-outline-primary" for="cad-typologyT4">T4</label>
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT5" value="T5">
                <label class="btn btn-outline-primary" for="cad-typologyT5">T5</label>
                <input type="radio" class="btn-check" name="cad-typology" id="cad-typologyT6" value="T6">
                <label class="btn btn-outline-primary" for="cad-typologyT6">T6</label>
            `;
            cadLivingRoomCountOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-livingRoomCount" id="cad-livingRoomCount1" value="1">
                <label class="btn btn-outline-primary" for="cad-livingRoomCount1">1</label>
                <input type="radio" class="btn-check" name="cad-livingRoomCount" id="cad-livingRoomCount2" value="2">
                <label class="btn btn-outline-primary" for="cad-livingRoomCount2">2</label>
                <input type="radio" class="btn-check" name="cad-livingRoomCount" id="cad-livingRoomCount3" value="3">
                <label class="btn btn-outline-primary" for="cad-livingRoomCount3">3</label>
            `;
            cadBathroomCountOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-bathroomCount" id="cad-bathroomCount1" value="1">
                <label class="btn btn-outline-primary" for="cad-bathroomCount1">1</label>
                <input type="radio" class="btn-check" name="cad-bathroomCount" id="cad-bathroomCount2" value="2">
                <label class="btn btn-outline-primary" for="cad-bathroomCount2">2</label>
                <input type="radio" class="btn-check" name="cad-bathroomCount" id="cad-bathroomCount3" value="3">
                <label class="btn btn-outline-primary" for="cad-bathroomCount3">3</label>
                <input type="radio" class="btn-check" name="cad-bathroomCount" id="cad-bathroomCount4" value="4">
                <label class="btn btn-outline-primary" for="cad-bathroomCount4">4</label>
            `;
            cadKitchenCountOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-kitchenCount" id="cad-kitchenCount1" value="1">
                <label class="btn btn-outline-primary" for="cad-kitchenCount1">1</label>
                <input type="radio" class="btn-check" name="cad-kitchenCount" id="cad-kitchenCount2" value="2">
                <label class="btn btn-outline-primary" for="cad-kitchenCount2">2</label>
            `;
            cadAndaresOptions.innerHTML = `
                <input type="radio" class="btn-check" name="cad-andares" id="cad-andares0" value="0">
                <label class="btn btn-outline-primary" for="cad-andares0">Nenhum</label>
                <input type="radio" class="btn-check" name="cad-andares" id="cad-andares1" value="1">
                <label class="btn btn-outline-primary" for="cad-andares1">1</label>
                <input type="radio" class="btn-check" name="cad-andares" id="cad-andares2" value="2">
                <label class="btn btn-outline-primary" for="cad-andares2">2</label>
                <input type="radio" class="btn-check" name="cad-andares" id="cad-andares3" value="3">
                <label class="btn btn-outline-primary" for="cad-andares3">3+</label>
            `;
            document.getElementById('cad-andares-container').style.display = 'block';
        }
    }

    // Inicializar campos do formulário
    cadTypeResi.addEventListener('change', updateFormFields);
    updateFormFields();

    // Visualização prévia de imagens
    cadImagesInput.addEventListener('change', function () {
        cadImagesPreview.innerHTML = '';
        const files = Array.from(this.files);
        if (files.length > 5) {
            alert('Selecione no máximo 5 imagens.');
            this.value = '';
            return;
        }
        files.forEach((file, index) => {
            if (!file.type.match('image.*')) {
                alert('Apenas arquivos de imagem são permitidos.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Imagem excede 5MB.');
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                const imgDiv = document.createElement('div');
                imgDiv.className = 'col-md-4 mb-2';
                imgDiv.innerHTML = `
                    <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;" alt="Prévia">
                    <button type="button" class="btn btn-danger btn-sm mt-1" onclick="this.parentElement.remove()">Remover</button>
                `;
                cadImagesPreview.appendChild(imgDiv);
            };
            reader.readAsDataURL(file);
        });
    });

    // Busca de localização com Yandex Geocode
    cadLocationInput.addEventListener('input', function () {
        const query = this.value;
        if (query.length < 3) {
            cadLocationSuggestions.innerHTML = '';
            return;
        }
        fetch(`https://geocode-maps.yandex.ru/1.x/?apikey=45f8077e-cd8f-4919-be26-31ce1a691183&format=json&geocode=${encodeURIComponent(query + ', Angola')}&lang=pt`)
            .then(response => response.json())
            .then(data => {
                const features = data?.response?.GeoObjectCollection?.featureMember || [];
                const results = features.map(feature => {
                    const components = feature.GeoObject.metaDataProperty?.GeocoderMetaData?.Address?.Components || [];
                    const city = components.find(c => c.kind === 'locality')?.name || '';
                    const district = components.find(c => c.kind === 'district')?.name || '';
                    const province = components.find(c => c.kind === 'province')?.name || '';
                    let displayName = city && district ? `${city}, ${district}` : district && province ? `${province}, ${district}` : feature.GeoObject.name || feature.GeoObject.description || '';
                    displayName = displayName.replace(/(Distrito|Município|Província)\s(do|da)\s/g, '').replace('Province of ', '');
                    const [lng, lat] = feature.GeoObject.Point.pos.split(' ').map(Number);
                    return { displayName, coordinates: { lat, lng } };
                }).filter(item => item.displayName).filter((item, index, self) => index === self.findIndex(i => i.displayName === item.displayName)).slice(0, 8);
                cadLocationSuggestions.innerHTML = results.map(item => `
                    <a href="#" class="list-group-item list-group-item-action" data-lat="${item.coordinates.lat}" data-lng="${item.coordinates.lng}">${item.displayName}</a>
                `).join('');
                cadLocationSuggestions.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        cadLocationInput.value = this.textContent;
                        document.getElementById('cad-latitude').value = this.dataset.lat;
                        document.getElementById('cad-longitude').value = this.dataset.lng;
                        cadLocationSuggestions.innerHTML = '';
                    });
                });
            })
            .catch(error => {
                cadLocationSuggestions.innerHTML = '<div class="list-group-item text-danger">Erro ao buscar localizações.</div>';
            });
    });

    // Manipular o formulário de cadastro
    cadForm.addEventListener('submit', function (e) {
        e.preventDefault();
        document.getElementById('msgAlertaErroCad').classList.add('d-none');
        if (!isFormValid()) return;
        const formData = new FormData(this);
        formData.set('typology', document.querySelector('input[name="cad-typology"]:checked').value);
        formData.set('livingRoomCount', document.querySelector('input[name="cad-livingRoomCount"]:checked').value);
        formData.set('bathroomCount', document.querySelector('input[name="cad-bathroomCount"]:checked').value);
        formData.set('kitchenCount', document.querySelector('input[name="cad-kitchenCount"]:checked').value);
        if (cadTypeResi.value === 'Vivenda' || cadTypeResi.value === 'Moradia') {
            formData.set('andares', document.querySelector('input[name="cad-andares"]:checked').value);
        }
        formData.set('quintal', document.getElementById('cad-quintal').checked ? 'true' : 'false');
        formData.set('garagem', document.getElementById('cad-garagem').checked ? 'true' : 'false');
        formData.set('varanda', document.getElementById('cad-varanda').checked ? 'true' : 'false');
        formData.set('hasWater', document.getElementById('cad-hasWater').checked ? 'true' : 'false');
        formData.set('hasElectricity', document.getElementById('cad-hasElectricity').checked ? 'true' : 'false');
        fetch('../../Backend/conect.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('msgAlertaErroCad').classList.remove('d-none');
                document.getElementById('msgAlertaErroCad').innerHTML = `<div class="alert ${data.status === 'success' ? 'alert-success' : 'alert-danger'}">${data.message}</div>`;
                if (data.status === 'success') {
                    carregarResidencias();
                    cadForm.reset();
                    cadImagesPreview.innerHTML = '';
                    updateFormFields();
                    bootstrap.Modal.getInstance(document.getElementById('cadastroResidenciaModal')).hide();
                }
            })
            .catch(error => {
                document.getElementById('msgAlertaErroCad').classList.remove('d-none');
                document.getElementById('msgAlertaErroCad').innerHTML = `<div class="alert alert-danger">Erro ao cadastrar residência: ${error.message}</div>`;
            });
    });

    // Carregar residências ao iniciar a página
    carregarResidencias();
});