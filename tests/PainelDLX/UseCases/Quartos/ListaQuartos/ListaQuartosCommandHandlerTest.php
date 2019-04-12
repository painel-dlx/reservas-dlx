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

namespace Reservas\PainelDLX\Tests\PainelDLX\UseCases\Quartos\ListaQuartos;

use DLX\Infra\EntityManagerX;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Repositories\QuartoRepositoryInterface;
use Reservas\Tests\ReservasTestCase;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommandHandler;

class ListaQuartosCommandHandlerTest extends ReservasTestCase
{
    /** @var ListaQuartosCommandHandler */
    private $handler;

    protected function setUp()
    {
        parent::setUp();

        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);
        $this->handler = new ListaQuartosCommandHandler($quarto_repository);
    }


    public function test_Handle_deve_retornar_array_com_Quartos()
    {
        $command = new ListaQuartosCommand([], [], 100);
        $lista_quartos = $this->handler->handle($command);

        $this->assertIsArray($lista_quartos);

        if (count($lista_quartos) > 0) {
            // Verificar se todos os itens do array são instâncias de Quarto
            $this->assertEmpty(array_filter($lista_quartos, function ($quarto) {
                return !$quarto instanceof Quarto;
            }));

            // Verificar se tem algum quarto marcado como deletado
            $quartos_deletados = array_filter($lista_quartos, function (Quarto $quarto) {
                return $quarto->isDeletado();
            });

            $this->assertEmpty($quartos_deletados);
        }
    }
}
