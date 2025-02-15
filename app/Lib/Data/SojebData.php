<?php

namespace App\Lib\Data;

use Symfony\Component\HttpFoundation\Response;

class SojebData
{
    /**
     * Generic function to export data from any model to CSV
     * 
     * @param \Illuminate\Database\Eloquent\Model|string $model The model class or instance
     * @param string $fileName The name of the CSV file to be downloaded
     * @param array $headers The column headers for the CSV
     * @param array $fieldMap The model fields to include, with optional custom accessors
     * @param array $relations Optional relations to load and include in the export
     * @param \Closure|null $customDataFormatter Optional callback for custom data formatting
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function exportToCsv($model, string $fileName, array $headers, array $fieldMap, array $relations = [], \Closure $customDataFormatter = null)
    {
        // If model is a class name, get all records, otherwise use the provided query/collection
        $data = is_string($model) ? $model::all() : $model;

        // If relations are specified, eager load them
        if (!empty($relations) && method_exists($data, 'with')) {
            $data = $data->with($relations);
        }

        $httpHeaders = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($data, $headers, $fieldMap, $customDataFormatter) {
            $handle = fopen('php://output', 'w');

            // Write CSV headers
            fputcsv($handle, $headers);

            // Write data rows
            foreach ($data as $item) {
                $row = [];

                // Process each field according to the field map
                foreach ($fieldMap as $key => $field) {
                    if (is_callable($field)) {
                        // If field is a callback, use it to get the value
                        $row[] = $field($item);
                    } elseif (str_contains($field, '.')) {
                        // Handle relation.field notation
                        $parts = explode('.', $field);
                        $value = $item;
                        foreach ($parts as $part) {
                            $value = $value->{$part} ?? null;
                        }
                        $row[] = $value;
                    } else {
                        // Regular field
                        $row[] = $item->{$field} ?? null;
                    }
                }

                // Apply custom formatter if provided
                if ($customDataFormatter) {
                    $row = $customDataFormatter($row, $item);
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, Response::HTTP_OK, $httpHeaders);
    }
}
