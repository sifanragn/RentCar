<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PickupController extends Controller
{
    public function distance(Request $request)
    {
        $request->validate(['alamat' => 'required|string|max:255']);

        $origin = '-6.890580,107.554220'; // lokasi rental kamu (contoh: Bandung)
        $apiKey = env('GOOGLE_MAPS_KEY');
        $alamatTujuan = $request->alamat;

        // 🧭 1️⃣ Gunakan Geocoding API untuk ubah alamat ke koordinat
        $geoResponse = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $alamatTujuan,
            'region' => 'id',
            'key' => $apiKey
        ]);

        $geoData = $geoResponse->json();

        if (
            empty($geoData['results']) ||
            $geoData['status'] !== 'OK'
        ) {
            Log::error('❌ Gagal geocoding alamat', ['response' => $geoData]);
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan, coba isi lebih lengkap (kota/kecamatan).'
            ]);
        }

        // Ambil koordinat tujuan
        $destinationLat = $geoData['results'][0]['geometry']['location']['lat'];
        $destinationLng = $geoData['results'][0]['geometry']['location']['lng'];
        $destination = "{$destinationLat},{$destinationLng}";

        // 🚗 2️⃣ Hitung jarak pakai Distance Matrix API
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $origin,
            'destinations' => $destination,
            'key' => $apiKey,
            'mode' => 'driving',
            'region' => 'id',
        ]);

        $data = $response->json();

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

        // 🎯 3️⃣ Kalkulasi ongkir
        $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
        $distanceText = $data['rows'][0]['elements'][0]['distance']['text'];
        $distanceKm = round($distanceMeters / 1000, 2);

        $tarifPerKm = 5000;
        $ongkir = ceil($distanceKm * $tarifPerKm);

        return response()->json([
            'success' => true,
            'distance_km' => $distanceKm,
            'distance_text' => $distanceText,
            'ongkir' => $ongkir,
        ]);
    }
}
