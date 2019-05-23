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

use PainelDLX\Application\Routes\ConfigSmtpRouter;
use PainelDLX\Application\Routes\ErrosRouter;
use PainelDLX\Application\Routes\GruposUsuariosRouter;
use PainelDLX\Application\Routes\HomeRouter;
use PainelDLX\Application\Routes\LoginRouter;
use PainelDLX\Application\Routes\PermissoesRouter;
use PainelDLX\Application\Routes\UsuariosRouter;
use Reservas\Application\Routes\DisponibilidadeRouter;
use Reservas\Application\Routes\EmailsRouter;
use Reservas\Application\Routes\PedidosRouter;
use Reservas\Application\Routes\QuartosRouter;
use Reservas\Application\Routes\ReservasRouter;

return [
    // Painel DLX
    HomeRouter::class,
    ErrosRouter::class,
    UsuariosRouter::class,
    PermissoesRouter::class,
    GruposUsuariosRouter::class,
    LoginRouter::class,
    ConfigSmtpRouter::class,

    // Reservas / Apart Hotel
    QuartosRouter::class,
    DisponibilidadeRouter::class,
    ReservasRouter::class,
    PedidosRouter::class,

    // Emails
    EmailsRouter::class,
];