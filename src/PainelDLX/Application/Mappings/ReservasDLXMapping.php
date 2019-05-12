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

namespace Reservas\PainelDLX\Application\Mappings;


use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommand;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommandHandler;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommand;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo\ListaDisponibilidadePorPeriodoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo\ListaDisponibilidadePorPeriodoCommandHandler;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommandHandler;
use Reservas\PainelDLX\UseCases\Emails\EnviarNotificacaoCancelamentoPedido\EnviarNotificacaoCancelamentoPedidoCommand;
use Reservas\PainelDLX\UseCases\Emails\EnviarNotificacaoCancelamentoPedido\EnviarNotificacaoCancelamentoPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Emails\EnviarNotificacaoConfirmacaoPedido\EnviarNotificacaoConfirmacaoPedidoCommand;
use Reservas\PainelDLX\UseCases\Emails\EnviarNotificacaoConfirmacaoPedido\EnviarNotificacaoConfirmacaoPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Pedidos\CancelarPedido\CancelarPedidoCommand;
use Reservas\PainelDLX\UseCases\Pedidos\CancelarPedido\CancelarPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommand;
use Reservas\PainelDLX\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommand;
use Reservas\PainelDLX\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler;
use Reservas\PainelDLX\UseCases\Pedidos\ListaPedidos\ListaPedidosCommand;
use Reservas\PainelDLX\UseCases\Pedidos\ListaPedidos\ListaPedidosCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\FiltrarReservasPorPeriodo\FiltrarReservasPorPeriodoCommand;
use Reservas\PainelDLX\UseCases\Reservas\FiltrarReservasPorPeriodo\FiltrarReservasPorPeriodoCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommand;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\ListaReservas\ListaReservasCommand;
use Reservas\PainelDLX\UseCases\Reservas\ListaReservas\ListaReservasCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\SalvarReserva\SalvarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\SalvarReserva\SalvarReservaCommandHandler;

class ReservasDLXMapping
{
    private $mapping = [
        GetQuartoPorIdCommand::class => GetQuartoPorIdCommandHandler::class,
        ListaQuartosCommand::class => ListaQuartosCommandHandler::class,
        SalvarQuartoCommand::class => SalvarQuartoCommandHandler::class,
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
        EnviarNotificacaoConfirmacaoPedidoCommand::class => EnviarNotificacaoConfirmacaoPedidoCommandHandler::class,
        CancelarPedidoCommand::class => CancelarPedidoCommandHandler::class,
        EnviarNotificacaoCancelamentoPedidoCommand::class => EnviarNotificacaoCancelamentoPedidoCommandHandler::class,
        GerarDisponibilidadesQuartoCommand::class => GerarDisponibilidadesQuartoCommandHandler::class,
        FiltrarReservasPorPeriodoCommand::class => FiltrarReservasPorPeriodoCommandHandler::class,
    ];

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }
}