/**
 * Excluir um quarto
 * @param quarto_id
 */
function excluirQuarto(quarto_id) {
    if (confirm('Deseja realmente excluir esse quarto?')) {
        $.post(
            '/painel-dlx/apart-hotel/quartos/excluir',
            {id: quarto_id},
            function (json, status, xhr) {
                if (json.retorno === 'sucesso') {
                    msgUsuario.adicionar(json.mensagem, json.retorno, xhr.id);
                    window.location = '/painel-dlx/apart-hotel/quartos';
                    return;
                }

                msgUsuario.mostrar(json.mensagem, json.retorno, xhr.id);
            },
            'json'
        );
    }
}