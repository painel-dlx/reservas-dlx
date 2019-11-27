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

namespace Reservas\Infrastructure\ORM\Doctrine\Repositories;


use DateTime;
use DLX\Infrastructure\ORM\Doctrine\Repositories\EntityRepository;
use Doctrine\DBAL\ParameterType;
use Exception;
use Reservas\Domain\Reservas\Repositories\ReservaRepositoryInterface;

class ReservaRepository extends EntityRepository implements ReservaRepositoryInterface
{

    /**
     * @param string|null $s_data_inicial
     * @param string|null $s_data_final
     * @param array $criteria
     * @param array $order_by
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws Exception
     */
    public function findReservasPorPeriodo(
        ?string $s_data_inicial,
        ?string $s_data_final,
        array $criteria = [],
        array $order_by = [],
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $data_inicial = new DateTime($s_data_inicial);

        // Se a data inicial não foi informada, iniciar com as reservas de 1 semana atrás
        if (empty($s_data_inicial)) {
            $data_inicial->modify('-1 week');
        }

        $data_final = empty($s_data_final) ? (clone $data_inicial)->modify('+1 month') : new DateTime($s_data_final);

        $qb = $this->createQueryBuilder('r')
            ->where('r.checkin >= :data_inicial')
            ->andWhere('r.checkin <= :data_final')
            ->setParameter(':data_inicial', $data_inicial->format('Y-m-d'), ParameterType::STRING)
            ->setParameter(':data_final', $data_final->format('Y-m-d'), ParameterType::STRING)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        foreach ($criteria as $campo => $valor) {
            $qb->andWhere($qb->expr()->eq($campo, $valor));
        }

        foreach ($order_by as $sort => $order) {
            $qb->addOrderBy($sort, $order);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }
}