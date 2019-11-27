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

use CPF\ORM\Doctrine\Types\CPFType;
use DLX\Core\Configure;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use PainelDLX\Application\Services\PainelDLX;

// ini_set('session.save_handler', 'files');

$dir_painel_dlx = !empty(PainelDLX::$dir) ? PainelDLX::$dir . DIRECTORY_SEPARATOR : '';

return [
    'tipo-ambiente' => Configure::DEV,

    'app' => [
        'nome' => 'reservas-dlx',
        'nome-amigavel' => 'Reservas DLX',
        'base-html' => '/',
        'base-url' => 'http://reservas-dlx.localhost/',
        'diretorios' => include 'painel-dlx/diretorios.php',
        'rotas' => include 'painel-dlx/rotas.php',
        'service-providers' => include 'painel-dlx/service_providers.php',
        'mapping' => include 'painel-dlx/mapping.php',
        'favicon' => PainelDLX::$dir . '/public/imgs/favicon.png',
        'versao' => '1.1.0'
    ],

    'bd' => [
        'orm' => 'doctrine',
        'mapping' => 'yaml',
        'dev-mode' => true,
        //'debug' => EchoSQLLogger::class,
        'dir' => [
            'src/Infrastructure/ORM/Doctrine/Mappings',
            'src/Infrastructure/ORM/Doctrine/Repositories',

            "{$dir_painel_dlx}src/Infrastructure/ORM/Doctrine/Mappings",
            "{$dir_painel_dlx}src/Infrastructure/ORM/Doctrine/Repositories"
        ],
        'types' => [
            'cpf' => CPFType::class
        ],
        'conexao' => [
            'dbname' => 'reservas',
            'user' => 'root',
            'password' => '$d5Ro0t',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ]
    ]
];