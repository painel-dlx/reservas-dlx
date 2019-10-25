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

namespace Reservas\UseCases\Emails\EnviarNotificacaoCancelamentoPedido;


use PainelDLX\Application\Services\Exceptions\ErroAoEnviarEmailException;
use PainelDLX\Domain\Emails\Repositories\ConfigSmtpRepositoryInterface;
use PainelDLX\Infrastructure\Services\Email\EnviarEmail;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;

class EnviarNotificacaoCancelamentoPedidoCommandHandler
{
    /**
     * @var PHPMailer
     */
    private $PHP_mailer;
    /**
     * @var ConfigSmtpRepositoryInterface
     */
    private $config_smtp_repository;
    /**
     * @var VileX
     */
    private $vile_x;

    /**
     * EnviarNotificacaoCancelamentoPedidoCommandHandler constructor.
     * @param PHPMailer $PHP_mailer
     * @param ConfigSmtpRepositoryInterface $config_smtp_repository
     * @param VileX $vile_x
     */
    public function __construct(
        PHPMailer $PHP_mailer,
        ConfigSmtpRepositoryInterface $config_smtp_repository,
        VileX $vile_x
    ) {
        $this->PHP_mailer = $PHP_mailer;
        $this->config_smtp_repository = $config_smtp_repository;
        $this->vile_x = $vile_x;
    }

    /**
     * @param EnviarNotificacaoCancelamentoPedidoCommand $command
     * @throws ContextoInvalidoException
     * @throws ErroAoEnviarEmailException
     * @throws Exception
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    public function handle(EnviarNotificacaoCancelamentoPedidoCommand $command)
    {
        $pedido = $command->getPedido();
        $config_smtp = $this->config_smtp_repository->findOneBy(['nome' => 'SMTP Gmail']);

        $corpo = $this->gerarCorpoHtml($pedido, $command->getMotivo());

        $enviar_email = new EnviarEmail($this->PHP_mailer, $config_smtp, 'Seu pedido de reservas foi cancelado!', $corpo);
        $enviar_email->enviarPara($pedido->getEmail());
    }

    /**
     * @param Pedido $pedido
     * @param string $motivo
     * @return string
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    private function gerarCorpoHtml(Pedido $pedido, string $motivo): string
    {
        $this->vile_x->setPaginaMestra('public/views/paginas-mestras/email-master.phtml');
        $this->vile_x->setViewRoot('public/views/emails');

        // Views
        $this->vile_x->addTemplate('topo');
        $this->vile_x->addTemplate('cancelamento_pedido');
        $this->vile_x->addTemplate('rodape');

        // Parâmetros
        $this->vile_x->setAtributo('titulo-email', "Pedido #{$pedido->getid()} Cancelado");
        $this->vile_x->setAtributo('pedido', $pedido);
        $this->vile_x->setAtributo('motivo', $motivo);

        $response = $this->vile_x->render();

        $corpo = (string)$response->getBody();

        return $corpo;
    }
}