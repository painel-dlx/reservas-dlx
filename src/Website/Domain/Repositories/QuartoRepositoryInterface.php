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

namespace Reservas\Website\Domain\Repositories;


use DateTime;
use DLX\Domain\Repositories\EntityRepositoryInterface;
use Reservas\PainelDLX\Domain\Entities\Quarto;

interface QuartoRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Obter quartos disponíveis.
     * @param DateTime $data_entrada
     * @param DateTime $data_saida
     * @param int $qtde_adultos
     * @param int $qtde_criancas
     * @param int $qtde_quartos
     * @return array
     */
    public function findQuartosDisponiveis(
        DateTime $data_entrada,
        DateTime $data_saida,
        int $qtde_adultos = 2,
        int $qtde_criancas = 0,
        int $qtde_quartos = 1
    ): array;
}