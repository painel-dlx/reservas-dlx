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

namespace Reservas\UseCases\Quartos\EditarQuarto;


use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Exceptions\QuartoNaoEncontradoException;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Domain\Quartos\Validators\QuartoValidator;
use Reservas\Domain\Quartos\Validators\QuartoValidatorEnum;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler;

/**
 * Class EditarQuartoCommandHandler
 * @package Reservas\UseCases\Quartos\EditarQuarto
 * @covers EditarQuartoCommandHandlerTest
 */
class EditarQuartoCommandHandler
{
    /**
     * @var QuartoRepositoryInterface
     */
    private $quarto_repository;
    /**
     * @var QuartoValidator
     */
    private $validator;

    /**
     * EditarQuartoCommandHandler constructor.
     * @param QuartoRepositoryInterface $quarto_repository
     * @param QuartoValidator $validator
     */
    public function __construct(
        QuartoRepositoryInterface $quarto_repository,
        QuartoValidator $validator
    ) {
        $this->quarto_repository = $quarto_repository;
        $this->validator = $validator;
    }

    /**
     * @param EditarQuartoCommand $command
     * @return Quarto
     * @throws QuartoNaoEncontradoException
     */
    public function handle(EditarQuartoCommand $command): Quarto
    {
        $get_quarto_por_id_handler = new GetQuartoPorIdCommandHandler($this->quarto_repository);
        $quarto = $get_quarto_por_id_handler->handle(new GetQuartoPorIdCommand($command->getQuartoId()));

        $quarto->setNome($command->getNome());
        $quarto->setQuantidade($command->getQtde());
        $quarto->setValorMinimo($command->getValorMin());
        $quarto->setDescricao($command->getDescricao());
        $quarto->setMaximoHospedes($command->getMaxHospedes());
        $quarto->setTamanhoM2($command->getTamanhoM2());
        $quarto->setLink($command->getLink());

        $this->validator->setValidators(QuartoValidatorEnum::EDITAR);
        $this->validator->validar($quarto, $this->quarto_repository);

        $this->quarto_repository->update($quarto);

        return $quarto;
    }
}