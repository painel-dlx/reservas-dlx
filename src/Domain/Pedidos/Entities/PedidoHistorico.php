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

namespace Reservas\Domain\Pedidos\Entities;


use DateTime;
use PainelDLX\Domain\Usuarios\Entities\Usuario;

/**
 * Class PedidoHistorico
 * @package Reservas\Domain\Pedidos\Entities
 * @covers PedidoHistoricoTest
 */
class PedidoHistorico
{
    /** @var int|null */
    private $id;
    /** @var Pedido */
    private $pedido;
    /** @var Usuario */
    private $usuario;
    /** @var DateTime */
    private $data;
    /** @var string */
    private $status;
    /** @var string */
    private $motivo;

    /**
     * PedidoHistorico constructor.
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
        $html = '
            <p class="pedido-historico">
                <span class="pedido-historico-titulo">%s</span>
                <span class="pedido-historico-motivo">%s</span>
            </p>
        ';

        $titulo = "{$this->getStatus()} por {$this->getUsuario()->getNome()} em {$this->getData()->format('d/m/Y H:i')}:";

        return sprintf($html, $titulo, $this->getMotivo());
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
     * @return PedidoHistorico
     */
    public function setId(?int $id): PedidoHistorico
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Pedido
     */
    public function getPedido(): Pedido
    {
        return $this->pedido;
    }

    /**
     * @param Pedido $pedido
     * @return PedidoHistorico
     */
    public function setPedido(Pedido $pedido): PedidoHistorico
    {
        $this->pedido = $pedido;
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
     * @return PedidoHistorico
     */
    public function setUsuario(Usuario $usuario): PedidoHistorico
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
     * @return PedidoHistorico
     */
    public function setData(DateTime $data): PedidoHistorico
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
     * @return PedidoHistorico
     */
    public function setStatus(string $status): PedidoHistorico
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
     * @return PedidoHistorico
     */
    public function setMotivo(string $motivo): PedidoHistorico
    {
        $this->motivo = $motivo;
        return $this;
    }
}