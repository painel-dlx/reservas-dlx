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

namespace Reservas\PainelDLX\Tests\UseCases\Quartos\GerarDisponibilidadesQuarto;

use DateTime;
use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\ORMException;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\PainelDLX\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GerarDisponibilidadesQuartoCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Quartos\GerarDisponibilidadesQuarto
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommandHandler
 */
class GerarDisponibilidadesQuartoCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return GerarDisponibilidadesQuartoCommandHandler
     * @throws ORMException
     */
    public function test__construct(): GerarDisponibilidadesQuartoCommandHandler
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        $dispon_repository = $this->createMock(DisponibilidadeRepositoryInterface::class);
        $dispon_repository->method('ultimaDataDisponibilidade')->willReturn((new DateTime())->modify('+10 days'));

        /** @var DisponibilidadeRepositoryInterface $dispon_repository */

        $handler = new GerarDisponibilidadesQuartoCommandHandler($quarto_repository, $dispon_repository);

        $this->assertInstanceOf(GerarDisponibilidadesQuartoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param GerarDisponibilidadesQuartoCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     * @throws ORMException
     * @throws DBALException
     * @throws \Reservas\PainelDLX\Domain\Quartos\Exceptions\QuartoIndisponivelException
     */
    public function test_Handle_deve_criar_disponibilidade_para_determinado_quarto(GerarDisponibilidadesQuartoCommandHandler $handler)
    {
        $query = 'insert into dlx_reservas_quartos (quarto_nome, quarto_maxhospedes, quarto_qtde, quarto_valor_min, quarto_tamanho_m2, quarto_link) values (:nome, :maxhospedes, :qtde, :valor_min, :tamanho_m2, :link)';
        $con = EntityManagerX::getInstance()->getConnection();

        $sql = $con->prepare($query);
        $sql->bindValue(':nome', 'Quarto Teste', ParameterType::STRING);
        $sql->bindValue(':maxhospedes', 10, ParameterType::INTEGER);
        $sql->bindValue(':qtde', 10, ParameterType::INTEGER);
        $sql->bindValue(':valor_min', 12.34, ParameterType::INTEGER);
        $sql->bindValue(':tamanho_m2', 10, ParameterType::INTEGER);
        $sql->bindValue(':link', '/teste/unitario', ParameterType::STRING);
        $sql->execute();

        $id = $con->lastInsertId();

        /** @var Quarto $quarto */
        $quarto = EntityManagerX::getReference(Quarto::class, $id);
        $dt_inicial = new DateTime();
        $dt_final = (clone $dt_inicial)->modify('+10 days');

        $command = new GerarDisponibilidadesQuartoCommand($quarto);
        $handler->handle($command);

        $query = 'select * from dlx_reservas_disponibilidade where dispon_quarto = :quarto and dispon_dia between :dt_inicial and :dt_final';
        $sql = $con->prepare($query);
        $sql->bindValue(':quarto', $id, ParameterType::INTEGER);
        $sql->bindValue(':dt_inicial', $dt_inicial->format('Y-m-d'), ParameterType::STRING);
        $sql->bindValue(':dt_final', $dt_final->format('Y-m-d'), ParameterType::STRING);
        $sql->execute();

        $qtde = $sql->rowCount();

        $this->assertTrue($qtde > 0);
    }
}
