<?php

namespace App\Services\Category\Contracts;

interface CategoryStrategyInterface
{
    public function getType(): string;

    public function getList(): array;

    public function getName(?string $code): ?string;
}
