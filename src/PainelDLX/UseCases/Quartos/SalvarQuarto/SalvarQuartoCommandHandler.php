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

namespace Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto;

use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\Domain\Quartos\Validators\QuartoValidator;
use Reservas\PainelDLX\Domain\Quartos\Validators\QuartoValidatorEnum;

/**
 * Class SalvarQuartoCommandHandler
 * @package Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto
 * @covers SalvarQuartoCommandHandlerTest
 */
class SalvarQuartoCommandHandler
{
    /**
     * @var QuartoRepositoryInterface
     */
    private $quarto_repository;

    /**
     * SalvarQuartoCommandHandler constructor.
     * @param QuartoRepositoryInterface $quarto_repository
     */
    public function __construct(QuartoRepositoryInterface $quarto_repository)
    {
        $this->quarto_repository = $quarto_repository;
    }

    /**
     * @param SalvarQuartoCommand $command
     * @return Quarto
     */
    public function handle(SalvarQuartoCommand $command): Quarto
    {
        $quarto = $command->getQuarto();

        if (empty($quarto->getId())) {
            (new QuartoValidator(QuartoValidatorEnum::CADASTRAR))->validar($quarto, $this->quarto_repository);
            $this->quarto_repository->create($quarto);
        } else {
            (new QuartoValidator(QuartoValidatorEnum::EDITAR))->validar($quarto, $this->quarto_repository);
            $this->quarto_repository->update($quarto);
        }

        return $quarto;
    }
}