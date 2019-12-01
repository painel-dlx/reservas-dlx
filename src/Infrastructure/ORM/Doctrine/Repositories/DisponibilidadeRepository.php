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
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Exception;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;
use Throwable;

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
            ->innerJoin('d.quarto', 'q')
            ->where('d.data BETWEEN :data_inicial AND :data_final')
            ->andWhere('q.deletado = 0')
            ->orderBy('d.quarto')
            ->addOrderBy('d.data')
            ->setParameter(':data_inicial', $data_inicial->format('Y-m-d'), ParameterType::STRING)
            ->setParameter(':data_final', $data_final->format('Y-m-d'), ParameterType::STRING);

        if (!is_null($quarto)) {
            $qb->andWhere('d.quarto = :quarto');
            $qb->setParameter(':quarto', $quarto);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Salvar disponibilidade por período
     * @param DateTime $data_inicial
     * @param DateTime $data_final
     * @param Quarto $quarto
     * @param int $qtde
     * @param array $valores
     * @param float $desconto
     * @return bool
     * @throws Exception
     */
    public function salvarDisponPorPeriodo(DateTime $data_inicial, DateTime $data_final, Quarto $quarto, int $qtde, array $valores, float $desconto = 0.): bool
    {
        $update_dispon = '
            update
                reservas.Disponibilidade
            set
                quantidade = :qtde,
                desconto = :desconto
            where
                quarto_id = :quarto_id
                and data between :data_inicial and :data_final
        ';

        $delete_dispon_valor = '
            delete
                v
            from
                reservas.Disponibilidade d 
            inner join 
                reservas.DisponibilidadeValor v on v.disponibilidade_id = d.disponibilidade_id
            where 
                d.quarto_id = :quarto_id
                and d.data between :data_inicial and :data_final
        ';

        $insert_dispon_valor = '
            insert into reservas.DisponibilidadeValor (disponibilidade_id, quantidade_pessoas, valor)
                select 
                    disponibilidade_id,
                    :qtde_pessoas,
                    :valor
                from
                    reservas.Disponibilidade
                where
                    quarto_id = :quarto_id
                    and data between :data_inicial and :data_final
        ';

        try {
            $this->_em->beginTransaction();

            $con = $this->_em->getConnection();

            // Atualizar as disponibilidades
            $sql = $con->prepare($update_dispon);
            $sql->bindValue(':qtde', $qtde, ParameterType::INTEGER);
            $sql->bindValue(':desconto', $desconto);
            $sql->bindValue(':quarto_id', $quarto->getId(), ParameterType::INTEGER);
            $sql->bindValue(':data_inicial', $data_inicial->format('Y-m-d'), ParameterType::STRING);
            $sql->bindValue(':data_final', $data_final->format('Y-m-d'), ParameterType::STRING);
            $sql->execute();

            // Excluir os valores atuais
            $sql = $con->prepare($delete_dispon_valor);
            $sql->bindValue(':quarto_id', $quarto->getId(), ParameterType::INTEGER);
            $sql->bindValue(':data_inicial', $data_inicial->format('Y-m-d'));
            $sql->bindValue(':data_final', $data_final->format('Y-m-d'));
            $sql->execute();

            // Reinserir os valore atualizados
            $sql = $con->prepare($insert_dispon_valor);

            foreach ($valores as $qtde_pessoas => $valor) {
                $sql->bindValue(':qtde_pessoas', $qtde_pessoas, ParameterType::INTEGER);
                $sql->bindValue(':valor', $valor);
                $sql->bindValue(':quarto_id', $quarto->getId(), ParameterType::INTEGER);
                $sql->bindValue(':data_inicial', $data_inicial->format('Y-m-d'));
                $sql->bindValue(':data_final', $data_final->format('Y-m-d'));
                $sql->execute();
            }

            $this->_em->commit();
        } catch (Exception $e) {
            $this->_em->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * Obter a última data com disponibilidade criada
     * @return DateTime
     * @throws DBALException
     * @throws Exception
     */
    public function ultimaDataDisponibilidade(): DateTime
    {
        $query = 'select max(data) from reservas.Disponibilidade';
        $sql = $this->_em->getConnection()->executeQuery($query);
        $data = $sql->fetchColumn();

        return new DateTime($data);
    }
}