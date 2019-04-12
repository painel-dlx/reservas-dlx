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
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Repositories\QuartoRepositoryInterface;
use Reservas\Tests\ReservasTestCase;
use Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommandHandler;

class ExcluirQuartoCommandHandlerTest extends ReservasTestCase
{
    /** @var ExcluirQuartoCommandHandler */
    private $handler;

    protected function setUp()
    {
        parent::setUp();

        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);
        $this->handler = new ExcluirQuartoCommandHandler($quarto_repository);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    public function test_Handle_retorna_true_caso_consiga_excluir_Quarto()
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
        $retorno = $this->handler->handle($command);

        $this->assertTrue($retorno);
        $this->assertNull($quarto->getId());
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    public function test_Handle_deve_setar_registro_como_deletado_quando_tem_erro_constraint()
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

        /** @var Quarto $quarto */
        $quarto = EntityManagerX::getRepository(Quarto::class)->find($quarto_id);
        $command = new ExcluirQuartoCommand($quarto);
        $this->handler->handle($command);

        // EntityManagerX::commit();

        $this->assertNotNull($quarto->getId());
        $this->assertTrue($quarto->isDeletado());
    }
}
