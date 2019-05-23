// export {mostrarCpfCompletoPedido}

/**
 * Mostrar o CPF completo do cliente de um determinado pedido
 * @param pedido_id
 */
function mostrarCpfCompletoPedido(pedido_id) {
    $.get(
        '/painel-dlx/apart-hotel/pedidos/mostrar-cpf-completo',
        {id: pedido_id},
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