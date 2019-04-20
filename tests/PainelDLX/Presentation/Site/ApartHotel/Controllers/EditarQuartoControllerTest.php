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
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Exception;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use PainelDLX\Application\Factories\CommandBusFactory;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\EditarQuartoController;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class EditarQuartoControllerTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass \Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\EditarQuartoController
 */
class EditarQuartoControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return EditarQuartoController
     * @throws ORMException
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     */
    public function test__construct(): EditarQuartoController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $command_bus = CommandBusFactory::create(self::$container, Configure::get('app', 'mapping'));

        $controller = new EditarQuartoController(
            new VileX(),
            $command_bus(),
            $session,
            new DoctrineTransaction(EntityManagerX::getInstance())
        );

        $this->assertInstanceOf(EditarQuartoController::class, $controller);

        return $controller;
    }

    /**
     * @return mixed[]
     * @throws DBALException
     * @throws ORMException
     * @throws Exception
     */
    public function getQuartoRandom()
    {
        $query = '
            select
                quarto_id
            from
                dlx_reservas_quartos
            where
                quarto_publicar = 1
                and quarto_delete = 0
            order by 
                rand()
            limit 1
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->prepare($query);
        $sql->execute();

        return $sql->fetchColumn();
    }

    /**
     * @param EditarQuartoController $controller
     * @throws ContextoInvalidoException
     * @throws DBALException
     * @throws ORMException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @covers ::formEditarQuarto
     * @depends test__construct
     */
    public function test_FormEditarQuarto_deve_retornar_HtmlResponse(EditarQuartoController $controller)
    {
        $quarto_id = $this->getQuartoRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['id' => $quarto_id]);

        /** @var ServerRequestInterface $request */
        $response = $controller->formEditarQuarto($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param EditarQuartoController $controller
     * @throws DBALException
     * @throws ORMException
     * @covers ::editarInformacoesQuarto
     * @depends test__construct
     */
    public function test_EditarInformacoesQuarto_deve_retornar_JsonResponse(EditarQuartoController $controller)
    {
        $quarto_id = $this->getQuartoRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $quarto_id,
            'nome' => 'TESTE UNITÁRIO',
            'descricao' => '',
            'max_hospedes' => 1,
            'qtde' => 1,
            'tamanho_m2' => 10,
            'valor_min' => 10.00,
            'link' => '/teste/teste'
        ]);

        /** @var ServerRequestInterface $request */
        $response = $controller->editarInformacoesQuarto($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
