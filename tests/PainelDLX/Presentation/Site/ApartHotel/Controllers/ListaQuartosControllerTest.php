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
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use PainelDLX\Application\Factories\CommandBusFactory;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\ListaQuartosController;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Factories\SessionFactory;
use Vilex\VileX;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class ListaQuartosControllerTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass \Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\ListaQuartosController
 */
class ListaQuartosControllerTest extends ReservasTestCase
{
    /**
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoEncontradoException
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoInformadoException
     * @throws \Doctrine\ORM\ORMException
     * @throws \PainelDLX\Application\Services\Exceptions\AmbienteNaoInformadoException
     * @throws \SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException
     * @throws \SechianeX\Exceptions\SessionAdapterNaoEncontradoException
     */
    public function test__construct(): ListaQuartosController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $command_bus = CommandBusFactory::create($this->container, Configure::get('app', 'mapping'));

        $controller = new ListaQuartosController(
            new VileX(),
            $command_bus(),
            $session,
            new DoctrineTransaction(EntityManagerX::getInstance())
        );

        $this->assertInstanceOf(ListaQuartosController::class, $controller);

        return $controller;
    }

    /**
     * @return array
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoEncontradoException
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoInformadoException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \PainelDLX\Application\Services\Exceptions\AmbienteNaoInformadoException
     * @throws \SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException
     * @throws \SechianeX\Exceptions\SessionAdapterNaoEncontradoException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function getQuartos(): array
    {
        self::setUp();

        $query = '
            select
                quarto_id
            from
                dlx_reservas_quartos
            limit 100     
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->prepare($query);
        $sql->execute();

        $ids = $sql->fetchAll();

        self::tearDown();

        return $ids;
    }

    /**
     * @param ListaQuartosController $controller
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
     * @covers ::listaQuartos
     * @depends test__construct
     */
    public function test_ListaQuartos_deve_retornar_HtmlResponse(ListaQuartosController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'campos' => [],
            'busca' => null
        ]);

        /** @var ServerRequestInterface $request */
        $response = $controller->listaQuartos($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @dataProvider getQuartos
     *
    public function test_ExcluirQuarto_deve_retornar_JsonResponse_sucesso(int $quarto_id)
    {
        $this->markTestSkipped('a exception do doctrine está fechando o EntityManager');

        /* $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn(['id' => $quarto_id]);

        /** @var ServerRequestInterface $request *
        $response = $this->controller->excluirQuarto($request);
        $this->assertInstanceOf(JsonResponse::class, $response); *
    } */
}
