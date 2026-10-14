<?php

namespace App\Http\Controllers;

use App\Models\events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    /**
     * Menampilkan daftar event yang sudah diurutkan dari yang terbaru
     * dan membagi hasil ke dalam halaman dengan paginasi.
     */
    public function index()
    {
        $events = events::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Menampilkan form untuk menambahkan event baru.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Menyimpan data event baru ke database, termasuk validasi input
     * dan upload gambar bila ada file yang dikirim.
     */
    public function store(Request $request)
    {
        // Validasi semua input form event sebelum disimpan.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Musik,Festival,Seminar',
            'price' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Simpan file gambar ke storage publik jika user mengunggah gambar.
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        // Buat data event baru berdasarkan input yang sudah tervalidasi.
        events::create($validated);

        return redirect()->route('events.index')->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail event tertentu.
     * Saat ini method ini masih kosong karena fitur detail belum digunakan.
     */
    public function show(events $events)
    {
        // TODO: Tampilkan detail event jika nanti fitur detail dibutuhkan.
    }

    /**
     * Menampilkan form edit untuk event yang dipilih.
     */
    public function edit(events $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Memperbarui data event yang sudah ada, termasuk validasi input
     * dan penggantian gambar lama apabila ada gambar baru yang diunggah.
     */
    public function update(Request $request, events $event)
    {
        // Validasi input edit event agar data tetap aman dan konsisten.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Musik,Festival,Seminar',
            'price' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Simpan foto baru; kalau tidak ada unggahan, foto lama tetap dipakai.
        $oldImage = $event->image;
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        // Perbarui data event dengan data valid yang baru.
        $event->update($validated);

        // Hapus foto lama setelah data event berhasil diperbarui.
        if (isset($validated['image']) && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('events.index')->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Menghapus event dari database dan membersihkan file gambar yang terkait.
     */
    public function destroy(events $event)
    {
        // Ambil path gambar sebelum data dihapus agar bisa dibersihkan.
        $image = $event->image;

        // Hapus data event dari database.
        $event->delete();

        // Hapus juga file foto agar tidak tersisa di penyimpanan.
        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('events.index')->with('success', 'Event berhasil dihapus.');
    }
}
