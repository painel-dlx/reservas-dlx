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

namespace Reservas\PainelDLX\Domain\Quartos\Entities;


use DLX\Domain\Entities\Entity;

/**
 * Class QuartoMidia
 * @package Reservas\PainelDLX\Domain\Quartos\Entities
 * @covers QuartoMidiaTest
 */
class QuartoMidia extends Entity
{
    const HTML_IMAGEM = '<figure><img src="%s" alt="%s"></figure>';
    const HTML_VIDEO = '<video><source src="%s" type="%s"></video>';

    /** @var Quarto */
    private $quarto;
    /** @var string */
    private $arquivo;
    /** @var string|null */
    private $mini;

    /**
     * QuartoMidia constructor.
     * @param string $arquivo
     */
    public function __construct(string $arquivo)
    {
        $this->arquivo = $arquivo;
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
    public function getArquivo(): string
    {
        return $this->arquivo;
    }

    /**
     * @param string $arquivo
     * @return QuartoMidia
     */
    public function setArquivo(string $arquivo): QuartoMidia
    {
        $this->arquivo = $arquivo;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMini(): ?string
    {
        return $this->mini;
    }

    /**
     * @param string|null $mini
     * @return QuartoMidia
     */
    public function setMini(?string $mini): QuartoMidia
    {
        $this->mini = $mini;
        return $this;
    }

    /**
     * Retorna a mídia em HTML
     * @param string|null $base_html
     * @param bool $mini
     * @return string|null
     */
    public function getTagHtml(?string $base_html = null, bool $mini = true): ?string
    {
        $arquivo = $mini && !empty($this->getMini()) ? $this->getMini() : $this->getArquivo();

        $mime_type = mime_content_type($this->getArquivo());
        $src = "{$base_html}{$arquivo}";

        if (preg_match('~^image/~', $mime_type)) {
            $html = sprintf(self::HTML_IMAGEM, $src, $this->getQuarto()->getNome());
        } elseif (preg_match('~^video/~', $mime_type)) {
            $html = sprintf(self::HTML_VIDEO, $src, $mime_type);
        }

        return $html;
    }
}