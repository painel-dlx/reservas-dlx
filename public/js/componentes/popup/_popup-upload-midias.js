/**
 * Abrir poup para fazer upload de mídia
 */
function popupUploadMidias() {
    var $popup = $('#popup-upload-midias');
    var dados = {'pg-mestra': 'conteudo-master'};

    abrirPopupExterno(
        $popup,
        '/painel-dlx/apart-hotel/quartos/upload-midias',
        dados,
        function () {
            $('#form-upload').formAjax({
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