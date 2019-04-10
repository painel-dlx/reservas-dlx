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

use PainelDLX\Application\Middlewares\Exceptions\UsuarioNaoLogadoException;
use PainelDLX\Application\Middlewares\Exceptions\UsuarioNaoPossuiPermissaoException;
use PainelDLX\Application\Services\PainelDLX;
use RautereX\Exceptions\RotaNaoEncontradaException;
use Zend\Diactoros\ServerRequestFactory;
use League\Container\Container;
use League\Container\ReflectionContainer;

$server_request = ServerRequestFactory::fromGlobals();

$container = new Container;
$container->delegate(new ReflectionContainer);

try {
    $painel_dlx = new PainelDLX($server_request, $container);
    $painel_dlx
        ->adicionarDiretorioInclusao(dirname(__FILE__))
        ->init()
        ->executar();
} catch (UsuarioNaoLogadoException $e) {
    $query_param = $server_request->getQueryParams();
    $query_param['task'] = '/painel-dlx/login';
    $server_request_login = $server_request->withQueryParams($query_param);
    $painel_dlx->redirect($server_request_login);
} catch (RotaNaoEncontradaException $e) {
    $query_param = $server_request->getQueryParams();
    $query_param['task'] = '/painel-dlx/erro-http';
    $query_param['erro'] = 404;
    $server_request_404 = $server_request->withQueryParams($query_param);
    $painel_dlx->redirect($server_request_404);
} catch (UsuarioNaoPossuiPermissaoException $e) {
    $query_param = $server_request->getQueryParams();
    $query_param['task'] = '/painel-dlx/erro-http';
    $query_param['erro'] = 403;
    $server_request_403 = $server_request->withQueryParams($query_param);
    $painel_dlx->redirect($server_request_403);
} catch (Exception $e) {
    var_dump($e);
}