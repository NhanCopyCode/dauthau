<?php

namespace App\Services\Category\Strategies;

use Illuminate\Support\Facades\Http;
use App\Services\Category\Contracts\CategoryStrategyInterface;

abstract class BaseCategoryStrategy implements CategoryStrategyInterface
{
    protected string $type;

    public function getType(): string
    {
        return $this->type;
    }

    public function getList(): array
    {
        return cache()->remember("categories.{$this->type}", 3600, function () {

            $response = Http::withHeaders([
                'Accept' => 'application/json, text/plain, */*',
                'Content-Type' => 'application/json',
                'Origin' => 'https://muasamcong.mpi.gov.vn',
                'Referer' => 'https://muasamcong.mpi.gov.vn/',
                'User-Agent' => 'Mozilla/5.0',
            ])
                ->withOptions([
                    'verify' => false
                ])
                ->timeout(10)
                ->post(
                    'https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/get/category',
                    [
                        "categoryTypeCodeLst" => [$this->type]
                    ]
                );

            if (!$response->successful()) {
                return [];
            }

            return data_get($response->json(), "categories.{$this->type}", []);
        });
    }

    public function getName(?string $code): ?string
    {
        if (!$code) return null;

        $found = collect($this->getList())->firstWhere('code', $code);

        return $found['name'] ?? $code;
    }
}
