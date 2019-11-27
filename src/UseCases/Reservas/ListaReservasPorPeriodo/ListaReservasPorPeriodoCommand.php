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

namespace Reservas\UseCases\Reservas\ListaReservasPorPeriodo;


use PainelDLX\Application\Contracts\ListaRegistrosCommand;

class ListaReservasPorPeriodoCommand extends ListaRegistrosCommand
{
    /**
     * @var string|null
     */
    private $data_inicial;
    /**
     * @var string|null
     */
    private $data_final;

    /**
     * ListaReservasPorPeriodoCommand constructor.
     * @param string|null $data_inicial
     * @param string|null $data_final
     * @param array $criteria
     * @param array $order_by
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(
        ?string $data_inicial,
        ?string $data_final,
        array $criteria = [],
        array $order_by = [],
        ?int $limit = null,
        ?int $offset = null
    ) {
        parent::__construct($criteria, $order_by, $limit, $offset);
        $this->data_inicial = $data_inicial;
        $this->data_final = $data_final;
    }

    /**
     * @return string|null
     */
    public function getDataInicial(): ?string
    {
        return $this->data_inicial;
    }

    /**
     * @return string|null
     */
    public function getDataFinal(): ?string
    {
        return $this->data_final;
    }
}