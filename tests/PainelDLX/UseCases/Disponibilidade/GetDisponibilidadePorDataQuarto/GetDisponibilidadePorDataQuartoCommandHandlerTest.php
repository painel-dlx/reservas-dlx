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

namespace Reservas\PainelDLX\Tests\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto;

use DateTime;
use DLX\Infra\EntityManagerX;
use Reservas\PainelDLX\Domain\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\PainelDLX\Tests\ReservasTestCase;
use Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommandHandler;

/**
 * Class GetDisponibilidadePorDataQuartoCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommandHandler
 */
class GetDisponibilidadePorDataQuartoCommandHandlerTest extends ReservasTestCase
{
    /** @var GetDisponibilidadePorDataQuartoCommandHandler */
    private $handler;

    protected function setUp()
    {
        parent::setUp();

        /** @var DisponibilidadeRepositoryInterface $disponibilidade_repository */
        $disponibilidade_repository = EntityManagerX::getRepository(Disponibilidade::class);
        $this->handler = new GetDisponibilidadePorDataQuartoCommandHandler($disponibilidade_repository);
    }

    /**
     * @throws \Exception
     * @covers ::handle
     */
    public function test_Handle_deve_retornar_null_quando_nao_encontrar_no_bd()
    {
        $quarto = new Quarto('Teste', 10, 10);
        $quarto->setId(0);

        $data = new DateTime();

        $command = new GetDisponibilidadePorDataQuartoCommand($quarto, $data);
        $disponibilidade = $this->handler->handle($command);

        $this->assertNull($disponibilidade);
    }

    /**
     * @throws \Exception
     * @covers ::handle
     */
    public function test_Handle_deve_retornar_Disponibilidade_quando_encontrar_no_bd()
    {
        $query = '
            select
                quarto_id
            from
                dlx_reservas_quartos
            where
                quarto_publicar = 1
                and quarto_delete = 0
            order by
                rand()
            limit 1
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->prepare($query);
        $sql->execute();

        $id = $sql->fetchColumn();

        /** @var Quarto $quarto */
        $quarto = EntityManagerX::getRepository(Quarto::class)->find($id);
        $data = new DateTime();

        $command = new GetDisponibilidadePorDataQuartoCommand($quarto, $data);
        $disponibilidade = $this->handler->handle($command);

        $this->assertInstanceOf(Disponibilidade::class, $disponibilidade);
    }
}
