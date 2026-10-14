<?php

namespace App\Http\Controllers;

use App\Models\events;
use App\Models\orders;
use App\Models\orderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Menampilkan halaman utama pelanggan dan daftar event yang tersedia.
     */
    public function index()
    {
        $events = events::all();
        return view('customer.index', compact('events'));
    }

    /**
     * Menyimpan pesanan pelanggan ke database beserta detail item yang dibeli.
     * Proses ini dilakukan dalam transaksi agar data tidak setengah tersimpan.
     */
    public function store(Request $request)
    {
        // Validasi input pesanan pelanggan sebelum diproses.
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'items'         => 'required|array',
            'items.*'       => 'nullable|integer|min:0',
        ]);

        // Abaikan event yang jumlah tiketnya nol.
        $orderedItems = array_filter($request->input('items'), fn ($quantity) => $quantity > 0);

        if (empty($orderedItems)) {
            return back()->withInput()->with('error', 'Pilih minimal satu event!');
        }

        // Simpan pesanan dan tiket sekaligus agar tidak ada data yang tersimpan setengah.
        $order = DB::transaction(function () use ($request, $orderedItems) {
            // Buat data utama pesanan dengan status awal Pending.
            $order = orders::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'total_price'   => 0,
                'status'        => 'Pending',
            ]);

            $totalPrice = 0;

            // Loop setiap event yang dibeli lalu hitung subtotal dan simpan detailnya.
            foreach ($orderedItems as $eventId => $quantity) {
                $event = events::findOrFail($eventId);
                // Gunakan harga dari database, bukan harga kiriman dari browser.
                $subtotal = $event->price * (int) $quantity;
                $totalPrice += $subtotal;

                orderDetails::create([
                    'order_id' => $order->id,
                    'event_id' => $event->id,
                    'quantity' => (int) $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total harga pesanan setelah semua detail selesai dibuat.
            $order->update(['total_price' => $totalPrice]);

            return $order;
        });

        return back()->with('success', 'Pesanan berhasil dibuat! Nomor pesanan: ' . $order->id);
    }

    /**
     * Menampilkan dashboard admin dengan daftar pesanan dan relasi detail event.
     */
    public function adminDashboard()
    {
        $orders = orders::with('orderDetails.event')->latest()->get();
        return view('dashboard', compact('orders'));
    }

    /**
     * Memperbarui status pesanan berdasarkan input admin dan normalisasi variasi nilai status.
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi status yang diizinkan agar hanya nilai yang aman yang diterima.
        $request->validate([
            'status' => 'required|string|in:Pending,Diproses,Selesai,Dibatalkan,pending,diproses,completed,selesai,cancelled,batal,dibatalkan',
        ]);
        $order = orders::findOrFail($id);

        // Samakan variasi penulisan status menjadi status yang dipakai aplikasi.
        $status = match (strtolower($request->status)) {
            'pending' => 'Pending',
            'diproses' => 'Diproses',
            'completed', 'selesai' => 'Selesai',
            'cancelled', 'batal', 'dibatalkan' => 'Dibatalkan',
        };

        // Simpan perubahan status ke database.
        $order->update(['status' => $status]);

        return back()->with('success', 'Status pesanan #' . $order->id . ' berhasil diperbarui!');
    }
}