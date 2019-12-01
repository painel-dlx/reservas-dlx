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

namespace Reservas\Tests\UseCases\Pedidos\SalvarPedido;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\UseCases\Pedidos\SalvarPedido\SalvarPedidoCommand;
use Reservas\UseCases\Pedidos\SalvarPedido\SalvarPedidoCommandHandler;

/**
 * Class SalvarPedidoCommandHandlerTest
 * @package Reservas\UseCases\Pedidos\SalvarPedido
 * @coversDefaultClass \Reservas\UseCases\Pedidos\SalvarPedido\SalvarPedidoCommandHandler
 */
class SalvarPedidoCommandHandlerTest extends TestCase
{
    /**
     * @throws Exception
     * @covers ::handle
     */
    public function test_Handle_deve_criar_novo_Pedido()
    {
        $this->markTestSkipped('A hanler SalvarpedidoCommandHandler não está mais sendo usada e/ou atualizada.');

        $quarto = $this->createMock(Quarto::class);
        $quarto->method('isDisponivelPeriodo')->willReturn(true);

        $pedido_repository = $this->createMock(PedidoRepositoryInterface::class);

        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('find')->willReturn($quarto);

        /** @var PedidoRepositoryInterface $pedido_repository */
        /** @var QuartoRepositoryInterface $quarto_repository */

        $checkin = (new DateTime())->modify('+1 day');
        $checkout = (clone $checkin)->modify('+1 day');

        $nome = 'Teste Unitário';
        $cpf = '652.602.110-73';
        $email = 'teste@unitario.com';
        $telefone = '(61) 9 8350-3517';
        $itens = [
            [
                'quartoID' => 1,
                'quartoNome' => 'Quarto de Teste',
                'checkin' => $checkin->format('Y-m-d'),
                'checkout' => $checkout->format('Y-m-d'),
                'adultos' => 1,
                'criancas' => 0,
                'valor' => 12.34
            ]
        ];

        $command = new SalvarPedidoCommand(
            $nome,
            $cpf,
            $email,
            $telefone,
            $itens
        );

        $pedido = (new SalvarPedidoCommandHandler($pedido_repository, $quarto_repository))->handle($command);

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertEquals($nome, $pedido->getNome());
        $this->assertEquals($cpf, $pedido->getCpf());
        $this->assertEquals($email, $pedido->getEmail());
        $this->assertEquals($telefone, $pedido->getTelefone());
        $this->assertEquals($itens, $pedido->getItens());

        $this->assertTrue($pedido->isPendente());
    }
}
