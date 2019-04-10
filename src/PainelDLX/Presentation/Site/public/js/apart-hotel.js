/**
 * Excluir um quarto
 * @param quarto_id
 */
function excluirQuarto(quarto_id) {
    if (confirm('Deseja realmente excluir esse quarto?')) {
        $.post(
            '/painel-dlx/apart-hotel/quartos/excluir',
            {id: quarto_id},
            function (json) {
                alert(json.mensagem);

                if (json.retorno === 'sucesso') {
                    window.location = '/painel-dlx/apart-hotel/quartos'
                }
            },
            'json'
        );
    }
}