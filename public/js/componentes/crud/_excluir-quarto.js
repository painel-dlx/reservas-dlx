/**
 * Excluir um quarto
 * @param quarto_id
 */
function excluirQuarto(quarto_id) {
    if (confirm('Deseja realmente excluir esse quarto?')) {
        $.ajax({
            url: '/painel-dlx/apart-hotel/quartos/excluir',
            data: {id: quarto_id},
            type: 'post',
            dataType: 'json',
            mensagem: 'Excluindo quarto.<br>Por favor aguarde...',
            success: function (json, status, xhr) {
                if (json.retorno === 'sucesso') {
                    msgUsuario.adicionar(json.mensagem, json.retorno, xhr.id);
                    window.location = '/painel-dlx/apart-hotel/quartos';
                    return;
                }

                msgUsuario.mostrar(json.mensagem, json.retorno, xhr.id);
            }
        });
    }
}