<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PickupController extends Controller
{
    /**
     * Menghitung jarak antara lokasi rental dan alamat tujuan user
     * Menggunakan Google Maps API (Geocoding + Distance Matrix)
     */
    public function distance(Request $request)
    {
        // ================= VALIDASI INPUT =================
        // alamat wajib diisi oleh user
        $request->validate([
            'alamat' => 'required|string|max:255'
        ]);

        // ================= SETUP DATA =================
        $origin = '-6.890580,107.554220'; // koordinat lokasi rental
        $apiKey = env('GOOGLE_MAPS_KEY'); // API key Google Maps
        $alamatTujuan = $request->alamat;

        // ================= 1. GEOCODING =================
        // Mengubah alamat (text) menjadi koordinat (latitude & longitude)
        $geoResponse = Http::get(
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'address' => $alamatTujuan,
                'region' => 'id',
                'key' => $apiKey
            ]
        );

        $geoData = $geoResponse->json();

        // Validasi hasil geocoding
        if (
            empty($geoData['results']) ||
            $geoData['status'] !== 'OK'
        ) {
            // Simpan log error jika gagal
            Log::error('❌ Gagal geocoding alamat', ['response' => $geoData]);

            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan, coba isi lebih lengkap (kota/kecamatan).'
            ]);
        }

        // ================= AMBIL KOORDINAT TUJUAN =================
        $destinationLat = $geoData['results'][0]['geometry']['location']['lat'];
        $destinationLng = $geoData['results'][0]['geometry']['location']['lng'];
        $destination = "{$destinationLat},{$destinationLng}";

        // ================= 2. DISTANCE MATRIX =================
        // Menghitung jarak antara origin dan destination
        $response = Http::get(
            'https://maps.googleapis.com/maps/api/distancematrix/json',
            [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => $apiKey,
                'mode' => 'driving',
                'region' => 'id',
            ]
        );

        $data = $response->json();

        // Validasi hasil perhitungan jarak
        if (
            !isset($data['rows'][0]['elements'][0]['distance']) ||
            $data['rows'][0]['elements'][0]['status'] !== 'OK'
        ) {
            Log::error('❌ Distance API gagal', ['response' => $data]);

            return response()->json([
                'success' => false,
                'message' => $data['error_message'] ?? 'Gagal menghitung jarak.'
            ]);
        }

        // ================= 3. KALKULASI ONGKIR =================
        // Ambil jarak dalam meter & text
        $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
        $distanceText = $data['rows'][0]['elements'][0]['distance']['text'];

        // Konversi ke kilometer
        $distanceKm = round($distanceMeters / 1000, 2);

        // Hitung ongkir berdasarkan tarif per km
        $tarifPerKm = 5000;
        $ongkir = ceil($distanceKm * $tarifPerKm);

        // ================= RESPONSE =================
        // Mengirim hasil jarak & ongkir ke frontend (JSON)
        return response()->json([
            'success' => true,
            'distance_km' => $distanceKm,
            'distance_text' => $distanceText,
            'ongkir' => $ongkir,
        ]);
    }
}