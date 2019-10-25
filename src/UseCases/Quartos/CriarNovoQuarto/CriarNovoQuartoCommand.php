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

namespace Reservas\UseCases\Quartos\CriarNovoQuarto;


class CriarNovoQuartoCommand
{
    /**
     * @var string
     */
    private $nome;
    /**
     * @var string
     */
    private $descricao;
    /**
     * @var int
     */
    private $max_hospedes;
    /**
     * @var int
     */
    private $qtde;
    /**
     * @var int
     */
    private $tamanho_m2;
    /**
     * @var float
     */
    private $valor_min;
    /**
     * @var string
     */
    private $link;

    /**
     * CriarNovoQuartoCommand constructor.
     * @param string $nome
     * @param string $descricao
     * @param int $max_hospedes
     * @param int $qtde
     * @param int $tamanho_m2
     * @param float $valor_min
     * @param string $link
     */
    public function __construct(
        string $nome,
        string $descricao,
        int $max_hospedes,
        int $qtde,
        int $tamanho_m2,
        float $valor_min,
        string $link
    ) {
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->max_hospedes = $max_hospedes;
        $this->qtde = $qtde;
        $this->tamanho_m2 = $tamanho_m2;
        $this->valor_min = $valor_min;
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @return int
     */
    public function getMaxHospedes(): int
    {
        return $this->max_hospedes;
    }

    /**
     * @return int
     */
    public function getQtde(): int
    {
        return $this->qtde;
    }

    /**
     * @return int
     */
    public function getTamanhoM2(): int
    {
        return $this->tamanho_m2;
    }

    /**
     * @return float
     */
    public function getValorMin(): float
    {
        return $this->valor_min;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }
}