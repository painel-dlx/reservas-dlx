<?php
/**
 * MIT License
 *
 * Copyright (c) 2018 PHP DLX
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Reservas\Domain\Quartos\Entities;


use DLX\Domain\Entities\Entity;

/**
 * Class QuartoMidia
 * @package Reservas\Domain\Quartos\Entities
 * @covers QuartoMidiaTest
 */
class QuartoMidia extends Entity
{
    const HTML_IMAGEM = '<figure><img src="%s" alt="%s"></figure>';
    const HTML_VIDEO = '<video><source src="%s" type="%s"></video>';

    /** @var Quarto */
    private $quarto;
    /** @var string */
    private $arquivo_original;
    /** @var string|null */
    private $miniatura;

    /**
     * QuartoMidia constructor.
     * @param string $arquivo
     */
    public function __construct(string $arquivo)
    {
        $this->arquivo_original = $arquivo;
    }

    /**
     * @return Quarto
     */
    public function getQuarto(): Quarto
    {
        return $this->quarto;
    }

    /**
     * @param Quarto $quarto
     * @return QuartoMidia
     */
    public function setQuarto(Quarto $quarto): QuartoMidia
    {
        $this->quarto = $quarto;
        return $this;
    }

    /**
     * @return string
     */
    public function getArquivoOriginal(): string
    {
        return $this->arquivo_original;
    }

    /**
     * @param string $arquivo_original
     * @return QuartoMidia
     */
    public function setArquivoOriginal(string $arquivo_original): QuartoMidia
    {
        $this->arquivo_original = $arquivo_original;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMiniatura(): ?string
    {
        return $this->miniatura;
    }

    /**
     * @param string|null $miniatura
     * @return QuartoMidia
     */
    public function setMiniatura(?string $miniatura): QuartoMidia
    {
        $this->miniatura = $miniatura;
        return $this;
    }

    /**
     * Retorna a mídia em HTML
     * @param bool $mini
     * @return string|null
     */
    public function getTagHtml(bool $mini = true): ?string
    {
        $arquivo = $mini && !empty($this->getMiniatura()) ? $this->getMiniatura() : $this->getArquivoOriginal();
        $caminho_absoluto = stream_resolve_include_path($arquivo);

        if ($caminho_absoluto === false) {
            return '';
        }

        $caminho_relativo = preg_replace("~^{$_SERVER['DOCUMENT_ROOT']}~", '', $caminho_absoluto);

        $mime_type = mime_content_type($arquivo);
        $html = null;

        if (preg_match('~^image/~', $mime_type)) {
            $html = sprintf(self::HTML_IMAGEM, $caminho_relativo, $this->getQuarto()->getNome());
        } elseif (preg_match('~^video/~', $mime_type)) {
            $html = sprintf(self::HTML_VIDEO, $caminho_relativo, $mime_type);
        }

        return $html;
    }
}