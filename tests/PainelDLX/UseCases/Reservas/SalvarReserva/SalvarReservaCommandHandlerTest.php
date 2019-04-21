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

namespace Reservas\PainelDLX\Tests\UseCases\Reservas\SalvarReserva;

use DateTime;
use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Repositories\ReservaRepositoryInterface;
use Reservas\PainelDLX\UseCases\Reservas\SalvarReserva\SalvarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\SalvarReserva\SalvarReservaCommandHandler;
use Reservas\Tests\Helpers\QuartoTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class SalvarReservaCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Reservas\ReservarQuarto
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Reservas\SalvarReserva\SalvarReservaCommandHandler
 */
class SalvarReservaCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return SalvarReservaCommandHandler
     * @throws ORMException
     */
    public function test__construct(): SalvarReservaCommandHandler
    {
        /** @var ReservaRepositoryInterface $reserva_repository */
        $reserva_repository = EntityManagerX::getRepository(Reserva::class);
        $handler = new SalvarReservaCommandHandler($reserva_repository);

        $this->assertInstanceOf(SalvarReservaCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param SalvarReservaCommandHandler $handler
     * @throws ORMException
     * @throws DBALException
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle(SalvarReservaCommandHandler $handler)
    {
        $quarto = QuartoTesteHelper::getRandom();
        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+4 days');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva
            ->setHospede('Teste da Silva')
            ->setCpf('000.000.000-00')
            ->setValor(10.00)
            ->setOrigem('Teste Unitário');

        $command = new SalvarReservaCommand($reserva);
        $handler->handle($command);

        $this->assertNotNull($reserva->getId());
        $this->assertIsInt($reserva->getId());
    }
}
