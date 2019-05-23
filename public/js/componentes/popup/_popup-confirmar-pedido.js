// global abrirPopupExterno

/**
 * Mostrar popup de confirmacao do pedido
 * @param acao
 * @param pedido_id
 */
function popupConfirmarPedido(acao, pedido_id) {
    abrirPopupExterno(
        $('#popup-confirmar-pedido'),
        '/painel-dlx/apart-hotel/pedidos/' + acao + '-pedido',
        {id: pedido_id, 'pg-mestra': 'conteudo-master'},
        function () {
            $('#form-confirmar-pedido, #form-cancelar-pedido').formAjax({
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