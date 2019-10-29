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
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use PainelDLX\Domain\Common\Entities\LogRegistro;
use PainelDLX\Infrastructure\ORM\Doctrine\Repositories\AbstractPainelDLXRepository;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;

class PedidoRepository extends AbstractPainelDLXRepository implements PedidoRepositoryInterface
{

    /**
     * Quantidade de pedidos no status solicitado com filtro adicional (e opcional) de data
     * @param string $status Status desejado
     * @param DateTime|null $data_inicial
     * @param DateTime|null $data_final
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getQuantidadePedidosPorStatus(string $status, ?DateTime $data_inicial, ?DateTime $data_final): int
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(p.id)')
            ->innerJoin(LogRegistro::class, 'lr', Join::WITH, 'lr.tabela = :tabela and lr.registro_id = p.id')
            ->where('p.status = :status')
            ->setParameter(':tabela', 'dlx_reservas_pedidos')
            ->setParameter(':status', $status);

        if (!is_null($data_inicial)) {
            $data_inicial->setTime(0, 0,  0);
            $qb->andWhere('p.data >= :data_inicial');
            $qb->setParameter(':data_inicial', $data_inicial->format('Y-m-d H:i:s'));
        }

        if (!is_null($data_final)) {
            $data_final->setTime(23, 59, 59);
            $qb->andWhere('p.data <= :data_final');
            $qb->setParameter(':data_final', $data_final->format('Y-m-d H:i:s'));
        }

        $sql = $qb->getQuery();

        return (int)current($sql->getSingleResult());
    }
}