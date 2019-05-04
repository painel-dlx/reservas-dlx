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

namespace Reservas\PainelDLX\Tests\Infra\ORM\Doctrine\Repositories;

use DateTime;
use DLX\Infra\EntityManagerX;
use Doctrine\ORM\ORMException;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Tests\ReservasTestCase;

/**
 * Class QuartoRepositoryTest
 * @package Reservas\PainelDLX\Tests\Infra\ORM\Doctrine\Repositories
 * @coversDefaultClass \Reservas\PainelDLX\Infra\ORM\Doctrine\Repositories\QuartoRepository
 */
class QuartoRepositoryTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return QuartoRepositoryInterface
     * @throws ORMException
     */
    public function getQuartoRepository(): QuartoRepositoryInterface
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);
        return $quarto_repository;
    }

    /**
     * @throws ORMException
     */
    public function test_FindQuartosDisponiveis_deve_retornar_array_com_quartos_disponiveis()
    {
        $quarto_repository = $this->getQuartoRepository();
        $checkin = new DateTime('2019-05-04');
        $checkout = new DateTime('2019-05-06');
        $qtde_hospedes = 1;
        $qtde_quartos = 1;

        $lista_quartos_disponiveis = $quarto_repository->findQuartosDisponiveis(
            $checkin,
            $checkout,
            $qtde_hospedes,
            $qtde_quartos
        );

        $this->assertIsArray($lista_quartos_disponiveis);
    }
}
