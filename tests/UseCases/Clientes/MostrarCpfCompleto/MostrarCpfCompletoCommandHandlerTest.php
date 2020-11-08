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

namespace Reservas\Tests\UseCases\Clientes\MostrarCpfCompleto;

use CPF\CPF;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Domain\Usuarios\Exceptions\UsuarioJaPossuiGrupoException;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\Domain\Reservas\Exceptions\VisualizarCpfException;
use Reservas\Domain\Reservas\Repositories\ReservaRepositoryInterface;
use Reservas\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommand;
use Reservas\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommandHandler;
use Reservas\Tests\Helpers\QuartoTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class MostrarCpfCompletoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Clientes\MostrarCpfCompleto
 * @coversDefaultClass \Reservas\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommandHandler
 */
class MostrarCpfCompletoCommandHandlerTest extends ReservasTestCase
{
    /**
     * @return MostrarCpfCompletoCommandHandler
     */
    public function test__construct(): MostrarCpfCompletoCommandHandler
    {
        // $reserva_repository = EntityManagerX::getRepository(Reserva::class);
        $reserva_repository = $this->createMock(ReservaRepositoryInterface::class);
        $reserva_repository->method('update')->willReturn(null);

        /** @var ReservaRepositoryInterface $reserva_repository */

        $handler = new MostrarCpfCompletoCommandHandler($reserva_repository);

        $this->assertInstanceOf(MostrarCpfCompletoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param MostrarCpfCompletoCommandHandler $handler
     * @throws DBALException
     * @throws ORMException
     * @throws VisualizarCpfException
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle(MostrarCpfCompletoCommandHandler $handler)
    {
        $quarto = QuartoTesteHelper::getRandom();
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+2 days');
        $reserva = new Reserva($quarto, $checkin, $checkout, 2);
        $reserva->setCpf(new CPF('780.854.440-03'));

        $usuario = new Usuario('Funcionário', 'funcionario@gmail.com');

        $command = new MostrarCpfCompletoCommand($reserva, $usuario);
        $cpf_completo = $handler->handle($command);

        $this->assertIsString($cpf_completo);
        $this->assertEquals($reserva->getCpf()->getCpfMask(), $cpf_completo);

        // todo: testar se a visualização foi registrada no bd
    }
}
