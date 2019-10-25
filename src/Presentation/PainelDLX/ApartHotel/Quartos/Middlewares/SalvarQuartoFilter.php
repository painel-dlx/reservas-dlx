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

namespace Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Middlewares;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SalvarQuartoFilter implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'quarto_id' => FILTER_VALIDATE_INT,
            'nome' => ['filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_EMPTY_STRING_NULL],
            'descricao' => ['filter' => FILTER_DEFAULT, 'flags' => FILTER_FLAG_EMPTY_STRING_NULL],
            'max_hospedes' => FILTER_VALIDATE_INT,
            'qtde' => FILTER_VALIDATE_INT,
            'tamanho_m2' => FILTER_VALIDATE_INT,
            'valor_min' => FILTER_VALIDATE_FLOAT,
            'link' => ['filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_EMPTY_STRING_NULL]
        ]);

        return $handler->handle($request->withParsedBody($post));
    }
}