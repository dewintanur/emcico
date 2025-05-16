<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BookingImport;
use Exception;
use Log;

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
            'file' => 'required|mimes:csv',
        ]);

        try {
            $import = new BookingImport(); // Gunakan instance agar bisa akses $errors
            Excel::import($import, $request->file('file'));

            if (count($import->errors) > 0) {
                return redirect()->route('booking.import')
                    ->with('error', 'Sebagian data gagal diimpor.')
                    ->with('import_errors', $import->errors); // Kirim daftar error
            }

            return redirect()->route('booking.import')->with('success', 'Data booking berhasil diimpor!');
        } catch (Exception $e) {
            // Menangani error jika terjadi kegagalan pada saat import
            Log::error('Import gagal: ' . $e->getMessage());

            // Memberikan pesan error kepada user
            return redirect()->route('booking.import')->with('error', 'Terjadi kesalahan saat mengimpor data. Silakan coba lagi.');
        }
    }
}
