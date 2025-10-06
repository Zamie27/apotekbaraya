<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductExportController extends Controller
{
    /**
     * Export products to CSV matching the import format.
     */
    public function exportCsv(): StreamedResponse
    {
        $filename = 'products_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = [
            'name',
            'slug',
            'category_slug',
            'price',
            'discount_percentage',
            'stock',
            'requires_prescription',
            'is_active',
            'unit',
            'kemasan',
            'produsen',
            'deskripsi',
            'komposisi',
            'manfaat',
            'dosis',
            'efek_samping',
            'lainnya',
        ];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w');
            // Uncomment BOM if needed for Excel compatibility
            // fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, $columns);

            $products = Product::with('category')->orderBy('product_id', 'asc')->get();

            foreach ($products as $product) {
                $spec = is_array($product->specifications) ? $product->specifications : [];

                $row = [
                    $product->name,
                    $product->slug,
                    optional($product->category)->slug,
                    $product->price,
                    $product->discount_percentage,
                    $product->stock,
                    (int) ((bool) $product->requires_prescription),
                    (int) ((bool) $product->is_active),
                    $product->unit,
                    $spec['Kemasan'] ?? null,
                    $spec['Produsen'] ?? null,
                    $product->description,
                    $spec['Komposisi'] ?? null,
                    $spec['Manfaat'] ?? null,
                    $spec['Dosis'] ?? null,
                    $spec['Efek Samping'] ?? null,
                    $spec['Lainnya'] ?? null,
                ];

                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}