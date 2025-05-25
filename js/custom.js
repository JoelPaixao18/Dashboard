// Elementos DOM
const tbody = document.querySelector('.listar-usuarios');
const cadForm = document.getElementById("cad-usuario-form");
const editForm = document.getElementById("edit-usuario-form");
const msgAlertaErroCad = document.getElementById("msgAlertaErroCad");
const msgAlertaErroEdit = document.getElementById("msgAlertaErroEdit");
const msgAlerta = document.getElementById("msgAlerta");
const cadModal = new bootstrap.Modal(document.getElementById("cadUsuarioModal"));
const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("searchInput");

// Variável global para armazenar o termo de pesquisa
let currentSearchTerm = '';

// Função principal para listar usuários
const listarUsuarios = async (pagina) => {
    try {
        const url = `../Controllers/list.php?pagina=${pagina}${currentSearchTerm ? `&search=${encodeURIComponent(currentSearchTerm)}` : ''}`;
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
    document.querySelectorAll('[onclick^="visUsuario"]').forEach(btn => {
        const id = btn.id;
        btn.onclick = (e) => {
            e.preventDefault();
            visUsuario(id);
        };
    });
    
    document.querySelectorAll('[onclick^="editUsuarioDados"]').forEach(btn => {
        const id = btn.id;
        btn.onclick = (e) => {
            e.preventDefault();
            editUsuarioDados(id);
        };
    });
    
    document.querySelectorAll('[onclick^="apagarUsuarioDados"]').forEach(btn => {
        const id = btn.id;
        btn.onclick = (e) => {
            e.preventDefault();
            apagarUsuarioDados(id);
        };
    });
    
    // Links de paginação
    document.querySelectorAll('.page-link').forEach(link => {
        if (link.getAttribute('onclick')) {
            const pageNum = link.getAttribute('onclick').match(/listarUsuarios\((\d+)\)/)[1];
            link.onclick = null;
            link.addEventListener('click', (e) => {
                e.preventDefault();
                listarUsuarios(pageNum);
            });
        }
    });
}

// Função de filtro
async function filterUsers() {
    currentSearchTerm = searchInput.value.trim();
    await listarUsuarios(1);
}

// Event Listeners
searchForm.addEventListener('submit', filterUsers);

searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        filterUsers();
    }
});

//CRUD --- Cadastrar Usuarios

cadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById("cad-usuario-btn").value = "Salvando...";

    if(document.getElementById("nome").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Nome!</div>";
    } else if(document.getElementById("email").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo E-mail!</div>";
    } else if(document.getElementById("senha").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Senha!</div>";
    } else if(!validarTelefone(document.getElementById("tel").value)){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Telefone inválido!</div>";
    } else if(!validarBI(document.getElementById("bi").value)) {
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Número de BI inválido!</div>";
    } else {

        const dadosForm = new FormData(cadForm);
        dadosForm.append('add', 1);
        
            try {
                const dados = await fetch('../Controllers/cadastrar.php', {
                    method: 'POST',
                    body: dadosForm,
                });
                
                if (!dados.ok) {
                    throw new Error(`HTTP error! status: ${dados.status}`);
                }
                
                const resposta = await dados.json();
                console.log(resposta); // Verifique no console do navegador
                
                if(resposta['erro']){
                    msgAlertaErroCad.innerHTML = resposta['msg'];
                }else{
                    msgAlerta.innerHTML = resposta['msg'];
                    cadForm.reset();
                    cadModal.hide();
                    listarUsuarios(1);
                }
            } catch (error) {
                console.error('Erro no cadastro:', error);
                msgAlertaErroCad.innerHTML = `<div class='alert alert-danger'>Erro na requisição: ${error.message}</div>`;
            }
    }

    document.getElementById("cad-usuario-btn").value = "Cadastrar";
});

// ------------------ CRUD - Visualizar (Usuário) ------------------

// Função de visualização
/*async function visUsuario(id) {
    const dados = await fetch('../Models/visualizar.php?id=' + id);
    const resposta = await dados.json();

    if(resposta['erro']) {
        msgAlerta.innerHTML = resposta['erro'];
    } else {
        const visModal = new bootstrap.Modal(document.getElementById("visUsuarioModal"));
        visModal.show();

        document.getElementById("idUsuario").innerHTML = resposta['dados'].id;
        document.getElementById("nomeUsuario").innerHTML = resposta['dados'].nome;
        document.getElementById("emailUsuario").innerHTML = resposta['dados'].email;
        document.getElementById("telUsuario").innerHTML = resposta['dados'].tel || 'Não informado';
        document.getElementById("biUsuario").innerHTML = resposta['dados'].BI || 'Não informado';
        document.getElementById("roleUsuario").innerHTML = resposta['dados'].role;
    }
}*/

// ------------------ CRUD - Editar (Usuário) ------------------

// Função de edição
async function editUsuarioDados(id) {
    msgAlertaErroEdit.innerHTML = "";

    const dados = await fetch('../Models/visualizar.php?id=' + id);
    const resposta = await dados.json();

    if (resposta['erro']) {
        msgAlerta.innerHTML = resposta['msg'];
    } else {
        const editModal = new bootstrap.Modal(document.getElementById("editUsuarioModal"));
        editModal.show();
        document.getElementById("editid").value = resposta['dados'].id;
        document.getElementById("editnome").value = resposta['dados'].nome;
        document.getElementById("editemail").value = resposta['dados'].email;
        document.getElementById("edittel").value = resposta['dados'].tel || '';
        document.getElementById("editbi").value = resposta['dados'].BI || '';
    }
}

editForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById("edit-usuario-btn").value = "Salvando...";

    const dadosForm = new FormData(editForm);
    console.log(dadosForm);
    /*for (var dadosFormEdit  of dadosForm.entries()) {
        console.log(dadosFormEdit[0] + ' - ' + dadosFormEdit[1]);
    }*/

    const dados = await fetch('../Models/editar.php', {
        method: 'POST',
        body: dadosForm,
    });

    const resposta = await dados.json();
    //console.log(resposta);

    if (resposta['erro']) {
        msgAlertaErroEdit.innerHTML = resposta['msg'];
    }
    else {
        msgAlertaErroEdit.innerHTML = resposta['msg'];
        listarUsuarios(1);
    }

    document.getElementById("edit-usuario-btn").value = "Salvar";
});

// --------------- CRUD - Deleter (Usuário) ---------------------

async function apagarUsuarioDados(id) {

    var confirmar = confirm("Tem certeza que deseja excluir o registro selectionado?");

    if (confirmar == true){
        
        const dados = await fetch('../Models/apagar.php?id=' + id);

        const resposta = await dados.json();
        if (resposta['erro']){
            msgAlerta.innerHTML = resposta['msg'];
        } else {
            msgAlerta.innerHTML = resposta['msg'];
        listarUsuarios(1);
        }
    }
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    listarUsuarios(1);
});