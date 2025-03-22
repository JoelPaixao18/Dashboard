// Seleciona os elementos
const toggleButton = document.querySelector('.toggle');
const navigation = document.querySelector('.navigation');
const mainContent = document.querySelector('.main');

// Adiciona um evento de clique ao ícone de menu
toggleButton.addEventListener('click', () => {
    navigation.classList.toggle('active');
    mainContent.classList.toggle('active');
});
