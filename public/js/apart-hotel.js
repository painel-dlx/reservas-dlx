/* global $, jQuery */
/**
 * Fechar um poup modal
 * @param $container
 */
function fecharPopupExterno($container) {
    $container.fadeOut('fast', function () {
        $container.html('');
        $(window).off('.__poupModal');
    });
}

/**
 * Abrir um popup modal
 * @param $container
 * @param url
 * @param dados
 * @param callback
 */
function abrirPopupExterno($container, url, dados, callback) {
    $.get(
        url,
        dados,
        function (html) {
            $container.html(html).show();

            $(window).on('keyup.__poupModal', function (evt) {
                const kc = evt.keycode || evt.which;

                if (kc === 27) {
                    fecharPopupExterno($container);
                }
            });

            if (typeof callback === 'function') {
                callback.apply();
            }
        },
        'html'
    );
}


// CRUD
// @codekit-append "componentes/crud/_excluir-quarto.js"
// @codekit-append "componentes/crud/_excluir-midia-quarto.js"

// POPUP
// @codekit-append "componentes/popup/_popup-confirmar-reserva.js"
// @codekit-append "componentes/popup/_popup-confirmar-pedido.js"
// @codekit-append "componentes/popup/_popup-upload-midias.js"
// @codekit-append "componentes/popup/_popup-detalhamento-periodo.js"

// FORM
// @codekit-append "componentes/form/_upload-preview.js"

// OUTROS
// @codekit-append "componentes/mostrar-cpf-completo/_mostrar-cpf-completo.js"
// @codekit-append "componentes/mostrar-cpf-completo/_mostrar-cpf-completo-pedido.js"
