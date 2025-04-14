const tbody = document.querySelector('.listar-residencias');
const cadForm = document.getElementById("cad-residencia-form");
const editForm = document.getElementById("edit-residencia-form");
const msgAlertaErroCad = document.getElementById("msgAlertaErroCad");
const msgAlertaErroEdit = document.getElementById("msgAlertaErroEdit");
const msgAlerta = document.getElementById("msgAlerta");
const cadModal = new bootstrap.Modal(document.getElementById("cadResidenciaModal"));

const listarResidencias = async (pagina) => {
    const dados = await fetch('../Controllers/list-resi.php?pagina=' + pagina);
    const resposta = await dados.text();
    tbody.innerHTML = resposta;
}

listarResidencias(1);

cadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById("cad-residencia-btn").value = "Salvando...";

    if(document.getElementById("typology").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário selecionar o Tipo de Residência!</div>";
    } else if(document.getElementById("location").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário pôr a sua Localização!</div>";
    } else if(document.getElementById("price").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário definir um Preço (Valor Avaliado)!</div>";
    } else if(document.getElementById("status").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário definir o Estado da Residênncia!</div>";
    } else if(document.getElementById("descricao").value === ""){
        msgAlertaErroCad.innerHTML = "<div class='alert alert-danger' role='alert'>Erro: Necessário descrever a Residência!</div>";
    } else {

        const dadosForm = new FormData(cadForm);
        dadosForm.append('add', 1);
        
            const dados = await fetch('../Controllers/cadastro-resi.php', {
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
                listarResidencias(1);
            }
    }

    document.getElementById("cad-residencia-btn").value = "Cadastrar";
});

// ------------------ CRUD - Visualizar (Residencia) ------------------

async function visResidencia(id) {
    const dados = await fetch('../Models/visualizar-resi.php?id=' + id);
    const resposta = await dados.json();
    //console.log(resposta);

    if(resposta['erro']) {
        msgAlerta.innerHTML = resposta['erro'];
    } else {
        const visModal = new bootstrap.Modal(document.getElementById("visResidenciaModal"));
        visModal.show();

        document.getElementById("idResidencia").innerHTML = resposta['dados'].id;
        document.getElementById("typologyResidencia").innerHTML = resposta['dados'].zonamento;
        document.getElementById("locationResidencia").innerHTML = resposta['dados'].localizacao;
        document.getElementById("priceResidencia").innerHTML = resposta['dados'].preco;
        document.getElementById("statusResidencia").innerHTML = resposta['dados'].status;
        document.getElementById("descricaoResidencia").innerHTML = resposta['dados'].descricao;
    }
}

// ------------------ CRUD - Editar (Usuário) ------------------

async function editResidenciaDados(id) {
    msgAlertaErroEdit.innerHTML = "";

    const dados = await fetch('../Models/visualizar-resi.php?id=' + id);
    const resposta = await dados.json();
    //console.log(resposta);

    if (resposta['erro']) {
        msgAlerta.innerHTML = resposta['msg'];
    } else {
        const editModal = new bootstrap.Modal(document.getElementById("editResidenciaModal"));
        editModal.show();
        document.getElementById("editid").value = resposta['dados'].id;
        document.getElementById("editzonamento").value = resposta['dados'].zonamento;
        document.getElementById("editlocalizacao").value = resposta['dados'].localizacao;
        document.getElementById("editpreco").value = resposta['dados'].preco;
        document.getElementById("editstatus").value = resposta['dados'].status;
        document.getElementById("editdescricao").value = resposta['dados'].descricao;
    }
}

editForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    document.getElementById("edit-residencia-btn").value = "Salvando...";

    const dadosForm = new FormData(editForm);
    console.log(dadosForm);
    /*for (var dadosFormEdit  of dadosForm.entries()) {
        console.log(dadosFormEdit[0] + ' - ' + dadosFormEdit[1]);
    }*/

    const dados = await fetch('../Models/editar-resi.php', {
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
        listarResidencias(1);
    }

    document.getElementById("edit-residencia-btn").value = "Salvar";
});

// --------------- CRUD - Deleter (Usuário) ---------------------

async function apagarResidenciaDados(id) {

    var confirmar = confirm("Tem certeza que deseja excluir o registro selectionado?");

    if (confirmar == true){
        
        const dados = await fetch('../Models/apagar-resi.php?id=' + id);

        const resposta = await dados.json();
        if (resposta['erro']){
            msgAlerta.innerHTML = resposta['msg'];
        } else {
            msgAlerta.innerHTML = resposta['msg'];
        listarResidencias(1);
        }
    }
}