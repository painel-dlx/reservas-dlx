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

namespace Reservas\Tests\UseCases\Quartos\CriarNovoQuarto;

use PHPUnit\Framework\TestCase;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Domain\Quartos\Validators\QuartoValidator;
use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommand;
use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommandHandler;
use SechianeX\Contracts\SessionInterface;

/**
 * Class CriarNovoQuartoCommandHandlerTest
 * @package Reservas\UseCases\Quartos\CriarNovoQuarto
 * @coversDefaultClass \Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommandHandler
 */
class CriarNovoQuartoCommandHandlerTest extends TestCase
{
    /**
     * @covers ::handle
     */
    public function test_Handle_deve_criar_uma_nova_instancia_de_Quarto()
    {
        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('editando:quarto')->willReturn(new Quarto('', 0, 0));

        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('create')->willReturn(null);

        $validator = $this->createMock(QuartoValidator::class);
        $validator->method('validar')->willReturn(true);

        /** @var SessionInterface $session */
        /** @var QuartoRepositoryInterface $quarto_repository */
        /** @var QuartoValidator $validator */

        $nome = 'Quarto Teste Unitário';
        $descricao = '';
        $max_hospedes = 1;
        $qtde = 1;
        $tamanho_m2 = 35;
        $valor_min = 199.99;
        $link = '/teste/teste';

        $command = new CriarNovoQuartoCommand(
            $nome,
            $descricao,
            $max_hospedes,
            $qtde,
            $tamanho_m2,
            $valor_min,
            $link
        );

        $quarto = (new CriarNovoQuartoCommandHandler($quarto_repository, $session, $validator))->handle($command);

        $this->assertInstanceOf(Quarto::class, $quarto);
        $this->assertEquals($nome, $quarto->getNome());
        $this->assertEquals($descricao, $quarto->getDescricao());
        $this->assertEquals($max_hospedes, $quarto->getMaxHospedes());
        $this->assertEquals($qtde, $quarto->getQtde());
        $this->assertEquals($tamanho_m2, $quarto->getTamanhoM2());
        $this->assertEquals($valor_min, $quarto->getValorMin());
        $this->assertEquals($link, $quarto->getLink());
    }
}
