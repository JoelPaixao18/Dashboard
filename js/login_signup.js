//Transação Login para Cadastro
function showForm(formId) {
    document.querySelectorAll(".form-box").forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}

var btnSignin = document.querySelector("#signin");
var btnSignup = document.querySelector("#signup1");

var body = document.querySelector("body");

btnSignin.addEventListener("click", function() {
    body.className = "sign-in-js";
});

btnSignup.addEventListener("click", function() {
    body.className = "sign-up-js";
});
