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

namespace Reservas\PainelDLX\Tests\PainelDLX\Presentation\Site\ApartHotel\Controllers;

use DLX\Core\CommandBus\CommandBusAdapter;
use DLX\Core\Configure;
use DLX\Infra\EntityManagerX;
use DLX\Infra\ORM\Doctrine\Services\DoctrineTransaction;
use Exception;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use PainelDLX\Application\Factories\CommandBusFactory;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\CadastrarQuartoController;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Factories\SessionFactory;
use Vilex\VileX;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class CadastrarQuartoControllerTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass \Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\CadastrarQuartoController
 */
class CadastrarQuartoControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return CadastrarQuartoController
     * @throws \Doctrine\ORM\ORMException
     * @throws \SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException
     * @throws \SechianeX\Exceptions\SessionAdapterNaoEncontradoException
     */
    public function test__construct(): CadastrarQuartoController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $command_bus = CommandBusFactory::create(self::$container, Configure::get('app', 'mapping'));

        $controller = new CadastrarQuartoController(
            new VileX(),
            $command_bus(),
            $session,
            new DoctrineTransaction(EntityManagerX::getInstance())
        );

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
        $request = $this->createMock(ServerRequestInterface::class);

        /** @var ServerRequestInterface $request */
        $response = $controller->formNovoQuarto($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param CadastrarQuartoController $controller
     * @covers ::salvarNovoQuarto
     * @depends test__construct
     */
    public function test_SalvarNovoQuarto_deve_retornar_JsonResponse(CadastrarQuartoController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'nome' => 'TESTE UNITÁRIO',
            'descricao' => '',
            'max_hospedes' => 1,
            'qtde' => 1,
            'tamanho_m2' => 10,
            'valor_min' => 10.00,
            'link' => '/teste/teste'
        ]);

        /** @var ServerRequestInterface $request */
        $response = $controller->salvarNovoQuarto($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
