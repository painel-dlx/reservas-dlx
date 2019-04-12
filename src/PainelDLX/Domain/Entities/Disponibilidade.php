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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Reservas\PainelDLX\Domain\Exceptions\ValorMenorQueMinimoQuartoException;
use Reservas\PainelDLX\Domain\Validators\Disponibilidade\ValidarValoresDisponValidator;

/**
 * Class Disponibilidade
 * @package Reservas\PainelDLX\Domain\Entities
 * @covers DisponibilidadeTest
 */
class Disponibilidade extends Entity
{
    /** @var int|null */
    private $id;
    /** @var DateTime */
    private $dia;
    /** @var Quarto */
    private $quarto;
    /** @var int */
    private $qtde;
    /** @var Collection */
    private $valores;

    /**
     * Disponibilidade constructor.
     * @param DateTime $dia
     * @param Quarto $quarto
     * @param float $qtde
     */
    public function __construct(Quarto $quarto, DateTime $dia, float $qtde)
    {
        $this->dia = $dia;
        $this->quarto = $quarto;
        $this->qtde = $qtde;
        $this->valores = new ArrayCollection();
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
     * @return Disponibilidade
     */
    public function setId(?int $id): Disponibilidade
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDia(): DateTime
    {
        return $this->dia;
    }

    /**
     * @param DateTime $dia
     * @return Disponibilidade
     */
    public function setDia(DateTime $dia): Disponibilidade
    {
        $this->dia = $dia;
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
     * @return Disponibilidade
     */
    public function setQuarto(Quarto $quarto): Disponibilidade
    {
        $this->quarto = $quarto;
        return $this;
    }

    /**
     * @return int
     */
    public function getQtde(): int
    {
        return $this->qtde;
    }

    /**
     * @param int $qtde
     * @return Disponibilidade
     */
    public function setQtde(int $qtde): Disponibilidade
    {
        $this->qtde = $qtde;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getValores(): Collection
    {
        return $this->valores;
    }

    /**
     * Adicionar um valor específico para uma quantidade de pesoas
     * @param int $qtde_pessoas
     * @param float $valor
     * @return Disponibilidade
     * @throws ValorMenorQueMinimoQuartoException
     */
    public function addValor(int $qtde_pessoas, float $valor): self
    {
        $dispon_valor = new DisponValor($qtde_pessoas, $valor);
        $dispon_valor->setDispon($this);

        $this->valores->add($dispon_valor);
        return $this;
    }

    /**
     * Obter o valor específico do quarto pela quantidade de pessoas.
     * @param int $qtde_pessoas
     * @return float|null
     */
    public function getValorPorQtdePessoas(int $qtde_pessoas): ?float
    {
        /** @var DisponValor|null $dispon_valor */
        $dispon_valor = $this->getValores()->filter(function (DisponValor $dispon_valor) use ($qtde_pessoas) {
            return $dispon_valor->getQtdePessoas() === $qtde_pessoas;
        })->first();

        return $dispon_valor ? $dispon_valor->getValor() : null;
    }

    /**
     * Altera o valor de acorodo com a qtde de pessoas.
     * @param int $qtde_pessoas
     * @param float $valor
     * @return Disponibilidade
     * @throws ValorMenorQueMinimoQuartoException
     */
    public function setValorPorQtdePessoas(int $qtde_pessoas, float $valor): self
    {
        $dispon_valor = $this->getValores()->filter(function (DisponValor $dispon_valor) use ($qtde_pessoas) {
            return $dispon_valor->getQtdePessoas() === $qtde_pessoas;
        })->first();

        if ($dispon_valor instanceof DisponValor) {
            $dispon_valor->setValor($valor);
        } else {
            $this->addValor($qtde_pessoas, $valor);
        }

        return $this;
    }

    /**
     * Verifica se o valor para determinada quantidade de pessoas está configurado.
     * @param int $qtde_pessoas
     * @return bool
     */
    public function hasValorPorQtdePessoas(int $qtde_pessoas): bool
    {
        return $this->getValores()->exists(function ($key, DisponValor $dispon_valor) use ($qtde_pessoas) {
            return $dispon_valor->getQtdePessoas() === $qtde_pessoas && $dispon_valor->getValor() > 0;
        });
    }

    /**
     * Verifica se todas as regras para ser publicado foram satisfeitas
     * @return bool
     */
    public function isPublicado(): bool
    {
        $max_hospedes = $this->getQuarto()->getMaxHospedes();
        $valor_min = $this->getQuarto()->getValorMin();

        if ($this->getValores()->count() > 0) {
            for ($i = 1; $i <= $max_hospedes; $i++) {
                if (!$this->hasValorPorQtdePessoas($i)) {
                    return false;
                }
            }

            $has_valor_invalido = $this->getValores()->exists(function ($key, DisponValor $dispon_valor) use ($valor_min) {
                return $valor_min > $dispon_valor->getValor();
            });

            return $this->getQtde() > 0 && !$has_valor_invalido;
        }

        return false;
    }
}