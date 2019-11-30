/* global $, abrirPopupExterno */

/**
 * Abrir poupup de detalhamento do período
 * @param pedido_item_id
 */
function popupDetalhamentoPeriodo(pedido_item_id) {
    abrirPopupExterno(
        $('#popup-detalhamento-periodo'),
        '/painel-dlx/apart-hotel/pedidos/detalhe/detalhamento-periodo',
        { pedido_item_id: pedido_item_id, 'pg-mestra': 'conteudo-master' }
    );
}