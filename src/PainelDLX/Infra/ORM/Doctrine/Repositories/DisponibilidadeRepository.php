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

namespace Reservas\PainelDLX\Infra\ORM\Doctrine\Repositories;


use DateTime;
use DLX\Infra\ORM\Doctrine\Repositories\EntityRepository;
use Doctrine\DBAL\ParameterType;
use Reservas\PainelDLX\Domain\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Repositories\DisponibilidadeRepositoryInterface;

class DisponibilidadeRepository extends EntityRepository implements DisponibilidadeRepositoryInterface
{

    /**
     * @param DateTime $data_inicial
     * @param DateTime $data_final
     * @param Quarto|null $quarto
     * @return array
     */
    public function findDisponibilidadePorPeriodo(DateTime $data_inicial, DateTime $data_final, ?Quarto $quarto): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d')
            ->from(Disponibilidade::class, 'd')
            ->where('d.dia BETWEEN :data_inicial AND :data_final')
            ->orderBy('d.quarto')
            ->addOrderBy('d.dia')
            ->setParameter(':data_inicial', $data_inicial->format('Y-m-d'), ParameterType::STRING)
            ->setParameter(':data_final', $data_final->format('Y-m-d'), ParameterType::STRING);

        if (!is_null($quarto)) {
            $qb->andWhere('d.quarto = :quarto');
            $qb->setParameter(':quarto', $quarto);
        }

        return $qb->getQuery()->getResult();
    }
}