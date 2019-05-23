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

namespace Reservas\Tests\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto;

use DateTime;
use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\ParameterType;
use Exception;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Tests\ReservasTestCase;
use Reservas\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommand;
use Reservas\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommandHandler;
use Reservas\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommand;
use Reservas\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommandHandler;

/**
 * Class SalvarDisponibilidadeQuartoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto
 * @coversDefaultClass \Reservas\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommandHandler
 */
class SalvarDisponibilidadeQuartoCommandHandlerTest extends ReservasTestCase
{
    /**
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoEncontradoException
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoInformadoException
     * @throws \Doctrine\ORM\ORMException
     * @throws \PainelDLX\Application\Services\Exceptions\AmbienteNaoInformadoException
     * @throws \Reservas\Domain\Exceptions\LinkQuartoUtilizadoException
     * @throws \Reservas\Domain\Exceptions\NomeQuartoUtilizadoException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function gerarQuarto(): Quarto
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        $query = '
            insert into dlx_reservas_quartos (quarto_nome, quarto_descricao, quarto_maxhospedes, quarto_qtde, quarto_valor_min, quarto_tamanho_m2, quarto_link)
                values (?, ?, ?, ?, ?, ?, ?) 
        ';

        $con = EntityManagerX::getInstance()->getConnection();

        $sql = $con->prepare($query);
        $sql->bindValue(1, 'Teste 1234', ParameterType::STRING);
        $sql->bindValue(2, '', ParameterType::STRING);
        $sql->bindValue(3, 3, ParameterType::INTEGER);
        $sql->bindValue(4, 5, ParameterType::INTEGER);
        $sql->bindValue(5, 12.34);
        $sql->bindValue(6, 30, ParameterType::INTEGER);
        $sql->bindValue(7, '/teste/teste/1234', ParameterType::INTEGER);
        $sql->execute();

        $id = $con->lastInsertId();

        /** @var Quarto|null $quarto */
        $quarto = $quarto_repository->find($id);

        return $quarto;
    }

    /**
     * @return SalvarDisponibilidadeQuartoCommandHandler
     * @throws \Doctrine\ORM\ORMException
     */
    public function test__construct(): SalvarDisponibilidadeQuartoCommandHandler
    {
        /** @var DisponibilidadeRepositoryInterface $disponibilidade_repository */
        $disponibilidade_repository = EntityManagerX::getRepository(Disponibilidade::class);
        $handler = new SalvarDisponibilidadeQuartoCommandHandler($disponibilidade_repository);

        $this->assertInstanceOf(SalvarDisponibilidadeQuartoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @covers ::handle
     * @param SalvarDisponibilidadeQuartoCommandHandler $handler
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoEncontradoException
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoInformadoException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \PainelDLX\Application\Services\Exceptions\AmbienteNaoInformadoException
     * @throws \Reservas\Domain\Exceptions\LinkQuartoUtilizadoException
     * @throws \Reservas\Domain\Exceptions\NomeQuartoUtilizadoException
     * @throws \Reservas\Domain\Exceptions\ValorMenorQueMinimoQuartoException
     * @depends test__construct
     */
    public function test_Handle_deve_retornar_Disponibilidade(SalvarDisponibilidadeQuartoCommandHandler $handler)
    {
        $quarto = $this->gerarQuarto();

        $disponibilidade = new Disponibilidade($quarto, new DateTime(), 10);
        $disponibilidade->addValor(1, 12.34);

        $command = new SalvarDisponibilidadeQuartoCommand($disponibilidade);
        $disponibilidade = $handler->handle($command);

        $this->assertInstanceOf(Disponibilidade::class, $disponibilidade);
        $this->assertNotNull($disponibilidade->getId());
    }
}
