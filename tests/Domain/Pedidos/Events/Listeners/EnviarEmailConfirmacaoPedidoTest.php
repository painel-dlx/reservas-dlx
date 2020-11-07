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

namespace Reservas\Tests\Domain\Pedidos\Events\Listeners;

use Doctrine\Common\Collections\ArrayCollection;
use PainelDLX\Application\Services\Exceptions\ErroAoEnviarEmailException;
use PainelDLX\Domain\Emails\Entities\ConfigSmtp;
use PainelDLX\Domain\Emails\Repositories\ConfigSmtpRepositoryInterface;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Events\Common\PedidoEvent;
use Reservas\Domain\Pedidos\Events\Listeners\EnviarEmailConfirmacaoPedido;
use Reservas\Tests\ReservasTestCase;
use Vilex\Exceptions\PaginaMestraInvalidaException;
use Vilex\Exceptions\TemplateInvalidoException;
use Vilex\VileX;

/**
 * Class EnviarEmailConfirmacaoPedidoTest
 * @package Reservas\Tests\Domain\Pedidos\Events\Listeners
 * @coversDefaultClass \Reservas\Domain\Pedidos\Events\Listeners\EnviarEmailConfirmacaoPedido
 */
class EnviarEmailConfirmacaoPedidoTest extends ReservasTestCase
{
    /**
     * @test
     * @covers ::handle
     * @throws Exception
     * @throws ErroAoEnviarEmailException
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     */
    public function deve_enviar_email_de_confirmacao_pedido()
    {
        $pedido = $this->createMock(Pedido::class);
        $pedido->method('getId')->willReturn(1234);
        $pedido->method('getEmail')->willReturn('dlepera88@gmail.com');
        $pedido->method('getItens')->willReturn(new ArrayCollection());

        $config_smtp = $this->createMock(ConfigSmtp::class);
        $config_smtp->method('getServidor')->willReturn('smtp.gmail.com');
        $config_smtp->method('getPorta')->willReturn(465);
        $config_smtp->method('isRequerAutent')->willReturn(true);
        $config_smtp->method('getConta')->willReturn('dlepera88.emails@gmail.com');
        $config_smtp->method('getSenha')->willReturn('tanqraktgugworjf');
        $config_smtp->method('getCripto')->willReturn('ssl');
        $config_smtp->method('isCorpoHtml')->willReturn(true);

        $config_smtp_repository = $this->createMock(ConfigSmtpRepositoryInterface::class);
        $config_smtp_repository->method('findOneBy')->willReturn($config_smtp);

        $pedido_event = $this->createMock(PedidoEvent::class);
        $pedido_event->method('getPedido')->willReturn($pedido);

        /** @var PedidoEvent $pedido_event */
        /** @var ConfigSmtpRepositoryInterface $config_smtp_repository */

        $php_mailer = new PHPMailer();

        $enviarEmailConfirmacaoPedido = new EnviarEmailConfirmacaoPedido(
            new VileX(),
            $php_mailer,
            $config_smtp_repository
        );

        $enviarEmailConfirmacaoPedido->handle($pedido_event);

        $this->assertFalse($php_mailer->isError());
    }
}
