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

namespace Reservas\Website\Infra\ORM\Doctrine\Repositories;


use DateTime;
use DLX\Infra\ORM\Doctrine\Repositories\EntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\Expr\Join;
use Reservas\Website\Domain\Entities\Disponibilidade;
use Reservas\Website\Domain\Entities\Quarto;
use Reservas\Website\Domain\Repositories\QuartoRepositoryInterface;

class QuartoRepository extends EntityRepository implements QuartoRepositoryInterface
{

    /**
     * Obter quartos disponíveis.
     * @param DateTime $data_entrada
     * @param DateTime $data_saida
     * @param int $qtde_adultos
     * @param int $qtde_criancas
     * @param int $qtde_quartos
     * @return array
     */
    public function findQuartosDisponiveis(
        DateTime $data_entrada,
        DateTime $data_saida,
        int $qtde_adultos = 2,
        int $qtde_criancas = 0,
        int $qtde_quartos = 1
    ): array {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['q'])
            ->from(Quarto::class, 'q')
            ->innerJoin(Disponibilidade::class, 'd', Join::WITH, 'd.quarto = q.id')
            ->where('q.max_hospedes >= :total_hospedes')
            ->andWhere('d.dia between :data_entrada and :data_saida')
            ->andWhere('d.qtde > 0')
            ->setParameter(':total_hospedes', $qtde_adultos + $qtde_criancas, ParameterType::INTEGER)
            ->setParameter(':data_entrada', $data_entrada->format('Y-m-d'))
            ->setParameter(':data_saida', $data_saida->format('Y-m-d'));

        $lista = $qb->getQuery()->getResult();

        return array_filter($lista, function () {
            return true;
        });
    }
}