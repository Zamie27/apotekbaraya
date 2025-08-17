<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSetting;
use App\Models\UserAddress;
use App\Services\DistanceCalculatorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    private DistanceCalculatorService $distanceCalculator;

    public function __construct(DistanceCalculatorService $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
    }

    /**
     * Calculate checkout summary including shipping
     */
    public function calculateCheckoutSummary(int $userId, string $shippingType, ?int $addressId = null): array
    {
        // Get cart items
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart || !$cart->items()->exists()) {
            throw new \Exception('Cart is empty');
        }

        $cartItems = $cart->items()->with('product')->get();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->final_price;
        });

        $shippingCost = 0;
        $shippingDistance = 0;
        $isFreeShipping = false;
        $deliveryAvailable = true;

        if ($shippingType === 'delivery') {
            if (!$addressId) {
                throw new \Exception('Address is required for delivery');
            }

            $address = UserAddress::where('address_id', $addressId)
                ->where('user_id', $userId)
                ->first();

            if (!$address) {
                throw new \Exception('Address not found');
            }

            // Calculate shipping if coordinates are available
            if ($address->latitude && $address->longitude) {
                try {
                    // Use accurate store coordinates from settings
                    $storeCoordinates = $this->distanceCalculator->getStoreCoordinates();
                    
                    $distanceData = $this->distanceCalculator->calculateDistance(
                        $storeCoordinates['latitude'],
                        $storeCoordinates['longitude'],
                        (float) $address->latitude,
                        (float) $address->longitude
                    );

                    $shippingDistance = $distanceData['distance_km'];
                    $deliveryAvailable = $this->distanceCalculator->isDeliveryAvailable($shippingDistance);

                    if ($deliveryAvailable) {
                        $shippingData = $this->distanceCalculator->calculateShippingCost($shippingDistance, $subtotal);
                        $shippingCost = $shippingData['final_cost'];
                        $isFreeShipping = $shippingData['is_free_shipping'];
                    }
                } catch (\Exception $e) {
                    // Log error but don't break checkout process
                    \Log::warning('Failed to calculate shipping distance: ' . $e->getMessage());
                    $deliveryAvailable = false;
                }
            }
        }

        $total = $subtotal + $shippingCost;

        return [
            'cart_items' => $cartItems,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'shipping_distance' => $shippingDistance,
            'is_free_shipping' => $isFreeShipping,
            'delivery_available' => $deliveryAvailable,
            'total' => $total,
            'shipping_type' => $shippingType,
            'address' => $shippingType === 'delivery' ? UserAddress::find($addressId) : null
        ];
    }

    /**
     * Process checkout and create order
     */
    public function processCheckout(int $userId, array $checkoutData): Order
    {
        return DB::transaction(function () use ($userId, $checkoutData) {
            // Validate checkout data
            $summary = $this->calculateCheckoutSummary(
                $userId,
                $checkoutData['shipping_type'],
                $checkoutData['address_id'] ?? null
            );

            if (!$summary['delivery_available'] && $checkoutData['shipping_type'] === 'delivery') {
                throw new \Exception('Delivery not available for this address (distance exceeds maximum limit)');
            }

            // Create order
            $orderNumber = $this->generateOrderNumber();
            
            $shippingAddress = null;
            if ($checkoutData['shipping_type'] === 'delivery' && $summary['address']) {
                $shippingAddress = [
                    'recipient_name' => $summary['address']->recipient_name,
                    'phone' => $summary['address']->phone,
                    'detailed_address' => $summary['address']->detailed_address,
                    'village' => $summary['address']->village,
                    'sub_district' => $summary['address']->sub_district,
                    'regency' => $summary['address']->regency,
                    'province' => $summary['address']->province,
                    'district' => $summary['address']->district,
                    'city' => $summary['address']->city,
                    'postal_code' => $summary['address']->postal_code,
                    'notes' => $summary['address']->notes,
                    'latitude' => $summary['address']->latitude,
                    'longitude' => $summary['address']->longitude
                ];
            } else {
                // Store pickup address
                $shippingAddress = [
                    'type' => 'store_pickup',
                    'store_name' => StoreSetting::get('store_name', 'Apotek Baraya'),
                    'store_address' => StoreSetting::get('store_address', '')
                ];
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $userId,
                'subtotal' => $summary['subtotal'],
                'delivery_fee' => $summary['shipping_cost'],
                'total_price' => $summary['total'],
                'shipping_type' => $checkoutData['shipping_type'],
                'shipping_distance' => $summary['shipping_distance'],
                'is_free_shipping' => $summary['is_free_shipping'],
                'shipping_address' => $shippingAddress,
                'notes' => $checkoutData['notes'] ?? null,
                'status' => 'pending'
            ]);

            // Create order items
            foreach ($summary['cart_items'] as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $cartItem->product_id,
                    'qty' => $cartItem->quantity,
                    'price' => $cartItem->product->final_price
                ]);
            }

            // Clear cart
            $cart = Cart::where('user_id', $userId)->first();
            if ($cart) {
                $cart->items()->delete();
            }

            return $order;
        });
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get store coordinates
     */
    public function getStoreCoordinates(): array
    {
        return $this->distanceCalculator->getStoreCoordinates();
    }

    /**
     * Update address coordinates using Google Maps API
     */
    public function updateAddressCoordinates(UserAddress $address): bool
    {
        try {
            // Build address with multiple strategies for better geocoding accuracy
            // Use new detailed fields if available, fallback to old fields for backward compatibility
            $village = $address->village ?? '';
            $subDistrict = $address->sub_district ?? $address->district;
            $regency = $address->regency ?? $address->city;
            $province = $address->province ?? '';
            $postalCode = $address->postal_code ?? '';
            $detailedAddress = $address->detailed_address ?? '';
            
            $addressStrategies = [
                // Strategy 1: Full detailed address with new structure
                $this->buildFullAddress($detailedAddress, $village, $subDistrict, $regency, $province, $postalCode),
                // Strategy 2: Simplified detailed address (remove RT/RW details)
                $this->buildFullAddress($this->simplifyDetailedAddress($detailedAddress), $village, $subDistrict, $regency, $province),
                // Strategy 3: Administrative areas only (village, sub-district, regency)
                $this->buildAdministrativeAddress($village, $subDistrict, $regency, $province),
                // Strategy 4: Sub-district and regency with postal code
                trim($subDistrict . ', ' . $regency . ', ' . $postalCode, ', '),
                // Strategy 5: Regency and province only
                trim($regency . ', ' . $province, ', ')
            ];
            
            $lastException = null;
            
            // Try each strategy until one succeeds
            foreach ($addressStrategies as $index => $fullAddress) {
                try {
                    \Log::info("Trying geocoding strategy " . ($index + 1) . " for address: {$fullAddress}");
                    
                    $coordinates = $this->distanceCalculator->getCoordinatesFromAddress($fullAddress);
                    
                    $address->update([
                        'latitude' => $coordinates['latitude'],
                        'longitude' => $coordinates['longitude']
                    ]);
                    
                    \Log::info("Geocoding successful using strategy " . ($index + 1) . " for address ID: {$address->address_id}");
                    return true;
                    
                } catch (\Exception $e) {
                    $lastException = $e;
                    \Log::warning("Geocoding strategy " . ($index + 1) . " failed: " . $e->getMessage());
                    continue;
                }
            }
            
            // If all strategies fail, log the last exception
            if ($lastException) {
                throw $lastException;
            }
            
            return false;
            
        } catch (\Exception $e) {
            \Log::error('Failed to update address coordinates for address ID ' . $address->address_id . ': ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Build full address string from detailed components
     */
    private function buildFullAddress(string $detailedAddress, string $village, string $subDistrict, string $regency, string $province, string $postalCode = ''): string
    {
        $addressParts = array_filter([
            $detailedAddress,
            $village,
            $subDistrict,
            $regency,
            $province,
            $postalCode
        ], function($part) {
            return !empty(trim($part));
        });
        
        return implode(', ', $addressParts);
    }
    
    /**
     * Build administrative address from area components only
     */
    private function buildAdministrativeAddress(string $village, string $subDistrict, string $regency, string $province): string
    {
        $addressParts = array_filter([
            $village,
            $subDistrict,
            $regency,
            $province
        ], function($part) {
            return !empty(trim($part));
        });
        
        return implode(', ', $addressParts);
    }

    /**
     * Simplify detailed address by removing RT/RW and other very specific details
     * that might not be recognized by geocoding services
     */
    private function simplifyDetailedAddress(string $address): string
    {
        // Remove RT/RW patterns
        $simplified = preg_replace('/\b(?:RT|RW)\s*[\.\\/]?\s*\d+\b/i', '', $address);
        
        // Remove Dusun/Dukuh patterns if they're at the beginning
        $simplified = preg_replace('/^(?:Dusun|Dukuh)\s+[^,]+,?\s*/i', '', $simplified);
        
        // Remove multiple commas and spaces
        $simplified = preg_replace('/,\s*,+/', ',', $simplified);
        $simplified = preg_replace('/\s+/', ' ', $simplified);
        
        // Remove leading/trailing commas and spaces
        $simplified = trim($simplified, ', ');
        
        return $simplified;
    }
}