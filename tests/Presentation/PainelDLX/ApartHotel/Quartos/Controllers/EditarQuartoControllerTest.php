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

use DLX\Infrastructure\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Exception;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers\EditarQuartoController;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Vilex\Exceptions\PaginaMestraInvalidaException;
use Vilex\Exceptions\TemplateInvalidoException;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class EditarQuartoControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass \Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers\EditarQuartoController
 */
class EditarQuartoControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return EditarQuartoController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     */
    public function test__construct(): EditarQuartoController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $controller = self::$painel_dlx->getContainer()->get(EditarQuartoController::class);

        $this->assertInstanceOf(EditarQuartoController::class, $controller);

        return $controller;
    }

    /**
     * @return mixed[]
     * @throws DBALException
     * @throws ORMException
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getQuartoRandom()
    {
        $query = '
            select
                quarto_id
            from
                reservas.Quarto
            where
                deletado = 0
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
     * @throws DBALException
     * @throws ORMException
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     * @throws \Doctrine\DBAL\Driver\Exception
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
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     * @covers ::formEditarQuarto
     * @depends test__construct
     */
    public function test_FormEditarQuarto_deve_renderizar_mensagem_de_erro_quando_nao_encontrar_Quarto(EditarQuartoController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['id' => 0]);

        /** @var ServerRequestInterface $request */
        $response = $controller->formEditarQuarto($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertRegExp('~Quarto não encontrado com o ID informado: \d+.~', (string)$response->getBody());
    }

    /**
     * @param EditarQuartoController $controller
     * @throws DBALException
     * @throws ORMException
     * @throws \Doctrine\DBAL\Driver\Exception
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
            'link' => '/teste/' . uniqid()
        ]);

        /** @var ServerRequestInterface $request */
        $response = $controller->editarInformacoesQuarto($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
