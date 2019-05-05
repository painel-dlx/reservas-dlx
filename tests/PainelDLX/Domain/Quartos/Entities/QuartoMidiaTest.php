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

namespace Reservas\PainelDLX\Tests\Domain\Quartos\Entities;

use PHPUnit\Framework\TestCase;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Entities\QuartoMidia;

/**
 * Class QuartoMidiaTest
 * @package Reservas\PainelDLX\Tests\Domain\Quartos\Entities
 * @covers \Reservas\PainelDLX\Domain\Quartos\Entities\QuartoMidia
 */
class QuartoMidiaTest extends TestCase
{
    /**
     * @return QuartoMidia
     */
    public function test__construct(): QuartoMidia
    {
        $arquivo = 'teste/teste/teste.png';
        $midia = new QuartoMidia($arquivo);

        $this->assertInstanceOf(QuartoMidia::class, $midia);
        $this->assertEquals($arquivo, $midia->getArquivo());

        return $midia;
    }

    public function test_GetTagHtml_deve_retornar_a_tag_html_correspondente_a_midia()
    {
        $regex_img = '~^\<img\s+~';
        $regex_video = '~^\<video\>+~';

        $quarto = new Quarto('Teste de Quarto', 10, 10);

        $arquivo_foto = '../../../../Helpers/arquivos/test_code.jpg';
        $midia_foto = new QuartoMidia($arquivo_foto);
        $midia_foto->setQuarto($quarto);

        $arquivo_video = '../../../../Helpers/arquivos/test_video.mp4';
        $midia_video = new QuartoMidia($arquivo_video);
        $midia_video->setQuarto($quarto);

        $this->assertRegExp($regex_img, $midia_foto->getTagHtml());
        $this->assertRegExp($regex_video, $midia_video->getTagHtml());
    }
}
