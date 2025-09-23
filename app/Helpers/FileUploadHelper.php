<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

/**
 * Helper class for handling structured file uploads
 * Generates standardized file names for order-related uploads
 */
class FileUploadHelper
{
    /**
     * File type constants for different upload types
     */
    const TYPE_STRUK = 'STRUK';
    const TYPE_PENGAMBILAN = 'PENGAMBILAN';
    const TYPE_PENGANTARAN = 'PENGANTARAN';

    /**
     * Generate structured filename for uploaded files
     * Format: TYPE-ORDER_ID.extension
     * 
     * @param string $type File type (STRUK, PENGAMBILAN, PENGANTARAN)
     * @param string|int $orderId Order ID
     * @param object $file File object with getClientOriginalExtension method
     * @param Carbon|null $date Optional date (not used in current format)
     * @return string Generated filename
     */
    public static function generateStructuredFilename(
        string $type, 
        $orderId, 
        $file, 
        ?Carbon $date = null
    ): string {
        // Get file extension
        $extension = $file->getClientOriginalExtension();
        
        // Ensure extension is lowercase
        $extension = strtolower($extension);
        
        // Generate filename: TYPE-ORDER_ID.extension
        // Example: STRUK-ORD-20250923-WMVWDT.jpg
        return sprintf('%s-%s.%s', 
            strtoupper($type), 
            $orderId, 
            $extension
        );
    }

    /**
     * Store file with structured filename
     * 
     * @param UploadedFile $file File to upload
     * @param string $type File type (STRUK, PENGAMBILAN, PENGANTARAN)
     * @param string|int $orderId Order ID
     * @param string $directory Storage directory
     * @param string $disk Storage disk (default: 'public')
     * @param Carbon|null $date Optional date
     * @return string|false Stored file path or false on failure
     */
    public static function storeWithStructuredName(
        UploadedFile $file,
        string $type,
        $orderId,
        string $directory,
        string $disk = 'public',
        ?Carbon $date = null
    ) {
        try {
            // Generate structured filename
            $filename = self::generateStructuredFilename($type, $orderId, $file, $date);
            
            // Store file with custom filename
            $path = $file->storeAs($directory, $filename, $disk);
            
            return $path;
        } catch (\Exception $e) {
            \Log::error('Error storing file with structured name: ' . $e->getMessage(), [
                'type' => $type,
                'order_id' => $orderId,
                'directory' => $directory,
                'original_filename' => $file->getClientOriginalName()
            ]);
            
            return false;
        }
    }

    /**
     * Validate file type for order uploads
     * 
     * @param string $type File type to validate
     * @return bool
     */
    public static function isValidFileType(string $type): bool
    {
        return in_array(strtoupper($type), [
            self::TYPE_STRUK,
            self::TYPE_PENGAMBILAN,
            self::TYPE_PENGANTARAN
        ]);
    }

    /**
     * Get human-readable file type name
     * 
     * @param string $type File type constant
     * @return string Human-readable name
     */
    public static function getFileTypeDisplayName(string $type): string
    {
        return match(strtoupper($type)) {
            self::TYPE_STRUK => 'Struk Konfirmasi',
            self::TYPE_PENGAMBILAN => 'Bukti Pengambilan',
            self::TYPE_PENGANTARAN => 'Bukti Pengantaran',
            default => 'File Upload'
        };
    }

    /**
     * Extract order ID from structured filename
     * 
     * @param string $filename Structured filename
     * @return string|null Order ID or null if not found
     */
    public static function extractOrderIdFromFilename(string $filename): ?string
    {
        // Pattern: TYPE-ORDER_ID-DDMMYYYY.extension
        if (preg_match('/^[A-Z]+-([^-]+)-\d{8}\.[a-z]+$/', $filename, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Extract file type from structured filename
     * 
     * @param string $filename Structured filename
     * @return string|null File type or null if not found
     */
    public static function extractFileTypeFromFilename(string $filename): ?string
    {
        // Pattern: TYPE-ORDER_ID-DDMMYYYY.extension
        if (preg_match('/^([A-Z]+)-[^-]+-\d{8}\.[a-z]+$/', $filename, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}