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

namespace Reservas\Infrastructure\ORM\Doctrine\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Reservas\Domain\Quartos\Contracts\QuartoMidiaCollectionInterface;
use Reservas\Domain\Quartos\Entities\QuartoMidia;

class QuartoMidiaCollection extends ArrayCollection implements QuartoMidiaCollectionInterface
{
    /**
     * Adicionar uma nova mídia a collection
     * @param QuartoMidia $midia
     */
    public function addMidia(QuartoMidia $midia): void
    {
        if ($this->hasArquivo($midia->getArquivo())) {
            // todo: criar exception para travar a inclusão de mídias duplicadas
        }

        $this->add($midia);
    }

    /**
     * Verifica se o arquivo existe dentro da collection
     * @param string $arquivo
     * @return bool
     */
    public function hasArquivo(string $arquivo): bool
    {
        return $this->exists(function ($key, QuartoMidia $midia) use ($arquivo) {
             return $midia->getArquivo() === $arquivo;
        });
    }

    /**
     * @param string $arquivo
     * @return QuartoMidia|null
     */
    public function findByArquivo(string $arquivo): ?QuartoMidia
    {
        $midia = $this->filter(function (QuartoMidia $quarto_midia) use ($arquivo) {
            return $quarto_midia->getArquivo() === $arquivo;
        })->first();

        return $midia ?: null;
    }
}