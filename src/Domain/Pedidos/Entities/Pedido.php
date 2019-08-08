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


use CPF\CPF;
use DateTime;
use DLX\Domain\Entities\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use PainelDLX\Domain\Common\Entities\LogRegistroTrait;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Pedidos\Validators\PedidoValidator;
use Reservas\Domain\Pedidos\Validators\PedidoValidatorEnum;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use stdClass;

/**
 * Class Pedido
 * @package Reservas\Domain\Pedidos\Entities
 * @covers PedidoTest
 */
class Pedido extends Entity
{
    const TABELA_BD = 'dlx_reservas_pedidos';
    use LogRegistroTrait;

    const STATUS_PENDENTE = 'Pendente';
    const STATUS_PAGO = 'Pago';
    const STATUS_CANCELADO = 'Cancelado';

    /** @var int|null */
    private $id;
    /** @var string */
    private $nome;
    /** @var CPF */
    private $cpf;
    /** @var string */
    private $email;
    /** @var string */
    private $telefone;
    /** @var float */
    private $valor_total;
    /** @var string */
    private $forma_pgto = 'digitada';
    /** @var string */
    private $status = 'Pendente';
    /** @var PedidoCartaoCredito|null */
    private $cartao_credito;
    /** @var PedidoEndereco|null */
    private $endereco;
    /** @var Collection */
    private $itens;
    /** @var Collection */
    private $reservas;
    /** @var Collection */
    private $historico;

    /**
     * Pedido constructor.
     */
    public function __construct()
    {
        $this->itens = new ArrayCollection();
        $this->reservas = new ArrayCollection();
        $this->historico = new ArrayCollection();
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
     * @return Pedido
     */
    public function setId(?int $id): Pedido
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return Pedido
     */
    public function setNome(string $nome): Pedido
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return CPF
     */
    public function getCpf(): CPF
    {
        return $this->cpf;
    }

    /**
     * @param CPF $cpf
     * @return Pedido
     */
    public function setCpf(CPF $cpf): Pedido
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Pedido
     */
    public function setEmail(string $email): Pedido
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getTelefone(): string
    {
        return $this->telefone;
    }

    /**
     * @param string $telefone
     * @return Pedido
     */
    public function setTelefone(string $telefone): Pedido
    {
        $this->telefone = $telefone;
        return $this;
    }

    /**
     * @return float
     */
    public function getValorTotal(): float
    {
        return $this->valor_total;
    }

    /**
     * @param float $valor_total
     * @return Pedido
     */
    public function setValorTotal(float $valor_total): Pedido
    {
        $this->valor_total = $valor_total;
        return $this;
    }

    /**
     * Adicionar um valor ao valor total do pedido
     * @param float $valor
     * @return Pedido
     */
    public function addValor(float $valor): self
    {
        $this->valor_total += $valor;
        return $this;
    }

    /**
     * Excluir um determinado valor do total do pedido
     * @param float $valor
     * @return Pedido
     */
    public function subtrairValor(float $valor): self
    {
        $this->valor_total -= $valor;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormaPgto(): string
    {
        return $this->forma_pgto;
    }

    /**
     * @param string $forma_pgto
     * @return Pedido
     */
    public function setFormaPgto(string $forma_pgto): Pedido
    {
        $this->forma_pgto = $forma_pgto;
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
     * @return Pedido
     */
    public function setStatus(string $status): Pedido
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return PedidoCartaoCredito|null
     */
    public function getCartaoCredito(): ?PedidoCartaoCredito
    {
        return $this->cartao_credito;
    }

    /**
     * @param PedidoCartaoCredito|null $cartao_credito
     * @return Pedido
     */
    public function setCartaoCredito(?PedidoCartaoCredito $cartao_credito): Pedido
    {
        $this->cartao_credito = $cartao_credito;
        return $this;
    }

    /**
     * @return PedidoEndereco|null
     */
    public function getEndereco(): ?PedidoEndereco
    {
        return $this->endereco;
    }

    /**
     * @param PedidoEndereco|null $endereco
     * @return Pedido
     */
    public function setEndereco(?PedidoEndereco $endereco): Pedido
    {
        $this->endereco = $endereco;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getItens(): Collection
    {
        return $this->itens;
    }

    /**
     * @param Quarto $quarto
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @param int $quantidade
     * @param int $adultos
     * @param int $criancas
     * @return Pedido
     */
    public function addItem(
        Quarto $quarto,
        DateTime $checkin,
        DateTime $checkout,
        int $quantidade,
        int $adultos,
        int $criancas
    ): self
    {
        $pedido_item = new PedidoItem(
            $this,
            $quarto,
            $checkin,
            $checkout,
            $quantidade,
            $adultos,
            $criancas
        );

        $this->addValor($pedido_item->getValorTotal());

        $this->itens->add($pedido_item);
        return $this;
    }

    /**
     * @param int $indice
     * @return Pedido
     */
    public function retirarItem(int $indice): self
    {
        /** @var PedidoItem $item_retirar */
        $item_retirar = $this->itens->get($indice);
        $valor_item = $item_retirar->getValorTotal();

        $this->itens->removeElement($item_retirar);
        $this->subtrairValor($valor_item);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReservas(): Collection
    {
        return $this->reservas;
    }

    /**
     * @param Reserva $reserva
     * @return Pedido
     */
    public function addReserva(Reserva $reserva): self
    {
        $reserva->setPedido($this);

        $this->reservas->add($reserva);
        return $this;
    }

    /**
     * Verifica se o pedido possui reservas vinculadass
     * @return bool
     */
    public function hasReservas(): bool
    {
        return $this->getReservas()->count() > 0;
    }

    /**
     * @return Collection
     */
    public function getHistorico(): Collection
    {
        return $this->historico;
    }

    /**
     * @param string $status
     * @param string $motivo
     * @param Usuario $usuario
     * @return Pedido
     * @throws Exception
     */
    public function addHistorico(string $status, string $motivo, Usuario $usuario): self
    {
        $historico = new PedidoHistorico($status, $motivo);
        $historico->setPedido($this);
        $historico->setUsuario($usuario);

        $this->historico->add($historico);
        return $this;
    }

    /**
     * Verifica se o pedido está pendente
     * @return bool
     */
    public function isPendente(): bool
    {
        return $this->getStatus() === self::STATUS_PENDENTE;
    }

    /**
     * Verifica se o pedido está pago / confirmado
     * @return bool
     */
    public function isPago(): bool
    {
        return $this->getStatus() === self::STATUS_PAGO;
    }

    /**
     * Verifica se o pedido está cancelado
     * @return bool
     */
    public function isCancelado(): bool
    {
        return $this->getStatus() === self::STATUS_CANCELADO;
    }

    /**
     * Informar que o pedido foi pago
     * @param string $motivo
     * @param Usuario $usuario
     * @return Pedido
     * @throws Exception
     */
    public function pago(string $motivo, Usuario $usuario): self
    {
        $validator = new PedidoValidator(PedidoValidatorEnum::CONFIRMAR);
        $validator->validar($this);

        // Confirmar todas as reservas
        $this->getReservas()->map(function (Reserva $reserva) use ($motivo, $usuario) {
            $reserva->confirmada($motivo, $usuario);
        });

        $this->addHistorico(self::STATUS_PAGO, $motivo, $usuario);
        $this->setStatus(self::STATUS_PAGO);
        return $this;
    }

    /**
     * Informar que o pedido foi cancelado. Ao cancelar o pedido, todas as reservas devem
     * ser canceladas também
     * @param string $motivo
     * @param Usuario $usuario
     * @return Pedido
     * @throws Exception
     */
    public function cancelado(string $motivo, Usuario $usuario): self
    {
        $validator = new PedidoValidator(PedidoValidatorEnum::CANCELAR);
        $validator->validar($this);

        $this->addHistorico(self::STATUS_CANCELADO, $motivo, $usuario);
        $this->setStatus(self::STATUS_CANCELADO);

        // Cancelar todas as reservas
        $this->getReservas()->map(function (Reserva $reserva) use ($motivo, $usuario) {
            $reserva->cancelada($motivo, $usuario);
        });

        return $this;
    }
}