<?php

namespace App\Services;

use Illuminate\Support\Collection;

class HsmtTreeService
{
    // public function build(Collection $chapters): Collection
    // {
    //     $grouped = $chapters->groupBy('pcode');

    //     $build = function ($parentCode = null, $prefix = '') use (&$build, $grouped) {

    //         return collect($grouped[$parentCode] ?? [])
    //             ->sortBy('order_index')
    //             ->values()
    //             ->map(function ($item, $index) use (&$build, $prefix) {

    //                 $number = $prefix === ''
    //                     ? (string)($index + 1)
    //                     : $prefix . '.' . ($index + 1);

    //                 return [
    //                     'id' => $item->id,
    //                     'name' => $item->name,
    //                     'code' => $item->code,
    //                     'level' => $item->level,
    //                     'number' => $number,
    //                     'is_webform' => $item->is_webform,

    //                     'children' => $build($item->code, $number),
    //                 ];
    //             });
    //     };

    //     return $build(null);
    // }

    // public function build(Collection $chapters, $isAgreeFrame = null): Collection
    // {
    //     $grouped = $chapters->groupBy('pcode');

    //     $build = function ($parentCode = null, $prefix = '') use (&$build, $grouped, $isAgreeFrame) {

    //         return collect($grouped[$parentCode] ?? [])
    //             ->sortBy('order_index') // ✅ đúng field DB
    //             ->values()
    //             ->map(function ($item, $index) use (&$build, $prefix, $isAgreeFrame) {

    //                 $number = $prefix === ''
    //                     ? (string)($index + 1)
    //                     : $prefix . '.' . ($index + 1);

    //                 // build children trước
    //                 $children = $build($item->code, $number);

    //                 // =========================
    //                 // DEBUG (QUAN TRỌNG)
    //                 // =========================
    //                 logger([
    //                     'code' => $item->code,
    //                     'level' => $item->level,
    //                     'isAgreeFrame_item' => $item->is_agree_frame ?? null,
    //                     'isAgreeFrame_current' => $isAgreeFrame,
    //                     'children_count' => $children->count(),
    //                 ]);

    //                 // =========================
    //                 // FILTER LOGIC
    //                 // =========================

    //                 // RULE 1: level hợp lệ
    //                 if (!in_array($item->level, [1, 2, 3])) {
    //                     return null;
    //                 }

    //                 // RULE 2: filter isAgreeFrame (nếu có)
    //                 if (!is_null($isAgreeFrame) && !is_null($item->is_agree_frame)) {
    //                     if ((int)$item->is_agree_frame !== (int)$isAgreeFrame) {
    //                         return null;
    //                     }
    //                 }

    //                 // RULE 3: cha không có con → bỏ
    //                 if ($item->level == 1 && $children->isEmpty()) {
    //                     return null;
    //                 }

    //                 return [
    //                     'id' => $item->id,
    //                     'name' => $item->name,
    //                     'code' => $item->code,
    //                     'level' => $item->level,
    //                     'number' => $number,
    //                     'is_webform' => $item->is_webform,
    //                     'is_agree_frame' => $item->is_agree_frame ?? null,
    //                     'children' => $children,
    //                 ];
    //             })
    //             ->filter()
    //             ->values();
    //     };

    //     return $build(null);
    // }

    public function build(Collection $chapters, $isAgreeFrame = null): Collection
    {
        $grouped = $chapters->groupBy('pcode');

        // HARD MAP GIỐNG HỆ THỐNG THẬT
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

                // FILTER isAgreeFrame
                if (!is_null($isAgreeFrame) && !is_null($item->is_agree_frame)) {
                    if ((int)$item->is_agree_frame !== (int)$isAgreeFrame) {
                        return null;
                    }
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code,
                    'level' => $item->level,
                    'number' => null, // sẽ set sau
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

            // ❗ nếu không có children thì bỏ luôn phần này
            if ($children->isEmpty()) {
                continue;
            }

            // đánh số
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

        // đánh số phần
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
