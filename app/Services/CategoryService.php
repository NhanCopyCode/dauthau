<?php

namespace App\Services;

use App\Services\Category\Contracts\CategoryStrategyInterface;

class CategoryService
{
    protected array $strategies = [];

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $this->strategies[$strategy->getType()] = $strategy;
        }
    }

    public function getStrategy(string $type): ?CategoryStrategyInterface
    {
        return $this->strategies[$type] ?? null;
    }

    public function getName(string $type, ?string $code): ?string
    {
        $strategy = $this->getStrategy($type);

        if (!$strategy) return $code;

        return $strategy->getName($code);
    }

    public function getList(string $type): array
    {
        $strategy = $this->getStrategy($type);

        if (!$strategy) return [];

        return $strategy->getList();
    }
}
