<?php

namespace App\Livewire\Apoteker;

use App\Models\Prescription;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Mail\PrescriptionOrderCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PrescriptionReception extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending';
    public $selectedPrescription = null;
    public $selectedProducts = [];
    public $showProductModal = false;
    public $productSearch = '';
    public $products = [];

    protected $paginationTheme = 'bootstrap';

    /**
     * Mount component
     */
    public function mount()
    {
        $this->loadProducts();
    }

    /**
     * Load products for selection
     */
    public function loadProducts()
    {
        $this->products = Product::where('is_active', true)
            ->where('stock', 'available')
            ->where('quantity', '>', 0)
            ->when($this->productSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->productSearch . '%');
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Updated product search
     */
    public function updatedProductSearch()
    {
        $this->loadProducts();
    }

    /**
     * Updated search
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Updated status filter
     */
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Select prescription for processing
     */
    public function selectPrescription($prescriptionId)
    {
        $this->selectedPrescription = Prescription::with('user')->find($prescriptionId);
        $this->selectedProducts = [];
        $this->showProductModal = true;
    }

    /**
     * Add product to selected products
     */
    public function addProduct($productId)
    {
        $product = Product::find($productId);
        
        if (!$product || $product->quantity <= 0) {
            session()->flash('error', 'Produk tidak tersedia atau stok habis.');
            return;
        }

        $existingIndex = collect($this->selectedProducts)->search(function ($item) use ($productId) {
            return $item['id'] == $productId;
        });

        if ($existingIndex !== false) {
            // Increase quantity if product already selected
            $this->selectedProducts[$existingIndex]['quantity']++;
        } else {
            // Add new product
            $this->selectedProducts[] = [
                'id' => $product->product_id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'available_stock' => $product->quantity
            ];
        }
    }

    /**
     * Remove product from selected products
     */
    public function removeProduct($index)
    {
        unset($this->selectedProducts[$index]);
        $this->selectedProducts = array_values($this->selectedProducts);
    }

    /**
     * Update product quantity
     */
    public function updateQuantity($index, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeProduct($index);
            return;
        }

        $product = $this->selectedProducts[$index];
        if ($quantity > $product['available_stock']) {
            session()->flash('error', 'Jumlah melebihi stok yang tersedia.');
            return;
        }

        $this->selectedProducts[$index]['quantity'] = $quantity;
    }

    /**
     * Create order from prescription
     */
    public function createOrder()
    {
        if (empty($this->selectedProducts)) {
            session()->flash('error', 'Pilih minimal satu produk untuk membuat pesanan.');
            return;
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = collect($this->selectedProducts)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            // Determine delivery method and address from prescription
            $deliveryMethod = $this->selectedPrescription->delivery_method ?? 'pickup';
            $deliveryFee = 0;
            $shippingAddress = [];

            if ($deliveryMethod === 'delivery' && $this->selectedPrescription->delivery_address) {
                // Use delivery address from prescription
                $deliveryAddress = $this->selectedPrescription->delivery_address;
                $deliveryFee = 15000; // Default delivery fee, can be calculated based on distance
                
                $shippingAddress = [
                    'recipient_name' => $deliveryAddress['recipient_name'] ?? $this->selectedPrescription->patient_name,
                    'phone' => $deliveryAddress['phone'] ?? $this->selectedPrescription->user->phone ?? '',
                    'address' => $deliveryAddress['address'] ?? '',
                    'village' => $deliveryAddress['village'] ?? '',
                    'district' => $deliveryAddress['district'] ?? '',
                    'city' => $deliveryAddress['city'] ?? '',
                    'province' => $deliveryAddress['province'] ?? '',
                    'postal_code' => $deliveryAddress['postal_code'] ?? '',
                    'notes' => $deliveryAddress['notes'] ?? '',
                    'is_main_address' => $deliveryAddress['is_main_address'] ?? false
                ];
            } else {
                // Pickup at pharmacy
                $shippingAddress = [
                    'recipient_name' => $this->selectedPrescription->patient_name,
                    'phone' => $this->selectedPrescription->user->phone ?? '',
                    'address' => 'Pickup di Apotek Baraya',
                    'village' => 'Pickup',
                    'district' => 'Pickup',
                    'city' => 'Pickup',
                    'province' => 'Pickup',
                    'postal_code' => '00000',
                    'notes' => 'Ambil langsung di apotek',
                    'is_main_address' => false
                ];
            }

            $totalPrice = $subtotal + $deliveryFee;

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => $this->selectedPrescription->user_id,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total_price' => $totalPrice,
                'status' => 'waiting_payment',
                'shipping_type' => $deliveryMethod,
                'shipping_address' => json_encode($shippingAddress),
                'notes' => 'Pesanan dibuat dari resep: ' . $this->selectedPrescription->prescription_number,
                'payment_expired_at' => now()->addDays(1)
            ]);

            // Create order items
            foreach ($this->selectedProducts as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['id'],
                    'qty' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Update product stock
                $product = Product::find($item['id']);
                $product->quantity -= $item['quantity'];
                
                // Update stock status if quantity reaches zero
                if ($product->quantity <= 0) {
                    $product->stock = 'out_of_stock';
                }
                
                $product->save();
            }

            // Update prescription status and link to order
            $this->selectedPrescription->update([
                'status' => 'processed',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
                'order_id' => $order->order_id,
                'confirmation_notes' => 'Pesanan berhasil dibuat dari resep'
            ]);

            // Send email notification
            try {
                Mail::to($this->selectedPrescription->user->email)
                    ->send(new PrescriptionOrderCreated($order, $this->selectedPrescription));
            } catch (\Exception $e) {
                // Log email error but don't fail the transaction
                \Log::error('Failed to send prescription order email: ' . $e->getMessage());
            }

            DB::commit();

            session()->flash('success', 'Pesanan berhasil dibuat dari resep. Notifikasi telah dikirim ke pelanggan.');
            
            $this->reset(['selectedPrescription', 'selectedProducts', 'showProductModal']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->reset(['selectedPrescription', 'selectedProducts', 'showProductModal']);
    }

    /**
     * Render component
     */
    public function render()
    {
        $prescriptions = Prescription::with(['user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('prescription_number', 'like', '%' . $this->search . '%')
                      ->orWhere('patient_name', 'like', '%' . $this->search . '%')
                      ->orWhere('doctor_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.apoteker.prescription-reception', [
            'prescriptions' => $prescriptions
        ]);
    }
}