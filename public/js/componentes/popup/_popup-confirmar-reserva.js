// global abrirPopupExterno

/**
 * Mostrar popup para confirmar uma reserva
 * @param acao
 * @param reserva_id
 */
function popupConfirmarReserva(acao, reserva_id) {
    abrirPopupExterno(
        $('#popup-confirmar-reserva'),
        '/painel-dlx/apart-hotel/reservas/' + acao + '-reserva',
        {id: reserva_id, 'pg-mestra': 'conteudo-master'},
        function () {
            $('#form-confirmar-reserva, #form-cancelar-reserva').formAjax({
                func_depois: function (json, form, xhr) {
                    if (json.retorno === 'sucesso') {
                        msgUsuario.adicionar(json.mensagem, json.retorno, xhr.id);
                        window.location.reload();
                        return;
                    }

                    msgUsuario.mostrar(json.mensagem, json.retorno, xhr.id);
                }
            });
        }
    );
}