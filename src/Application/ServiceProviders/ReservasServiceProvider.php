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

namespace Reservas\Application\ServiceProviders;

use League\Container\Container;
use League\Container\ServiceProvider\AbstractServiceProvider;
use PainelDLX\Infrastructure\ORM\Doctrine\Services\RepositoryFactory;
use Reservas\Domain\Common\Events\EventManagerInterface;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Entities\PedidoItem;
use Reservas\Domain\Pedidos\Events\Listeners\EnviarEmailConfirmacaoPedido;
use Reservas\Domain\Pedidos\Events\Listeners\EnviarEmailPedidoCancelado;
use Reservas\Domain\Pedidos\Events\Listeners\MascararDadosCartao;
use Reservas\Domain\Pedidos\Events\PagamentoPedidoConfirmado;
use Reservas\Domain\Pedidos\Events\PedidoCancelado;
use Reservas\Domain\Pedidos\Repositories\PedidoItemRepositoryInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\Domain\Reservas\Repositories\ReservaRepositoryInterface;
use Reservas\Infrastructure\Events\EventManager;

class ReservasServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        QuartoRepositoryInterface::class,
        DisponibilidadeRepositoryInterface::class,
        ReservaRepositoryInterface::class,
        PedidoRepositoryInterface::class,
        PedidoItemRepositoryInterface::class,
        EventManagerInterface::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepositories();
        $this->registerEvents();
    }

    private function registerRepositories(): void
    {
        /** @var Container $container */
        $container = $this->getContainer();

        $container->add(
            QuartoRepositoryInterface::class,
            RepositoryFactory::create(Quarto::class)
        );

        $container->add(
            DisponibilidadeRepositoryInterface::class,
            RepositoryFactory::create(Disponibilidade::class)
        );

        $container->add(
            ReservaRepositoryInterface::class,
            RepositoryFactory::create(Reserva::class)
        );

        $container->add(
            PedidoRepositoryInterface::class,
            RepositoryFactory::create(Pedido::class)
        );

        $container->add(
            PedidoItemRepositoryInterface::class,
            RepositoryFactory::create(PedidoItem::class)
        );
    }

    private function registerEvents(): void
    {
        /** @var Container $container */
        $container = $this->getContainer();

        $container->add(
            EventManagerInterface::class,
            function () use ($container) {
                $event_manager = new EventManager($container);

                $event_manager->addListener(
                    PagamentoPedidoConfirmado::class,
                    MascararDadosCartao::class,
                    EnviarEmailConfirmacaoPedido::class
                );

                $event_manager->addListener(
                    PedidoCancelado::class,
                    MascararDadosCartao::class,
                    EnviarEmailPedidoCancelado::class
                );

                return $event_manager;
            }
        );
    }
}