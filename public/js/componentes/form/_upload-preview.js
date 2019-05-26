(function ($) {
    $.fn.uploadPreview = function () {
        var gerarPreviewImagem = function (url_imagem) {
            return $(document.createElement('figure'))
                .html(
                    '<img src="' + url_imagem + '" alt="">'
                );
        };

        return this.each(function () {
            var $this = $(this);

            $this.on('change.__uploadPreview', function () {
                var qtde_arquivos = this.files.length;
                var file_reader;

                for (var i = 0; i < qtde_arquivos; i++) {
                    file_reader = new FileReader();
                    file_reader.readAsDataURL(this.files[i]);
                    file_reader.onloadend = function (evt) {
                        var $div = $(document.createElement('div')).addClass('arquivo-preview');
                        var $img = gerarPreviewImagem(evt.target.result);
                        var $preview = $this.parents('.upload-arquivos').find('.upload-arquivos-preview');

                        $img.appendTo($div);
                        $div.appendTo($preview);
                    };
                }
            });

            return $this;
        });
    };
})(jQuery);