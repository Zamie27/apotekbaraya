<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get cart items count for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCartCount(Request $request): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json(['count' => 0]);
            }

            $count = $this->cartService->getCartItemsCount(auth()->id());
            
            return response()->json([
                'count' => $count,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'count' => 0,
                'status' => 'error',
                'message' => 'Failed to get cart count'
            ], 500);
        }
    }

    /**
     * Get cart summary for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCartSummary(Request $request): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'count' => 0,
                    'total' => 0,
                    'items' => []
                ]);
            }

            $summary = $this->cartService->getCartSummary(auth()->user());
            
            return response()->json([
                'count' => $summary['count'],
                'total' => $summary['total'],
                'formatted_total' => 'Rp ' . number_format($summary['total'], 0, ',', '.'),
                'items' => $summary['items'],
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'count' => 0,
                'total' => 0,
                'items' => [],
                'status' => 'error',
                'message' => 'Failed to get cart summary'
            ], 500);
        }
    }
}