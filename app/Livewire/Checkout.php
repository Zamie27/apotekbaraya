<?php

namespace App\Livewire;

use App\Models\UserAddress;
use App\Services\CheckoutService;
use App\Services\DistanceCalculatorService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Checkout extends Component
{
    public $shippingType = 'pickup'; // pickup or delivery
    public $selectedAddressId = null;
    public $addresses = [];
    public $checkoutSummary = [];
    public $notes = '';
    public $showAddressForm = false;
    public $isProcessing = false;
    
    // New address form fields
    public $newAddress = [
        'label' => 'rumah',
        'recipient_name' => '',
        'phone' => '',
        'detailed_address' => '',
        'village' => '',
        'sub_district' => '',
        'regency' => '',
        'province' => '',
        'postal_code' => '',
        'notes' => '',
        // Keep old fields for backward compatibility
        'district' => '',
        'city' => ''
    ];

    protected $rules = [
        'shippingType' => 'required|in:pickup,delivery',
        'selectedAddressId' => 'required_if:shippingType,delivery|exists:user_addresses,address_id',
        'notes' => 'nullable|string|max:500',
        'newAddress.label' => 'required|in:rumah,kantor,kost,lainnya',
        'newAddress.recipient_name' => 'required|string|max:255',
        'newAddress.phone' => 'required|string|max:20',
        'newAddress.detailed_address' => 'required|string',
        'newAddress.village' => 'required|string|max:255',
        'newAddress.sub_district' => 'required|string|max:255',
        'newAddress.regency' => 'required|string|max:255',
        'newAddress.province' => 'required|string|max:255',
        'newAddress.postal_code' => 'required|string|max:10',
        'newAddress.notes' => 'nullable|string|max:500'
    ];

    public function mount()
    {
        $this->loadAddresses();
        $this->calculateSummary();
        
        // Set default recipient name and phone from user
        $user = Auth::user();
        $this->newAddress['recipient_name'] = $user->name;
        $this->newAddress['phone'] = $user->phone ?? '';
    }

    public function loadAddresses()
    {
        $this->addresses = UserAddress::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Auto-select default address if exists
        $defaultAddress = $this->addresses->where('is_default', true)->first();
        if ($defaultAddress && $this->shippingType === 'delivery') {
            $this->selectedAddressId = $defaultAddress->address_id;
        }
    }

    public function updatedShippingType()
    {
        if ($this->shippingType === 'pickup') {
            $this->selectedAddressId = null;
        } else {
            // Auto-select default address for delivery
            $defaultAddress = $this->addresses->where('is_default', true)->first();
            if ($defaultAddress) {
                $this->selectedAddressId = $defaultAddress->address_id;
            }
        }
        
        $this->calculateSummary();
    }

    public function updatedSelectedAddressId()
    {
        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        try {
            $checkoutService = app(CheckoutService::class);
            
            $this->checkoutSummary = $checkoutService->calculateCheckoutSummary(
                Auth::id(),
                $this->shippingType,
                $this->selectedAddressId
            );
        } catch (\Exception $e) {
            $this->checkoutSummary = [
                'error' => $e->getMessage(),
                'cart_items' => [],
                'subtotal' => 0,
                'shipping_cost' => 0,
                'total' => 0,
                'delivery_available' => false
            ];
        }
    }

    public function toggleAddressForm()
    {
        $this->showAddressForm = !$this->showAddressForm;
        
        if (!$this->showAddressForm) {
            $this->resetNewAddressForm();
        }
    }

    public function saveNewAddress()
    {
        $this->validate([
            'newAddress.label' => 'required|in:rumah,kantor,kost,lainnya',
            'newAddress.recipient_name' => 'required|string|max:255',
            'newAddress.phone' => 'required|string|max:20',
            'newAddress.detailed_address' => 'required|string',
            'newAddress.village' => 'required|string|max:255',
            'newAddress.sub_district' => 'required|string|max:255',
            'newAddress.regency' => 'required|string|max:255',
            'newAddress.province' => 'required|string|max:255',
            'newAddress.postal_code' => 'required|string|max:10',
            'newAddress.notes' => 'nullable|string|max:500'
        ]);

        try {
            $address = UserAddress::create([
                'user_id' => Auth::id(),
                'label' => $this->newAddress['label'],
                'recipient_name' => $this->newAddress['recipient_name'],
                'phone' => $this->newAddress['phone'],
                // New detailed address fields
                'detailed_address' => $this->newAddress['detailed_address'],
                'village' => $this->newAddress['village'],
                'sub_district' => $this->newAddress['sub_district'],
                'regency' => $this->newAddress['regency'],
                'province' => $this->newAddress['province'],
                'postal_code' => $this->newAddress['postal_code'],
                'notes' => $this->newAddress['notes'] ?? null,
                // Backward compatibility fields
                'district' => $this->newAddress['sub_district'], // Use sub_district as fallback
                'city' => $this->newAddress['regency'], // Use regency as fallback
                'is_default' => $this->addresses->isEmpty()
            ]);

            // Auto-geocoding: Get coordinates for new address
            $checkoutService = app(CheckoutService::class);
            $geocodingSuccess = $checkoutService->updateAddressCoordinates($address);
            
            // Show appropriate message based on geocoding result
            if ($geocodingSuccess) {
                session()->flash('success', 'Alamat berhasil ditambahkan dan koordinat lokasi telah diperoleh!');
            } else {
                session()->flash('success', 'Alamat berhasil ditambahkan! (Koordinat lokasi akan diperbarui secara otomatis)');
            }

            $this->loadAddresses();
            $this->selectedAddressId = $address->address_id;
            $this->showAddressForm = false;
            $this->resetNewAddressForm();
            $this->calculateSummary();

            session()->flash('success', 'Alamat berhasil ditambahkan!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan alamat: ' . $e->getMessage());
        }
    }

    public function processCheckout()
    {
        $this->validate();
        
        if (empty($this->checkoutSummary['cart_items'])) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        if ($this->shippingType === 'delivery' && !$this->checkoutSummary['delivery_available']) {
            session()->flash('error', 'Pengiriman tidak tersedia untuk alamat ini (melebihi jarak maksimal)!');
            return;
        }

        $this->isProcessing = true;

        try {
            $checkoutService = app(CheckoutService::class);
            
            $order = $checkoutService->processCheckout(Auth::id(), [
                'shipping_type' => $this->shippingType,
                'address_id' => $this->selectedAddressId,
                'notes' => $this->notes
            ]);

            session()->flash('success', 'Pesanan berhasil dibuat! Nomor pesanan: ' . $order->order_number);
            
            // Redirect to order detail or success page
            return redirect()->route('pelanggan.orders.show', $order->order_id);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    private function resetNewAddressForm()
    {
        $user = Auth::user();
        $this->newAddress = [
            'label' => 'rumah',
            'recipient_name' => $user->name,
            'phone' => $user->phone ?? '',
            'detailed_address' => '',
            'village' => '',
            'sub_district' => '',
            'regency' => '',
            'province' => '',
            'district' => '',
            'city' => '',
            'postal_code' => '',
            'notes' => ''
        ];
    }

    public function render()
    {
        return view('livewire.checkout')
            ->layout('components.layouts.user');
    }
}