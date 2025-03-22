const tbody = document.querySelector('.listar-proprietarios');
const cadForm = document.getElementById("cad-proprietario-form");
const editForm = document.getElementById("edit-proprietario-form");
const msgAlertaErroCad = document.getElementById("msgAlertaErroCad");
const msgAlertaErroEdit = document.getElementById("msgAlertaErroEdit");
const msgAlerta = document.getElementById("msgAlerta");
const cadModal = new bootstrap.Modal(document.getElementById("cadProprietarioModal"));

const listarProprietarios = async (pagina) => {
    const dados = await fetch('../Controllers/list-proprie.php?pagina=' + pagina);
    const resposta = await dados.text();
    tbody.innerHTML = resposta;
}

listarProprietarios(1);

cadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById("cad-proprietario-btn").value = "Salvando...";

    if(document.getElementById("nome").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Nome!</div>";
    } else if(document.getElementById("email").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo E-mail!</div>";
    } else if(document.getElementById("tel").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo de Contato!</div>";
    } else if(document.getElementById("BI").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo de Bilhete de Identidade!</div>";
    } else if(document.getElementById("endereco").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo do seu Endereço!</div>";
    } else {

        const dadosForm = new FormData(cadForm);
        dadosForm.append('add', 1);
        
            const dados = await fetch('../Controllers/cadastrar-propriet.php', {
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
                listarProprietarios(1);
            }
    }

    document.getElementById("cad-proprietario-btn").value = "Cadastrar";
});

// ------------------ CRUD - Visualizar (Proprietario) ------------------

async function visProprietario(id) {
    const dados = await fetch('../Models/visualizar-prop.php?id=' + id);
    const resposta = await dados.json();
    //console.log(resposta);

    if(resposta['erro']) {
        msgAlerta.innerHTML = resposta['erro'];
    } else {
        const visModal = new bootstrap.Modal(document.getElementById("visProprietarioModal"));
        visModal.show();

        document.getElementById("idProprietario").innerHTML = resposta['dados'].id;
        document.getElementById("nomeProprietario").innerHTML = resposta['dados'].nome;
        document.getElementById("emailProprietario").innerHTML = resposta['dados'].email;
        document.getElementById("contatoProprietario").innerHTML = resposta['dados'].tel;
        document.getElementById("biProprietario").innerHTML = resposta['dados'].BI;
        document.getElementById("enderecoProprietario").innerHTML = resposta['dados'].endereco;
    }
}

// ------------------ CRUD - Editar (Usuário) ------------------

async function editProprietarioDados(id) {
    msgAlertaErroEdit.innerHTML = "";

    const dados = await fetch('../Models/visualizar-prop.php?id=' + id);
    const resposta = await dados.json();
    //console.log(resposta);

    if (resposta['erro']) {
        msgAlerta.innerHTML = resposta['msg'];
    } else {
        const editModal = new bootstrap.Modal(document.getElementById("editProprietarioModal"));
        editModal.show();
        document.getElementById("editid").value = resposta['dados'].id;
        document.getElementById("editnome").value = resposta['dados'].nome;
        document.getElementById("editemail").value = resposta['dados'].email;
        document.getElementById("edittel").value = resposta['dados'].tel;
        document.getElementById("editbi").value = resposta['dados'].BI;
        document.getElementById("editendereco").value = resposta['dados'].endereco;
    }
}

editForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById("edit-proprietario-btn").value = "Salvando...";

    const dadosForm = new FormData(editForm);
    console.log(dadosForm);
    /*for (var dadosFormEdit  of dadosForm.entries()) {
        console.log(dadosFormEdit[0] + ' - ' + dadosFormEdit[1]);
    }*/

    const dados = await fetch('../Models/editar-prop.php', {
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
        listarProprietarios(1);
    }

    document.getElementById("edit-proprietario-btn").value = "Salvar";
});

// --------------- CRUD - Deleter (Usuário) ---------------------

async function apagarProprietarioDados(id) {

    var confirmar = confirm("Tem certeza que deseja excluir o registro selectionado?");

    if (confirmar == true){
        
        const dados = await fetch('../Models/apagar-prop.php?id=' + id);

        const resposta = await dados.json();
        if (resposta['erro']){
            msgAlerta.innerHTML = resposta['msg'];
        } else {
            msgAlerta.innerHTML = resposta['msg'];
        listarProprietarios(1);
        }
    }
}