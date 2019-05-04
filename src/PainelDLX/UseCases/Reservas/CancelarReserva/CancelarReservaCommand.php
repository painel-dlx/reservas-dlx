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

namespace Reservas\PainelDLX\UseCases\Reservas\CancelarReserva;


use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;

/**
 * Class ConfirmarReservaCommand
 * @package Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva
 * @covers CancelarReservaCommandTest
 */
class CancelarReservaCommand
{
    /**
     * @var Reserva
     */
    private $reserva;
    /**
     * @var string
     */
    private $motivo;
    /**
     * @var Usuario
     */
    private $usuario;

    /**
     * ConfirmarReservaCommand constructor.
     * @param Reserva $reserva
     * @param Usuario $usuario
     * @param string $motivo
     */
    public function __construct(Reserva $reserva, Usuario $usuario, string $motivo)
    {
        $this->reserva = $reserva;
        $this->usuario = $usuario;
        $this->motivo = $motivo;
    }

    /**
     * @return \Reservas\PainelDLX\Domain\Reservas\Entities\Reserva
     */
    public function getReserva(): Reserva
    {
        return $this->reserva;
    }

    /**
     * @return string
     */
    public function getMotivo(): string
    {
        return $this->motivo;
    }

    /**
     * @return Usuario
     */
    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }
}