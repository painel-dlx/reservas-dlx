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

namespace Reservas\Tests\Presentation\Site\ApartHotel\Controllers;

use DLX\Core\Exceptions\ArquivoConfiguracaoNaoEncontradoException;
use DLX\Core\Exceptions\ArquivoConfiguracaoNaoInformadoException;
use DLX\Infrastructure\EntityManagerX;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use PainelDLX\Application\Services\Exceptions\AmbienteNaoInformadoException;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers\ListaQuartosController;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Class ListaQuartosControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass ListaQuartosController
 */
class ListaQuartosControllerTest extends ReservasTestCase
{
    /**
     * @return ListaQuartosController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     */
    public function test__construct(): ListaQuartosController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $controller = self::$painel_dlx->getContainer()->get(ListaQuartosController::class);

        $this->assertInstanceOf(ListaQuartosController::class, $controller);

        return $controller;
    }

    /**
     * @return array
     * @throws MappingException
     * @throws ORMException
     * @throws DBALException
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
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
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
