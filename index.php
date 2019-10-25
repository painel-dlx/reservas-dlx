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

include __DIR__ . '/vendor/autoload.php';

use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use PainelDLX\Application\Adapters\Router\League\LeagueContainerAdapter;
use PainelDLX\Application\Adapters\Router\League\LeagueRouterAdapter;
use PainelDLX\Application\Services\PainelDLX;
use Zend\Diactoros\ServerRequestFactory;
use League\Container\Container;
use League\Container\ReflectionContainer;

try {
    $server_request = ServerRequestFactory::fromGlobals();

    $league_container = new Container;
    $league_container->delegate(new ReflectionContainer);

    $container = new LeagueContainerAdapter($league_container);

    $strategy = new ApplicationStrategy;
    $strategy->setContainer($container);

    $league_router = new Router();
    $league_router->setStrategy($strategy);

    $router = new LeagueRouterAdapter($league_router);

    $painel_dlx = new PainelDLX(
        $server_request,
        $router,
        $container
    );

    $painel_dlx->init()->executar();
} catch (Exception $e) {
    var_dump($e);
}