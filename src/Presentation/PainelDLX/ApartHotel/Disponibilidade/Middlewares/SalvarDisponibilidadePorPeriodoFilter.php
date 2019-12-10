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

namespace Reservas\Presentation\PainelDLX\ApartHotel\Disponibilidade\Middlewares;


use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class SalvarDisponibilidadePorPeriodoFilter implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'quarto_id' => FILTER_VALIDATE_INT,
            'data_inicial' => FILTER_DEFAULT,
            'data_final' => FILTER_DEFAULT,
            'qtde' => FILTER_VALIDATE_INT,
            'desconto' => ['filter' => FILTER_VALIDATE_FLOAT, 'options' => ['min_range' => 0.00, 'max_range' => 99.99, 'default' => 0]],
            'valores' => ['filter' => FILTER_VALIDATE_FLOAT, 'flags' => FILTER_REQUIRE_ARRAY]
        ]);

        try {
            if (empty($post['quarto_id'])) {
                throw new InvalidArgumentException('Por favor, informe o quarto.');
            }

            if (empty($post['qtde'])) {
                throw new InvalidArgumentException('Por favor, informe a quantidade total de quartos disponíveis.');
            }

            if (empty($post['data_inicial'])) {
                throw new InvalidArgumentException('Data inicial inválida.');
            }

            if (empty($post['data_final'])) {
                throw new InvalidArgumentException('Data final inválida.');
            }

            if ($post['desconto'] < 0.00 || $post['desconto'] > 99.99) {
                throw new InvalidArgumentException('O desconto deve ser entre 0% e 99.99%.');
            }
        } catch (InvalidArgumentException $e) {
            $json['tipo'] = 'erro';
            $json['mensagem'] = $e->getMessage();

            return new JsonResponse($json);
        }

        return $handler->handle($request->withParsedBody($post));
    }
}