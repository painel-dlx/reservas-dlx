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
use Exception;
use Reservas\PainelDLX\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;

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
            ->where('d.dia BETWEEN :data_inicial AND :data_final')
            ->andWhere('q.publicar = 1')
            ->andWhere('q.deletado = 0')
            ->orderBy('d.quarto')
            ->addOrderBy('d.dia')
            ->setParameter(':data_inicial', $data_inicial->format('Y-m-d'), ParameterType::STRING)
            ->setParameter(':data_final', $data_final->format('Y-m-d'), ParameterType::STRING);

        if (!is_null($quarto)) {
            $qb->andWhere('q.quarto = :quarto');
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
     * @return bool
     * @throws \Throwable
     */
    public function salvarDisponPorPeriodo(DateTime $data_inicial, DateTime $data_final, Quarto $quarto, int $qtde, array $valores): bool
    {
        $update_dispon = '
            update
                dlx_reservas_disponibilidade
            set
                dispon_qtde = :qtde
            where
                dispon_quarto = :quarto_id
                and dispon_dia between :data_inicial and :data_final
        ';

        $delete_dispon_valor = '
            delete
                v
            from
                dlx_reservas_disponibilidade d 
            inner join 
                reservas_disponibilidade_valores v on v.dispon_id = d.dispon_id
            where 
                d.dispon_quarto = :quarto_id
                and d.dispon_dia between :data_inicial and :data_final
        ';

        $insert_dispon_valor = '
            insert into reservas_disponibilidade_valores (dispon_id, qtde_pessoas, valor)
                select 
                    dispon_id,
                    :qtde_pessoas,
                    :valor
                from
                    dlx_reservas_disponibilidade
                where
                    dispon_quarto = :quarto_id
                    and dispon_dia between :data_inicial and :data_final
        ';

        try {
            $this->_em->beginTransaction();

            $con = $this->_em->getConnection();

            // Atualizar as disponibilidades
            $sql = $con->prepare($update_dispon);
            $sql->bindValue(':qtde', $qtde, ParameterType::INTEGER);
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
}