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

namespace Reservas\PainelDLX\Tests\UseCases\Quartos\GetQuartoPorId;

use DLX\Infra\EntityManagerX;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\Tests\ReservasTestCase;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler;

class GetQuartoPorIdCommandHandlerTest extends ReservasTestCase
{
    /** @var GetQuartoPorIdCommandHandler */
    private $handler;

    protected function setUp()
    {
        parent::setUp();

        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        $this->handler = new GetQuartoPorIdCommandHandler($quarto_repository);
    }


    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    public function test_Handle_deve_retornar_Quarto_se_encontrar_no_BD()
    {
        $query = '
            select
                quarto_id
            from
                dlx_reservas_quartos
            where
                quarto_delete = 0
                and quarto_publicar = 1
            order by 
                rand()
            limit 1
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->prepare($query);
        $sql->execute();

        $quarto_id = $sql->fetchColumn();

        if (empty($quarto_id)) {
            $this->markTestSkipped('Nenhum quarto encontrado no banco de dados');
        }

        $command = new GetQuartoPorIdCommand($quarto_id);
        $quarto = $this->handler->handle($command);

        $this->assertInstanceOf(Quarto::class, $quarto);
    }

    /**
     *
     */
    public function test_Handle_deve_retornar_null_se_encontrar_Quarto_no_BD()
    {
        $command = new GetQuartoPorIdCommand(0);
        $quarto = $this->handler->handle($command);

        $this->assertNull($quarto);
    }
}
