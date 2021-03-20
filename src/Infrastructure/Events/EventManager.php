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

namespace Reservas\Infrastructure\Events;


use PainelDLX\Application\Services\PainelDLX;
use Psr\Container\ContainerInterface;
use Reservas\Domain\Common\Events\Event;
use Reservas\Domain\Common\Events\EventListener;
use Reservas\Domain\Common\Events\EventManagerInterface;

class EventManager implements EventManagerInterface
{
    /**
     * @var array
     */
    private $listeners = [];
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * EventManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function addListener(string $nome_evento, string ...$listeners): EventManagerInterface
    {
        foreach ($listeners as $listener) {
            $this->listeners[$nome_evento][] = $listener;
        }

        return $this;
    }

    /**
     * @param string $nome_evento
     * @param string ...$listeners
     * @return EventManagerInterface
     * @deprecated use o método addListener. Esse método foi criado com nome incorreto.
     */
    public function addLitener(string $nome_evento, string ...$listeners): EventManagerInterface
    {
        return $this->addListener($nome_evento, ... $listeners);
    }

    /**
     * @inheritDoc
     */
    public function dispatch(Event $event): void
    {
        $event_listeners = $this->listeners[get_class($event)];

        foreach ($event_listeners as $listener_class) {
            /** @var EventListener $listener */
            $listener = $this->container->get($listener_class);
            $listener->handle($event);
        }
    }
}