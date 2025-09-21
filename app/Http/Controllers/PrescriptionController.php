<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->validate([
            'doctor_name' => 'required|string|max:255',
            'patient_name' => 'required|string|max:255',
            'prescription_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Upload prescription image
        $image = $request->file('prescription_image');
        $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('prescriptions', $imageName, 'public');

        // Create prescription record
        $prescription = Prescription::create([
            'user_id' => Auth::id(),
            'doctor_name' => $request->doctor_name,
            'patient_name' => $request->patient_name,
            'prescription_image' => $imagePath,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect()->route('customer.prescriptions.show', $prescription->id)
            ->with('success', 'Resep berhasil diunggah! Silakan tunggu konfirmasi dari apoteker.');
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

        return redirect()->route('apoteker.prescriptions.detail', $prescription->id)
            ->with('success', $message);
    }
}
