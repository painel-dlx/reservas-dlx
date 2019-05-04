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

namespace Reservas\PainelDLX\Tests\Domain\Pedidos\Validators;

use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Reservas\PainelDLX\Domain\Pedidos\Entities\Pedido;
use Reservas\PainelDLX\Domain\Pedidos\Validators\ValidarReservasGeradas;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;
use Reservas\PainelDLX\Domain\Pedidos\Exceptions\PedidoInvalidoException;
use Reservas\Tests\Helpers\QuartoTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ValidarReservasGeradasTest
 * @package Reservas\PainelDLX\Domain\Validators\Pedidos
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Pedidos\Validators\ValidarReservasGeradas
 */
class ValidarReservasGeradasTest extends ReservasTestCase
{

    /**
     * @throws DBALException
     * @throws ORMException
     * @throws \Reservas\PainelDLX\Domain\Pedidos\Exceptions\PedidoInvalidoException
     */
    public function test_Validar_deve_lancar_excecao_quando_nenhuma_reserva_tiver_sido_gerada()
    {
        $quarto = QuartoTesteHelper::getRandom();
        $data = date('Y-m-d');

        $pedido = new Pedido();
        $pedido->addItem($quarto, $data, $data, 1, 0, 1);

        $this->expectException(PedidoInvalidoException::class);

        (new ValidarReservasGeradas())->validar($pedido);
    }

    /**
     * @throws DBALException
     * @throws ORMException
     * @throws \Reservas\PainelDLX\Domain\Pedidos\Exceptions\PedidoInvalidoException
     */
    public function test_Validar_deve_lancar_excecao_quando_qtde_itens_diferente_reservas()
    {
        $data = date('Y-m-d');

        $quarto1 = QuartoTesteHelper::getRandom();
        $quarto2 = QuartoTesteHelper::getRandom();

        $pedido = new Pedido();
        $pedido->addItem($quarto1, $data, $data, 1, 0, 1);
        $pedido->addItem($quarto2, $data, $data, 1, 0, 1);

        $pedido->addReserva(new Reserva($quarto1, new DateTime(), new DateTime(), 1));

        $this->expectException(PedidoInvalidoException::class);

        (new ValidarReservasGeradas())->validar($pedido);
    }
}
