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

/**
 * Mostrar o CPF completo do cliente de um determinado pedido
 * @param pedido_id
 */
function mostrarCpfCompletoPedido(pedido_id) {
    $.get(
        '/painel-dlx/apart-hotel/pedidos/mostrar-cpf-completo',
        {id: pedido_id},
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

function popupConfirmarReserva(acao, reserva_id) {
    $.get(
        '/painel-dlx/apart-hotel/reservas/' + acao + '-reserva',
        {id: reserva_id, 'pg-mestra': 'conteudo-master'},
        function (html) {
            $('#popup-confirmar-reserva')
                .html(html)
                .show();

            $('#form-confirmar-reserva, #form-cancelar-reserva').formAjax({
                func_depois: function (json) {
                    alert(json.mensagem);

                    if (json.retorno === 'sucesso') {
                        window.location.reload();
                    }
                }
            });

            $(window).on('keyup.__confirmarReserva', function (evt) {
                var kc = evt.keycode || evt.which;

                if (kc === 27) {
                    fecharPopupConfirmarReserva();
                }
            });
        },
        'html'
    );
}

/**
 * Fechar o popup de confirmação / cancelamento
 */
function fecharPopupConfirmarReserva() {
    $('#popup-confirmar-reserva').fadeOut('fast', function () {
        $(this).html('');
        $(window).off('keyup.__confirmarReserva');
    });
}

/**
 * Mostrar popup de confirmacao do pedido
 * @param acao
 * @param pedido_id
 */
function popupConfirmarPedido(acao, pedido_id) {
    $.get(
        '/painel-dlx/apart-hotel/pedidos/' + acao + '-pedido',
        {id: pedido_id, 'pg-mestra': 'conteudo-master'},
        function (html) {
            $('#popup-confirmar-pedido').html(html).show();

            $('#form-confirmar-pedido, #form-cancelar-pedido').formAjax({
                func_depois: function (json) {
                    alert(json.mensagem);

                    if (json.retorno === 'sucesso') {
                        window.location.reload();
                    }
                }
            });

            $(window).on('keyup.__confirmarPedido', function (evt) {
                var kc = evt.keycode || evt.which;

                if (kc === 27) {
                    fecharPopupConfirmarPedido();
                }
            });
        },
        'html'
    );
}

/**
 * Fechar o popup de confirmação / cancelamento do pedido
 */
function fecharPopupConfirmarPedido() {
    $('#popup-confirmar-pedido').fadeOut('fast', function () {
        $(this).html('');
        $(window).off('keyup.__confirmarPedido');
    });
}