<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Mail\OrderCreatedFromPrescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrescriptionController extends Controller
{
    /**
     * Display prescription upload form for customers
     */
    public function create()
    {
        return view('customer.prescriptions.create');
    }

    /**
     * Store a new prescription upload
     */
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'doctor_name' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'prescription_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:1000',
            'delivery_method' => 'required|in:pickup,delivery'
        ];

        // Add delivery address validation if delivery method is selected
        if ($request->delivery_method === 'delivery') {
            $rules['delivery_address'] = 'required|array';
            $rules['delivery_address.recipient_name'] = 'required|string|max:255';
            $rules['delivery_address.phone'] = 'required|string|max:20';
            $rules['delivery_address.address'] = 'required|string|max:500';
            $rules['delivery_address.village'] = 'required|string|max:100';
            $rules['delivery_address.district'] = 'required|string|max:100';
            $rules['delivery_address.city'] = 'required|string|max:100';
            $rules['delivery_address.province'] = 'required|string|max:100';
            $rules['delivery_address.postal_code'] = 'required|string|max:10';
        }

        $request->validate($rules);

        // Generate unique prescription number first
        $prescriptionNumber = 'RX-' . strtoupper(Str::random(8));

        // Upload prescription image with custom naming format
        $image = $request->file('prescription_image');
        $imageName = 'RESEP-' . $prescriptionNumber . '.jpg';
        $imagePath = $image->storeAs('prescriptions', $imageName, 'public');

        // Prepare prescription data
        $prescriptionData = [
            'prescription_number' => $prescriptionNumber,
            'user_id' => Auth::id(),
            'doctor_name' => $request->doctor_name,
            'patient_name' => $request->patient_name,
            'prescription_image' => $imagePath,
            'file' => $imagePath, // Fill file field with same value as prescription_image
            'notes' => $request->notes,
            'delivery_method' => $request->delivery_method,
            'status' => 'pending'
        ];

        // Add delivery address if delivery method is selected
        if ($request->delivery_method === 'delivery' && $request->has('delivery_address')) {
            $prescriptionData['delivery_address'] = $request->delivery_address;
        }

        // Create prescription record
        $prescription = Prescription::create($prescriptionData);

        return redirect()->route('customer.prescriptions.show', $prescription->getKey())
            ->with('success', 'Prescription uploaded successfully!');
    }

    /**
     * Display prescription details for customer
     */
    public function show(Prescription $prescription)
    {
        // Ensure customer can only view their own prescriptions
        if ($prescription->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('customer.prescriptions.show', compact('prescription'));
    }

    /**
     * Display a listing of user's prescriptions
     */
    public function index()
    {
        $prescriptions = Prescription::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('customer.prescriptions.index', compact('prescriptions'));
    }

    /**
     * Display prescription management page for pharmacists
     */
    public function manage()
    {
        $query = Prescription::with(['user', 'confirmedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if (request('status')) {
            $query->where('status', request('status'));
        }

        $prescriptions = $query->paginate(10);

        // Get statistics for dashboard cards
        $stats = [
            'pending' => Prescription::where('status', 'pending')->count(),
            'confirmed' => Prescription::where('status', 'confirmed')->count(),
            'rejected' => Prescription::where('status', 'rejected')->count(),
            'processed' => Prescription::where('status', 'processed')->count(),
        ];

        return view('apoteker.prescriptions.manage', compact('prescriptions', 'stats'));
    }

    /**
     * Show prescription details for apoteker
     */
    public function detail(Prescription $prescription)
    {
        $prescription->load(['user', 'confirmedBy', 'order']);
        return view('apoteker.prescriptions.detail', compact('prescription'));
    }

    /**
     * Confirm prescription by apoteker
     */
    public function confirm(Request $request, Prescription $prescription)
    {
        $request->validate([
            'status' => 'required|in:confirmed,rejected',
            'confirmation_notes' => 'nullable|string|max:1000'
        ]);

        $prescription->update([
            'status' => $request->status,
            'confirmed_by' => Auth::id(),
            'confirmation_notes' => $request->confirmation_notes,
            'confirmed_at' => now()
        ]);

        $message = $request->status === 'confirmed' 
            ? 'Resep berhasil dikonfirmasi!' 
            : 'Resep ditolak.';

        return redirect()->route('apoteker.prescriptions.detail', $prescription->getKey())
            ->with('success', $message);
    }

    /**
     * Show create order form for confirmed prescription
     */
    public function createOrder(Prescription $prescription)
    {
        // Check if prescription is confirmed and doesn't have an order yet
        if ($prescription->status !== 'confirmed') {
            return redirect()->route('apoteker.prescriptions.detail', $prescription->getKey())
                ->with('error', 'Resep harus dikonfirmasi terlebih dahulu sebelum membuat pesanan.');
        }

        if ($prescription->order_id) {
            return redirect()->route('apoteker.prescriptions.detail', $prescription->getKey())
                ->with('error', 'Pesanan untuk resep ini sudah dibuat.');
        }

        $prescription->load(['user']);
        
        // Get available products for order creation
        // Fix: gunakan scope active() dan available() (stock > 0) agar produk tidak kosong.
        // Kolom stock bertipe integer; sebelumnya salah memakai nilai string 'available' dan memfilter quantity.
        $products = Product::with(['category', 'images'])
            ->active()
            ->available()
            ->orderBy('name')
            ->get();

        return view('apoteker.prescriptions.create-order', compact('prescription', 'products'));
    }

    /**
     * Store order from prescription
     */
    public function storeOrder(Request $request, Prescription $prescription)
    {
        // Validate request
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:1',
            'order_notes' => 'nullable|string|max:1000'
        ], [
            'products.required' => 'Pilih minimal satu produk.',
            'products.min' => 'Pilih minimal satu produk.',
            'products.*.quantity.required' => 'Jumlah produk harus diisi.',
            'products.*.quantity.min' => 'Jumlah produk minimal 1.'
        ]);

        // Check if prescription is confirmed and no order exists
        if ($prescription->status !== 'confirmed' || $prescription->order_id) {
            return redirect()->back()->with('error', 'Resep tidak dapat diproses.');
        }

        DB::beginTransaction();
        try {
            // Calculate total amount and discount
            $totalAmount = 0; // subtotal menggunakan harga final (mempertimbangkan diskon)
            $totalDiscount = 0; // akumulasi diskon
            $orderItems = [];
            
            foreach ($request->products as $productId => $productData) {
                if (isset($productData['selected']) && $productData['selected']) {
                    $product = Product::findOrFail($productId);
                    $quantity = (int) $productData['quantity'];
                    
                    // Check stock availability menggunakan kolom stock (integer)
                    if ((int) $product->stock < $quantity) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi. Stok tersedia: {$product->stock}");
                    }
                    
                    // Gunakan harga final (discount_price jika ada) untuk perhitungan
                    $finalPrice = $product->discount_price ?? $product->price;
                    $subtotal = $finalPrice * $quantity;
                    $totalAmount += $subtotal;
                    // Hitung diskon item: selisih harga normal dengan harga final
                    $itemDiscount = max(0, ($product->price - $finalPrice)) * $quantity;
                    $totalDiscount += $itemDiscount;
                    
                    $orderItems[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'price' => $finalPrice,
                        'subtotal' => $subtotal
                    ];
                }
            }

            if (empty($orderItems)) {
                throw new \Exception('Tidak ada produk yang dipilih.');
            }

            // Calculate delivery fee based on prescription delivery method
            $deliveryFee = 0;
            if ($prescription->delivery_method === 'delivery') {
                $deliveryFee = 10000; // Set delivery fee for home delivery
            }
            
            $finalTotalPrice = $totalAmount + $deliveryFee;

            // Create order
            $order = Order::create([
                'user_id' => $prescription->user_id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT),
                'subtotal' => $totalAmount,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $totalDiscount,
                'total_price' => $finalTotalPrice,
                'status' => 'waiting_payment',
                'shipping_address' => $prescription->delivery_method === 'delivery' 
                    ? ($prescription->user->address ?? 'Alamat tidak tersedia') 
                    : 'Ambil di toko',
                'notes' => $request->order_notes,
                'waiting_payment_at' => now()
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Update product stock
                // Kurangi stok berdasarkan kolom stock
                Product::where('product_id', $item['product_id'])
                       ->decrement('stock', $item['quantity']);
            }

            // Get default payment method (prioritize bank_transfer, then cod, then any active method)
            $defaultPaymentMethod = PaymentMethod::where('code', 'bank_transfer')->where('is_active', true)->first()
                ?? PaymentMethod::where('code', 'cod')->where('is_active', true)->first() 
                ?? PaymentMethod::where('is_active', true)->first();
            
            if (!$defaultPaymentMethod) {
                throw new \Exception('No payment method available. Please contact administrator.');
            }

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->order_id,
                'payment_method_id' => $defaultPaymentMethod->payment_method_id,
                'amount' => $finalTotalPrice,
                'status' => 'pending', // Will be updated by Midtrans callback
            ]);

            // Update prescription with order_id and status
            $prescription->update([
                'order_id' => $order->order_id,
                'status' => 'processed'
            ]);

            DB::commit();

            // Send notification to customer
            try {
                Mail::to($prescription->user->email)->send(new OrderCreatedFromPrescription($order, $prescription));
            } catch (\Exception $e) {
                \Log::error('Failed to send order notification email: ' . $e->getMessage());
            }

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                // Log successful order creation for debugging
                \Log::info('Order created successfully', [
                    'order_id' => $order->order_id,
                    'order_number' => $order->order_number,
                    'prescription_id' => $prescription->prescription_id
                ]);
                
                // Ensure all data is properly formatted
                $responseData = [
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat dari resep.',
                    'order_id' => (string) $order->order_id,
                    'order_number' => (string) $order->order_number,
                    'total_price' => (float) $order->total_price,
                    'redirect_url' => route('apoteker.orders.detail', $order->order_id)
                ];
                
                // Log the response data for debugging
                \Log::info('Sending JSON response', $responseData);
                
                return response()->json($responseData, 200, [
                    'Content-Type' => 'application/json'
                ]);
            }

            return redirect()->route('apoteker.prescriptions.detail', $prescription)
                           ->with('success', 'Pesanan berhasil dibuat dari resep. Nomor pesanan: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollback();
            
            // Log error for debugging
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'prescription_id' => $prescription->prescription_id ?? null,
                'user_id' => auth()->id()
            ]);
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
                    'error_details' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ] : null
                ], 422);
            }
            
            return redirect()->back()
                           ->with('error', 'Gagal membuat pesanan: ' . $e->getMessage())
                           ->withInput();
        }
    }
}
