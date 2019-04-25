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
use Doctrine\Common\Collections\Criteria;
use Reservas\PainelDLX\Domain\Exceptions\ValorMenorQueMinimoQuartoException;

class Quarto extends Entity
{
    /** @var int|null */
    private $id;
    /** @var string */
    private $nome;
    /** @var string|null */
    private $descricao;
    /** @var int */
    private $max_hospedes = 1;
    /** @var int */
    private $qtde;
    /** @var float */
    private $valor_min;
    /** @var int|null */
    private $tamanho_m2;
    /** @var string|null */
    private $link;
    /** @var bool */
    private $publicar = true;
    /** @var bool */
    private $deletado = false;
    /** @var Collection */
    private $dispon;

    /**
     * Quarto constructor.
     * @param string $nome
     * @param int $qtde
     * @param float $valor_min
     */
    public function __construct(string $nome, int $qtde, float $valor_min)
    {
        $this->nome = $nome;
        $this->qtde = $qtde;
        $this->valor_min = $valor_min;
        $this->dispon = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNome();
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
     * @return Quarto
     */
    public function setId(?int $id): Quarto
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
     * @return Quarto
     */
    public function setNome(string $nome): Quarto
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     * @return Quarto
     */
    public function setDescricao(?string $descricao): Quarto
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxHospedes(): int
    {
        return $this->max_hospedes;
    }

    /**
     * @param int $max_hospedes
     * @return Quarto
     */
    public function setMaxHospedes(int $max_hospedes): Quarto
    {
        $this->max_hospedes = $max_hospedes;
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
     * @return Quarto
     */
    public function setQtde(int $qtde): Quarto
    {
        $this->qtde = $qtde;
        return $this;
    }

    /**
     * @return float
     */
    public function getValorMin(): float
    {
        return $this->valor_min;
    }

    /**
     * @param float $valor_min
     * @return Quarto
     */
    public function setValorMin(float $valor_min): Quarto
    {
        $this->valor_min = $valor_min;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTamanhoM2(): ?int
    {
        return $this->tamanho_m2;
    }

    /**
     * @param int|null $tamanho_m2
     * @return Quarto
     */
    public function setTamanhoM2(?int $tamanho_m2): Quarto
    {
        $this->tamanho_m2 = $tamanho_m2;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     * @return Quarto
     */
    public function setLink(?string $link): Quarto
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublicar(): bool
    {
        return $this->publicar;
    }

    /**
     * @param bool $publicar
     * @return Quarto
     */
    public function setPublicar(bool $publicar): Quarto
    {
        $this->publicar = $publicar;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeletado(): bool
    {
        return $this->deletado;
    }

    /**
     * @param bool $deletado
     * @return Quarto
     */
    public function setDeletado(bool $deletado): Quarto
    {
        $this->deletado = $deletado;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getDispon(DateTime $checkin, DateTime $chekcout): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->gte('dia', $checkin));
        $criteria->andWhere(Criteria::expr()->lte('dia', $chekcout));

        return $this->dispon->matching($criteria);
    }

    /**
     * @param DateTime $data
     * @param int $qtde
     * @param array $valores
     * @throws ValorMenorQueMinimoQuartoException
     */
    public function addDispon(DateTime $data, int $qtde, array $valores)
    {
        $dispon = $this->dispon->filter(function (Disponibilidade $dispon) use ($data) {
            return $dispon->getDia()->format('Y-m-d') ===  $data->format('Y-m-d');
        })->first();

        // Criar uma nova disponibilidade
        if (!$dispon) {
            $dispon = new Disponibilidade($this, $data, $qtde);
            $this->dispon->add($dispon);
        } else { // Editar uma dispobibilidade existente
            $dispon->setQtde($qtde);
        }

        foreach ($valores as $qtde => $valor) {
            $dispon->setValorPorQtdePessoas($qtde, $valor);
        }

        return $this;
    }
}