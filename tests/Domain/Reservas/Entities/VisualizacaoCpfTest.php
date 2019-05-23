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

namespace Reservas\Tests\Domain\Reservas\Entities;

use DateTime;
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PHPUnit\Framework\TestCase;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\Domain\Reservas\Entities\VisualizacaoCpf;

/**
 * Class VisualizacaoCpfTest
 * @package Reservas\Tests\Domain\Reservas\Entities
 * @coversDefaultClass VisualizacaoCpf
 */
class VisualizacaoCpfTest extends TestCase
{
    /**
     * @return VisualizacaoCpf
     * @throws Exception
     */
    public function test__construct(): VisualizacaoCpf
    {
        $quarto = new Quarto('Teste de Quarto', 10, 10);
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+1 day');
        $reserva = new Reserva($quarto, $checkin, $checkout, 1);
        $usuario = new Usuario('Nome do Funcionário', 'funcionario@gmail.com');

        $vis_cpf = new VisualizacaoCpf($reserva, $usuario);

        $this->assertInstanceOf(VisualizacaoCpf::class, $vis_cpf);

        $this->assertInstanceOf(Reserva::class, $vis_cpf->getReserva());
        $this->assertEquals($reserva, $vis_cpf->getReserva());

        $this->assertInstanceOf(Usuario::class, $vis_cpf->getUsuario());
        $this->assertEquals($usuario, $vis_cpf->getUsuario());

        $this->assertInstanceOf(DateTime::class, $vis_cpf->getData());
        $this->assertEquals(new DateTime(), $vis_cpf->getData(), '', 0.1);

        return $vis_cpf;
    }
}
