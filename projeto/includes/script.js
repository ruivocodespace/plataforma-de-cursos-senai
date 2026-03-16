//Função para exibir a data atual na tela, formatada como "Segunda-feira, 01/01/2024"

function mostrarDataCompleta(idElemento) {
    const data = new Date();

    const opcoes = {
        weekday: 'long',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    };

    let formatada = data.toLocaleDateString('pt-BR', opcoes);

    // Deixa a primeira letra maiúscula
    formatada = formatada.charAt(0).toUpperCase() + formatada.slice(1);

    const elemento = document.getElementById(idElemento);
    if (elemento) {
        elemento.innerText = formatada;
    }
}
