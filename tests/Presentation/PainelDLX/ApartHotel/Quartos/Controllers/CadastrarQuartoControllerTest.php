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

namespace Reservas\Tests\Presentation\PainelDLX\ApartHotel\Quartos\Controllers;

use Exception;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers\CadastrarQuartoController;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class CadastrarQuartoControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass CadastrarQuartoController
 */
class CadastrarQuartoControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return CadastrarQuartoController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     */
    public function test__construct(): CadastrarQuartoController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $controller = self::$painel_dlx->getContainer()->get(CadastrarQuartoController::class);

        $this->assertInstanceOf(CadastrarQuartoController::class, $controller);

        return $controller;
    }

    /**
     * @param CadastrarQuartoController $controller
     * @covers ::formNovoQuarto
     * @depends test__construct
     * @throws Exception
     */
    public function test_FormNovoQuarto_deve_retornar_HtmlResponse(CadastrarQuartoController $controller)
    {
        $response = $controller->formNovoQuarto();
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param CadastrarQuartoController $controller
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     * @covers ::salvarNovoQuarto
     * @depends test__construct
     */
    public function test_SalvarNovoQuarto_deve_retornar_JsonResponse(CadastrarQuartoController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'nome' => 'Teste: CadastrarQuartoController::salvarNovoQuarto',
            'descricao' => '',
            'max_hospedes' => 1,
            'qtde' => 1,
            'tamanho_m2' => 10,
            'valor_min' => 12.34,
            'link' => '/teste/' . uniqid()
        ]);

        $session = SessionFactory::createPHPSession();
        $session->set('editando:quarto', new Quarto('', 5, 100));

        /** @var ServerRequestInterface $request */
        $response = $controller->salvarNovoQuarto($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
