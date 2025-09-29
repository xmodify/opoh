<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\OpInsurance;

class DashboardController extends Controller
{
    public function index()
    {
        $totals = \DB::table('op_insurance')->selectRaw("
            COALESCE(SUM(total_visit),0)   as total_visit,
            COALESCE(SUM(endpoint),0)      as endpoint,
            COALESCE(SUM(non_hmain),0)     as non_hmain,
            COALESCE(SUM(uc_anywhere),0)   as uc_anywhere,
            COALESCE(SUM(uc_cr),0)         as uc_cr,
            COALESCE(SUM(uc_herb),0)       as uc_herb,
            COALESCE(SUM(uc_healthmed),0)  as uc_healthmed,
            COALESCE(SUM(ppfs),0)          as ppfs
        ")->first();

        // ส่งเป็น array ใช้ง่าย ๆ ใน Blade
        $cards = [
            'total_visit'  => (int)$totals->total_visit,
            'endpoint'     => (int)$totals->endpoint,
            'non_hmain'    => (int)$totals->non_hmain,
            'uc_anywhere'  => (int)$totals->uc_anywhere,
            'uc_cr'        => (int)$totals->uc_cr,
            'uc_herb'      => (int)$totals->uc_herb,
            'uc_healthmed' => (int)$totals->uc_healthmed,
            'ppfs'         => (int)$totals->ppfs,
        ];

        return view('opinsurance.dashboard', compact('cards'));
    }
}
