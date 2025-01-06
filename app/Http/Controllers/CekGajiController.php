<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\JadwalShift;                                                             
use App\Models\TipePekerjaan;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;

class CekGajiController extends Controller
{
    // get data
    public function getOutletData()
    {
        // Token API dan URL
        $apiToken = '92|BN2EvdcWabONwrvbSIbFgSZyPoEoFwjsRwse7li6';
        $apiUrl = 'https://pos.lakesidefnb.group/api/outlet'; // Menyesuaikan URL API

        // GuzzleHttp client untuk membuat request
        $client = new Client();

        try {
            // Mengirim request GET ke API
            $response = $client->request('GET', $apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Mengambil body dari response dan mengubah menjadi array
            $responseData = json_decode($response->getBody(), true);

            // Mengembalikan data outlet jika tersedia, atau array kosong jika tidak
            if (isset($responseData['data']) && is_array($responseData['data'])) {
                return $responseData['data'];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            // Logging jika terjadi kesalahan
            Log::error('API Request Error: ' . $e->getMessage());
            return [];
        }
    }

    //Manager Function

    public function listOutlets(Request $request)
    {
        $apiOutlet = $this->getOutletData();
        $apiOutlet = collect($apiOutlet);

        // Create a mapping of outlet IDs to outlet names
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass data to the view
        return view('manager.chooseoutlet', compact('apiOutlet', 'outletMapping'));
    }

    public function IndexManager(Request $request, $id)
    {
        $Users = User::all();
        $apiOutlet = $this->getOutletData();
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $id);
    
        if (!$selectedOutlet) {
            abort(404, 'Outlet tidak ditemukan');
        }

        $startDate = null; // or you can set to a default date
        $endDate = now()->format('Y-m-d'); // Today’s date or any default date

    
        $dataGaji = JadwalShift::with('tipePekerjaan') // Ambil data tipe pekerjaan
            ->select('id_user', 'id_tipe_pekerjaan', \DB::raw('COUNT(*) as jumlah_shift'))
            ->where('id_outlet', $id)
            ->groupBy('id_user', 'id_tipe_pekerjaan')
            ->get();
    
        $dataGaji->transform(function ($item)  use ($id) {
            $user = User::find($item->id_user);
            $item->nama_pegawai = $user ? $user->name : 'Unknown';
    
            $tipePekerjaan = TipePekerjaan::find($item->id_tipe_pekerjaan);
            $gajiPerShift = $tipePekerjaan ? $tipePekerjaan->fee : 0;
            $item->gaji_per_shift = $gajiPerShift;
            $item->total_gaji = $item->jumlah_shift * $gajiPerShift;
            $item->nama_pekerjaan = $item->tipePekerjaan->tipe_pekerjaan ?? 'Unknown';

            // Ambil semua tanggal shift
            $tanggalShift = JadwalShift::where('id_user', $item->id_user)
            ->where('id_tipe_pekerjaan', $item->id_tipe_pekerjaan)
            ->where('id_outlet', $id) 
            ->pluck('tanggal')->toArray();

            // Gabungkan tanggal-tanggal menjadi string
            $item->tanggal_shift = implode(', ', $tanggalShift); 
            
            return $item;
        });

    
    
        // Ambil data outlet
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');
    
        return view('manager.cekgaji', compact('Users', 'selectedOutlet', 'outletMapping', 'dataGaji', 'startDate', 'endDate'));
    }
    

    public function filterByDateManager(Request $request, $id)
    {
        $startDate = $request->start_date;
        $endDate = now()->format('Y-m-d');
        $searchQuery = $request->search_query; // Get the search query
        $idOutlet = $id;

        if (!$startDate) {
            return redirect()->back()->with('error', 'Tanggal Mulai harus diisi.');
        }

        $dataGaji = JadwalShift::with('tipePekerjaan')
            ->select('id_user', 'id_tipe_pekerjaan', \DB::raw('COUNT(*) as jumlah_shift'))
            ->where('id_outlet', $idOutlet)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('id_user', 'id_tipe_pekerjaan')
            ->get();

        if ($searchQuery) {
            $dataGaji = $dataGaji->filter(function ($item) use ($searchQuery) {
                $user = User::find($item->id_user);
                $item->nama_pegawai = $user ? $user->name : 'Unknown';
                return stripos($item->nama_pegawai, $searchQuery) !== false;
            });
        }

        // Transform data
        $dataGaji->transform(function ($item) use ($id){
            $user = User::find($item->id_user);
            $item->nama_pegawai = $user ? $user->name : 'Unknown';

            $tipePekerjaan = TipePekerjaan::find($item->id_tipe_pekerjaan);
            $item->gaji_per_shift = $tipePekerjaan ? $tipePekerjaan->fee : 0;
            $item->total_gaji = $item->jumlah_shift * $item->gaji_per_shift;
            $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';

             // Ambil semua tanggal shift
             $tanggalShift = JadwalShift::where('id_user', $item->id_user)
             ->where('id_tipe_pekerjaan', $item->id_tipe_pekerjaan)
             ->where('id_outlet', $id)
             ->pluck('tanggal')->toArray();
 
             // Gabungkan tanggal-tanggal menjadi string
             $item->tanggal_shift = implode(', ', $tanggalShift); 

            return $item;
        });



        $apiOutlet = $this->getOutletData();
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $idOutlet);

        return view('manager.cekgaji', compact('dataGaji', 'selectedOutlet', 'startDate', 'endDate', 'searchQuery'));
    }

    public function searchByNameManager(Request $request, $id)
    {
        $searchQuery = $request->search_query;
        $startDate = $request->start_date; // Get the start date
        $endDate = now()->format('Y-m-d'); // Define an end date
        $idOutlet = $id;

        $dataGaji = JadwalShift::with('tipePekerjaan', 'user')
            ->select('id_user', 'id_tipe_pekerjaan', 'id_outlet', \DB::raw('COUNT(*) as jumlah_shift'))
            ->where('id_outlet', $id)
            ->groupBy('id_user', 'id_tipe_pekerjaan', 'id_outlet')
            ->get();

        if ($searchQuery) {
            $dataGaji = $dataGaji->filter(function ($item) use ($searchQuery) {
                $user = User::find($item->id_user);
                $item->nama_pegawai = $user ? $user->name : 'Unknown';
                return stripos($item->nama_pegawai, $searchQuery) !== false;
            });
        }

        // Transform data
        $dataGaji->transform(function ($item) use($id) {
            $user = User::find($item->id_user);
            $item->nama_pegawai = $user ? $user->name : 'Unknown';

            $tipePekerjaan = TipePekerjaan::find($item->id_tipe_pekerjaan);
            $item->gaji_per_shift = $tipePekerjaan ? $tipePekerjaan->fee : 0;
            $item->total_gaji = $item->jumlah_shift * $item->gaji_per_shift;
            $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';

             // Ambil semua tanggal shift
             $tanggalShift = JadwalShift::where('id_user', $item->id_user)
             ->where('id_tipe_pekerjaan', $item->id_tipe_pekerjaan)
             ->where('id_outlet', $id)
             ->pluck('tanggal')->toArray();
 
             // Gabungkan tanggal-tanggal menjadi string
             $item->tanggal_shift = implode(', ', $tanggalShift); 

            return $item;
        });


        $apiOutlet = $this->getOutletData();
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $idOutlet);

        return view('manager.cekgaji', compact('dataGaji', 'selectedOutlet', 'startDate', 'endDate', 'searchQuery'));
    }

    public function cetakPDF(Request $request, $id)
{
    // Ambil tanggal mulai dan selesai dari request (jika ada)
    $startDate = $request->start_date ?? now()->format('Y-m-01');
    $endDate = now()->format('Y-m-d'); // Tanggal selesai adalah hari ini

    // Ambil data gaji dengan filter tanggal dan outlet
    $dataGaji = JadwalShift::with('tipePekerjaan', 'user') // Mengambil data tipe pekerjaan dan user
        ->select('id_user', 'id_tipe_pekerjaan', 'id_outlet', \DB::raw('COUNT(*) as jumlah_shift'))
        ->where('id_outlet', $id)
        ->whereBetween('tanggal', [$startDate, $endDate])
        ->groupBy('id_user', 'id_tipe_pekerjaan', 'id_outlet')
        ->get();

    // Transformasi data gaji
    $dataGaji->transform(function ($item) {
        $user = User::find($item->id_user);
        $item->nama_pegawai = $user ? $user->name : 'Unknown';

        $tipePekerjaan = TipePekerjaan::find($item->id_tipe_pekerjaan);
        $item->gaji_per_shift = $tipePekerjaan ? $tipePekerjaan->fee : 0;
        $item->total_gaji = $item->jumlah_shift * $item->gaji_per_shift;
        $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';
        return $item;
    });

    // Ambil data outlet
    $apiOutlet = $this->getOutletData();
    $selectedOutlet = collect($apiOutlet)->firstWhere('id', $id);

    if (!$selectedOutlet) {
        abort(404, 'Outlet tidak ditemukan');
    }

    // Inisialisasi DomPDF
    $dompdf = new Dompdf();
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $dompdf->setOptions($options);

    // Load view untuk template PDF
    $html = view('manager.cetakPDF', compact('dataGaji', 'selectedOutlet', 'startDate', 'endDate'))->render();
    $dompdf->loadHtml($html);

    // Set ukuran kertas
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF
    $dompdf->render();

    // Output PDF ke browser
    return $dompdf->stream('data_gaji.pdf', ['Attachment' => 0]);
}
    

    // Staff Function
    public function IndexStaff(Request $request)
    {
        $userId = Auth::id(); // Ambil ID pengguna yang sedang login
    
        // Ambil data outlet dari API
        $apiOutlet = $this->getOutletData();
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');
        
        // Ambil TipePekerjaan
        $TipePekerjaan = TipePekerjaan::all();
        
        // Inisialisasi array untuk menyimpan data untuk tampilan
        $dataGaji = [];
        
        // Loop melalui setiap TipePekerjaan untuk memeriksa shift
        foreach ($TipePekerjaan as $tipe) {
            // Ambil semua shift terkait pengguna saat ini dan tipe_pekerjaan
            $shiftData = JadwalShift::select('id_user', 'id_tipe_pekerjaan', 'id_outlet', \DB::raw('COUNT(*) as jumlah_shift'))
                ->where('id_user', $userId) // Filter berdasarkan pengguna yang sedang login
                ->where('id_tipe_pekerjaan', $tipe->id)
                ->groupBy('id_user', 'id_tipe_pekerjaan', 'id_outlet')
                ->get(); // Ambil semua record yang cocok
        
            // Loop setiap shift yang ditemukan
            foreach ($shiftData as $shift) {
                // Ambil nama pengguna
                $user = User::find($shift->id_user);
                $shift->nama_pegawai = $user ? $user->name : 'Unknown';
        
                // Ambil nama outlet dari mapping
                $shift->nama_outlet = $outletMapping[$shift->id_outlet] ?? 'Unknown Outlet';
        
                // Hitung gaji berdasarkan tipe pekerjaan
                $fee = $tipe->fee; // Ambil fee berdasarkan tipe pekerjaan
                $shift->gaji_per_shift = $fee;
                $shift->total_gaji = $shift->jumlah_shift * $fee;
                $shift->nama_pekerjaan = $tipe->tipe_pekerjaan; // Ambil nama pekerjaan
        
                // Tambahkan data ke array dataGaji
                $dataGaji[] = $shift;
            }
        }
    
        // Pass data dan informasi outlet ke tampilan
        return view('staff.cekgaji', [
            'apiOutlet' => $apiOutlet,
            'outletMapping' => $outletMapping,
            'dataGaji' => $dataGaji,
        ]);
    }
    
    public function filterByDateStaff(Request $request)
    {
        $userId = Auth::id(); // Ambil ID pengguna yang sedang login
        $startDate = $request->start_date;
        $endDate = now()->format('Y-m-d');
    
        if (!$startDate) {
            return redirect()->back()->with('error', 'Tanggal Mulai harus diisi.');
        }
    
        $dataGaji = JadwalShift::with('tipePekerjaan')
            ->select('id_user', 'id_tipe_pekerjaan', 'id_outlet', \DB::raw('COUNT(*) as jumlah_shift'))
            ->where('id_user', $userId) // Filter berdasarkan pengguna yang sedang login
            ->whereBetween('tanggal', [$startDate, $endDate]) // Filter tanggal
            ->groupBy('id_user', 'id_tipe_pekerjaan', 'id_outlet')
            ->get();
    
        $dataGaji->transform(function ($item) {
            $user = User::find($item->id_user);
            $item->nama_pegawai = $user ? $user->name : 'Unknown';
    
            $tipePekerjaan = TipePekerjaan::find($item->id_tipe_pekerjaan);
            $item->gaji_per_shift = $tipePekerjaan ? $tipePekerjaan->fee : 0;
            $item->total_gaji = $item->jumlah_shift * $item->gaji_per_shift;
            $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';
    
            $apiOutlet = $this->getOutletData();
            $item->nama_outlet = collect($apiOutlet)->pluck('outlet_name', 'id')[$item->id_outlet] ?? 'Unknown Outlet';
            return $item;
        });
    
        $apiOutlet = $this->getOutletData();
    
        return view('staff.cekgaji', compact('dataGaji', 'apiOutlet', 'startDate', 'endDate'));
    }
    
    
    
}
