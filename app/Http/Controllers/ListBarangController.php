<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListBarang;

class ListBarangController extends Controller
{
    /**
     * Menampilkan daftar barang
     */
    public function index()
    {
        $barang = ListBarang::all();
        return view('IT.listBarang.index', compact('barang'));
    }

    /**
     * Menampilkan form tambah barang
     */
    public function create()
    {
        return view('IT.listBarang.tambah');
    }

    /**
     * Menyimpan barang ke database
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:255',
        ]);

        // Simpan data ke database
        ListBarang::create([
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
        ]);

        return redirect()->route('list_barang.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    /**
     * Hapus barang dari database
     */
    public function destroy($id)
    {
        $barang = ListBarang::findOrFail($id);
        $barang->delete();

        return redirect()->route('list_barang.index')->with('success', 'Barang berhasil dihapus!');
    }
    public function edit($id)
{
    $barang = ListBarang::findOrFail($id);
    return view('IT.listBarang.edit', compact('barang'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_barang' => 'required|string|max:255',
        'jumlah' => 'required|integer|min:0',
        'satuan' => 'required|string|max:255',
    ]);

    $barang = ListBarang::findOrFail($id);
    $barang->update([
        'nama_barang' => $request->nama_barang,
        'jumlah' => $request->jumlah,
        'satuan' => $request->satuan,
    ]);

    return redirect()->route('list_barang.index')->with('success', 'Barang berhasil diperbarui!');
}
}
