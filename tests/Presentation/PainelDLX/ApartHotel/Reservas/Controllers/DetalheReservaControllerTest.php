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

use DateTime;
use DLX\Infrastructure\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\ORMException;
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Presentation\PainelDLX\ApartHotel\Reservas\Controllers\DetalheReservaController;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class DetalheReservaControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass DetalheReservaController
 */
class DetalheReservaControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @param bool $is_checkin_valido
     * @return int
     * @throws DBALException
     * @throws ORMException
     */
    public function getRandomReserva(bool $is_checkin_valido = false): int
    {
        $query_select = '
            select
                reserva_id,
                quarto_id
            from 
                reservas.Reserva
            order by 
                rand()
            limit 1
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->executeQuery($query_select);
        $rs = $sql->fetch();
        $reserva_id = $rs['reserva_id'];
        $quarto_id = $rs['quarto_id'];

        if ($is_checkin_valido) {
            $dt_checkin = (new DateTime())->modify('+1 day');
            $dt_checkout = (clone $dt_checkin)->modify('+2 days');

            $query_update = "
                update
                    reservas.Reserva
                set
                    checkin = :data_checkin,
                    checkout = :data_checkout,
                    status = 'Pendente'
                where
                    reserva_id = :reserva_id
            ";

            $sql = EntityManagerX::getInstance()->getConnection()->prepare($query_update);
            $sql->bindValue(':data_checkin', $dt_checkin->format('Y-m-d'));
            $sql->bindValue(':data_checkout', $dt_checkout->format('Y-m-d'));
            $sql->bindValue(':reserva_id', $reserva_id);
            $sql->execute();

            $query_delete_valores = '
                delete
                    v
                from
                    reservas.DisponibilidadeValor v
                inner join 
                    reservas.Disponibilidade as d on d.disponibilidade_id = v.disponibilidade_id
                inner join
                    reservas.Reserva r on r.quarto_id = d.quarto_id
                where
                    r.reserva_id = :reserva_id
                    and d.data between r.checkin and r.checkout
            ';

            $sql = EntityManagerX::getInstance()->getConnection()->prepare($query_delete_valores);
            $sql->bindValue(':reserva_id', $reserva_id, ParameterType::INTEGER);
            $sql->execute();

            $query_delete_dispon = '
                delete
                    d
                from
                    reservas.Disponibilidade as d
                inner join
                    reservas.Reserva r on r.quarto_id = d.quarto_id
                where
                    r.reserva_id = :reserva_id
                    and d.data between r.checkin and r.checkout
            ';

            $sql = EntityManagerX::getInstance()->getConnection()->prepare($query_delete_dispon);
            $sql->bindValue(':reserva_id', $reserva_id, ParameterType::INTEGER);
            $sql->execute();

            $query_gerar_dispon = 'call gerar_calendario (:dt_inicial, :dt_final, :quarto)';

            $sql = EntityManagerX::getInstance()->getConnection()->prepare($query_gerar_dispon);
            $sql->bindValue(':dt_inicial', $dt_checkin->format('Y-m-d'));
            $sql->bindValue(':dt_final', $dt_checkout->format('Y-m-d'));
            $sql->bindValue(':quarto', $quarto_id, ParameterType::INTEGER);
            $sql->execute();

            $query_update_dispon = '
                update
                    reservas.Disponibilidade
                set
                    quantidade = 10
                where
                    data between :data_inicial and :data_final
                    and quarto_id = :quarto_id
            ';

            $sql = EntityManagerX::getInstance()->getConnection()->prepare($query_update_dispon);
            $sql->bindValue(':data_inicial', $dt_checkin->format('Y-m-d'));
            $sql->bindValue(':data_final', $dt_checkout->format('Y-m-d'));
            $sql->bindValue(':quarto_id', $quarto_id, ParameterType::INTEGER);
            $sql->execute();

            $query_incluir_valores = "
                insert into reservas.DisponibilidadeValor (disponibilidade_id, quantidade_pessoas, valor)
                    select
                        d.disponibilidade_id,
                        1,
                        q.valor_minimo
                    from
                        reservas.Disponibilidade d
                    inner join
                        reservas.Reserva r on r.quarto_id = d.quarto_id
                    inner join
                        reservas.Quarto q on q.quarto_id = r.quarto_id
                    where
                        d.data between r.checkin and r.checkout
                        and r.reserva_id = :reserva_id1
                    
                    union
                    
                    select
                        d.disponibilidade_id,
                        2,
                        q.valor_minimo
                    from
                        reservas.Disponibilidade d
                    inner join
                        reservas.Reserva r on r.quarto_id = d.quarto_id
                    inner join
                        reservas.Quarto q on q.quarto_id = r.quarto_id
                    where
                        d.data between r.checkin and r.checkout
                        and r.reserva_id = :reserva_id2

                    union
                    
                    select
                        d.disponibilidade_id,
                        3,
                        q.valor_minimo
                    from
                        reservas.Disponibilidade d
                    inner join
                        reservas.Reserva r on r.quarto_id = d.quarto_id
                    inner join
                        reservas.Quarto q on q.quarto_id = r.quarto_id
                    where
                        d.data between r.checkin and r.checkout
                        and r.reserva_id = :reserva_id3
            ";

            $sql = EntityManagerX::getInstance()->getConnection()->prepare($query_incluir_valores);
            $sql->bindValue(':reserva_id1', $reserva_id, ParameterType::INTEGER);
            $sql->bindValue(':reserva_id2', $reserva_id, ParameterType::INTEGER);
            $sql->bindValue(':reserva_id3', $reserva_id, ParameterType::INTEGER);
            $sql->execute();
        }

        return $reserva_id;
    }

    /**
     * @return DetalheReservaController
     * @throws ORMException
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     */
    public function test__construct(): DetalheReservaController
    {
        /** @var Usuario|null $usuario */
        $usuario = EntityManagerX::getRepository(Usuario::class)->find(2);

        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');
        $session->set('usuario-logado', $usuario);

        $controller = self::$painel_dlx->getContainer()->get(DetalheReservaController::class);

        $this->assertInstanceOf(DetalheReservaController::class, $controller);

        return $controller;
    }

    /**
     * @param DetalheReservaController $controller
     * @throws DBALException
     * @throws ORMException
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @covers ::detalhesReserva
     * @depends test__construct
     */
    public function test_DetalhesReserva_deve_retornar_HtmlResponse(DetalheReservaController $controller)
    {
        $query = '
            select
                reserva_id
            from
                reservas.Reserva
            order by 
                rand()
            limit 1
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->executeQuery($query);
        $id = $sql->fetchColumn();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'id' => $id
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->detalhesReserva($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalheReservaController $controller
     * @throws Exception
     * @covers ::formConfirmarReserva
     * @depends test__construct
     */
    public function test_FormConfirmarReserva_deve_retornar_um_HtmlResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['id' => $id]);

        /** @var ServerRequestInterface $request */

        $response = $controller->formConfirmarReserva($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalheReservaController $controller
     * @throws Exception
     * @covers ::formCancelarReserva
     * @depends test__construct
     */
    public function test_FormCancelarReserva_deve_retornar_um_HtmlResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['id' => $id]);

        /** @var ServerRequestInterface $request */

        $response = $controller->formCancelarReserva($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalheReservaController $controller
     * @throws DBALException
     * @throws ORMException
     * @covers ::confirmarReserva
     * @depends test__construct
     */
    public function test_ConfimarReserva_deve_retornar_JsonResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva(true);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $id,
            'motivo' => ''
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->confirmarReserva($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @param DetalheReservaController $controller
     * @throws DBALException
     * @throws ORMException
     * @covers ::cancelarReserva
     * @depends test__construct
     */
    public function test_CancelarReserva_deve_retornar_JsonResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva(true);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $id,
            'motivo' => 'pq sim'
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->cancelarReserva($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @param DetalheReservaController $controller
     * @throws DBALException
     * @throws ORMException
     * @covers ::mostrarCpfCompleto
     * @depends test__construct
     */
    public function test_MostrarCpfCompleto_deve_retornar_JsonResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'id' => $id
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->mostrarCpfCompleto($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
