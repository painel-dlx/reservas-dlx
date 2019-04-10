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
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\EditarQuartoController;
use Reservas\PainelDLX\Tests\ReservasTestCase;
use SechianeX\Factories\SessionFactory;
use Vilex\VileX;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class EditarQuartoControllerTest extends ReservasTestCase
{
    /** @var EditarQuartoController */
    private $controller;

    /**
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoEncontradoException
     * @throws \DLX\Core\Exceptions\ArquivoConfiguracaoNaoInformadoException
     * @throws \Doctrine\ORM\ORMException
     * @throws \PainelDLX\Application\Services\Exceptions\AmbienteNaoInformadoException
     * @throws \SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException
     * @throws \SechianeX\Exceptions\SessionAdapterNaoEncontradoException
     */
    protected function setUp()
    {
        parent::setUp();

        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $this->controller = new EditarQuartoController(
            new VileX(),
            CommandBusAdapter::create(new CommandHandlerMiddleware(
                new ClassNameExtractor,
                new ContainerLocator($this->container, Configure::get('app', 'mapping')),
                new HandleInflector
            )),
            $session,
            new DoctrineTransaction(EntityManagerX::getInstance())
        );
    }

    /**
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function getQuartosAtivos()
    {
        self::setUp();

        $query = '
            select
                quarto_id
            from
                dlx_reservas_quartos
            where
                quarto_publicar = 1
                and quarto_delete = 0
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->prepare($query);
        $sql->execute();

        $ids = $sql->fetchAll();

        self::tearDown();

        return $ids;
    }

    /**
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
     * @dataProvider getQuartosAtivos
     */
    public function test_FormEditarQuarto_deve_retornar_HtmlResponse(int $quarto_id)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['id' => $quarto_id]);

        /** @var ServerRequestInterface $request */
        $response = $this->controller->formEditarQuarto($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param int $quarto_id
     * @dataProvider getQuartosAtivos
     */
    public function test_EditarInformacoesQuarto_deve_retornar_JsonResponse(int $quarto_id)
    {
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
        $response = $this->controller->editarInformacoesQuarto($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
