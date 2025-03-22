const tbody = document.querySelector('.listar-usuarios');
const cadForm = document.getElementById("cad-usuario-form");
const editForm = document.getElementById("edit-usuario-form");
const msgAlertaErroCad = document.getElementById("msgAlertaErroCad");
const msgAlertaErroEdit = document.getElementById("msgAlertaErroEdit");
const msgAlerta = document.getElementById("msgAlerta");
const cadModal = new bootstrap.Modal(document.getElementById("cadUsuarioModal"));

const listarUsuarios = async (pagina) => {
    const dados = await fetch('../Controllers/list.php?pagina=' + pagina);
    const resposta = await dados.text();
    tbody.innerHTML = resposta;
}

listarUsuarios(1);

cadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById("cad-usuario-btn").value = "Salvando...";

    if(document.getElementById("nome").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Nome!</div>";
    } else if(document.getElementById("email").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo E-mail!</div>";
    } else if(document.getElementById("senha").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Senha!</div>";
    } else {

        const dadosForm = new FormData(cadForm);
        dadosForm.append('add', 1);
        
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
                listarUsuarios(1);
            }
    }

    document.getElementById("cad-usuario-btn").value = "Cadastrar";
});

// ------------------ CRUD - Visualizar (Usuário) ------------------

async function visUsuario(id) {
    const dados = await fetch('../Models/visualizar.php?id=' + id);
    const resposta = await dados.json();
    //console.log(resposta);

    if(resposta['erro']) {
        msgAlerta.innerHTML = resposta['erro'];
    } else {
        const visModal = new bootstrap.Modal(document.getElementById("visUsuarioModal"));
        visModal.show();

        document.getElementById("idUsuario").innerHTML = resposta['dados'].id;
        document.getElementById("nomeUsuario").innerHTML = resposta['dados'].nome;
        document.getElementById("emailUsuario").innerHTML = resposta['dados'].email;
        document.getElementById("roleUsuario").innerHTML = resposta['dados'].role;
    }
}

// ------------------ CRUD - Editar (Usuário) ------------------

async function editUsuarioDados(id) {
    msgAlertaErroEdit.innerHTML = "";

    const dados = await fetch('../Models/visualizar.php?id=' + id);
    const resposta = await dados.json();
    //console.log(resposta);

    if (resposta['erro']) {
        msgAlerta.innerHTML = resposta['msg'];
    } else {
        const editModal = new bootstrap.Modal(document.getElementById("editUsuarioModal"));
        editModal.show();
        document.getElementById("editid").value = resposta['dados'].id;
        document.getElementById("editnome").value = resposta['dados'].nome;
        document.getElementById("editemail").value = resposta['dados'].email;
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

// --------------------- Pesquisa Dinamica -------------------------

/*$(function() {
    $("#pesquisa").autocomplete({
        source: '../Models/pesquisa.php'
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var searchInput = document.getElementById("searchInput");
    var tableRows = document.querySelectorAll("#userTable tbody tr");

    searchInput.addEventListener("keyup", function () {
        var searchText = searchInput.value.toLowerCase();

        tableRows.forEach(function (row) {
            var rowText = row.textContent.toLocaleLowerCase();
            row.style.display = rowText.includes(searchText) ? "" : "none";
        });
    });
});*/
