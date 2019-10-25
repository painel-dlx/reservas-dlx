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


use CPF\CPF;
use DateTime;
use DLX\Domain\Entities\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use PainelDLX\Domain\Common\Entities\LogRegistroTrait;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Reservas\Entities\ReservaHistorico;
use Reservas\Domain\Reservas\Entities\VisualizacaoCpf;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Validators\ReservaValidator;
use Reservas\Domain\Reservas\Validators\ReservaValidatorsEnum;

/**
 * Class Reserva
 * @package Reservas\Domain\Entities
 * @covers ReservaTest
 */
class Reserva extends Entity
{
    const TABELA_BD = 'dlx_reservas_cadastro';
    use LogRegistroTrait;

    const STATUS_PENDENTE = 'Pendente';
    const STATUS_CONFIRMADA = 'Confirmada';
    const STATUS_CANCELADA = 'Cancelada';

    /** @var int|null */
    private $id;
    /** @var Pedido|null */
    private $pedido;
    /** @var Quarto */
    private $quarto;
    /** @var string */
    private $hospede;
    /** @var CPF */
    private $cpf;
    /** @var string */
    private $telefone;
    /** @var string */
    private $email;
    /** @var DateTime */
    private $checkin;
    /** @var DateTime */
    private $checkout;
    /** @var int */
    private $adultos;
    /** @var int */
    private $criancas = 0;
    /** @var float */
    private $valor;
    /** @var string */
    private $status = 'Pendente';
    /** @var string|null */
    private $origem;
    /** @var Collection */
    private $historico;
    /** @var Collection */
    private $visualizacoes_cpf;

    /**
     * Reserva constructor.
     * @param Quarto $quarto
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @param int $adultos
     */
    public function __construct(Quarto $quarto, DateTime $checkin, DateTime $checkout, int $adultos)
    {
        $this->quarto = $quarto;
        $this->checkin = $checkin;
        $this->checkout = $checkout;
        $this->adultos = $adultos;
        $this->historico = new ArrayCollection();
        $this->visualizacoes_cpf = new ArrayCollection();
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
     * @return Reserva
     */
    public function setId(?int $id): Reserva
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Pedido|null
     */
    public function getPedido(): ?Pedido
    {
        return $this->pedido;
    }

    /**
     * @param Pedido|null $pedido
     * @return Reserva
     */
    public function setPedido(?Pedido $pedido): Reserva
    {
        $this->pedido = $pedido;
        return $this;
    }

    /**
     * @return Quarto
     */
    public function getQuarto(): Quarto
    {
        return $this->quarto;
    }

    /**
     * @param Quarto $quarto
     * @return Reserva
     */
    public function setQuarto(Quarto $quarto): Reserva
    {
        $this->quarto = $quarto;
        return $this;
    }

    /**
     * @return string
     */
    public function getHospede(): string
    {
        return $this->hospede;
    }

    /**
     * @param string $hospede
     * @return Reserva
     */
    public function setHospede(string $hospede): Reserva
    {
        $this->hospede = $hospede;
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
     * @return Reserva
     */
    public function setCpf(CPF $cpf): Reserva
    {
        $this->cpf = $cpf;
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
     * @return Reserva
     */
    public function setTelefone(string $telefone): Reserva
    {
        $this->telefone = $telefone;
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
     * @return Reserva
     */
    public function setEmail(string $email): Reserva
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCheckin(): DateTime
    {
        return $this->checkin;
    }

    /**
     * @param DateTime $checkin
     * @return Reserva
     */
    public function setCheckin(DateTime $checkin): Reserva
    {
        $this->checkin = $checkin;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCheckout(): DateTime
    {
        return $this->checkout;
    }

    /**
     * @param DateTime $checkout
     * @return Reserva
     */
    public function setCheckout(DateTime $checkout): Reserva
    {
        $this->checkout = $checkout;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdultos(): int
    {
        return $this->adultos;
    }

    /**
     * @param int $adultos
     * @return Reserva
     */
    public function setAdultos(int $adultos): Reserva
    {
        $this->adultos = $adultos;
        return $this;
    }

    /**
     * @return int
     */
    public function getCriancas(): int
    {
        return $this->criancas;
    }

    /**
     * @param int $criancas
     * @return Reserva
     */
    public function setCriancas(int $criancas): Reserva
    {
        $this->criancas = $criancas;
        return $this;
    }

    /**
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }

    /**
     * @param float $valor
     * @return Reserva
     */
    public function setValor(float $valor): Reserva
    {
        $this->valor = $valor;
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
     * @return Reserva
     */
    public function setStatus(string $status): Reserva
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrigem(): ?string
    {
        return $this->origem;
    }

    /**
     * @param string|null $origem
     * @return Reserva
     */
    public function setOrigem(?string $origem): Reserva
    {
        $this->origem = $origem;
        return $this;
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
     * @return Reserva
     */
    public function addHistorico(string $status, string $motivo, Usuario $usuario): self
    {
        $historico = new ReservaHistorico($status, $motivo);
        $historico->setReserva($this);
        $historico->setUsuario($usuario);

        $this->historico->add($historico);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getVisualizacoesCpf(): Collection
    {
        return $this->visualizacoes_cpf;
    }

    /**
     * Adicionar visualização de CPF
     * @param Usuario $usuario
     * @return Reserva
     */
    public function addVisualizacaoCpf(Usuario $usuario): self
    {
        $vis_cpf = new VisualizacaoCpf($this, $usuario);
        $this->visualizacoes_cpf->add($vis_cpf);

        return $this;
    }

    /**
     * Verifica se a reserva está pendente
     * @return bool
     */
    public function isPendente(): bool
    {
        return $this->getStatus() === self::STATUS_PENDENTE;
    }

    /**
     * Verifica se a reserva foi cancelada
     * @return bool
     */
    public function isCancelada(): bool
    {
        return $this->getStatus() === self::STATUS_CANCELADA;
    }

    /**
     * Verifica se a reserva foi confirmada
     * @return bool
     */
    public function isConfirmada(): bool
    {
        return $this->getStatus() === self::STATUS_CONFIRMADA;
    }

    /**
     * Calcula o valor da reserva e seta na propriedade valor
     * @return Reserva
     */
    public function calcularValor(): self
    {
        $valor_reserva = 0;
        $total_hospedes = $this->getTotalHospedes();

        $dispon_quarto = $this->getQuarto()->getDispon($this->getCheckin(), $this->getCheckout());
        $dispon_quarto->map(function (Disponibilidade $dispon) use (&$valor_reserva, $total_hospedes) {
            $valor_reserva += (float)$dispon->getValorPorQtdePessoas($total_hospedes);
        });

        $this->setValor($valor_reserva);
        return $this;
    }

    /**
     * Conta a quantidade total de hóspedes do quarto (adultos + crianças)
     * @return int
     */
    public function getTotalHospedes(): int
    {
        return $this->getAdultos() + $this->getCriancas();
    }

    /**
     * Seta a reserva como confirmada.
     * @param string $motivo
     * @param Usuario $usuario
     * @return Reserva
     */
    public function confirmada(string $motivo, Usuario $usuario): self
    {
        $validator = new ReservaValidator(ReservaValidatorsEnum::CONFIRMAR);
        $validator->validar($this);

        $this->setStatus(self::STATUS_CONFIRMADA);
        $this->addHistorico(self::STATUS_CONFIRMADA, $motivo, $usuario);

        // Retirar a disponibilidade do quarto
        $dispon_quarto = $this->getQuarto()->getDispon($this->getCheckin(), $this->getCheckout());
        $dispon_quarto->map(function (Disponibilidade $dispon) {
            $dispon->setQtde($dispon->getQtde() - 1);
        });

        return $this;
    }

    /**
     * Seta a reserva como cancelada.
     * @param string $motivo
     * @return Reserva
     */
    public function cancelada(string $motivo, Usuario $usuario): self
    {
        $validator = new ReservaValidator(ReservaValidatorsEnum::CANCELAR);
        $validator->validar($this);

        $this->setStatus(self::STATUS_CANCELADA);
        $this->addHistorico(self::STATUS_CANCELADA, $motivo, $usuario);
        return $this;
    }

    /**
     * Verifica se um determinado usuário pode visualizar o CPF completo
     * @param Usuario $usuario
     * @return bool
     */
    public function podeVisualizarCpfCompleto(Usuario $usuario): bool
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->eq('usuario', $usuario));

        $qtde_vis = $this->visualizacoes_cpf->matching($criteria)->count();

        return $qtde_vis < 3;
    }
}