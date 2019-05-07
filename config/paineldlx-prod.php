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
    'tipo-ambiente' => Configure::PRODUCAO,

    'app' => [
        'nome' => 'reservas-dlx',
        'nome-amigavel' => 'Painel DLX',
        'base-html' => '/',
        'base-url' => 'https://brasiliaaparthoteis.com.br/',
        'rotas' => include 'rotas.php',
        'service-providers' => include 'service_providers.php',
        'mapping' => include 'mapping.php',
        'favicon' => PainelDLX::$dir . '/src/Presentation/Site/public/imgs/favicon.png'
    ],

    'bd' => [
        'orm' => 'doctrine',
        'mapping' => 'yaml',
        'dev-mode' => true,
        //'debug' => EchoSQLLogger::class,
        'dir' => [
            'src/PainelDLX/Infra/ORM/Doctrine/Mappings',
            'src/PainelDLX/Infra/ORM/Doctrine/Repositories',

            // todo: colocar isso no outro arquivo de configuração
            'src/Website/Infra/ORM/Doctrine/Mappings',
            'src/Website/Infra/ORM/Doctrine/Repositories',

            "{$dir_painel_dlx}src/Infra/ORM/Doctrine/Mappings",
            "{$dir_painel_dlx}src/Infra/ORM/Doctrine/Repositories"
        ],
        'types' => [
            'cpf' => CPFType::class
        ],
        'conexao' => [
            'dbname' => 'brasilia_aphoteis',
            'user' => 'brasilia_aphotel',
            'password' => 'H0t31$',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ]
    ]
];