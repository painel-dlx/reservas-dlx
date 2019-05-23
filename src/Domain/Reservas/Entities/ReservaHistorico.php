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

namespace Reservas\Domain\Reservas\Entities;


use DateTime;
use DLX\Domain\Entities\Entity;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Reservas\Entities\Reserva;

/**
 * Class ReservaHistorico
 * @package Reservas\Domain\Entities
 * @covers ReservaHistoricoTest
 */
class ReservaHistorico extends Entity
{
    /** @var int|null */
    private $id;
    /** @var Reserva */
    private $reserva;
    /** @var Usuario */
    private $usuario;
    /** @var DateTime */
    private $data;
    /** @var string */
    private $status;
    /** @var string */
    private $motivo;

    /**
     * ReservaHistorico constructor.
     * @param string $status
     * @param string $motivo
     */
    public function __construct(string $status, string $motivo)
    {
        $this->status = $status;
        $this->motivo = $motivo;
        $this->setData(new DateTime());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->getStatus()} em {$this->getData()->format('d/m/Y H:i')}: {$this->getMotivo()}";
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return ReservaHistorico
     */
    public function setId(?int $id): ReservaHistorico
    {
        $this->id = $id;
        return $this;
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
     * @return ReservaHistorico
     */
    public function setReserva(Reserva $reserva): ReservaHistorico
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
     * @return ReservaHistorico
     */
    public function setUsuario(Usuario $usuario): ReservaHistorico
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
     * @return ReservaHistorico
     */
    public function setData(DateTime $data): ReservaHistorico
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return ReservaHistorico
     */
    public function setStatus(string $status): ReservaHistorico
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getMotivo(): string
    {
        return $this->motivo;
    }

    /**
     * @param string $motivo
     * @return ReservaHistorico
     */
    public function setMotivo(string $motivo): ReservaHistorico
    {
        $this->motivo = $motivo;
        return $this;
    }
}