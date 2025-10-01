<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\OpInsurance;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $budget_year_select = DB::table('budget_year')
            ->select('LEAVE_YEAR_ID', 'LEAVE_YEAR_NAME')
            ->orderByDesc('LEAVE_YEAR_ID')
            ->limit(5)
            ->get();
        $budget_year_now = DB::table('budget_year')
            ->whereDate('DATE_END', '>=', date('Y-m-d'))
            ->whereDate('DATE_BEGIN', '<=', date('Y-m-d'))
            ->value('LEAVE_YEAR_ID');       
        $budget_year = $request->budget_year ?: $budget_year_now;
        $year_data = DB::table('budget_year')
            ->whereIn('LEAVE_YEAR_ID', [$budget_year, $budget_year - 4])
            ->pluck('DATE_BEGIN', 'LEAVE_YEAR_ID');
        $start_date   = $year_data[$budget_year]     ?? null;
        $start_date_y = $year_data[$budget_year - 4] ?? null;
        $end_date = DB::table('budget_year')
            ->where('LEAVE_YEAR_ID', $budget_year)
            ->value('DATE_END');

        $totals = DB::table('op_insurance')
            ->whereBetween('vstdate', [$start_date, $end_date])
            ->selectRaw("
                COALESCE(SUM(visit_total),0) as visit_total,
                COALESCE(SUM(visit_total_op),0) as visit_total_op,
                COALESCE(SUM(visit_total_pp),0) as visit_total_pp,
                COALESCE(SUM(visit_ucs_outprov),0) as visit_ucs_outprov,
                COALESCE(SUM(inc_ucs_outprov),0) as inc_ucs_outprov,
                COALESCE(SUM(visit_ucs_cr),0) as visit_ucs_cr,
                COALESCE(SUM(inc_uccr),0) as inc_uccr,
                COALESCE(SUM(visit_ucs_herb),0) as visit_ucs_herb,
                COALESCE(SUM(inc_herb),0) as inc_herb,
                COALESCE(SUM(visit_ppfs),0) as visit_ppfs,
                COALESCE(SUM(inc_ppfs),0) as inc_ppfs
            ")->first();

        // ส่งเป็น array ใช้ง่าย ๆ ใน Blade
        $cards = [
            'visit_total'  => (int)$totals->visit_total,
            'visit_total_op'     => (int)$totals->visit_total_op,
            'visit_total_pp'    => (int)$totals->visit_total_pp,
            'visit_ucs_outprov'  => (int)$totals->visit_ucs_outprov,
            'inc_ucs_outprov'  => (int)$totals->inc_ucs_outprov,
            'visit_ucs_cr'        => (int)$totals->visit_ucs_cr,
            'inc_uccr'        => (int)$totals->inc_uccr,
            'visit_ucs_herb'      => (int)$totals->visit_ucs_herb,
            'inc_herb'      => (int)$totals->inc_herb,
            'visit_ppfs' => (int)$totals->visit_ppfs,
            'inc_ppfs' => (int)$totals->inc_ppfs,
        ];

        return view('dashboard', array_merge(compact('budget_year_select','budget_year'),$cards));
    }
}
