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

namespace Reservas\PainelDLX\Domain\Entities;


use DateTime;
use DLX\Domain\Entities\Entity;
use PainelDLX\Domain\Usuarios\Entities\Usuario;

class VisualizacaoCpf extends Entity
{
    /** @var Reserva */
    private $reserva;
    /** @var Usuario */
    private $usuario;
    /** @var DateTime */
    private $data;

    /**
     * VisualizacaoCpf constructor.
     * @param Reserva $reserva
     * @param Usuario $usuario
     */
    public function __construct(Reserva $reserva, Usuario $usuario)
    {
        $this->reserva = $reserva;
        $this->usuario = $usuario;
        $this->data = new DateTime();
    }

    /**
     * @return Reserva
     */
    public function getReserva(): Reserva
    {
        return $this->reserva;
    }

    /**
     * @param Reserva $reserva
     * @return VisualizacaoCpf
     */
    public function setReserva(Reserva $reserva): VisualizacaoCpf
    {
        $this->reserva = $reserva;
        return $this;
    }

    /**
     * @return Usuario
     */
    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    /**
     * @param Usuario $usuario
     * @return VisualizacaoCpf
     */
    public function setUsuario(Usuario $usuario): VisualizacaoCpf
    {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getData(): DateTime
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     * @return VisualizacaoCpf
     */
    public function setData(DateTime $data): VisualizacaoCpf
    {
        $this->data = $data;
        return $this;
    }
}