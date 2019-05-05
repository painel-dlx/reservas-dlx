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

namespace Reservas\PainelDLX\Tests\UseCases\Quartos\GetQuartoPorLink;

use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorLink\GetQuartoPorLinkCommand;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorLink\GetQuartoPorLinkCommandHandler;
use Reservas\Tests\Helpers\QuartoTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GetQuartoPorLinkCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Quartos\GetQuartoPorLink
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorLink\GetQuartoPorLinkCommandHandler
 */
class GetQuartoPorLinkCommandHandlerTest extends ReservasTestCase
{
    /**
     * @return GetQuartoPorLinkCommandHandler
     * @throws ORMException
     */
    public function test__construct(): GetQuartoPorLinkCommandHandler
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);
        $handler = new GetQuartoPorLinkCommandHandler($quarto_repository);

        $this->assertInstanceOf(GetQuartoPorLinkCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param GetQuartoPorLinkCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_retornar_null_quando_nao_encontrar_registro_no_bd(GetQuartoPorLinkCommandHandler $handler)
    {
        $command = new GetQuartoPorLinkCommand('nossos-quartos/teste-quarto-unitario');
        $quarto = $handler->handle($command);

        $this->assertNull($quarto);
    }

    /**
     * @param GetQuartoPorLinkCommandHandler $handler
     * @throws ORMException
     * @throws DBALException
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_retornar_Quarto_quando_encontrar_registro_no_bd(GetQuartoPorLinkCommandHandler $handler)
    {
        $quarto_random = QuartoTesteHelper::getRandom();

        $command = new GetQuartoPorLinkCommand($quarto_random->getLink());
        $quarto = $handler->handle($command);

        $this->assertInstanceOf(Quarto::class, $quarto);
        $this->assertEquals($quarto_random->getLink(), $quarto->getLink());
    }
}
