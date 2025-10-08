<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\OpInsurance;
// ใช้ Carbon ของ Laravel เพื่อความยืดหยุ่น (แทน date('Y-m-d'))
use Carbon\Carbon;

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

        $today = Carbon::today()->toDateString(); // ได้รูปแบบ YYYY-MM-DD เช่น 2025-10-07

        $total = DB::table('opd')
            ->whereBetween('vstdate', [$today, $today])
            ->selectRaw("
                COALESCE(SUM(visit_total),0)        AS visit_total,
                COALESCE(SUM(visit_total_op),0)     AS visit_total_op,
                COALESCE(SUM(visit_total_pp),0)     AS visit_total_pp,
                COALESCE(SUM(visit_endpoint),0)     AS visit_endpoint,
                COALESCE(SUM(visit_ucs_outprov),0)  AS visit_ucs_outprov,
                COALESCE(SUM(inc_ucs_outprov),0)    AS inc_ucs_outprov,
                COALESCE(SUM(visit_ucs_cr),0)       AS visit_ucs_cr,
                COALESCE(SUM(inc_uccr),0)           AS inc_uccr,
                COALESCE(SUM(visit_ucs_herb),0)     AS visit_ucs_herb,
                COALESCE(SUM(inc_herb),0)           AS inc_herb,
                COALESCE(SUM(visit_ppfs),0)         AS visit_ppfs,
                COALESCE(SUM(inc_ppfs),0)           AS inc_ppfs,
                COALESCE(SUM(visit_referout_inprov),0)      AS visit_referout_inprov,
                COALESCE(SUM(visit_referout_outprov),0)     AS visit_referout_outprov,
                COALESCE(SUM(visit_ucs_incup),0)+COALESCE(SUM(visit_ucs_inprov),0)+COALESCE(SUM(visit_ucs_outprov),0)     AS visit_ucs,
                COALESCE(SUM(inc_ucs_incup),0)+COALESCE(SUM(inc_ucs_inprov),0)+COALESCE(SUM(inc_ucs_outprov),0)     AS inc_ucs,
                COALESCE(SUM(visit_ofc),0)         AS visit_ofc,
                COALESCE(SUM(inc_ofc),0)           AS inc_ofc,
                COALESCE(SUM(visit_lgo),0)         AS visit_lgo,
                COALESCE(SUM(inc_lgo),0)           AS inc_lgo,
                COALESCE(SUM(visit_sss),0)         AS visit_sss,
                COALESCE(SUM(inc_sss),0)           AS inc_sss,
                COALESCE(SUM(visit_pay),0)         AS visit_pay,
                COALESCE(SUM(inc_pay),0)           AS inc_pay
            ")->first();

        // ส่งเป็น array ใช้ง่าย ๆ ใน Blade
        $card = [
            'visit_total'       => (int)$total->visit_total,
            'visit_total_op'    => (int)$total->visit_total_op,
            'visit_total_pp'    => (int)$total->visit_total_pp,           
            'visit_endpoint'    => (int)$total->visit_endpoint,
            'visit_ucs_outprov' => (int)$total->visit_ucs_outprov,
            'inc_ucs_outprov'   => (float)$total->inc_ucs_outprov,
            'visit_ucs_cr'      => (int)$total->visit_ucs_cr,
            'inc_uccr'          => (float)$total->inc_uccr,
            'visit_ucs_herb'    => (int)$total->visit_ucs_herb,
            'inc_herb'          => (float)$total->inc_herb,  
            'visit_ppfs'        => (int)$total->visit_ppfs,         
            'inc_ppfs'          => (float)$total->inc_ppfs,
            'visit_referout_inprov'        => (int)$total->visit_referout_inprov, 
            'visit_referout_outprov'       => (int)$total->visit_referout_outprov, 
            'visit_ucs'         => (int)$total->visit_ucs,
            'inc_ucs'           => (float)$total->inc_ucs,
            'visit_ofc'         => (int)$total->visit_ofc,
            'inc_ofc'           => (float)$total->inc_ofc,
            'visit_lgo'         => (int)$total->visit_lgo,
            'inc_lgo'           => (float)$total->inc_lgo,
            'visit_sss'         => (int)$total->visit_sss,
            'inc_sss'           => (float)$total->inc_sss,
            'visit_pay'         => (int)$total->visit_pay,
            'inc_pay'           => (float)$total->inc_pay,
        ];

        $hospitalSummary = DB::table('opd')
            ->join('hospital_config', 'opd.hospcode', '=', 'hospital_config.hospcode')
            ->whereBetween('vstdate', [$today, $today])
            ->select(
                'opd.hospcode',
                'hospital_config.hospname',
                DB::raw('MAX(opd.updated_at) AS last_updated_at'),
                DB::raw('COALESCE(SUM(visit_total),0) AS visit_total'),
                DB::raw('COALESCE(SUM(visit_total_op),0) AS visit_total_op'),
                DB::raw('COALESCE(SUM(visit_total_pp),0) AS visit_total_pp'),
                DB::raw('COALESCE(SUM(visit_endpoint),0) AS visit_endpoint'),
                DB::raw('COALESCE(SUM(visit_ucs_outprov),0) AS visit_ucs_outprov'),
                DB::raw('COALESCE(SUM(inc_ucs_outprov),0) AS inc_ucs_outprov'),
                DB::raw('COALESCE(SUM(visit_ucs_cr),0) AS visit_ucs_cr'),
                DB::raw('COALESCE(SUM(inc_uccr),0) AS inc_uccr'),
                DB::raw('COALESCE(SUM(visit_ucs_herb),0) AS visit_ucs_herb'),
                DB::raw('COALESCE(SUM(inc_herb),0) AS inc_herb'),
                DB::raw('COALESCE(SUM(visit_ppfs),0) AS visit_ppfs'),
                DB::raw('COALESCE(SUM(inc_ppfs),0) AS inc_ppfs'),
                DB::raw('COALESCE(SUM(visit_referout_inprov),0) AS visit_referout_inprov'),
                DB::raw('COALESCE(SUM(visit_referout_outprov),0) AS visit_referout_outprov'),
                DB::raw('COALESCE(SUM(visit_ucs_incup),0)+COALESCE(SUM(visit_ucs_inprov),0)+COALESCE(SUM(visit_ucs_outprov),0) AS visit_ucs'),
                DB::raw('COALESCE(SUM(inc_ucs_incup),0)+COALESCE(SUM(inc_ucs_inprov),0)+COALESCE(SUM(inc_ucs_outprov),0) AS inc_ucs'),
                DB::raw('COALESCE(SUM(visit_ofc),0) AS visit_ofc'),
                DB::raw('COALESCE(SUM(inc_ofc),0) AS inc_ofc'),
                DB::raw('COALESCE(SUM(visit_lgo),0) AS visit_lgo'),
                DB::raw('COALESCE(SUM(inc_lgo),0) AS inc_lgo'),
                DB::raw('COALESCE(SUM(visit_sss),0) AS visit_sss'),
                DB::raw('COALESCE(SUM(inc_sss),0) AS inc_sss'),
                 DB::raw('COALESCE(SUM(visit_pay),0) AS visit_pay'),
                DB::raw('COALESCE(SUM(inc_pay),0) AS inc_pay')
            )
            ->groupBy('opd.hospcode', 'hospital_config.hospname')
            ->orderBy('hospital_config.hospname')
            ->get();

        // ดึงข้อมูลโรงพยาบาลทั้งหมด
        $hospitals = DB::table('hospital_config')
            ->select('hospcode', 'hospname', 'bed_qty', 'bed_use','updated_at')
            ->get();
        // รวมยอดเตียงทั้งหมด
        $total_bed_qty = $hospitals->sum('bed_qty') ?? 0;
        $total_bed_use = $hospitals->sum('bed_use') ?? 0;
        $total_bed_empty = $total_bed_qty - $total_bed_use;
    

        $update_at10985 = DB::table('opd')->where('hospcode', '10985')->max('updated_at');
        $update_at10986 = DB::table('opd')->where('hospcode', '10986')->max('updated_at');
        $update_at10987 = DB::table('opd')->where('hospcode', '10987')->max('updated_at');
        $update_at10988 = DB::table('opd')->where('hospcode', '10988')->max('updated_at');
        $update_at10989 = DB::table('opd')->where('hospcode', '10989')->max('updated_at');
        $update_at10990 = DB::table('opd')->where('hospcode', '10990')->max('updated_at');

// OPD------------------------------------------------------------------------------------------------------------------

        $total_10985 = DB::select("
            SELECT MIN(CASE
            WHEN MONTH(vstdate)=10 THEN CONCAT('ต.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=11 THEN CONCAT('พ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=12 THEN CONCAT('ธ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=1  THEN CONCAT('ม.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=2  THEN CONCAT('ก.พ. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=3  THEN CONCAT('มี.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=4  THEN CONCAT('เม.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=5  THEN CONCAT('พ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=6  THEN CONCAT('มิ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=7  THEN CONCAT('ก.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=8  THEN CONCAT('ส.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=9  THEN CONCAT('ก.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            END) AS month, 
            SUM(hn_total)            AS hn_total,
            SUM(visit_total)         AS visit_total,
            SUM(visit_total_op)      AS visit_total_op,
            SUM(visit_total_pp)      AS visit_total_pp,
            SUM(visit_ucs_incup)     AS visit_ucs_incup,
            SUM(visit_ucs_inprov)    AS visit_ucs_inprov,
            SUM(visit_ucs_outprov)   AS visit_ucs_outprov,
            SUM(visit_ofc)           AS visit_ofc,
            SUM(visit_bkk)           AS visit_bkk,
            SUM(visit_bmt)           AS visit_bmt,
            SUM(visit_sss)           AS visit_sss,
            SUM(visit_lgo)           AS visit_lgo,
            SUM(visit_fss)           AS visit_fss,
            SUM(visit_stp)           AS visit_stp,
            SUM(visit_pay)           AS visit_pay,
            SUM(visit_ppfs)          AS visit_ppfs,
            SUM(visit_ucs_cr)        AS visit_ucs_cr,
            SUM(visit_ucs_herb)      AS visit_ucs_herb,
            SUM(visit_ucs_healthmed) AS visit_ucs_healthmed,
            SUM(inc_total)            AS inc_total,
            SUM(inc_lab_total)        AS inc_lab_total,
            SUM(inc_drug_total)       AS inc_drug_total,
            SUM(inc_ucs_incup)        AS inc_ucs_incup,
            SUM(inc_lab_ucs_incup)    AS inc_lab_ucs_incup,
            SUM(inc_drug_ucs_incup)   AS inc_drug_ucs_incup,
            SUM(inc_ucs_inprov)       AS inc_ucs_inprov,
            SUM(inc_lab_ucs_inprov)   AS inc_lab_ucs_inprov,
            SUM(inc_drug_ucs_inprov)  AS inc_drug_ucs_inprov,
            SUM(inc_ucs_outprov)      AS inc_ucs_outprov,
            SUM(inc_lab_ucs_outprov)  AS inc_lab_ucs_outprov,
            SUM(inc_drug_ucs_outprov) AS inc_drug_ucs_outprov,
            SUM(inc_ofc)              AS inc_ofc,
            SUM(inc_lab_ofc)          AS inc_lab_ofc,
            SUM(inc_drug_ofc)         AS inc_drug_ofc,
            SUM(inc_bkk)              AS inc_bkk,
            SUM(inc_lab_bkk)          AS inc_lab_bkk,
            SUM(inc_drug_bkk)         AS inc_drug_bkk,
            SUM(inc_bmt)              AS inc_bmt,
            SUM(inc_lab_bmt)          AS inc_lab_bmt,
            SUM(inc_drug_bmt)         AS inc_drug_bmt,
            SUM(inc_sss)              AS inc_sss,
            SUM(inc_lab_sss)          AS inc_lab_sss,
            SUM(inc_drug_sss)         AS inc_drug_sss,
            SUM(inc_lgo)              AS inc_lgo,
            SUM(inc_lab_lgo)          AS inc_lab_lgo,
            SUM(inc_drug_lgo)         AS inc_drug_lgo,
            SUM(inc_fss)              AS inc_fss,
            SUM(inc_lab_fss)          AS inc_lab_fss,
            SUM(inc_drug_fss)         AS inc_drug_fss,
            SUM(inc_stp)              AS inc_stp,
            SUM(inc_lab_stp)          AS inc_lab_stp,
            SUM(inc_drug_stp)         AS inc_drug_stp,
            SUM(inc_pay)              AS inc_pay,
            SUM(inc_lab_pay)          AS inc_lab_pay,
            SUM(inc_drug_pay)         AS inc_drug_pay,
            SUM(inc_ppfs)             AS inc_ppfs,
            SUM(inc_uccr)             AS inc_uccr,
            SUM(inc_herb)             AS inc_herb
            FROM opd
            WHERE vstdate BETWEEN ? AND ?
            AND hospcode = 10985
            GROUP BY YEAR(vstdate), MONTH(vstdate)
            ORDER BY YEAR(vstdate), MONTH(vstdate) ", [$start_date, $end_date]);

        $total_10986 = DB::select("
            SELECT MIN(CASE
            WHEN MONTH(vstdate)=10 THEN CONCAT('ต.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=11 THEN CONCAT('พ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=12 THEN CONCAT('ธ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=1  THEN CONCAT('ม.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=2  THEN CONCAT('ก.พ. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=3  THEN CONCAT('มี.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=4  THEN CONCAT('เม.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=5  THEN CONCAT('พ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=6  THEN CONCAT('มิ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=7  THEN CONCAT('ก.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=8  THEN CONCAT('ส.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=9  THEN CONCAT('ก.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            END) AS month, 
            SUM(hn_total)            AS hn_total,
            SUM(visit_total)         AS visit_total,
            SUM(visit_total_op)      AS visit_total_op,
            SUM(visit_total_pp)      AS visit_total_pp,
            SUM(visit_ucs_incup)     AS visit_ucs_incup,
            SUM(visit_ucs_inprov)    AS visit_ucs_inprov,
            SUM(visit_ucs_outprov)   AS visit_ucs_outprov,
            SUM(visit_ofc)           AS visit_ofc,
            SUM(visit_bkk)           AS visit_bkk,
            SUM(visit_bmt)           AS visit_bmt,
            SUM(visit_sss)           AS visit_sss,
            SUM(visit_lgo)           AS visit_lgo,
            SUM(visit_fss)           AS visit_fss,
            SUM(visit_stp)           AS visit_stp,
            SUM(visit_pay)           AS visit_pay,
            SUM(visit_ppfs)          AS visit_ppfs,
            SUM(visit_ucs_cr)        AS visit_ucs_cr,
            SUM(visit_ucs_herb)      AS visit_ucs_herb,
            SUM(visit_ucs_healthmed) AS visit_ucs_healthmed,
            SUM(inc_total)            AS inc_total,
            SUM(inc_lab_total)        AS inc_lab_total,
            SUM(inc_drug_total)       AS inc_drug_total,
            SUM(inc_ucs_incup)        AS inc_ucs_incup,
            SUM(inc_lab_ucs_incup)    AS inc_lab_ucs_incup,
            SUM(inc_drug_ucs_incup)   AS inc_drug_ucs_incup,
            SUM(inc_ucs_inprov)       AS inc_ucs_inprov,
            SUM(inc_lab_ucs_inprov)   AS inc_lab_ucs_inprov,
            SUM(inc_drug_ucs_inprov)  AS inc_drug_ucs_inprov,
            SUM(inc_ucs_outprov)      AS inc_ucs_outprov,
            SUM(inc_lab_ucs_outprov)  AS inc_lab_ucs_outprov,
            SUM(inc_drug_ucs_outprov) AS inc_drug_ucs_outprov,
            SUM(inc_ofc)              AS inc_ofc,
            SUM(inc_lab_ofc)          AS inc_lab_ofc,
            SUM(inc_drug_ofc)         AS inc_drug_ofc,
            SUM(inc_bkk)              AS inc_bkk,
            SUM(inc_lab_bkk)          AS inc_lab_bkk,
            SUM(inc_drug_bkk)         AS inc_drug_bkk,
            SUM(inc_bmt)              AS inc_bmt,
            SUM(inc_lab_bmt)          AS inc_lab_bmt,
            SUM(inc_drug_bmt)         AS inc_drug_bmt,
            SUM(inc_sss)              AS inc_sss,
            SUM(inc_lab_sss)          AS inc_lab_sss,
            SUM(inc_drug_sss)         AS inc_drug_sss,
            SUM(inc_lgo)              AS inc_lgo,
            SUM(inc_lab_lgo)          AS inc_lab_lgo,
            SUM(inc_drug_lgo)         AS inc_drug_lgo,
            SUM(inc_fss)              AS inc_fss,
            SUM(inc_lab_fss)          AS inc_lab_fss,
            SUM(inc_drug_fss)         AS inc_drug_fss,
            SUM(inc_stp)              AS inc_stp,
            SUM(inc_lab_stp)          AS inc_lab_stp,
            SUM(inc_drug_stp)         AS inc_drug_stp,
            SUM(inc_pay)              AS inc_pay,
            SUM(inc_lab_pay)          AS inc_lab_pay,
            SUM(inc_drug_pay)         AS inc_drug_pay,
            SUM(inc_ppfs)             AS inc_ppfs,
            SUM(inc_uccr)             AS inc_uccr,
            SUM(inc_herb)             AS inc_herb
            FROM opd
            WHERE vstdate BETWEEN ? AND ?
            AND hospcode = 10986
            GROUP BY YEAR(vstdate), MONTH(vstdate)
            ORDER BY YEAR(vstdate), MONTH(vstdate) ", [$start_date, $end_date]);

        $total_10987 = DB::select("
            SELECT MIN(CASE
            WHEN MONTH(vstdate)=10 THEN CONCAT('ต.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=11 THEN CONCAT('พ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=12 THEN CONCAT('ธ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=1  THEN CONCAT('ม.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=2  THEN CONCAT('ก.พ. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=3  THEN CONCAT('มี.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=4  THEN CONCAT('เม.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=5  THEN CONCAT('พ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=6  THEN CONCAT('มิ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=7  THEN CONCAT('ก.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=8  THEN CONCAT('ส.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=9  THEN CONCAT('ก.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            END) AS month, 
            SUM(hn_total)            AS hn_total,
            SUM(visit_total)         AS visit_total,
            SUM(visit_total_op)      AS visit_total_op,
            SUM(visit_total_pp)      AS visit_total_pp,
            SUM(visit_ucs_incup)     AS visit_ucs_incup,
            SUM(visit_ucs_inprov)    AS visit_ucs_inprov,
            SUM(visit_ucs_outprov)   AS visit_ucs_outprov,
            SUM(visit_ofc)           AS visit_ofc,
            SUM(visit_bkk)           AS visit_bkk,
            SUM(visit_bmt)           AS visit_bmt,
            SUM(visit_sss)           AS visit_sss,
            SUM(visit_lgo)           AS visit_lgo,
            SUM(visit_fss)           AS visit_fss,
            SUM(visit_stp)           AS visit_stp,
            SUM(visit_pay)           AS visit_pay,
            SUM(visit_ppfs)          AS visit_ppfs,
            SUM(visit_ucs_cr)        AS visit_ucs_cr,
            SUM(visit_ucs_herb)      AS visit_ucs_herb,
            SUM(visit_ucs_healthmed) AS visit_ucs_healthmed,
            SUM(inc_total)            AS inc_total,
            SUM(inc_lab_total)        AS inc_lab_total,
            SUM(inc_drug_total)       AS inc_drug_total,
            SUM(inc_ucs_incup)        AS inc_ucs_incup,
            SUM(inc_lab_ucs_incup)    AS inc_lab_ucs_incup,
            SUM(inc_drug_ucs_incup)   AS inc_drug_ucs_incup,
            SUM(inc_ucs_inprov)       AS inc_ucs_inprov,
            SUM(inc_lab_ucs_inprov)   AS inc_lab_ucs_inprov,
            SUM(inc_drug_ucs_inprov)  AS inc_drug_ucs_inprov,
            SUM(inc_ucs_outprov)      AS inc_ucs_outprov,
            SUM(inc_lab_ucs_outprov)  AS inc_lab_ucs_outprov,
            SUM(inc_drug_ucs_outprov) AS inc_drug_ucs_outprov,
            SUM(inc_ofc)              AS inc_ofc,
            SUM(inc_lab_ofc)          AS inc_lab_ofc,
            SUM(inc_drug_ofc)         AS inc_drug_ofc,
            SUM(inc_bkk)              AS inc_bkk,
            SUM(inc_lab_bkk)          AS inc_lab_bkk,
            SUM(inc_drug_bkk)         AS inc_drug_bkk,
            SUM(inc_bmt)              AS inc_bmt,
            SUM(inc_lab_bmt)          AS inc_lab_bmt,
            SUM(inc_drug_bmt)         AS inc_drug_bmt,
            SUM(inc_sss)              AS inc_sss,
            SUM(inc_lab_sss)          AS inc_lab_sss,
            SUM(inc_drug_sss)         AS inc_drug_sss,
            SUM(inc_lgo)              AS inc_lgo,
            SUM(inc_lab_lgo)          AS inc_lab_lgo,
            SUM(inc_drug_lgo)         AS inc_drug_lgo,
            SUM(inc_fss)              AS inc_fss,
            SUM(inc_lab_fss)          AS inc_lab_fss,
            SUM(inc_drug_fss)         AS inc_drug_fss,
            SUM(inc_stp)              AS inc_stp,
            SUM(inc_lab_stp)          AS inc_lab_stp,
            SUM(inc_drug_stp)         AS inc_drug_stp,
            SUM(inc_pay)              AS inc_pay,
            SUM(inc_lab_pay)          AS inc_lab_pay,
            SUM(inc_drug_pay)         AS inc_drug_pay,
            SUM(inc_ppfs)             AS inc_ppfs,
            SUM(inc_uccr)             AS inc_uccr,
            SUM(inc_herb)             AS inc_herb
            FROM opd
            WHERE vstdate BETWEEN ? AND ?
            AND hospcode = 10987
            GROUP BY YEAR(vstdate), MONTH(vstdate)
            ORDER BY YEAR(vstdate), MONTH(vstdate) ", [$start_date, $end_date]);

        $total_10988 = DB::select("
            SELECT MIN(CASE
            WHEN MONTH(vstdate)=10 THEN CONCAT('ต.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=11 THEN CONCAT('พ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=12 THEN CONCAT('ธ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=1  THEN CONCAT('ม.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=2  THEN CONCAT('ก.พ. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=3  THEN CONCAT('มี.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=4  THEN CONCAT('เม.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=5  THEN CONCAT('พ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=6  THEN CONCAT('มิ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=7  THEN CONCAT('ก.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=8  THEN CONCAT('ส.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=9  THEN CONCAT('ก.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            END) AS month, 
            SUM(hn_total)            AS hn_total,
            SUM(visit_total)         AS visit_total,
            SUM(visit_total_op)      AS visit_total_op,
            SUM(visit_total_pp)      AS visit_total_pp,
            SUM(visit_ucs_incup)     AS visit_ucs_incup,
            SUM(visit_ucs_inprov)    AS visit_ucs_inprov,
            SUM(visit_ucs_outprov)   AS visit_ucs_outprov,
            SUM(visit_ofc)           AS visit_ofc,
            SUM(visit_bkk)           AS visit_bkk,
            SUM(visit_bmt)           AS visit_bmt,
            SUM(visit_sss)           AS visit_sss,
            SUM(visit_lgo)           AS visit_lgo,
            SUM(visit_fss)           AS visit_fss,
            SUM(visit_stp)           AS visit_stp,
            SUM(visit_pay)           AS visit_pay,
            SUM(visit_ppfs)          AS visit_ppfs,
            SUM(visit_ucs_cr)        AS visit_ucs_cr,
            SUM(visit_ucs_herb)      AS visit_ucs_herb,
            SUM(visit_ucs_healthmed) AS visit_ucs_healthmed,
            SUM(inc_total)            AS inc_total,
            SUM(inc_lab_total)        AS inc_lab_total,
            SUM(inc_drug_total)       AS inc_drug_total,
            SUM(inc_ucs_incup)        AS inc_ucs_incup,
            SUM(inc_lab_ucs_incup)    AS inc_lab_ucs_incup,
            SUM(inc_drug_ucs_incup)   AS inc_drug_ucs_incup,
            SUM(inc_ucs_inprov)       AS inc_ucs_inprov,
            SUM(inc_lab_ucs_inprov)   AS inc_lab_ucs_inprov,
            SUM(inc_drug_ucs_inprov)  AS inc_drug_ucs_inprov,
            SUM(inc_ucs_outprov)      AS inc_ucs_outprov,
            SUM(inc_lab_ucs_outprov)  AS inc_lab_ucs_outprov,
            SUM(inc_drug_ucs_outprov) AS inc_drug_ucs_outprov,
            SUM(inc_ofc)              AS inc_ofc,
            SUM(inc_lab_ofc)          AS inc_lab_ofc,
            SUM(inc_drug_ofc)         AS inc_drug_ofc,
            SUM(inc_bkk)              AS inc_bkk,
            SUM(inc_lab_bkk)          AS inc_lab_bkk,
            SUM(inc_drug_bkk)         AS inc_drug_bkk,
            SUM(inc_bmt)              AS inc_bmt,
            SUM(inc_lab_bmt)          AS inc_lab_bmt,
            SUM(inc_drug_bmt)         AS inc_drug_bmt,
            SUM(inc_sss)              AS inc_sss,
            SUM(inc_lab_sss)          AS inc_lab_sss,
            SUM(inc_drug_sss)         AS inc_drug_sss,
            SUM(inc_lgo)              AS inc_lgo,
            SUM(inc_lab_lgo)          AS inc_lab_lgo,
            SUM(inc_drug_lgo)         AS inc_drug_lgo,
            SUM(inc_fss)              AS inc_fss,
            SUM(inc_lab_fss)          AS inc_lab_fss,
            SUM(inc_drug_fss)         AS inc_drug_fss,
            SUM(inc_stp)              AS inc_stp,
            SUM(inc_lab_stp)          AS inc_lab_stp,
            SUM(inc_drug_stp)         AS inc_drug_stp,
            SUM(inc_pay)              AS inc_pay,
            SUM(inc_lab_pay)          AS inc_lab_pay,
            SUM(inc_drug_pay)         AS inc_drug_pay,
            SUM(inc_ppfs)             AS inc_ppfs,
            SUM(inc_uccr)             AS inc_uccr,
            SUM(inc_herb)             AS inc_herb
            FROM opd
            WHERE vstdate BETWEEN ? AND ?
            AND hospcode = 10988
            GROUP BY YEAR(vstdate), MONTH(vstdate)
            ORDER BY YEAR(vstdate), MONTH(vstdate) ", [$start_date, $end_date]);

        $total_10989 = DB::select("
            SELECT MIN(CASE
            WHEN MONTH(vstdate)=10 THEN CONCAT('ต.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=11 THEN CONCAT('พ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=12 THEN CONCAT('ธ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=1  THEN CONCAT('ม.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=2  THEN CONCAT('ก.พ. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=3  THEN CONCAT('มี.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=4  THEN CONCAT('เม.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=5  THEN CONCAT('พ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=6  THEN CONCAT('มิ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=7  THEN CONCAT('ก.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=8  THEN CONCAT('ส.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=9  THEN CONCAT('ก.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            END) AS month, 
            SUM(hn_total)            AS hn_total,
            SUM(visit_total)         AS visit_total,
            SUM(visit_total_op)      AS visit_total_op,
            SUM(visit_total_pp)      AS visit_total_pp,
            SUM(visit_ucs_incup)     AS visit_ucs_incup,
            SUM(visit_ucs_inprov)    AS visit_ucs_inprov,
            SUM(visit_ucs_outprov)   AS visit_ucs_outprov,
            SUM(visit_ofc)           AS visit_ofc,
            SUM(visit_bkk)           AS visit_bkk,
            SUM(visit_bmt)           AS visit_bmt,
            SUM(visit_sss)           AS visit_sss,
            SUM(visit_lgo)           AS visit_lgo,
            SUM(visit_fss)           AS visit_fss,
            SUM(visit_stp)           AS visit_stp,
            SUM(visit_pay)           AS visit_pay,
            SUM(visit_ppfs)          AS visit_ppfs,
            SUM(visit_ucs_cr)        AS visit_ucs_cr,
            SUM(visit_ucs_herb)      AS visit_ucs_herb,
            SUM(visit_ucs_healthmed) AS visit_ucs_healthmed,
            SUM(inc_total)            AS inc_total,
            SUM(inc_lab_total)        AS inc_lab_total,
            SUM(inc_drug_total)       AS inc_drug_total,
            SUM(inc_ucs_incup)        AS inc_ucs_incup,
            SUM(inc_lab_ucs_incup)    AS inc_lab_ucs_incup,
            SUM(inc_drug_ucs_incup)   AS inc_drug_ucs_incup,
            SUM(inc_ucs_inprov)       AS inc_ucs_inprov,
            SUM(inc_lab_ucs_inprov)   AS inc_lab_ucs_inprov,
            SUM(inc_drug_ucs_inprov)  AS inc_drug_ucs_inprov,
            SUM(inc_ucs_outprov)      AS inc_ucs_outprov,
            SUM(inc_lab_ucs_outprov)  AS inc_lab_ucs_outprov,
            SUM(inc_drug_ucs_outprov) AS inc_drug_ucs_outprov,
            SUM(inc_ofc)              AS inc_ofc,
            SUM(inc_lab_ofc)          AS inc_lab_ofc,
            SUM(inc_drug_ofc)         AS inc_drug_ofc,
            SUM(inc_bkk)              AS inc_bkk,
            SUM(inc_lab_bkk)          AS inc_lab_bkk,
            SUM(inc_drug_bkk)         AS inc_drug_bkk,
            SUM(inc_bmt)              AS inc_bmt,
            SUM(inc_lab_bmt)          AS inc_lab_bmt,
            SUM(inc_drug_bmt)         AS inc_drug_bmt,
            SUM(inc_sss)              AS inc_sss,
            SUM(inc_lab_sss)          AS inc_lab_sss,
            SUM(inc_drug_sss)         AS inc_drug_sss,
            SUM(inc_lgo)              AS inc_lgo,
            SUM(inc_lab_lgo)          AS inc_lab_lgo,
            SUM(inc_drug_lgo)         AS inc_drug_lgo,
            SUM(inc_fss)              AS inc_fss,
            SUM(inc_lab_fss)          AS inc_lab_fss,
            SUM(inc_drug_fss)         AS inc_drug_fss,
            SUM(inc_stp)              AS inc_stp,
            SUM(inc_lab_stp)          AS inc_lab_stp,
            SUM(inc_drug_stp)         AS inc_drug_stp,
            SUM(inc_pay)              AS inc_pay,
            SUM(inc_lab_pay)          AS inc_lab_pay,
            SUM(inc_drug_pay)         AS inc_drug_pay,
            SUM(inc_ppfs)             AS inc_ppfs,
            SUM(inc_uccr)             AS inc_uccr,
            SUM(inc_herb)             AS inc_herb
            FROM opd
            WHERE vstdate BETWEEN ? AND ?
            AND hospcode = 10989
            GROUP BY YEAR(vstdate), MONTH(vstdate)
            ORDER BY YEAR(vstdate), MONTH(vstdate) ", [$start_date, $end_date]);

        $total_10990 = DB::select("
            SELECT MIN(CASE
            WHEN MONTH(vstdate)=10 THEN CONCAT('ต.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=11 THEN CONCAT('พ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=12 THEN CONCAT('ธ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=1  THEN CONCAT('ม.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=2  THEN CONCAT('ก.พ. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=3  THEN CONCAT('มี.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=4  THEN CONCAT('เม.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=5  THEN CONCAT('พ.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=6  THEN CONCAT('มิ.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=7  THEN CONCAT('ก.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=8  THEN CONCAT('ส.ค. ', RIGHT(YEAR(vstdate)+543, 2))
            WHEN MONTH(vstdate)=9  THEN CONCAT('ก.ย. ', RIGHT(YEAR(vstdate)+543, 2))
            END) AS month, 
            SUM(hn_total)            AS hn_total,
            SUM(visit_total)         AS visit_total,
            SUM(visit_total_op)      AS visit_total_op,
            SUM(visit_total_pp)      AS visit_total_pp,
            SUM(visit_ucs_incup)     AS visit_ucs_incup,
            SUM(visit_ucs_inprov)    AS visit_ucs_inprov,
            SUM(visit_ucs_outprov)   AS visit_ucs_outprov,
            SUM(visit_ofc)           AS visit_ofc,
            SUM(visit_bkk)           AS visit_bkk,
            SUM(visit_bmt)           AS visit_bmt,
            SUM(visit_sss)           AS visit_sss,
            SUM(visit_lgo)           AS visit_lgo,
            SUM(visit_fss)           AS visit_fss,
            SUM(visit_stp)           AS visit_stp,
            SUM(visit_pay)           AS visit_pay,
            SUM(visit_ppfs)          AS visit_ppfs,
            SUM(visit_ucs_cr)        AS visit_ucs_cr,
            SUM(visit_ucs_herb)      AS visit_ucs_herb,
            SUM(visit_ucs_healthmed) AS visit_ucs_healthmed,
            SUM(inc_total)            AS inc_total,
            SUM(inc_lab_total)        AS inc_lab_total,
            SUM(inc_drug_total)       AS inc_drug_total,
            SUM(inc_ucs_incup)        AS inc_ucs_incup,
            SUM(inc_lab_ucs_incup)    AS inc_lab_ucs_incup,
            SUM(inc_drug_ucs_incup)   AS inc_drug_ucs_incup,
            SUM(inc_ucs_inprov)       AS inc_ucs_inprov,
            SUM(inc_lab_ucs_inprov)   AS inc_lab_ucs_inprov,
            SUM(inc_drug_ucs_inprov)  AS inc_drug_ucs_inprov,
            SUM(inc_ucs_outprov)      AS inc_ucs_outprov,
            SUM(inc_lab_ucs_outprov)  AS inc_lab_ucs_outprov,
            SUM(inc_drug_ucs_outprov) AS inc_drug_ucs_outprov,
            SUM(inc_ofc)              AS inc_ofc,
            SUM(inc_lab_ofc)          AS inc_lab_ofc,
            SUM(inc_drug_ofc)         AS inc_drug_ofc,
            SUM(inc_bkk)              AS inc_bkk,
            SUM(inc_lab_bkk)          AS inc_lab_bkk,
            SUM(inc_drug_bkk)         AS inc_drug_bkk,
            SUM(inc_bmt)              AS inc_bmt,
            SUM(inc_lab_bmt)          AS inc_lab_bmt,
            SUM(inc_drug_bmt)         AS inc_drug_bmt,
            SUM(inc_sss)              AS inc_sss,
            SUM(inc_lab_sss)          AS inc_lab_sss,
            SUM(inc_drug_sss)         AS inc_drug_sss,
            SUM(inc_lgo)              AS inc_lgo,
            SUM(inc_lab_lgo)          AS inc_lab_lgo,
            SUM(inc_drug_lgo)         AS inc_drug_lgo,
            SUM(inc_fss)              AS inc_fss,
            SUM(inc_lab_fss)          AS inc_lab_fss,
            SUM(inc_drug_fss)         AS inc_drug_fss,
            SUM(inc_stp)              AS inc_stp,
            SUM(inc_lab_stp)          AS inc_lab_stp,
            SUM(inc_drug_stp)         AS inc_drug_stp,
            SUM(inc_pay)              AS inc_pay,
            SUM(inc_lab_pay)          AS inc_lab_pay,
            SUM(inc_drug_pay)         AS inc_drug_pay,
            SUM(inc_ppfs)             AS inc_ppfs,
            SUM(inc_uccr)             AS inc_uccr,
            SUM(inc_herb)             AS inc_herb
            FROM opd
            WHERE vstdate BETWEEN ? AND ?
            AND hospcode = 10990
            GROUP BY YEAR(vstdate), MONTH(vstdate)
            ORDER BY YEAR(vstdate), MONTH(vstdate) ", [$start_date, $end_date]);

// IPD------------------------------------------------------------------------------------------------------------------

        $total_10985_ipd = DB::select("
            SELECT  CASE WHEN MONTH(i.dchdate)=10 THEN CONCAT('ต.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=11 THEN CONCAT('พ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=12 THEN CONCAT('ธ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=1 THEN CONCAT('ม.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=2 THEN CONCAT('ก.พ. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=3 THEN CONCAT('มี.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=4 THEN CONCAT('เม.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=5 THEN CONCAT('พ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=6 THEN CONCAT('มิ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=7 THEN CONCAT('ก.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=8 THEN CONCAT('ส.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=9 THEN CONCAT('ก.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            END AS 'month',
            SUM(i.an_total) AS an_total ,SUM(i.admdate) AS admdate,
            ROUND((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate))),2) AS 'bed_occupancy',
            ROUND(((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate)))*h.bed_report)/100,2) AS 'active_bed',
            ROUND(SUM(i.adjrw),4) AS adjrw ,
            ROUND(SUM(i.adjrw)/SUM(i.an_total),2) AS cmi,i.inc_total,i.inc_lab_total,i.inc_drug_total
            FROM ipd i
            LEFT JOIN hospital_config h ON h.hospcode=i.hospcode 
            WHERE i.dchdate BETWEEN ? AND ?
            AND i.hospcode = 10985
            GROUP BY MONTH(i.dchdate)
            ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)", [$start_date, $end_date]);

        $total_10986_ipd = DB::select("
            SELECT  CASE WHEN MONTH(i.dchdate)=10 THEN CONCAT('ต.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=11 THEN CONCAT('พ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=12 THEN CONCAT('ธ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=1 THEN CONCAT('ม.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=2 THEN CONCAT('ก.พ. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=3 THEN CONCAT('มี.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=4 THEN CONCAT('เม.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=5 THEN CONCAT('พ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=6 THEN CONCAT('มิ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=7 THEN CONCAT('ก.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=8 THEN CONCAT('ส.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=9 THEN CONCAT('ก.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            END AS 'month',
            SUM(i.an_total) AS an_total ,SUM(i.admdate) AS admdate,
            ROUND((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate))),2) AS 'bed_occupancy',
            ROUND(((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate)))*h.bed_report)/100,2) AS 'active_bed',
            ROUND(SUM(i.adjrw),4) AS adjrw ,
            ROUND(SUM(i.adjrw)/SUM(i.an_total),2) AS cmi,i.inc_total,i.inc_lab_total,i.inc_drug_total
            FROM ipd i
            LEFT JOIN hospital_config h ON h.hospcode=i.hospcode 
            WHERE i.dchdate BETWEEN ? AND ?
            AND i.hospcode = 10986
            GROUP BY MONTH(i.dchdate)
            ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)", [$start_date, $end_date]);
        
        $total_10987_ipd = DB::select("
            SELECT  CASE WHEN MONTH(i.dchdate)=10 THEN CONCAT('ต.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=11 THEN CONCAT('พ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=12 THEN CONCAT('ธ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=1 THEN CONCAT('ม.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=2 THEN CONCAT('ก.พ. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=3 THEN CONCAT('มี.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=4 THEN CONCAT('เม.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=5 THEN CONCAT('พ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=6 THEN CONCAT('มิ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=7 THEN CONCAT('ก.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=8 THEN CONCAT('ส.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=9 THEN CONCAT('ก.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            END AS 'month',
            SUM(i.an_total) AS an_total ,SUM(i.admdate) AS admdate,
            ROUND((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate))),2) AS 'bed_occupancy',
            ROUND(((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate)))*h.bed_report)/100,2) AS 'active_bed',
            ROUND(SUM(i.adjrw),4) AS adjrw ,
            ROUND(SUM(i.adjrw)/SUM(i.an_total),2) AS cmi,i.inc_total,i.inc_lab_total,i.inc_drug_total
            FROM ipd i
            LEFT JOIN hospital_config h ON h.hospcode=i.hospcode 
            WHERE i.dchdate BETWEEN ? AND ?
            AND i.hospcode = 10987
            GROUP BY MONTH(i.dchdate)
            ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)", [$start_date, $end_date]);

        $total_10988_ipd = DB::select("
            SELECT  CASE WHEN MONTH(i.dchdate)=10 THEN CONCAT('ต.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=11 THEN CONCAT('พ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=12 THEN CONCAT('ธ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=1 THEN CONCAT('ม.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=2 THEN CONCAT('ก.พ. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=3 THEN CONCAT('มี.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=4 THEN CONCAT('เม.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=5 THEN CONCAT('พ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=6 THEN CONCAT('มิ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=7 THEN CONCAT('ก.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=8 THEN CONCAT('ส.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=9 THEN CONCAT('ก.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            END AS 'month',
            SUM(i.an_total) AS an_total ,SUM(i.admdate) AS admdate,
            ROUND((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate))),2) AS 'bed_occupancy',
            ROUND(((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate)))*h.bed_report)/100,2) AS 'active_bed',
            ROUND(SUM(i.adjrw),4) AS adjrw ,
            ROUND(SUM(i.adjrw)/SUM(i.an_total),2) AS cmi,i.inc_total,i.inc_lab_total,i.inc_drug_total
            FROM ipd i
            LEFT JOIN hospital_config h ON h.hospcode=i.hospcode 
            WHERE i.dchdate BETWEEN ? AND ?
            AND i.hospcode = 10988
            GROUP BY MONTH(i.dchdate)
            ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)", [$start_date, $end_date]);

        $total_10989_ipd = DB::select("
            SELECT  CASE WHEN MONTH(i.dchdate)=10 THEN CONCAT('ต.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=11 THEN CONCAT('พ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=12 THEN CONCAT('ธ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=1 THEN CONCAT('ม.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=2 THEN CONCAT('ก.พ. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=3 THEN CONCAT('มี.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=4 THEN CONCAT('เม.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=5 THEN CONCAT('พ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=6 THEN CONCAT('มิ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=7 THEN CONCAT('ก.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=8 THEN CONCAT('ส.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=9 THEN CONCAT('ก.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            END AS 'month',
            SUM(i.an_total) AS an_total ,SUM(i.admdate) AS admdate,
            ROUND((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate))),2) AS 'bed_occupancy',
            ROUND(((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate)))*h.bed_report)/100,2) AS 'active_bed',
            ROUND(SUM(i.adjrw),4) AS adjrw ,
            ROUND(SUM(i.adjrw)/SUM(i.an_total),2) AS cmi,i.inc_total,i.inc_lab_total,i.inc_drug_total
            FROM ipd i
            LEFT JOIN hospital_config h ON h.hospcode=i.hospcode 
            WHERE i.dchdate BETWEEN ? AND ?
            AND i.hospcode = 10989
            GROUP BY MONTH(i.dchdate)
            ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)", [$start_date, $end_date]);

        $total_10990_ipd = DB::select("
            SELECT  CASE WHEN MONTH(i.dchdate)=10 THEN CONCAT('ต.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=11 THEN CONCAT('พ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=12 THEN CONCAT('ธ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=1 THEN CONCAT('ม.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=2 THEN CONCAT('ก.พ. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=3 THEN CONCAT('มี.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=4 THEN CONCAT('เม.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=5 THEN CONCAT('พ.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=6 THEN CONCAT('มิ.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=7 THEN CONCAT('ก.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=8 THEN CONCAT('ส.ค. ',RIGHT(YEAR(i.dchdate)+543,2))
            WHEN MONTH(i.dchdate)=9 THEN CONCAT('ก.ย. ',RIGHT(YEAR(i.dchdate)+543,2))
            END AS 'month',
            SUM(i.an_total) AS an_total ,SUM(i.admdate) AS admdate,
            ROUND((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate))),2) AS 'bed_occupancy',
            ROUND(((SUM(i.admdate)*100)/(h.bed_report*DAY(LAST_DAY(i.dchdate)))*h.bed_report)/100,2) AS 'active_bed',
            ROUND(SUM(i.adjrw),4) AS adjrw ,
            ROUND(SUM(i.adjrw)/SUM(i.an_total),2) AS cmi,i.inc_total,i.inc_lab_total,i.inc_drug_total
            FROM ipd i
            LEFT JOIN hospital_config h ON h.hospcode=i.hospcode 
            WHERE i.dchdate BETWEEN ? AND ?
            AND i.hospcode = 10990
            GROUP BY MONTH(i.dchdate)
            ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)", [$start_date, $end_date]);

        return view('dashboard', array_merge($card,compact('budget_year_select','budget_year','update_at10985','total_10985',
            'update_at10986','total_10986','update_at10987','total_10987','update_at10988','total_10988','update_at10989','total_10989',
            'update_at10990','total_10990','total_bed_qty','total_bed_empty','hospitals','hospitalSummary','total_10985_ipd',
            'total_10986_ipd','total_10987_ipd','total_10988_ipd','total_10989_ipd','total_10990_ipd')));
    }
}
