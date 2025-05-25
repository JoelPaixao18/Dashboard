// Elementos DOM
const tbody = document.querySelector('.listar-admin');
const cadForm = document.getElementById("cad-admin-form");
const editForm = document.getElementById("edit-admin-form");
const msgAlertaErroCad = document.getElementById("msgAlertaErroCad");
const msgAlertaErroEdit = document.getElementById("msgAlertaErroEdit");
const msgAlerta = document.getElementById("msgAlerta");
const cadModal = new bootstrap.Modal(document.getElementById("cadAdminModal"));
const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("searchInput");

// Variável global para armazenar o termo de pesquisa
let currentSearchTerm = '';

// Função para inicializar as máscaras
function initializeMasks() {
    if ($.fn.inputmask) {
        $("#edittel").inputmask({
            mask: '+244 999 999 999',
            placeholder: '+244 ___ ___ ___',
            showMaskOnHover: false,
            showMaskOnFocus: true,
            clearMaskOnLostFocus: false
        });
        
        $("#editbi").inputmask({
            mask: '999999999AA999',
            placeholder: '__________ ___',
            definitions: {
                'A': {
                    validator: '[A-Za-z]',
                    casing: 'upper'
                }
            }
        });
    } else {
        console.error('Inputmask plugin não está carregado');
    }
}

// Função principal para listar administradores
const listarAdmin = async (pagina) => {
    try {
        const url = `../Controllers/list-admin.php?pagina=${pagina}${currentSearchTerm ? `&search=${encodeURIComponent(currentSearchTerm)}` : ''}`;
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
    document.querySelectorAll('[onclick^="editAdminDados"]').forEach(btn => {
        const id = btn.id;
        btn.onclick = (e) => {
            e.preventDefault();
            editAdminDados(id);
        };
    });
    
    document.querySelectorAll('[onclick^="apagarAdminDados"]').forEach(btn => {
        const id = btn.id;
        btn.onclick = (e) => {
            e.preventDefault();
            apagarAdminDados(id);
        };
    });
    
    // Links de paginação
    document.querySelectorAll('.page-link').forEach(link => {
        if (link.getAttribute('onclick')) {
            const pageNum = link.getAttribute('onclick').match(/listarAdmin\((\d+)\)/)[1];
            link.onclick = null;
            link.addEventListener('click', (e) => {
                e.preventDefault();
                listarAdmin(pageNum);
            });
        }
    });
}

// Função de filtro
async function filterAdmins() {
    currentSearchTerm = searchInput.value.trim();
    await listarAdmin(1);
}

// Event Listeners
searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    filterAdmins();
});

searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        filterAdmins();
    }
});

//CRUD --- Cadastrar Administradores

/*cadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById("cad-admin-btn").value = "Salvando...";

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
        dadosForm.append('role', 'Admin'); // Forçar role como Admin
        
            const dados = await fetch('../Controllers/cadastrar.php', {
                method: 'POST',
                body: dadosForm,
            });

            const resposta = await dados.json();
            if(resposta['erro']){
                msgAlertaErroCad.innerHTML = resposta['msg'];
            }else{
                msgAlerta.innerHTML = resposta['msg'];
                cadForm.reset();
                cadModal.hide();
                listarAdmin(1);
            }
    }

    document.getElementById("cad-admin-btn").value = "Cadastrar";
});*/

// ------------------ CRUD - Editar (Administrador) ------------------

// Função de edição aprimorada
async function editAdminDados(id) {
    try {
        msgAlertaErroEdit.innerHTML = "";
        
        // Primeiro, verificar se o modal existe
        const editModal = document.getElementById("editAdminModal");
        if (!editModal) {
            throw new Error('Modal de edição não encontrado');
        }

        // Encontrar o botão de edição
        const editBtn = document.getElementById("edit-admin-btn");
        if (!editBtn) {
            throw new Error('Botão de edição não encontrado');
        }

        // Salvar o texto original do botão
        const originalBtnText = editBtn.innerHTML;
        
        try {
            // Mostrar loader no botão
            editBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Carregando...';
            editBtn.disabled = true;

            const response = await fetch('../Models/visualizar-admin.php?id=' + id);
            
            if (!response.ok) {
                throw new Error('Erro ao carregar dados do administrador');
            }
            
            const resposta = await response.json();

            if (resposta['erro']) {
                msgAlerta.innerHTML = resposta['msg'];
            } else {
                const bootstrapModal = new bootstrap.Modal(editModal);
                
                // Preencher formulário
                document.getElementById("editid").value = resposta['dados'].id;
                document.getElementById("editnome").value = resposta['dados'].nome;
                document.getElementById("editemail").value = resposta['dados'].email;
                document.getElementById("edittel").value = resposta['dados'].tel || '';
                document.getElementById("editbi").value = resposta['dados'].BI || '';
                
                // Adicionar máscaras
                if ($.fn.inputmask) {
                    $('#edittel').inputmask({
                        mask: '+244 999 999 999',
                        placeholder: '+244 ___ ___ ___',
                        showMaskOnHover: false,
                        showMaskOnFocus: true
                    });
                    
                    $('#editbi').inputmask({
                        mask: '999999999AA999',
                        placeholder: '__________ ___',
                        definitions: {
                            'A': {
                                validator: '[A-Za-z]',
                                casing: 'upper'
                            }
                        }
                    });
                }
                
                bootstrapModal.show();
                
                // Resetar validação do formulário
                const form = document.getElementById('edit-admin-form');
                if (form) {
                    form.classList.remove('was-validated');
                }
            }
        } finally {
            // Sempre restaurar o botão ao estado original
            editBtn.innerHTML = originalBtnText;
            editBtn.disabled = false;
        }
    } catch (error) {
        console.error('Erro:', error);
        msgAlertaErroEdit.innerHTML = `<div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${error.message}
        </div>`;
    }
}

// Validação do formulário de edição
editForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const form = e.currentTarget;
    const editBtn = document.getElementById("edit-admin-btn");
    
    // Verificar validação do formulário
    if (!form.checkValidity()) {
        e.stopPropagation();
        form.classList.add('was-validated');
        return;
    }
    
    // Salvar o texto original do botão
    const btnText = editBtn.innerHTML;
    
    try {
        // Mudar o texto do botão para loading
        editBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';
        editBtn.disabled = true;
        
        const dadosForm = new FormData(form);
        dadosForm.append('role', 'Admin');
        
        const response = await fetch('../Models/editar-admin.php', {
            method: 'POST',
            body: dadosForm,
        });
        
        if (!response.ok) {
            throw new Error('Erro ao salvar alterações');
        }
        
        const resposta = await response.json();
        
        if (resposta['erro']) {
            msgAlertaErroEdit.innerHTML = resposta['msg'];
            msgAlertaErroEdit.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            msgAlerta.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>${resposta['msg']}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            setTimeout(() => {
                const editModal = bootstrap.Modal.getInstance(document.getElementById("editAdminModal"));
                if (editModal) {
                    editModal.hide();
                }
            }, 1000);
            
            listarAdmin(1);
        }
    } catch (error) {
        console.error('Erro:', error);
        msgAlertaErroEdit.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Erro ao salvar alterações: ${error.message}
            </div>
        `;
    } finally {
        // Restaurar o texto original do botão
        editBtn.innerHTML = btnText;
        editBtn.disabled = false;
    }
});

// --------------- CRUD - Deletar (Administrador) ---------------------

async function apagarAdminDados(id) {
    var confirmar = confirm("Tem certeza que deseja excluir este administrador?");

    if (confirmar == true){
        const dados = await fetch('../Models/apagar-admin.php?id=' + id);

        const resposta = await dados.json();
        if (resposta['erro']){
            msgAlerta.innerHTML = resposta['msg'];
        } else {
            msgAlerta.innerHTML = resposta['msg'];
            listarAdmin(1);
        }
    }
}

// Inicializar quando o documento estiver pronto
$(document).ready(function() {
    $('#editAdminModal').on('shown.bs.modal', function () {
        setTimeout(initializeMasks, 100); // Pequeno delay para garantir que o DOM está pronto
    });
    
    listarAdmin(1);
    initializeMasks();
});