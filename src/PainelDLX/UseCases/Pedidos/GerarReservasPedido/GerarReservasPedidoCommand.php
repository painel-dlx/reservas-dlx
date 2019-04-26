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

namespace Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido;


use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\PainelDLX\Domain\Entities\Pedido;

/**
 * Class GerarReservasPedidoCommand
 * @package Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido
 * @covers GerarReservasPedidoCommandTest
 */
class GerarReservasPedidoCommand
{
    /**
     * @var Pedido
     */
    private $pedido;
    /**
     * @var Usuario
     */
    private $usuario;

    /**
     * GerarReservasPedidoCommand constructor.
     * @param Pedido $pedido
     * @param Usuario $usuario
     */
    public function __construct(Pedido $pedido, Usuario $usuario)
    {
        $this->pedido = $pedido;
        $this->usuario = $usuario;
    }

    /**
     * @return Pedido
     */
    public function getPedido(): Pedido
    {
        return $this->pedido;
    }

    /**
     * @return Usuario
     */
    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }
}