<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\JadwalShift;                                                             
use App\Models\TipePekerjaan;
use Illuminate\Support\Facades\Auth;
use App\Models\PeriodeGaji;
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

    // get Outlet revenue
    public function getRevenue($startDate, $endDate, $outletId = null)
    {
        // Token API dan URL
        $apiToken = '92|BN2EvdcWabONwrvbSIbFgSZyPoEoFwjsRwse7li6';
        $apiUrl = 'https://pos.lakesidefnb.group/api/order/ext/report';
        
        // GuzzleHttp client untuk membuat request
        $client = new \GuzzleHttp\Client();
    
        try {
            // Parameter query yang digunakan dalam API request
            $queryParams = [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
    
            // Tambahkan outlet_id jika tersedia
            if ($outletId) {
                $queryParams['outlet_id'] = $outletId;
            }
    
            // Mengirim request GET ke API
            $response = $client->request('GET', $apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => $queryParams, // Tambahkan parameter query
            ]);
    
            // Mengambil body dari response dan mengubah menjadi array
            $responseData = json_decode($response->getBody(), true);
    
            // Mengembalikan data jika tersedia, atau array kosong jika tidak
            if (isset($responseData) && is_array($responseData)) {
                return $responseData;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            // Logging jika terjadi kesalahan
            \Log::error('API Request Error: ' . $e->getMessage());
            return [];
        }
    }
    
    // Calculate fee
    private function calculateFee($revenue, $tipePekerjaan)
    {   
        // Jika tidak ada tipe pekerjaan, kembalikan 0 atau nilai default
        if (!$tipePekerjaan) {
            return 0; // Atau nilai default yang sesuai
        }

        // Ambil nilai dari tipe pekerjaan
        $minFee = $tipePekerjaan->min_fee;
        $avgFee = $tipePekerjaan->avg_fee;
        $maxFee = $tipePekerjaan->max_fee;
        $pendapatanBatasBawah = $tipePekerjaan->pendapatan_batas_bawah;
        $pendapatanBatasAtas = $tipePekerjaan->pendapatan_batas_atas;

        // Logika untuk menentukan fee berdasarkan pendapatan
        if($revenue == 0) {
            return 0;
        } else if ($revenue < $pendapatanBatasBawah) {
            return $minFee;
        } elseif ($revenue >= $pendapatanBatasBawah && $revenue < $pendapatanBatasAtas) {
            return $avgFee;
        } else { // $revenue >= $pendapatanBatasAtas
            return $maxFee;
        }
    }
    
    public function listOutlets(Request $request)
    {
        $apiOutlet = $this->getOutletData();
        $apiOutlet = collect($apiOutlet);

        // Create a mapping of outlet IDs to outlet names
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass data to the view
        return view('manager.chooseoutlet', compact('apiOutlet', 'outletMapping'));
    }

    //Manager Function
    public function IndexManager(Request $request, $id)
    {
        $Users = User::all();
        $apiOutlet = $this->getOutletData();
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $id);
        $periode_gaji = PeriodeGaji::all();

        if (!$selectedOutlet) {
            abort(404, 'Outlet tidak ditemukan');
        }

        $id_periode = $request->id_periode;

        if (!$id_periode) {
            return view('manager.cekgaji', [
                'Users' => $Users,
                'periode_gaji' => $periode_gaji,
                'selectedOutlet' => $selectedOutlet,
                'dataGaji' => collect(), //Data Kosong
            ]);
        }

        $periode = PeriodeGaji::find($id_periode);
        $startDate = $periode->tgl_mulai;
        $endDate = $periode->tgl_akhir;

        $revenueAPI = $this->getRevenue($startDate, $endDate, $id);
        $revenueData = collect($revenueAPI['data'] ?? [])->keyBy('order_date');

        $baseDataGaji = JadwalShift::with(['tipePekerjaan', 'user'])
            ->select('id_user', 'id_tipe_pekerjaan')
            ->where('id_outlet', $id)
            ->groupBy('id_user', 'id_tipe_pekerjaan')
            ->get();


            return view('manager.cekgaji', compact(
                'Users',
                'selectedOutlet',
                'periode_gaji', // Tambahkan ini
                'dataGaji',
                'startDate',
                'endDate'
            ));
    }


    public function filterByDateManager(Request $request, $id)
    {
        $id_periode = $request->id_periode;
        if (!$id_periode) {
            return redirect()->back()->with('error', 'Pilih periode terlebih dahulu');
        }

        $periode = PeriodeGaji::findOrFail($id_periode);
        $startDate = $periode->tgl_mulai;
        $endDate = $periode->tgl_akhir;
        
        $searchQuery = $request->search_query;
        $periode_gaji = PeriodeGaji::all(); 

        $apiOutlet = $this->getOutletData();
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $id);

        if (!$selectedOutlet) {
            abort(404, 'Outlet tidak ditemukan');
        }

        // Get revenue data for the specified date range
        $revenueAPI = $this->getRevenue($startDate, $endDate, $id);
        $revenueData = collect($revenueAPI['data'] ?? [])->keyBy('order_date');

        // Get shift data for the specified date range
        $baseDataGaji = JadwalShift::with(['tipePekerjaan', 'user'])
            ->select('id_user', 'id_tipe_pekerjaan')
            ->where('id_outlet', $id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('id_user', 'id_tipe_pekerjaan')
            ->get();

        // Transform the data
        $dataGaji = $baseDataGaji->map(function ($item) use ($id, $revenueData, $selectedOutlet, $startDate, $endDate) {
            if (!$item->id_user) {
                return null;
            }

            $user = User::find($item->id_user);
            $item->nama_pegawai = $user ? $user->name : 'Unknown';

            $tipePekerjaan = $item->tipePekerjaan;
            $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';

            $shifts = JadwalShift::where('id_user', $item->id_user)
                ->where('id_tipe_pekerjaan', $item->id_tipe_pekerjaan)
                ->where('id_outlet', $id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();

            $totalGaji = 0;
            $shiftsWithRevenue = collect();

            foreach ($shifts as $shift) {
                $shiftDate = \Carbon\Carbon::parse($shift->tanggal)->format('d-m-Y');
                $dailyRevenue = $revenueData[$shiftDate][$selectedOutlet['outlet_name']]['Total'] ?? 0;

                $dailyFee = $this->calculateFee($dailyRevenue, $tipePekerjaan);
                $totalGaji += $dailyFee;

                $shiftWithRevenue = new \stdClass();
                $shiftWithRevenue->tanggal = $shift->tanggal;
                $shiftWithRevenue->revenue = $dailyRevenue;
                $shiftWithRevenue->fee = $dailyFee;

                $shiftsWithRevenue->push($shiftWithRevenue);
            }

            $item->jumlah_shift = $shifts->count();
            $item->detail_shifts = $shiftsWithRevenue;
            $item->total_gaji = $totalGaji;

            return $item;
        })->filter();

        // Filter data by search query
        if ($searchQuery) {
            $dataGaji = $dataGaji->filter(function ($item) use ($searchQuery) {
                return stripos($item->nama_pegawai, $searchQuery) !== false;
            });
        }

        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        return view('manager.cekgaji', compact(
            'dataGaji',
            'selectedOutlet',
            'periode_gaji',
            'startDate',
            'endDate',
            'searchQuery',
            'outletMapping'
        ));
    }

    public function searchByNameManager(Request $request, $id)
    {
        $searchQuery = $request->search_query;
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $periode_gaji = PeriodeGaji::all();
        $idOutlet = $id;

        $apiOutlet = $this->getOutletData();
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $idOutlet);

        if (!$selectedOutlet) {
            abort(404, 'Outlet tidak ditemukan');
        }

        // Ambil data pendapatan outlet
        $revenueAPI = $this->getRevenue($startDate ?? '2000-01-01', $endDate, $id);
        $revenueData = collect($revenueAPI['data'] ?? [])->keyBy('order_date');

        // Ambil data shift
        $baseDataGaji = JadwalShift::with(['tipePekerjaan', 'user'])
            ->select('id_user', 'id_tipe_pekerjaan')
            ->where('id_outlet', $idOutlet);

        // Filter tanggal jika tersedia
        if ($startDate) {
            $baseDataGaji->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $baseDataGaji = $baseDataGaji->groupBy('id_user', 'id_tipe_pekerjaan')->get();

        // Filter berdasarkan nama jika ada query
        if ($searchQuery) {
            $baseDataGaji = $baseDataGaji->filter(function ($item) use ($searchQuery) {
                $user = User::find($item->id_user);
                $item->nama_pegawai = $user ? $user->name : 'Unknown';
                return stripos($item->nama_pegawai, $searchQuery) !== false;
            });
        }

        // Transformasi data
        $dataGaji = $baseDataGaji->map(function ($item) use ($idOutlet, $revenueData, $selectedOutlet, $startDate, $endDate) {
            if (!$item->id_user) {
                return null;
            }

            $user = User::find($item->id_user);
            $item->nama_pegawai = $user ? $user->name : 'Unknown';

            $tipePekerjaan = $item->tipePekerjaan;
            $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';

            $shifts = JadwalShift::where('id_user', $item->id_user)
                ->where('id_tipe_pekerjaan', $item->id_tipe_pekerjaan)
                ->where('id_outlet', $idOutlet);

            if ($startDate) {
                $shifts->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $shifts = $shifts->get();

            $totalGaji = 0;
            $shiftsWithRevenue = collect();

            foreach ($shifts as $shift) {
                $shiftDate = \Carbon\Carbon::parse($shift->tanggal)->format('d-m-Y');
                $dailyRevenue = $revenueData[$shiftDate][$selectedOutlet['outlet_name']]['Total'] ?? 0;

                $dailyFee = $this->calculateFee($dailyRevenue, $tipePekerjaan);
                $totalGaji += $dailyFee;

                $shiftWithRevenue = new \stdClass();
                $shiftWithRevenue->tanggal = $shift->tanggal;
                $shiftWithRevenue->revenue = $dailyRevenue;
                $shiftWithRevenue->fee = $dailyFee;

                $shiftsWithRevenue->push($shiftWithRevenue);
            }

            $item->jumlah_shift = $shifts->count();
            $item->detail_shifts = $shiftsWithRevenue;
            $item->total_gaji = $totalGaji;

            return $item;
        })->filter();

        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        return view('manager.cekgaji', compact(
            'dataGaji',
            'selectedOutlet',
            'periode_gaji',
            'startDate',
            'endDate',
            'searchQuery',
            'outletMapping'
        ));
    }

    public function cetakPDF(Request $request, $id)
    {
        $id_periode = $request->id_periode;
        if (!$id_periode) {
            return redirect()->back()->with('error', 'Pilih periode terlebih dahulu');
        }

        // Ambil tanggal mulai dan selesai dari request
        $periode = PeriodeGaji::findOrFail($id_periode);
        $startDate = $periode->tgl_mulai;
        $endDate = $periode->tgl_akhir;

        // Ambil data outlet
        $apiOutlet = $this->getOutletData();
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $id);

        if (!$selectedOutlet) {
            abort(404, 'Outlet tidak ditemukan');
        }

        // Ambil data pendapatan berdasarkan tanggal
        $revenueAPI = $this->getRevenue($startDate, $endDate, $id);
        $revenueData = collect($revenueAPI['data'] ?? [])->keyBy('order_date');

        // Ambil data shift berdasarkan outlet dan tanggal
        $baseDataGaji = JadwalShift::with(['tipePekerjaan', 'user'])
            ->select('id_user', 'id_tipe_pekerjaan')
            ->where('id_outlet', $id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('id_user', 'id_tipe_pekerjaan')
            ->get();

        // Transformasi data gaji
        $dataGaji = $baseDataGaji->map(function ($item) use ($id, $revenueData, $selectedOutlet, $startDate, $endDate) {
            if (!$item->id_user) {
                return null;
            }

            $user = User::find($item->id_user);
            $item->nama_pegawai = $user ? $user->name : 'Unknown';

            $tipePekerjaan = $item->tipePekerjaan;
            $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';

            $shifts = JadwalShift::where('id_user', $item->id_user)
                ->where('id_tipe_pekerjaan', $item->id_tipe_pekerjaan)
                ->where('id_outlet', $id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();

            $totalGaji = 0;
            $shiftsWithRevenue = collect();

            foreach ($shifts as $shift) {
                $shiftDate = \Carbon\Carbon::parse($shift->tanggal)->format('d-m-Y');
                $dailyRevenue = $revenueData[$shiftDate][$selectedOutlet['outlet_name']]['Total'] ?? 0;

                $dailyFee = $this->calculateFee($dailyRevenue, $tipePekerjaan);
                $totalGaji += $dailyFee;

                $shiftWithRevenue = new \stdClass();
                $shiftWithRevenue->tanggal = $shift->tanggal;
                $shiftWithRevenue->revenue = $dailyRevenue;
                $shiftWithRevenue->fee = $dailyFee;

                $shiftsWithRevenue->push($shiftWithRevenue);
            }

            $item->jumlah_shift = $shifts->count();
            $item->detail_shifts = $shiftsWithRevenue;
            $item->total_gaji = $totalGaji;

            return $item;
        })->filter();

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

        // Tentukan mode: pratinjau (stream) atau unduh (download)
        $mode = $request->get('mode', 'preview'); // Default ke 'preview'
        if ($mode === 'download') {
            return $dompdf->stream('data_gaji.pdf', ['Attachment' => true]); // Unduh PDF
        }

        return response($dompdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', $mode === 'download' ? 'attachment; filename="data_gaji.pdf"' : 'inline; filename="data_gaji.pdf"');

    }

    // Staff Function
    public function IndexStaff(Request $request)
    {
        $userId = Auth::id();
        $apiOutlet = $this->getOutletData();
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');
        
        // Get all salary periods
        $periode_gaji = PeriodeGaji::all();
        
        // Get the current date
        $currentDate = now()->format('Y-m-d');

        // Find the period that includes the current date
        $currentPeriod = PeriodeGaji::where('tgl_mulai', '<=', $currentDate)
            ->where('tgl_akhir', '>=', $currentDate)
            ->first();    

        $startDate = $currentPeriod->tgl_mulai;
        $endDate = $currentPeriod->tgl_akhir;


        // Get revenue data for all outlets since we're looking at a specific user
        $revenueAPI = $this->getRevenue($startDate, $endDate);
        if (!isset($revenueAPI['data']) || empty($revenueAPI['data'])) {
            return redirect()->back()->with('warning', 'Data pendapatan tidak ditemukan.');
        }

        // Index revenue data by date
        $revenueData = collect($revenueAPI['data'])->keyBy('order_date');

        // Get base shift data for the user
        $baseDataGaji = JadwalShift::with(['tipePekerjaan'])
            ->select('id_user', 'id_tipe_pekerjaan', 'id_outlet')
            ->where('id_user', $userId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('id_user', 'id_tipe_pekerjaan', 'id_outlet')
            ->get();

        // Transform data
        $dataGaji = $baseDataGaji->map(function ($item) use ($userId, $revenueData, $outletMapping, $startDate, $endDate) {
            $outletName = $outletMapping[$item->id_outlet] ?? 'Unknown Outlet';
            $item->nama_outlet = $outletName;

            $tipePekerjaan = $item->tipePekerjaan;
            $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';

            // Get all shifts for this user, job type, and outlet
            $shifts = JadwalShift::where('id_user', $userId)
                ->where('id_tipe_pekerjaan', $item->id_tipe_pekerjaan)
                ->where('id_outlet', $item->id_outlet)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal', 'asc')
                ->get();

            $totalGaji = 0;
            $totalRevenue = 0;
            $shiftsWithRevenue = collect();

            foreach ($shifts as $shift) {
                $shiftDate = \Carbon\Carbon::parse($shift->tanggal)->format('d-m-Y');
                
                $dailyRevenue = $revenueData[$shiftDate][$outletName]['Total'] ?? 0;
                $totalRevenue += $dailyRevenue;
                
                // Calculate fee based on revenue and tipe pekerjaan
                $dailyFee = $this->calculateFee($dailyRevenue, $tipePekerjaan);
                $totalGaji += $dailyFee;

                $shiftWithRevenue = new \stdClass();
                $shiftWithRevenue->tanggal = $shift->tanggal;
                $shiftWithRevenue->revenue = $dailyRevenue;
                $shiftWithRevenue->fee = $dailyFee;

                $shiftsWithRevenue->push($shiftWithRevenue);
            }

            $item->jumlah_shift = $shifts->count();
            $item->detail_shifts = $shiftsWithRevenue;
            $item->total_gaji = $totalGaji;
            $item->outlet_revenue = $totalRevenue;

            return $item;
        });

        return view('staff.cekgaji', compact(
                    'periode_gaji',
                    'dataGaji', 
                    'apiOutlet', 
                    'outletMapping', 
                    'startDate', 
                    'endDate',
                    'currentPeriod')
                );
    }

    public function filterByDateStaff(Request $request)
    {
        $userId = Auth::id();

        $id_periode = $request->id_periode;
        
        if (!$id_periode) {
            return redirect()->back()->with('error', 'Pilih periode terlebih dahulu');
        }

        $periode = PeriodeGaji::findOrFail($id_periode);
        $startDate = $periode->tgl_mulai;
        $endDate = $periode->tgl_akhir;
        
        $periode_gaji = PeriodeGaji::all(); 

        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Pilih Periode Dahulu !');
        }

        $apiOutlet = $this->getOutletData();
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Get revenue data for the date range
        $revenueAPI = $this->getRevenue($startDate, $endDate);
      
        // Index revenue data by date
        $revenueData = collect($revenueAPI['data'])->keyBy('order_date');

        // Get shifts for the logged-in user and date range
        $baseDataGaji = JadwalShift::with(['tipePekerjaan'])
            ->select('id_user', 'id_tipe_pekerjaan', 'id_outlet')
            ->where('id_user', $userId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('id_user', 'id_tipe_pekerjaan', 'id_outlet')
            ->get();

        // Transform the data
        $dataGaji = $baseDataGaji->map(function ($item) use ($userId, $revenueData, $outletMapping, $startDate, $endDate) {
            $item->nama_outlet = $outletMapping[$item->id_outlet] ?? 'Unknown Outlet';

            $tipePekerjaan = $item->tipePekerjaan;
            $item->nama_pekerjaan = $tipePekerjaan->tipe_pekerjaan ?? 'Unknown';

            $shifts = JadwalShift::where('id_user', $item->id_user)
                ->where('id_tipe_pekerjaan', $item->id_tipe_pekerjaan)
                ->where('id_outlet', $item->id_outlet)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal', 'asc')
                ->get();

            $totalGaji = 0;
            $shiftsWithRevenue = collect();

            foreach ($shifts as $shift) {
                $shiftDate = \Carbon\Carbon::parse($shift->tanggal)->format('d-m-Y');
                $dailyRevenue = $revenueData[$shiftDate][$item->nama_outlet]['Total'] ?? 0;

                $dailyFee = $this->calculateFee($dailyRevenue, $tipePekerjaan);
                $totalGaji += $dailyFee;

                $shiftWithRevenue = new \stdClass();
                $shiftWithRevenue->tanggal = $shift->tanggal;
                $shiftWithRevenue->revenue = $dailyRevenue;
                $shiftWithRevenue->fee = $dailyFee;

                $shiftsWithRevenue->push($shiftWithRevenue);
            }

            $item->jumlah_shift = $shifts->count();
            $item->detail_shifts = $shiftsWithRevenue;
            $item->total_gaji = $totalGaji;

            return $item;
        });

        return view('staff.cekgaji', compact(
                    'dataGaji', 
                    'apiOutlet', 
                    'startDate', 
                    'endDate', 
                    'outletMapping',
                    'periode_gaji',
                    'id_periode'
                )
            );
    }


    
    
    
}
