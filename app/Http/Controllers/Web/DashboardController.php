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
           if ($today > $end_date) {
                $calc_end_date = $end_date; // ถ้าเลยปีงบแล้วใช้วันสิ้นสุดปีงบ
            } else {
                $calc_end_date = $today; // ถ้ายังอยู่ในปีงบ ใช้วันปัจจุบัน
            }
        //คำนวณจำนวนวันตั้งแต่ต้นปีงบ (1 ต.ค.) ถึงวันปัจจุบัน
        $diff_days = Carbon::parse($start_date)->diffInDays(Carbon::parse($calc_end_date)) + 1;

        $total = DB::table('opd')
            ->whereBetween('vstdate', [$today, $today])
            ->selectRaw("                
                COALESCE(SUM(visit_referout_inprov),0)          AS visit_referout_inprov,
                COALESCE(SUM(visit_referout_inprov_ipd),0)      AS visit_referout_inprov_ipd,
                COALESCE(SUM(visit_referout_outprov),0)         AS visit_referout_outprov,
                COALESCE(SUM(visit_referout_outprov_ipd),0)     AS visit_referout_outprov_ipd,
                COALESCE(SUM(visit_referin_inprov),0)           AS visit_referin_inprov,
                COALESCE(SUM(visit_referin_outprov),0)          AS visit_referin_outprov,
                COALESCE(SUM(visit_referback_inprov),0)         AS visit_referback_inprov,
                COALESCE(SUM(visit_referback_outprov),0)        AS visit_referback_outprov           
            ")->first();

        // ส่งเป็น array ใช้ง่าย ๆ ใน Blade
        $card = [           
            'visit_referout_inprov'       => (int)$total->visit_referout_inprov, 
            'visit_referout_inprov_ipd'   => (int)$total->visit_referout_inprov_ipd, 
            'visit_referout_outprov'      => (int)$total->visit_referout_outprov, 
            'visit_referout_outprov_ipd'  => (int)$total->visit_referout_outprov_ipd, 
            'visit_referin_inprov'        => (int)$total->visit_referin_inprov, 
            'visit_referin_outprov'       => (int)$total->visit_referin_outprov, 
            'visit_referback_inprov'      => (int)$total->visit_referback_inprov, 
            'visit_referback_outprov'     => (int)$total->visit_referback_outprov,             
        ];

        $hospitalSummary = DB::table('opd')
            ->join('hospital_config', 'opd.hospcode', '=', 'hospital_config.hospcode')
            ->whereBetween('vstdate', [$today, $today])
            ->select(
                'opd.hospcode',
                'hospital_config.hospname',
                DB::raw('MAX(opd.updated_at) AS last_updated_at'),                
                DB::raw('COALESCE(SUM(visit_referout_inprov),0) AS visit_referout_inprov'),
                DB::raw('COALESCE(SUM(visit_referout_inprov_ipd),0) AS visit_referout_inprov_ipd'),
                DB::raw('COALESCE(SUM(visit_referout_outprov),0) AS visit_referout_outprov'),
                DB::raw('COALESCE(SUM(visit_referout_outprov_ipd),0) AS visit_referout_outprov_ipd'),
                DB::raw('COALESCE(SUM(visit_referin_inprov),0) AS visit_referin_inprov'),
                DB::raw('COALESCE(SUM(visit_referin_outprov),0) AS visit_referin_outprov'),
                DB::raw('COALESCE(SUM(visit_referback_inprov),0) AS visit_referback_inprov'),
                DB::raw('COALESCE(SUM(visit_referback_outprov),0) AS visit_referback_outprov'),              
            )
            ->groupBy('opd.hospcode', 'hospital_config.hospname')
            ->orderBy('opd.hospcode')
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
        $update_at10703 = DB::table('opd')->where('hospcode', '10703')->max('updated_at');

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
            ROUND((SUM(i.admdate) * 100) / (h.bed_report * CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate))END), 2) AS bed_occupancy,
            ROUND((SUM(i.admdate) / CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate)) END), 2) AS active_bed,             
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
            ROUND((SUM(i.admdate) * 100) / (h.bed_report * CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate))END), 2) AS bed_occupancy,
            ROUND((SUM(i.admdate) / CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate)) END), 2) AS active_bed,     
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
            ROUND((SUM(i.admdate) * 100) / (h.bed_report * CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate))END), 2) AS bed_occupancy,
            ROUND((SUM(i.admdate) / CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate)) END), 2) AS active_bed,     
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
            ROUND((SUM(i.admdate) * 100) / (h.bed_report * CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate))END), 2) AS bed_occupancy,
            ROUND((SUM(i.admdate) / CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate)) END), 2) AS active_bed,     
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
            ROUND((SUM(i.admdate) * 100) / (h.bed_report * CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate))END), 2) AS bed_occupancy,
            ROUND((SUM(i.admdate) / CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate)) END), 2) AS active_bed,     
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
            ROUND((SUM(i.admdate) * 100) / (h.bed_report * CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate))END), 2) AS bed_occupancy,
            ROUND((SUM(i.admdate) / CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate)) END), 2) AS active_bed,     
            ROUND(SUM(i.adjrw),4) AS adjrw ,
            ROUND(SUM(i.adjrw)/SUM(i.an_total),2) AS cmi,i.inc_total,i.inc_lab_total,i.inc_drug_total
            FROM ipd i
            LEFT JOIN hospital_config h ON h.hospcode=i.hospcode 
            WHERE i.dchdate BETWEEN ? AND ?
            AND i.hospcode = 10990
            GROUP BY MONTH(i.dchdate)
            ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)", [$start_date, $end_date]);

        $total_10703_ipd = DB::select("
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
            ROUND((SUM(i.admdate) * 100) / (h.bed_report * CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate))END), 2) AS bed_occupancy,
            ROUND((SUM(i.admdate) / CASE WHEN YEAR(i.dchdate) = YEAR(CURDATE()) AND MONTH(i.dchdate) = MONTH(CURDATE()) 
                THEN DAY(CURDATE()) ELSE DAY(LAST_DAY(i.dchdate)) END), 2) AS active_bed,     
            ROUND(SUM(i.adjrw),4) AS adjrw ,
            ROUND(SUM(i.adjrw)/SUM(i.an_total),2) AS cmi,i.inc_total,i.inc_lab_total,i.inc_drug_total
            FROM ipd i
            LEFT JOIN hospital_config h ON h.hospcode=i.hospcode 
            WHERE i.dchdate BETWEEN ? AND ?
            AND i.hospcode = 10703
            GROUP BY MONTH(i.dchdate)
            ORDER BY YEAR(i.dchdate) , MONTH(i.dchdate)", [$start_date, $end_date]);

        return view('dashboard', array_merge($card,compact('budget_year_select','budget_year','diff_days','update_at10985',
            'update_at10986','update_at10987','update_at10988','update_at10989','update_at10990','update_at10703',
            'total_bed_qty','total_bed_use','total_bed_empty','hospitals','hospitalSummary','total_10985_ipd',
            'total_10986_ipd','total_10987_ipd','total_10988_ipd','total_10989_ipd','total_10990_ipd','total_10703_ipd')));
    }
//###############################################################################################################################
    public function bed_dep($hospcode)
    {
        $beds = DB::table('ipd_bed_dep as d')
            ->leftJoin('ipd_bed_type as t', 'd.bed_code', '=', 't.bed_code')
            ->where('d.hospcode', $hospcode)
            ->select(
                'd.bed_code',
                't.bed_name',
                't.unit',
                'd.bed_qty',
                'd.bed_use',
                DB::raw('
                    CASE 
                        WHEN d.bed_qty > 0 
                        THEN ROUND((d.bed_use / d.bed_qty) * 100, 2) 
                        ELSE 0 
                    END as bed_rate
                ')
            )
            ->orderBy('t.bed_name')
            ->get();

        // รวมผล
        $sum_bed_qty = $beds->sum('bed_qty');
        $sum_bed_use = $beds->sum('bed_use');
        $sum_bed_empty = $sum_bed_qty - $sum_bed_use;
        $sum_rate = $sum_bed_qty > 0 ? round(($sum_bed_use / $sum_bed_qty) * 100, 2) : 0;

        return response()->json([
            'beds' => $beds,
            'summary' => [
                'total' => $sum_bed_qty,
                'used'  => $sum_bed_use,
                'empty' => $sum_bed_empty,
                'rate'  => $sum_rate
            ]
        ]);
    }
    
}
