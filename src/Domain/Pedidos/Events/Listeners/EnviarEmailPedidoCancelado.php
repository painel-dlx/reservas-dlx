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

namespace Reservas\Domain\Pedidos\Events\Listeners;


use PainelDLX\Application\Services\Exceptions\ErroAoEnviarEmailException;
use PainelDLX\Domain\Emails\Repositories\ConfigSmtpRepositoryInterface;
use PainelDLX\Infrastructure\Services\Email\EnviarEmail;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Entities\PedidoHistorico;
use Reservas\Domain\Pedidos\Events\Common\PedidoEvent;
use Reservas\Domain\Pedidos\Events\Common\PedidoEventListener;
use Vilex\Exceptions\PaginaMestraInvalidaException;
use Vilex\Exceptions\TemplateInvalidoException;
use Vilex\VileX;

class EnviarEmailPedidoCancelado implements PedidoEventListener
{
    /**
     * @var VileX
     */
    private $vile_x;
    /**
     * @var ConfigSmtpRepositoryInterface
     */
    private $config_smtp_repository;
    /**
     * @var PHPMailer
     */
    private $php_mailer;

    /**
     * EnviarEmailPedidoCancelado constructor.
     * @param VileX $vile_x
     * @param PHPMailer $php_mailer
     * @param ConfigSmtpRepositoryInterface $config_smtp_repository
     */
    public function __construct(
        VileX $vile_x,
        PHPMailer $php_mailer,
        ConfigSmtpRepositoryInterface $config_smtp_repository
    ) {
        $this->vile_x = $vile_x;
        $this->config_smtp_repository = $config_smtp_repository;
        $this->php_mailer = $php_mailer;
    }

    /**
     * @inheritDoc
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     * @throws ErroAoEnviarEmailException
     * @throws Exception
     */
    public function handle(PedidoEvent $pedido_event): void
    {
        $pedido = $pedido_event->getPedido();
        $config_smtp = $this->config_smtp_repository->findOneBy(['nome' => 'SMTP Gmail']);

        /** @var PedidoHistorico $ultimo_historico */
        $ultimo_historico = $pedido->getHistorico()->last();
        $motivo = $ultimo_historico->getMotivo();

        $corpo = $this->gerarCorpoHtml($pedido, $motivo);

        $enviar_email = new EnviarEmail($this->php_mailer, $config_smtp, 'Seu pedido de reservas foi cancelado!', $corpo);
        $enviar_email->enviarPara($pedido->getEmail());
    }

    /**
     * @param Pedido $pedido
     * @param string $motivo
     * @return string
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     */
    private function gerarCorpoHtml(Pedido $pedido, string $motivo): string
    {
        $this->vile_x->setPaginaMestra('../paginas-mestras/email-master');
        $this->vile_x->setViewRoot('public/views/emails/');

        // Views
        $this->vile_x->addTemplate('topo');
        $this->vile_x->addTemplate('cancelamento_pedido');
        $this->vile_x->addTemplate('rodape');

        // Parâmetros
        $this->vile_x->setAtributo('titulo-email', "Pedido #{$pedido->getid()} Cancelado");
        $this->vile_x->setAtributo('pedido', $pedido);
        $this->vile_x->setAtributo('motivo', $motivo);

        $response = $this->vile_x->render();

        return (string)$response->getBody();
    }
}