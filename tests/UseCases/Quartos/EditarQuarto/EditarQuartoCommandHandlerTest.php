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

namespace Reservas\Tests\UseCases\Quartos\EditarQuarto;

use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Exceptions\QuartoNaoEncontradoException;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Domain\Quartos\Validators\QuartoValidator;
use Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommand;
use Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommandHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class EditarQuartoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Quartos\EditarQuarto
 * @coversDefaultClass \Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommandHandler
 */
class EditarQuartoCommandHandlerTest extends TestCase
{
    /**
     * @covers ::handle
     * @throws QuartoNaoEncontradoException
     */
    public function test_Handle_deve_alterar_informacoes_do_Quarto()
    {
        $quarto_id = mt_rand();

        $quarto = new Quarto('Quarto Teste Atual', 10, 100);

        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('find')->willReturn($quarto);
        $quarto_repository->method('update')->willReturn(null);

        $validator = $this->createMock(QuartoValidator::class);
        $validator->method('validar')->willReturn(true);

        /** @var QuartoRepositoryInterface $quarto_repository */
        /** @var QuartoValidator $validator */

        // Novas informações do Quarto
        $nome = 'Quarto Teste Unitário';
        $descricao = '';
        $max_hospedes = 1;
        $qtde = 1;
        $tamanho_m2 = 35;
        $valor_min = 199.99;
        $link = '/teste/teste';

        $comamnd = new EditarQuartoCommand(
            $nome,
            $descricao,
            $max_hospedes,
            $qtde,
            $tamanho_m2,
            $valor_min,
            $link,
            $quarto_id
        );

        $quarto = (new EditarQuartoCommandHandler($quarto_repository, $validator))->handle($comamnd);

        $this->assertInstanceOf(Quarto::class, $quarto);
        $this->assertEquals($nome, $quarto->getNome());
        $this->assertEquals($descricao, $quarto->getDescricao());
        $this->assertEquals($max_hospedes, $quarto->getMaximoHospedes());
        $this->assertEquals($qtde, $quarto->getQuantidade());
        $this->assertEquals($tamanho_m2, $quarto->getTamanhoM2());
        $this->assertEquals($valor_min, $quarto->getValorMinimo());
        $this->assertEquals($link, $quarto->getLink());
    }
}
