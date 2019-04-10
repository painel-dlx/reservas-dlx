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

namespace Reservas\PainelDLX\Tests\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto;

use DateTime;
use DLX\Infra\EntityManagerX;
use Reservas\PainelDLX\Domain\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\PainelDLX\Domain\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\Tests\ReservasTestCase;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommandHandler;

/**
 * Class SalvarDisponibilidadeQuartoCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommandHandler
 */
class SalvarDisponibilidadeQuartoCommandHandlerTest extends ReservasTestCase
{
    /** @var SalvarDisponibilidadeQuartoCommandHandler */
    private $handler;

    protected function setUp()
    {
        parent::setUp();

        /** @var DisponibilidadeRepositoryInterface $disponibilidade_repository */
        $disponibilidade_repository = EntityManagerX::getRepository(Disponibilidade::class);
        $this->handler = new SalvarDisponibilidadeQuartoCommandHandler($disponibilidade_repository);
    }

    /**
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoEncontradoException
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoInformadoException
     * @throws \Doctrine\ORM\ORMException
     * @throws \PainelDLX\Application\Services\Exceptions\AmbienteNaoInformadoException
     * @throws \Reservas\PainelDLX\Domain\Exceptions\LinkQuartoUtilizadoException
     * @throws \Reservas\PainelDLX\Domain\Exceptions\NomeQuartoUtilizadoException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function gerarQuarto()
    {
        parent::setUp();

        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto = new Quarto('Teste 1234', 1, 10);
        $quarto->setLink('/teste/teste');
        $quarto->setTamanhoM2(mt_rand(10, 90));

        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        $command = new SalvarQuartoCommand($quarto);
        $quarto = (new SalvarQuartoCommandHandler($quarto_repository))->handle($command);

        return [[$quarto]];
    }

    /**
     * @covers ::handle
     * @param Quarto $quarto
     * @throws \Exception
     * @dataProvider gerarQuarto
     */
    public function test_Handle_deve_retornar_Disponibilidade(Quarto $quarto)
    {
        $disponibilidade = new Disponibilidade($quarto, new DateTime(), 10);
        $disponibilidade->setValor(12.34);

        $command = new SalvarDisponibilidadeQuartoCommand($disponibilidade);
        $disponibilidade = $this->handler->handle($command);

        $this->assertInstanceOf(Disponibilidade::class, $disponibilidade);
        $this->assertNotNull($disponibilidade->getId());
    }
}
