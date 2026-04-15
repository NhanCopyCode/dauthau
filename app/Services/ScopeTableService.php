<?php

namespace App\Services;

class ScopeTableService
{
    public function transform(array $scope): array
    {
        $columns = $scope['columns'] ?? [];
        $rows = $scope['rows'] ?? [];

        $headings = collect($columns)->pluck('title')->toArray();

        $flatRows = [];
        $this->flatten($rows, $columns, $flatRows);

        return [$headings, $flatRows];
    }

    private function flatten(array $rows, array $columns, array &$result, int $level = 0)
    {
        foreach ($rows as $row) {

            $mapped = [];

            foreach ($columns as $col) {
                $key = $col['key'];
                $type = $col['type'];

                $value = $row[$key] ?? null;

                // format theo type
                if ($type === 'increment') {
                    $value = $row['pos'] ?? null;
                }

                if ($type === 'money' && is_numeric($value)) {
                    $value = (float) $value;
                }

                // indent nếu là children (optional)
                if ($level > 0 && $key === 'name' && $value) {
                    $value = str_repeat('   ', $level) . $value;
                }

                $mapped[] = $value;
            }

            $result[] = $mapped;

            // recursion children
            if (!empty($row['children'])) {
                $this->flatten($row['children'], $columns, $result, $level + 1);
            }
        }
    }
}
