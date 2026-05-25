<?php

namespace App\Services;

use Illuminate\Support\Collection;

class HsmtTreeService
{
  

    public function build(Collection $chapters, $isAgreeFrame = null): Collection
    {
        $grouped = $chapters->groupBy('pcode');

        $parts = [
            'P1' => 'Phần 1: Thủ tục đấu thầu',
            'P2' => 'Phần 2: Yêu cầu về kỹ thuật',
            'P3' => 'Phần 3: Điều kiện hợp đồng và biểu mẫu hợp đồng',
        ];

        $result = collect();

        foreach ($parts as $pcode => $partName) {

            $items = collect($grouped[$pcode] ?? [])
                ->sortBy('order_index')
                ->values();

            $children = $items->map(function ($item, $index) use ($isAgreeFrame) {

                if (!is_null($isAgreeFrame) && !is_null($item->is_agree_frame)) {
                    if ((int)$item->is_agree_frame !== (int)$isAgreeFrame) {
                        return null;
                    }
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'pcode' => $item->pcode,
                    'level' => $item->level,
                    'number' => null, 
                    'is_webform' => $item->is_webform,
                    'attachments' => collect(json_decode($item->attachments, true))
                        ->map(function ($file) {
                            return [
                                'name' => $file['name'] ?? null,
                                'type' => $file['type'] ?? null,
                                'id' => $file['id'] ?? null,
                            ];
                        })
                        ->filter()
                        ->values(),
                    'children' => [], // tạm thời chưa xử lý sâu
                ];
            })
                ->filter()
                ->values();

            if ($children->isEmpty()) {
                continue;
            }

            $children = $children->map(function ($child, $index) use (&$pcode) {
                $child['number'] = ($index + 1);
                return $child;
            });

            $result->push([
                'name' => $partName,
                'number' => null,
                'children' => $children,
            ]);
        }

        return $result->values()->map(function ($part, $index) {
            $part['number'] = (string)($index + 1);

            $part['children'] = collect($part['children'])->map(function ($child, $childIndex) use ($index) {
                $child['number'] = ($index + 1) . '.' . ($childIndex + 1);
                return $child;
            });

            return $part;
        });
    }
}
