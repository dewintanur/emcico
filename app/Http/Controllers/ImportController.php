<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BookingImport;

class ImportController extends Controller
{
    /**
     * Menampilkan form upload CSV.
     */
    public function showImportForm()
    {
        return view('it.import');
    }

    /**
     * Mengimpor data booking dari CSV.
     */
    public function import(Request $request)
    {
        // Validasi file CSV
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        // Mengimpor file CSV
        Excel::import(new BookingImport, $request->file('file'));

        return redirect()->route('booking.import')->with('success', 'Data booking berhasil diimpor!');
    }
}
