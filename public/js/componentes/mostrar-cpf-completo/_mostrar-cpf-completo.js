// export {mostrarCpfCompleto}

/**
 * Mostrar o CPF completo do cliente de uma determinada reserva
 * @param reserva_id
 */
function mostrarCpfCompleto(reserva_id) {
    $.get(
        '/painel-dlx/apart-hotel/reservas/mostrar-cpf-completo',
        {id: reserva_id},
        function (json, status, xhr) {
            if (json.retorno === 'erro') {
                msgUsuario.mostrar(json.mensagem, json.retorno, xhr.id);
                return;
            }

            msgUsuario.mostrar('Exibindo CPF do cliente...', 'sucesso', xhr.id);

            $('#cpf-cliente').html(json.cpf);
            $('#btn-cpf-completo').remove();
        },
        'json'
    );
}