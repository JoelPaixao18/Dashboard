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
if (cadForm) {
    cadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const cadBtn = document.getElementById("cad-residencia-btn");
        if (cadBtn) cadBtn.value = "Salvando...";

        // Resetar mensagens de erro
        if (msgAlertaErroCad) msgAlertaErroCad.innerHTML = '';
        
        // Validações básicas
        const typeResi = document.getElementById("typeResi").value;
        const typology = document.querySelector('input[name="typology"]:checked')?.value;
        const location = document.getElementById("location").value;
        const price = document.getElementById("price").value;
        const status = document.getElementById("status").value;

        let isValid = true;
        const errors = [];

        if (!typeResi) errors.push("Necessário selecionar o Tipo de Imóvel");
        if (!typology) errors.push("Necessário selecionar a Tipologia do Imóvel");
        if (!location) errors.push("Necessário informar a Localização");
        if (!price || isNaN(price) || parseFloat(price) <= 0) errors.push("Preço inválido (deve ser maior que 0)");
        if (!status) errors.push("Necessário definir o Estado da Residência");

        // Validações específicas para Vivenda
        if (typeResi === 'Vivenda') {
            const houseSize = parseFloat(document.getElementById("houseSize")?.value);
            if (!houseSize || houseSize < 10) errors.push("Área inválida (mínimo 10m²)");

            const livingRoomCount = document.querySelector('input[name="livingRoomCount"]:checked')?.value;
            const bathroomCount = document.querySelector('input[name="bathroomCount"]:checked')?.value;
            const kitchenCount = document.querySelector('input[name="kitchenCount"]:checked')?.value;
            const quintal = document.querySelector('input[name="quintal"]:checked')?.value;
            const andares = document.querySelector('input[name="andares"]:checked')?.value;
            const garagem = document.querySelector('input[name="garagem"]:checked')?.value;
            const hasWater = document.querySelector('input[name="hasWater"]:checked')?.value;
            const hasElectricity = document.querySelector('input[name="hasElectricity"]:checked')?.value;

            if (!livingRoomCount) errors.push("Selecione a quantidade de salas");
            if (!bathroomCount) errors.push("Selecione a quantidade de banheiros");
            if (!kitchenCount) errors.push("Selecione a quantidade de cozinhas");
            if (!quintal) errors.push("Informe se tem quintal/jardim");
            if (!andares) errors.push("Selecione o número de andares");
            if (!garagem) errors.push("Informe se tem garagem");
            if (!hasWater) errors.push("Informe se tem água");
            if (!hasElectricity) errors.push("Informe se tem energia elétrica");
        }

        if (errors.length > 0) {
            if (msgAlertaErroCad) {
                msgAlertaErroCad.innerHTML = `<div class="alert alert-danger">${errors.join('<br>')}</div>`;
            }
            if (cadBtn) cadBtn.value = "Cadastrar";
            return;
        }

        try {
            const dadosForm = new FormData(cadForm);
            dadosForm.append('add', 1);
            
            const response = await fetch('../Controllers/cadastro-resi.php', {
                method: 'POST',
                body: dadosForm,
            });

            if (!response.ok) throw new Error('Erro na requisição');

            const resposta = await response.json();
            
            if (resposta.erro) {
                if (msgAlertaErroCad) msgAlertaErroCad.innerHTML = resposta.msg;
            } else {
                if (msgAlerta) msgAlerta.innerHTML = resposta.msg;
                cadForm.reset();
                cadModal.hide();
                await listarResidencias(1);
            }
        } catch (error) {
            console.error('Erro ao cadastrar residência:', error);
            if (msgAlertaErroCad) {
                msgAlertaErroCad.innerHTML = `<div class="alert alert-danger">Erro ao cadastrar: ${error.message}</div>`;
            }
        }

        if (cadBtn) cadBtn.value = "Cadastrar";
    });
}

// CRUD - Visualizar
async function visResidencia(id) {
    console.log('Visualizando residência ID:', id);
    try {
        const response = await fetch('../Models/visualizar-resi.php?id=' + id);
        if (!response.ok) throw new Error('Erro na requisição');
        
        const resposta = await response.json();
        console.log('Dados da residência:', resposta);

        if (resposta.erro) {
            if (msgAlerta) msgAlerta.innerHTML = resposta.erro;
        } else {
            const visModal = new bootstrap.Modal(document.getElementById("visResidenciaModal"));
            visModal.show();

            // Preencher os dados básicos
            document.getElementById("idResidencia").innerHTML = resposta.dados.id;
            document.getElementById("typeResiResidencia").innerHTML = resposta.dados.typeResi || 'N/A';
            document.getElementById("typologyResidencia").innerHTML = resposta.dados.typology || 'N/A';
            document.getElementById("locationResidencia").innerHTML = resposta.dados.location || 'N/A';
            document.getElementById("priceResidencia").innerHTML = resposta.dados.price ? 
                parseFloat(resposta.dados.price).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'}) : 'N/A';
            document.getElementById("statusResidencia").innerHTML = resposta.dados.status || 'N/A';

            // Preencher os campos específicos da Vivenda
            document.getElementById("houseSizeResidencia").innerHTML = resposta.dados.houseSize ? 
                parseFloat(resposta.dados.houseSize).toLocaleString('pt-BR', {minimumFractionDigits: 1, maximumFractionDigits: 1}) + ' m²' : 'N/A';
            document.getElementById("livingRoomCountResidencia").innerHTML = resposta.dados.livingRoomCount || 'N/A';
            document.getElementById("bathroomCountResidencia").innerHTML = resposta.dados.bathroomCount || 'N/A';
            document.getElementById("kitchenCountResidencia").innerHTML = resposta.dados.kitchenCount || 'N/A';
            document.getElementById("quintalResidencia").innerHTML = resposta.dados.quintal || 'N/A';
            document.getElementById("andaresResidencia").innerHTML = resposta.dados.andares || 'N/A';
            document.getElementById("garagemResidencia").innerHTML = resposta.dados.garagem || 'N/A';
            document.getElementById("hasWaterResidencia").innerHTML = resposta.dados.hasWater || 'N/A';
            document.getElementById("hasElectricityResidencia").innerHTML = resposta.dados.hasElectricity || 'N/A';
        }
    } catch (error) {
        console.error('Erro ao visualizar residência:', error);
        if (msgAlerta) {
            msgAlerta.innerHTML = `<div class="alert alert-danger">Erro ao carregar dados: ${error.message}</div>`;
        }
    }
}

// CRUD - Editar
async function editResidenciaDados(id) {
    console.log('Editando residência ID:', id);
    if (msgAlertaErroEdit) msgAlertaErroEdit.innerHTML = "";

    try {
        const response = await fetch('../Models/visualizar-resi.php?id=' + id);
        if (!response.ok) throw new Error('Erro na requisição');
        
        const resposta = await response.json();
        console.log('Dados para edição:', resposta);

        if (resposta.erro) {
            if (msgAlerta) msgAlerta.innerHTML = resposta.msg;
        } else {
            const editModal = new bootstrap.Modal(document.getElementById("editResidenciaModal"));
            editModal.show();
            
            // Preencher os campos básicos
            document.getElementById("editid").value = resposta.dados.id;
            document.getElementById("edittypeResi").value = resposta.dados.typeResi || '';
            document.getElementById("edittypology").value = resposta.dados.typology || '';
            document.getElementById("editlocation").value = resposta.dados.location || '';
            document.getElementById("editprice").value = resposta.dados.price || '';
            document.getElementById("editstatus").value = resposta.dados.status || '';

            // Preencher os campos específicos da Vivenda
            if (document.getElementById("edithouseSize")) {
                document.getElementById("edithouseSize").value = resposta.dados.houseSize || '';
            }
            setRadioValue('editlivingRoomCount', resposta.dados.livingRoomCount);
            setRadioValue('editbathroomCount', resposta.dados.bathroomCount);
            setRadioValue('editkitchenCount', resposta.dados.kitchenCount);
            setRadioValue('editquintal', resposta.dados.quintal);
            setRadioValue('editandares', resposta.dados.andares);
            setRadioValue('editgaragem', resposta.dados.garagem);
            setRadioValue('edithasWater', resposta.dados.hasWater);
            setRadioValue('edithasElectricity', resposta.dados.hasElectricity);
        }
    } catch (error) {
        console.error('Erro ao editar residência:', error);
        if (msgAlerta) {
            msgAlerta.innerHTML = `<div class="alert alert-danger">Erro ao carregar dados: ${error.message}</div>`;
        }
    }
}

// Função auxiliar para definir valor de radio buttons
function setRadioValue(name, value) {
    const radios = document.querySelectorAll(`input[name="${name}"]`);
    radios.forEach(radio => {
        radio.checked = (radio.value === value);
    });
}

// Event listener para formulário de edição
if (editForm) {
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const editBtn = document.getElementById("edit-residencia-btn");
        if (editBtn) editBtn.value = "Salvando...";

        try {
            const dadosForm = new FormData(editForm);
            
            // Validações básicas
            const typeResi = dadosForm.get('edittypeResi');
            const typology = dadosForm.get('edittypology');
            const location = dadosForm.get('editlocation');
            const price = dadosForm.get('editprice');
            const status = dadosForm.get('editstatus');

            let isValid = true;
            const errors = [];

            if (!typeResi) errors.push("Necessário selecionar o Tipo de Imóvel");
            if (!typology) errors.push("Necessário selecionar a Tipologia do Imóvel");
            if (!location) errors.push("Necessário informar a Localização");
            if (!price || isNaN(price) || parseFloat(price) <= 0) errors.push("Preço inválido (deve ser maior que 0)");
            if (!status) errors.push("Necessário definir o Estado da Residência");

            // Validações específicas para Vivenda
            if (typeResi === 'Vivenda') {
                const houseSize = parseFloat(dadosForm.get('edithouseSize'));
                if (!houseSize || houseSize < 10) errors.push("Área inválida (mínimo 10m²)");

                const livingRoomCount = dadosForm.get('editlivingRoomCount');
                const bathroomCount = dadosForm.get('editbathroomCount');
                const kitchenCount = dadosForm.get('editkitchenCount');
                const quintal = dadosForm.get('editquintal');
                const andares = dadosForm.get('editandares');
                const garagem = dadosForm.get('editgaragem');
                const hasWater = dadosForm.get('edithasWater');
                const hasElectricity = dadosForm.get('edithasElectricity');

                if (!livingRoomCount) errors.push("Selecione a quantidade de salas");
                if (!bathroomCount) errors.push("Selecione a quantidade de banheiros");
                if (!kitchenCount) errors.push("Selecione a quantidade de cozinhas");
                if (!quintal) errors.push("Informe se tem quintal/jardim");
                if (!andares) errors.push("Selecione o número de andares");
                if (!garagem) errors.push("Informe se tem garagem");
                if (!hasWater) errors.push("Informe se tem água");
                if (!hasElectricity) errors.push("Informe se tem energia elétrica");
            }

            if (errors.length > 0) {
                if (msgAlertaErroEdit) {
                    msgAlertaErroEdit.innerHTML = `<div class="alert alert-danger">${errors.join('<br>')}</div>`;
                }
                if (editBtn) editBtn.value = "Editar";
                return;
            }

            const response = await fetch('../Models/editar-resi.php', {
                method: 'POST',
                body: dadosForm,
            });

            if (!response.ok) throw new Error('Erro na requisição');

            const resposta = await response.json();
            console.log('Resposta da edição:', resposta);

            if (resposta.erro) {
                if (msgAlertaErroEdit) msgAlertaErroEdit.innerHTML = resposta.msg;
            } else {
                if (msgAlerta) msgAlerta.innerHTML = resposta.msg;
                await listarResidencias(1);
                bootstrap.Modal.getInstance(document.getElementById("editResidenciaModal")).hide();
            }
        } catch (error) {
            console.error('Erro ao editar residência:', error);
            if (msgAlertaErroEdit) {
                msgAlertaErroEdit.innerHTML = `<div class="alert alert-danger">Erro ao editar: ${error.message}</div>`;
            }
        }

        if (editBtn) editBtn.value = "Editar";
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