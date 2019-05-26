/**
 * Excluir mídia de um quarto
 * @param {string} arquivo
 */
function excluirMidiaQuarto(arquivo) {
    if (confirm('Deseja realmente excluir essa foto/vídeo?')) {
        $.post(
            '/painel-dlx/apart-hotel/quartos/excluir-midia',
            {arquivo: arquivo},
            function (json, status, xhr) {
                if (json.retorno === 'sucesso') {
                    msgUsuario.adicionar(json.mensagem, json.retorno, xhr.id);
                    window.location.reload();
                    return;
                }

                msgUsuario.mostrar(json.mensagem, json.retorno, xhr.id);
            },
            'json'
        );
    }
}