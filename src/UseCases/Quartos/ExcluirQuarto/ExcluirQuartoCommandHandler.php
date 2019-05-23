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
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;

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
        // Caso ocorra um erro por constraint, o registro deve ser marcado como deletado
        $command->getQuarto()->setDeletado(true);
        $this->quarto_repository->update($command->getQuarto());

        try {
            $this->quarto_repository->delete($command->getQuarto());
        } catch (ForeignKeyConstraintViolationException $e) {
            // Erro de exclusão por constraint não precisa ser lançada pra controller,
            // pois o registro já foi marcado como deletado
        }

        return true;
    }
}