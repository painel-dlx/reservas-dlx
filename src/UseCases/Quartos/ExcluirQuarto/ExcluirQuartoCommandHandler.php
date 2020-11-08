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

namespace Reservas\UseCases\Quartos\ExcluirQuarto;


use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;

/**
 * Class ExcluirQuartoCommandHandler
 * @package Reservas\UseCases\Quartos\ExcluirQuarto
 * @covers ExcluirQuartoCommandHandlerTest
 */
class ExcluirQuartoCommandHandler
{
    /**
     * @var QuartoRepositoryInterface
     */
    private $quarto_repository;

    /**
     * ExcluirQuartoCommandHandler constructor.
     * @param QuartoRepositoryInterface $quarto_repository
     */
    public function __construct(QuartoRepositoryInterface $quarto_repository)
    {
        $this->quarto_repository = $quarto_repository;
    }

    /**
     * @param ExcluirQuartoCommand $command
     * @return bool
     */
    public function handle(ExcluirQuartoCommand $command): bool
    {
        $quarto = $command->getQuarto();

        $has_reservas = $this->quarto_repository->hasReservas($quarto);
        $has_reservas ? $this->marcarQuartoComoDeletado($quarto) : $this->excluirQuarto($quarto);

        return true;
    }

    /**
     * @param Quarto $quarto
     */
    private function marcarQuartoComoDeletado(Quarto $quarto): void
    {
        $quarto->setDeletado(true);
        $this->quarto_repository->update($quarto);
    }

    /**
     * @param Quarto $quarto
     */
    private function excluirQuarto(Quarto $quarto): void
    {
        $this->quarto_repository->delete($quarto);
    }
}