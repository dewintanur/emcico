<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;

class BarcodeController extends Controller
{
    public function index()
    {
        $tomorrow = now()->addDay()->toDateString(); // Ambil tanggal besok
        $bookings = Booking::where('tanggal', $tomorrow)->get();
        return view('barcode.index', compact('bookings'));
    }

    public function generateAndSend(Request $request)
    {
        $kodeBookings = $request->kode_booking;
        $barcodes = [];

        foreach ($kodeBookings as $kode) {
            $booking = Booking::where('kode_booking', $kode)->first();
            if (!$booking)
                continue;

            // 🔹 Buat folder jika belum ada
            $folderPath = storage_path('app/public/qrcodes/');
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            // ✅ Simpan QR Code sebagai file PNG
            $qrCodePath = "qrcodes/$kode.png";
            $filePath = storage_path("app/public/" . $qrCodePath);
            file_put_contents($filePath, QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($kode));

            // ✅ Pastikan URL gambar dapat diakses
            $qrCodeUrl = asset("storage/qrcodes/$kode.png"); // Gambar dapat diakses melalui URL ini

            // ✅ Buat pesan WhatsApp
            $waMessage = "Halo, berikut QR code untuk check-in:\nKode Booking: $kode";

            // Kirim URL gambar sebagai bagian dari pesan
            $waLink = "https://wa.me/62" . $booking->no_pic . "?text=" . urlencode($waMessage) . "%0A" . urlencode($qrCodeUrl);

            // ✅ Simpan data barcode
            $barcodes[] = [
                'kode' => $kode,
                'qr' => $qrCodeUrl,
                'whatsapp' => $waLink, // Kirimkan URL gambar melalui WhatsApp link
            ];
        }

        $bookings = Booking::where('tanggal', now()->addDay()->toDateString())->get();
        return view('barcode.index', compact('barcodes', 'bookings'));
    }
}
