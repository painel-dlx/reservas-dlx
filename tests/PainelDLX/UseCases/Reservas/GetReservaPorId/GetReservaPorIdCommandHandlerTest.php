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

namespace Reservas\PainelDLX\Tests\UseCases\Reservas\GetReservaPorId;


use DLX\Infra\EntityManagerX;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Repositories\ReservaRepositoryInterface;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommand;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GetReservaPorIdCommandHandlerTest
 * @package Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommandHandler
 */
class GetReservaPorIdCommandHandlerTest extends ReservasTestCase
{
    /**
     * @return GetReservaPorIdCommandHandler
     * @throws \Doctrine\ORM\ORMException
     */
    public function test__construct(): GetReservaPorIdCommandHandler
    {
        /** @var ReservaRepositoryInterface $reserva_repository */
        $reserva_repository = EntityManagerX::getRepository(Reserva::class);
        $handler = new GetReservaPorIdCommandHandler($reserva_repository);

        $this->assertInstanceOf(GetReservaPorIdCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param GetReservaPorIdCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_retornar_null_quando_nao_encontrar_registro_no_bd(GetReservaPorIdCommandHandler $handler)
    {
        $command = new GetReservaPorIdCommand(0);
        $reserva = $handler->handle($command);

        $this->assertNull($reserva);
    }

    /**
     * @param GetReservaPorIdCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    public function test_Handle_deve_retornar_Reserva_quando_encontrar_registro_no_bd(GetReservaPorIdCommandHandler $handler)
    {
        $query = '
            select
                reserva_id
            from
                dlx_reservas_cadastro
            order by 
                rand()
            limit 1
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->executeQuery($query);
        $id = $sql->fetchColumn();

        if (empty($id)) {
            $this->markTestIncomplete('Nenhuma reserva encontrada para testar.');
        }

        $command = new GetReservaPorIdCommand($id);
        $reserva = $handler->handle($command);

        $this->assertInstanceOf(Reserva::class, $reserva);
        $this->assertEquals($id, $reserva->getId());
    }
}
