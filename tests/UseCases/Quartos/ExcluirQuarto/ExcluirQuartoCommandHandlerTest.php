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

namespace Reservas\Tests\UseCases\Quartos\ExcluirQuarto;

use DLX\Infrastructure\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\ORMException;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Domain\Quartos\Validators\QuartoValidator;
use Reservas\Tests\ReservasTestCase;
use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommand;
use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommandHandler;
use Reservas\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommand;
use Reservas\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;

/**
 * Class ExcluirQuartoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Quartos\ExcluirQuarto
 * @coversDefaultClass \Reservas\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler
 */
class ExcluirQuartoCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return ExcluirQuartoCommandHandler
     * @throws ORMException
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
     * @throws ORMException
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_retorna_true_caso_consiga_excluir_Quarto(ExcluirQuartoCommandHandler $handler)
    {
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        $validator = $this->createMock(QuartoValidator::class);
        $validator->method('validar')->willReturn(true);

        $quarto = new Quarto('OUTRO QUARTO', 2, 1);

        /** @var QuartoRepositoryInterface $quarto_repository */
        /** @var QuartoValidator $validator */

        $session = SessionFactory::createPHPSession();
        $session->set('editando:quarto', $quarto);

        $command = new CriarNovoQuartoCommand(
            'QUARTO TESTE UNITÁRIO',
            '',
            3,
            5,
            30,
            100,
            'teste/teste'
        );

        (new CriarNovoQuartoCommandHandler($quarto_repository, $session, $validator))->handle($command);

        // Excluir o quarto
        $command = new ExcluirQuartoCommand($quarto);
        $retorno = $handler->handle($command);

        $this->assertTrue($retorno);
        $this->assertNull($quarto->getId());
    }

    /**
     * @param ExcluirQuartoCommandHandler $handler
     * @throws ORMException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_setar_registro_como_deletado_quando_tem_erro_constraint(ExcluirQuartoCommandHandler $handler)
    {
        // Encontrar um quarto qualquer que tenha reserva para excluir (marcar como deletado)
        $query = '
            select
                q.quarto_id
            from
                reservas.Quarto q 
            inner join  
                reservas.Reserva drc on q.quarto_id = drc.quarto_id
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

        /** @var Quarto $quarto */
        $quarto = EntityManagerX::getRepository(Quarto::class)->find($quarto_id);
        $command = new ExcluirQuartoCommand($quarto);
        $handler->handle($command);

        // EntityManagerX::commit();
        $this->assertNotNull($quarto->getId());
        $this->assertTrue($quarto->isDeletado());
    }
}
