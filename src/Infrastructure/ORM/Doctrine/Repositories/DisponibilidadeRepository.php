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
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Disponibilidade\Entities\DisponibilidadeValor;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;

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
        $disponibilidade_meta_data = $this->_em->getClassMetadata(Disponibilidade::class);
        $tbl_disponibilidade = $disponibilidade_meta_data->getSchemaName() . '.' . $disponibilidade_meta_data->getTableName();

        $disponibilidade_valor_meta_data = $this->_em->getClassMetadata(DisponibilidadeValor::class);
        $tbl_disponibilidade_valor = $disponibilidade_valor_meta_data->getSchemaName() . '.' . $disponibilidade_valor_meta_data->getTableName();

        $conn = $this->_em->getConnection();

        $select_dispon = $conn->createQueryBuilder()
            ->select('disponibilidade_id')
            ->from($tbl_disponibilidade, 'd')
            ->where('quarto_id = :quarto_id')
            ->andWhere('data between :data_inicial and :data_final');

        $update_dispon = $conn->createQueryBuilder()
            ->update($tbl_disponibilidade)
            ->set('quantidade', ':qtde')
            ->set('desconto', ':desconto')
            ->where('quarto_id = :quarto_id')
            ->andWhere('data between :data_inicial and :data_final');

        $delete_dispon_valor = $conn->createQueryBuilder();
        $delete_dispon_valor->delete($tbl_disponibilidade_valor)
            ->where($delete_dispon_valor->expr()->in(
                'disponibilidade_id',
                $select_dispon->getSQL()
            ));

        $insert_dispon_valor = $conn->createQueryBuilder()
            ->insert($tbl_disponibilidade_valor)
            ->setValue('disponibilidade_id', ':disponibilidade_id')
            ->setValue('quantidade_pessoas', ':qtde_pessoas')
            ->setValue('valor', ':valor');

        try {
            $this->_em->beginTransaction();

            // Atualizar as disponibilidades
            $update_dispon
                ->setParameter(':qtde', $qtde, ParameterType::INTEGER)
                ->setParameter(':desconto', $desconto)
                ->setParameter(':quarto_id', $quarto->getId(), ParameterType::INTEGER)
                ->setParameter(':data_inicial', $data_inicial->format('Y-m-d'), ParameterType::STRING)
                ->setParameter(':data_final', $data_final->format('Y-m-d'), ParameterType::STRING)
                ->execute();

            // Excluir os valores atuais
            $delete_dispon_valor
                ->setParameter(':quarto_id', $quarto->getId(), ParameterType::INTEGER)
                ->setParameter(':data_inicial', $data_inicial->format('Y-m-d'))
                ->setParameter(':data_final', $data_final->format('Y-m-d'))
                ->execute();

            $lista_dispon = $select_dispon
                ->setParameter(':quarto_id', $quarto->getId())
                ->setParameter(':data_inicial', $data_inicial->format('Y-m-d'))
                ->setParameter(':data_final', $data_final->format('Y-m-d'))
                ->execute()
                ->fetchAll();

            // Reinserir os valore atualizados
            foreach ($valores as $qtde_pessoas => $valor) {
                foreach ($lista_dispon as $dispon) {
                    $insert_dispon_valor
                        ->setParameter(':disponibilidade_id', $dispon['disponibilidade_id'], ParameterType::INTEGER)
                        ->setParameter(':qtde_pessoas', $qtde_pessoas, ParameterType::INTEGER)
                        ->setParameter(':valor', $valor)
                        ->execute();
                }
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