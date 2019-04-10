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

namespace Reservas\PainelDLX\Application\ServiceProviders;


use DLX\Infra\EntityManagerX;
use League\Container\Container;
use League\Container\ServiceProvider\AbstractServiceProvider;
use PainelDLX\Infra\ORM\Doctrine\Services\RepositoryFactory;
use Reservas\PainelDLX\Domain\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\PainelDLX\Domain\Repositories\QuartoRepositoryInterface;

class ReservasServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        QuartoRepositoryInterface::class,
        DisponibilidadeRepositoryInterface::class
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    public function register()
    {
        /** @var Container $container */
        $container = $this->getContainer();

        $container->add(
            QuartoRepositoryInterface::class,
            RepositoryFactory::create(Quarto::class)
        );

        $container->add(
            DisponibilidadeRepositoryInterface::class,
            RepositoryFactory::create(Disponibilidade::class)
        );
    }
}