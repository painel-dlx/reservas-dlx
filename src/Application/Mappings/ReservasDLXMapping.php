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

namespace Reservas\Application\Mappings;


use Reservas\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommand;
use Reservas\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommandHandler;
use Reservas\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommand;
use Reservas\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommandHandler;
use Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand;
use Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommandHandler;
use Reservas\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo\ListaDisponibilidadePorPeriodoCommand;
use Reservas\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo\ListaDisponibilidadePorPeriodoCommandHandler;
use Reservas\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommand;
use Reservas\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommandHandler;
use Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommand;
use Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommandHandler;
use Reservas\UseCases\Pedidos\CancelarPedido\CancelarPedidoCommand;
use Reservas\UseCases\Pedidos\CancelarPedido\CancelarPedidoCommandHandler;
use Reservas\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommand;
use Reservas\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommandHandler;
use Reservas\UseCases\Pedidos\FindPedidoItemPorId\FindPedidoItemPorIdCommand;
use Reservas\UseCases\Pedidos\FindPedidoItemPorId\FindPedidoItemPorIdCommandHandler;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommand;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler;
use Reservas\UseCases\Pedidos\ListaPedidos\ListaPedidosCommand;
use Reservas\UseCases\Pedidos\ListaPedidos\ListaPedidosCommandHandler;
use Reservas\UseCases\Pedidos\QuantidadePedidosPorStatus\QuantidadePedidosPorStatusCommand;
use Reservas\UseCases\Pedidos\QuantidadePedidosPorStatus\QuantidadePedidosPorStatusCommandHandler;
use Reservas\UseCases\Quartos\AdicionarMidiasQuarto\AdicionarMidiasQuartoCommand;
use Reservas\UseCases\Quartos\AdicionarMidiasQuarto\AdicionarMidiasQuartoCommandHandler;
use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommand;
use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommandHandler;
use Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommand;
use Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommandHandler;
use Reservas\UseCases\Quartos\ExcluirMidiaQuarto\ExcluirMidiaQuartoCommand;
use Reservas\UseCases\Quartos\ExcluirMidiaQuarto\ExcluirMidiaQuartoCommandHandler;
use Reservas\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommand;
use Reservas\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler;
use Reservas\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommand;
use Reservas\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommandHandler;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler;
use Reservas\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use Reservas\UseCases\Quartos\ListaQuartos\ListaQuartosCommandHandler;
use Reservas\UseCases\Reservas\CancelarReserva\CancelarReservaCommand;
use Reservas\UseCases\Reservas\CancelarReserva\CancelarReservaCommandHandler;
use Reservas\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommand;
use Reservas\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommandHandler;
use Reservas\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommand;
use Reservas\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommandHandler;
use Reservas\UseCases\Reservas\ListaReservas\ListaReservasCommand;
use Reservas\UseCases\Reservas\ListaReservas\ListaReservasCommandHandler;
use Reservas\UseCases\Reservas\ListaReservasPorPeriodo\ListaReservasPorPeriodoCommandHandler;
use Reservas\UseCases\Reservas\ListaReservasPorPeriodo\ListaReservasPorPeriodoCommand;
use Reservas\UseCases\Reservas\SalvarReserva\SalvarReservaCommand;
use Reservas\UseCases\Reservas\SalvarReserva\SalvarReservaCommandHandler;

class ReservasDLXMapping
{
    private $mapping = [
        GetQuartoPorIdCommand::class => GetQuartoPorIdCommandHandler::class,
        ListaQuartosCommand::class => ListaQuartosCommandHandler::class,
        CriarNovoQuartoCommand::class => CriarNovoQuartoCommandHandler::class,
        EditarQuartoCommand::class => EditarQuartoCommandHandler::class,
        ExcluirQuartoCommand::class => ExcluirQuartoCommandHandler::class,
        GetDisponibilidadePorDataQuartoCommand::class => GetDisponibilidadePorDataQuartoCommandHandler::class,
        ListaDisponibilidadePorPeriodoCommand::class => ListaDisponibilidadePorPeriodoCommandHandler::class,
        SalvarDisponibilidadeQuartoCommand::class => SalvarDisponibilidadeQuartoCommandHandler::class,
        SalvarDisponPeriodoCommand::class => SalvarDisponPeriodoCommandHandler::class,
        GetReservaPorIdCommand::class => GetReservaPorIdCommandHandler::class,
        ListaReservasCommand::class => ListaReservasCommandHandler::class,
        ConfirmarReservaCommand::class => ConfirmarReservaCommandHandler::class,
        CancelarReservaCommand::class => CancelarReservaCommandHandler::class,
        SalvarReservaCommand::class => SalvarReservaCommandHandler::class,
        ListaPedidosCommand::class => ListaPedidosCommandHandler::class,
        GetPedidoPorIdCommand::class => GetPedidoPorIdCommandHandler::class,
        GerarReservasPedidoCommand::class => GerarReservasPedidoCommandHandler::class,
        ConfirmarPgtoPedidoCommand::class => ConfirmarPgtoPedidoCommandHandler::class,
        MostrarCpfCompletoCommand::class => MostrarCpfCompletoCommandHandler::class,
        MostrarCpfCompletoPedidoCommand::class => MostrarCpfCompletoPedidoCommandHandler::class,
        CancelarPedidoCommand::class => CancelarPedidoCommandHandler::class,
        GerarDisponibilidadesQuartoCommand::class => GerarDisponibilidadesQuartoCommandHandler::class,
        AdicionarMidiasQuartoCommand::class => AdicionarMidiasQuartoCommandHandler::class,
        ExcluirMidiaQuartoCommand::class => ExcluirMidiaQuartoCommandHandler::class,
        QuantidadePedidosPorStatusCommand::class => QuantidadePedidosPorStatusCommandHandler::class,
        ListaReservasPorPeriodoCommand::class => ListaReservasPorPeriodoCommandHandler::class,
        FindPedidoItemPorIdCommand::class => FindPedidoItemPorIdCommandHandler::class,
    ];

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }
}