<?php

namespace App\Services;

use Illuminate\Support\Collection;

class HsmtTreeService
{
    public function build(Collection $chapters): Collection
    {
        $grouped = $chapters->groupBy('pcode');

        $build = function ($parentCode = null, $prefix = '') use (&$build, $grouped) {

            return collect($grouped[$parentCode] ?? [])
                ->sortBy('order_index')
                ->values()
                ->map(function ($item, $index) use (&$build, $prefix) {

                    $number = $prefix === ''
                        ? (string)($index + 1)
                        : $prefix . '.' . ($index + 1);

                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'code' => $item->code,
                        'level' => $item->level,
                        'number' => $number,
                        'is_webform' => $item->is_webform,

                        'children' => $build($item->code, $number),
                    ];
                });
        };

        return $build(null);
    }
}
