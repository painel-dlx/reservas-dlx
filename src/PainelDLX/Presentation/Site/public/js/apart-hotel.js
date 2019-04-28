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

/**
 * Mostrar o CPF completo do cliente de uma determinada reserva
 * @param reserva_id
 */
function mostrarCpfCompleto(reserva_id) {
    $.get(
        '/painel-dlx/apart-hotel/reservas/mostrar-cpf-completo',
        {id: reserva_id},
        function (json) {
            if (json.retorno === 'erro') {
                alert(json.mensagem);
                return;
            }

            $('#cpf-cliente').html(json.cpf);
            $('#btn-cpf-completo').remove();
        },
        'json'
    );
}