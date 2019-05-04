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

namespace Reservas\PainelDLX\Tests\PainelDLX\UseCases\Quartos\ExcluirQuarto;

use DLX\Infra\EntityManagerX;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Tests\ReservasTestCase;
use Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommandHandler;

/**
 * Class ExcluirQuartoCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\UseCases\Quartos\ExcluirQuarto
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler
 */
class ExcluirQuartoCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return ExcluirQuartoCommandHandler
     * @throws \Doctrine\ORM\ORMException
     */
    public function test__construct(): ExcluirQuartoCommandHandler
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);
        $handler = new ExcluirQuartoCommandHandler($quarto_repository);

        $this->assertInstanceOf(ExcluirQuartoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param ExcluirQuartoCommandHandler $handler
     * @throws \Doctrine\ORM\ORMException
     * @throws \Reservas\PainelDLX\Domain\Exceptions\LinkQuartoUtilizadoException
     * @throws \Reservas\PainelDLX\Domain\Exceptions\NomeQuartoUtilizadoException
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_retorna_true_caso_consiga_excluir_Quarto(ExcluirQuartoCommandHandler $handler)
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        // Salvar um novo quarto para testar a exclusão
        $quarto = new Quarto('QUARTO TESTE UNITÁRIO', 5, 100);
        $quarto
            ->setTamanhoM2(30)
            ->setLink('teste/teste');
        $command = new SalvarQuartoCommand($quarto);
        (new SalvarQuartoCommandHandler($quarto_repository))->handle($command);

        // Excluir o quarto
        $command = new ExcluirQuartoCommand($quarto);
        $retorno = $handler->handle($command);

        $this->assertTrue($retorno);
        $this->assertNull($quarto->getId());
    }

    /**
     * @param ExcluirQuartoCommandHandler $handler
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_setar_registro_como_deletado_quando_tem_erro_constraint(ExcluirQuartoCommandHandler $handler)
    {
        // Encontrar um quarto qualquer que tenha reserva para excluir (marcar como deletado)
        $query = '
            select
                quarto_id
            from
                dlx_reservas_quartos q 
            inner join  
                dlx_reservas_cadastro drc on q.quarto_id = drc.reserva_quarto
            limit 1
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->prepare($query);
        $sql->execute();

        $quarto_id = $sql->fetchColumn();

        if (empty($quarto_id)) {
            $this->markTestSkipped('Nenhum quarto com reserva encontrado para testar a marcação de "deletado".');
        }

        // Testar esse processo dentro de uma transação, pois quando o doctrine dispara uma exceção, ele fecha a conexão
        // e marca a transação para rollback!
        // EntityManagerX::beginTransaction();

        /** @var \Reservas\PainelDLX\Domain\Quartos\Entities\Quarto $quarto */
        $quarto = EntityManagerX::getRepository(Quarto::class)->find($quarto_id);
        $command = new ExcluirQuartoCommand($quarto);
        $handler->handle($command);

        // EntityManagerX::commit();

        $this->assertNotNull($quarto->getId());
        $this->assertTrue($quarto->isDeletado());
    }
}
