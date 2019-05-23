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

namespace Reservas\Domain\Quartos\Validators;


use Reservas\Domain\Quartos\Contracts\QuartoValidatorInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Exceptions\LinkQuartoUtilizadoException;
use Reservas\Domain\Quartos\Exceptions\ValidarQuartoException;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;

/**
 * Class VerificarLinkUtilizado
 * @package Reservas\Domain\Validators\Quartos
 * @covers VerificarLinkUtilizadoTest
 */
class VerificarLinkUtilizado implements QuartoValidatorInterface
{
    /**
     * @var QuartoRepositoryInterface
     */
    private $quarto_repository;

    /**
     * VerificarNomeDuplicado constructor.
     * @param QuartoRepositoryInterface $quarto_repository
     */
    public function __construct(QuartoRepositoryInterface $quarto_repository)
    {
        $this->quarto_repository = $quarto_repository;
    }

    /**
     * @param Quarto $quarto
     * @return bool
     * @throws LinkQuartoUtilizadoException
     * @throws ValidarQuartoException
     */
    public function validar(Quarto $quarto): bool
    {
        $exists = $this->quarto_repository->existsOutroQuartoComMesmoLink($quarto);

        if ($exists) {
            throw ValidarQuartoException::linkQuartoJaUtilizado($quarto->getLink());
        }

        return true;
    }
}