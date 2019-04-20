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

namespace Reservas\PainelDLX\Tests\UseCases\Reservas\ConfirmarReserva;

use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\ParameterType;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Repositories\ReservaRepositoryInterface;
use Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ConfirmarReservaCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Reservas\ConfirmarReserva
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommandHandler
 */
class ConfirmarReservaCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return ConfirmarReservaCommandHandler
     * @throws \Doctrine\ORM\ORMException
     */
    public function test__construct(): ConfirmarReservaCommandHandler
    {
        /** @var ReservaRepositoryInterface $reserva_repository */
        $reserva_repository = EntityManagerX::getRepository(Reserva::class);

        $handler = new ConfirmarReservaCommandHandler($reserva_repository);
        $this->assertInstanceOf(ConfirmarReservaCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param ConfirmarReservaCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function test_Handle(ConfirmarReservaCommandHandler $handler)
    {
        $query = 'insert into dlx_reservas_cadastro (
                                    reserva_quarto,
                                    reserva_hospede, 
                                    reserva_cpf, 
                                    reserva_telefone, 
                                    reserva_email, 
                                    reserva_checkin, 
                                    reserva_checkout, 
                                    reserva_adultos, 
                                    reserva_criancas, 
                                    reserva_valor, 
                                    reserva_status, 
                                    reserva_origem
                                ) values (
                                    :quarto,
                                    :hospede,
                                    :cpf,
                                    :telefone,
                                    :email,
                                    :checkin,
                                    :checkout,
                                    :adultos,
                                    :criancas,
                                    :valor,
                                    :status,
                                    :origem
                                )';

        $con = EntityManagerX::getInstance()->getConnection();

        $sql = $con->prepare($query);
        $sql->bindValue(':quarto', 1, ParameterType::INTEGER);
        $sql->bindValue(':hospede', 'Teste de Cliente', ParameterType::STRING);
        $sql->bindValue(':cpf', '000.000.000-00', ParameterType::STRING);
        $sql->bindValue(':telefone', '(00) 0000-0000', ParameterType::STRING);
        $sql->bindValue(':email', 'cliente@gmail.com', ParameterType::STRING);
        $sql->bindValue(':checkin', '2019-01-01', ParameterType::STRING);
        $sql->bindValue(':checkout', '2019-01-05', ParameterType::STRING);
        $sql->bindValue(':adultos', 2, ParameterType::INTEGER);
        $sql->bindValue(':criancas', 0, ParameterType::INTEGER);
        $sql->bindValue(':valor', 0, ParameterType::INTEGER);
        $sql->bindValue(':status', 'Pendente', ParameterType::STRING);
        $sql->bindValue(':origem', 'Teste Unitário', ParameterType::STRING);
        $sql->execute();

        $id = $con->lastInsertId();

        /** @var Reserva $reserva */
        $reserva = EntityManagerX::getRepository(Reserva::class)->find($id);

        /** @var Usuario $usuario */
        $usuario = EntityManagerX::getRepository(Usuario::class)->find(2);

        $command = new ConfirmarReservaCommand($reserva, $usuario, 'Reserva confirmada.');
        $handler->handle($command);

        $this->assertTrue($reserva->isConfirmada());
    }
}
