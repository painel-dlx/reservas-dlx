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

namespace Reservas\PainelDLX\Tests\UseCases\Reservas\CancelarReserva;

use DateTime;
use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\ParameterType;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;
use Reservas\PainelDLX\Domain\Reservas\Repositories\ReservaRepositoryInterface;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class CancelarReservaCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Reservas\CancelarReserva
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommandHandler
 */
class CancelarReservaCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return CancelarReservaCommandHandler
     * @throws \Doctrine\ORM\ORMException
     */
    public function test__construct(): CancelarReservaCommandHandler
    {
        /** @var \Reservas\PainelDLX\Domain\Reservas\Repositories\ReservaRepositoryInterface $reserva_repository */
        $reserva_repository = EntityManagerX::getRepository(Reserva::class);

        $handler = new CancelarReservaCommandHandler($reserva_repository);
        $this->assertInstanceOf(CancelarReservaCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param CancelarReservaCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function test_Handle(CancelarReservaCommandHandler $handler)
    {
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+2 days');

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
        $sql->bindValue(':checkin', $checkin->format('Y-m-d'), ParameterType::STRING);
        $sql->bindValue(':checkout', $checkout->format('Y-m-d'), ParameterType::STRING);
        $sql->bindValue(':adultos', 2, ParameterType::INTEGER);
        $sql->bindValue(':criancas', 0, ParameterType::INTEGER);
        $sql->bindValue(':valor', 0, ParameterType::INTEGER);
        $sql->bindValue(':status', 'Pendente', ParameterType::STRING);
        $sql->bindValue(':origem', 'Teste Unitário', ParameterType::STRING);
        $sql->execute();

        $id = $con->lastInsertId();

        /** @var \Reservas\PainelDLX\Domain\Reservas\Entities\Reserva $reserva */
        $reserva = EntityManagerX::getRepository(Reserva::class)->find($id);

        /** @var Usuario $usuario */
        $usuario = EntityManagerX::getRepository(Usuario::class)->find(2);

        $command = new CancelarReservaCommand($reserva, $usuario, 'Reserva cancelada.');
        $handler->handle($command);

        $this->assertTrue($reserva->isCancelada());
    }
}
