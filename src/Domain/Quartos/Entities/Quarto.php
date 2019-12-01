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

namespace Reservas\Domain\Quartos\Entities;


use DateTime;
use DLX\Domain\Entities\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use PainelDLX\Domain\Common\Entities\LogRegistroTrait;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Quartos\Contracts\QuartoMidiaCollectionInterface;
use Reservas\Domain\Quartos\Exceptions\QuartoIndisponivelException;
use Reservas\Domain\Quartos\Services\VerificarDisponQuarto;
use Reservas\Infrastructure\ORM\Doctrine\Collections\QuartoMidiaCollection;
use Reservas\Tests\Domain\Quartos\Entities\QuartoTest;

/**
 * Class Quarto
 * @package Reservas\Domain\Quartos\Entities
 * @covers QuartoTest
 */
class Quarto extends Entity
{
    const TABELA_BD = 'Quarto';
    use LogRegistroTrait;

    /** @var int|null */
    private $id;
    /** @var string */
    private $nome;
    /** @var string|null */
    private $descricao;
    /** @var int */
    private $maximo_hospedes = 1;
    /** @var int */
    private $quantidade;
    /** @var float */
    private $valor_minimo;
    /** @var int|null */
    private $tamanho_m2;
    /** @var string|null */
    private $link;
    /** @var bool */
    private $deletado = false;
    /** @var Collection */
    private $disponibilidade;
    /** @var QuartoMidiaCollectionInterface */
    private $midias;

    /**
     * Quarto constructor.
     * @param string $nome
     * @param int $quantidade
     * @param float $valor_minimo
     */
    public function __construct(string $nome, int $quantidade, float $valor_minimo)
    {
        $this->nome = $nome;
        $this->quantidade = $quantidade;
        $this->valor_minimo = $valor_minimo;
        $this->disponibilidade = new ArrayCollection();
        $this->midias = new QuartoMidiaCollection();
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
    public function getMaximoHospedes(): int
    {
        return $this->maximo_hospedes;
    }

    /**
     * @param int $maximo_hospedes
     * @return Quarto
     */
    public function setMaximoHospedes(int $maximo_hospedes): Quarto
    {
        $this->maximo_hospedes = $maximo_hospedes;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    /**
     * @param int $quantidade
     * @return Quarto
     */
    public function setQuantidade(int $quantidade): Quarto
    {
        $this->quantidade = $quantidade;
        return $this;
    }

    /**
     * @return float
     */
    public function getValorMinimo(): float
    {
        return $this->valor_minimo;
    }

    /**
     * @param float $valor_minimo
     * @return Quarto
     */
    public function setValorMinimo(float $valor_minimo): Quarto
    {
        $this->valor_minimo = $valor_minimo;
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
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @return Collection
     */
    public function getDisponibilidade(DateTime $checkin, DateTime $checkout): Collection
    {
        $checkin = (clone $checkin)->setTime(0, 0, 0);
        $checkout = (clone $checkout)->setTime(23, 59, 59);

        // A disponibilidade não é necessária para a data de checkout
        $checkout->modify('-1 day');

        $criteria = Criteria::create();

        $criteria->where(Criteria::expr()->gte('data', $checkin));
        $criteria->andWhere(Criteria::expr()->lte('data', $checkout));

        return $this->disponibilidade->matching($criteria);
    }

    /**
     * @param DateTime $data
     * @param int $quantidade
     * @param array $valores
     * @param float $desconto
     * @return Quarto
     */
    public function addDisponibilidade(DateTime $data, int $quantidade, array $valores, float $desconto = 0.): self
    {
        /** @var Disponibilidade $disponibilidade */
        $disponibilidade = $this->disponibilidade->filter(function (Disponibilidade $dispon) use ($data) {
            return $dispon->getData()->format('Y-m-d') ===  $data->format('Y-m-d');
        })->first();

        // Criar uma nova disponibilidade
        if (!$disponibilidade) {
            $disponibilidade = new Disponibilidade($this, $data, $quantidade, $desconto);
            $this->disponibilidade->add($disponibilidade);
        } else { // Editar uma dispobibilidade existente
            $disponibilidade->setQuantidade($quantidade);
            $disponibilidade->setDesconto($desconto);
        }

        foreach ($valores as $quantidade => $valor) {
            $disponibilidade->setValorPorQtdePessoas($quantidade, $valor);
        }

        return $this;
    }

    /**
     * @return QuartoMidiaCollectionInterface
     */
    public function getMidias(): Collection
    {
        if (!$this->midias instanceof QuartoMidiaCollectionInterface) {
            $this->midias = new QuartoMidiaCollection($this->midias->toArray());
        }

        return $this->midias;
    }

    /**
     * @param string $arquivo
     * @param string|null $mini
     * @return Quarto
     */
    public function addMidia(string $arquivo, ?string $mini = null): self
    {
        $midia = new QuartoMidia($arquivo);
        $midia->setQuarto($this);
        $midia->setMiniatura($mini);

        if ($this->midias instanceof PersistentCollection) {
            $this->midias->add($midia);
        } else {
            $this->midias->addMidia($midia);
        }

        return $this;
    }

    /**
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @return bool
     * @throws QuartoIndisponivelException
     */
    public function isDisponivelPeriodo(DateTime $checkin, DateTime $checkout): bool
    {
        return (new VerificarDisponQuarto())->executar($this, $checkin, $checkout);
    }
}