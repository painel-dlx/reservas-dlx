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

namespace Reservas\UseCases\Quartos\EditarQuarto;


use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommand;

class EditarQuartoCommand extends CriarNovoQuartoCommand
{
    /**
     * @var int
     */
    private $quarto_id;

    /**
     * EditarQuartoCommand constructor.
     * @param string $nome
     * @param string $descricao
     * @param int $max_hospedes
     * @param int $qtde
     * @param int $tamanho_m2
     * @param float $valor_min
     * @param string $link
     * @param int $quarto_id
     */
    public function __construct(
        string $nome,
        string $descricao,
        int $max_hospedes,
        int $qtde,
        int $tamanho_m2,
        float $valor_min,
        string $link,
        int $quarto_id
    ) {
        parent::__construct($nome, $descricao, $max_hospedes, $qtde, $tamanho_m2, $valor_min, $link);
        $this->quarto_id = $quarto_id;
    }

    /**
     * @return int
     */
    public function getQuartoId(): int
    {
        return $this->quarto_id;
    }
}