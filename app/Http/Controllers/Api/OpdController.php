<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpdController extends Controller
{
    public function opd(Request $request)
    {
        // Auth: อนุญาตเฉพาะ user ที่เป็นโรงพยาบาลและมี ability: ingest
        $hospital = Auth::user();
        if (!$hospital || !$hospital->tokenCan('ingest')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate เฉพาะฟิลด์ที่ต้องใช้ เพื่อกัน nested array ทำให้เกิด error แปลกๆ
        $validated = $request->validate([
            'records' => ['required', 'array', 'min:1'],
            'records.*.vstdate' => ['required', 'date_format:Y-m-d'],

            // จำนวน visit (int)
            'records.*.hn_total'            => ['required', 'integer', 'min:0'],
            'records.*.visit_total'         => ['required', 'integer', 'min:0'],
            'records.*.visit_total_op'      => ['required', 'integer', 'min:0'],
            'records.*.visit_total_pp'      => ['required', 'integer', 'min:0'],
            'records.*.visit_endpoint'      => ['required', 'integer', 'min:0'],            
            'records.*.visit_ucs_incup'     => ['required', 'integer', 'min:0'],
            'records.*.visit_ucs_inprov'    => ['required', 'integer', 'min:0'],
            'records.*.visit_ucs_outprov'   => ['required', 'integer', 'min:0'],
            'records.*.visit_ofc'           => ['required', 'integer', 'min:0'],
            'records.*.visit_bkk'           => ['required', 'integer', 'min:0'],
            'records.*.visit_bmt'           => ['required', 'integer', 'min:0'],
            'records.*.visit_sss'           => ['required', 'integer', 'min:0'],
            'records.*.visit_lgo'           => ['required', 'integer', 'min:0'],
            'records.*.visit_fss'           => ['required', 'integer', 'min:0'],
            'records.*.visit_stp'           => ['required', 'integer', 'min:0'],
            'records.*.visit_pay'           => ['required', 'integer', 'min:0'],
            'records.*.visit_ppfs'          => ['required', 'integer', 'min:0'],
            'records.*.visit_ucs_cr'        => ['required', 'integer', 'min:0'],
            'records.*.visit_ucs_herb'      => ['required', 'integer', 'min:0'],
            'records.*.visit_ucs_healthmed' => ['required', 'integer', 'min:0'],
            'records.*.visit_healthmed'     => ['required', 'integer', 'min:0'],
            'records.*.visit_dent'          => ['required', 'integer', 'min:0'],
            'records.*.visit_physic'        => ['required', 'integer', 'min:0'],
            'records.*.visit_referout_inprov'       => ['required', 'integer', 'min:0'],
            'records.*.visit_referout_outprov'      => ['required', 'integer', 'min:0'],

            // รายได้ (float/double)
            'records.*.inc_total'            => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_total'        => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_total'       => ['required', 'numeric', 'min:0'],
            'records.*.inc_ucs_incup'        => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_ucs_incup'    => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_ucs_incup'   => ['required', 'numeric', 'min:0'],
            'records.*.inc_ucs_inprov'       => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_ucs_inprov'   => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_ucs_inprov'  => ['required', 'numeric', 'min:0'],
            'records.*.inc_ucs_outprov'      => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_ucs_outprov'  => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_ucs_outprov' => ['required', 'numeric', 'min:0'],
            'records.*.inc_ofc'              => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_ofc'          => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_ofc'         => ['required', 'numeric', 'min:0'],
            'records.*.inc_bkk'              => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_bkk'          => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_bkk'         => ['required', 'numeric', 'min:0'],
            'records.*.inc_bmt'              => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_bmt'          => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_bmt'         => ['required', 'numeric', 'min:0'],
            'records.*.inc_sss'              => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_sss'          => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_sss'         => ['required', 'numeric', 'min:0'],
            'records.*.inc_lgo'              => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_lgo'          => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_lgo'         => ['required', 'numeric', 'min:0'],
            'records.*.inc_fss'              => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_fss'          => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_fss'         => ['required', 'numeric', 'min:0'],
            'records.*.inc_stp'              => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_stp'          => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_stp'         => ['required', 'numeric', 'min:0'],
            'records.*.inc_pay'              => ['required', 'numeric', 'min:0'],
            'records.*.inc_lab_pay'          => ['required', 'numeric', 'min:0'],
            'records.*.inc_drug_pay'         => ['required', 'numeric', 'min:0'],
            'records.*.inc_ppfs'             => ['required', 'numeric', 'min:0'],
            'records.*.inc_uccr'             => ['required', 'numeric', 'min:0'],
            'records.*.inc_herb'             => ['required', 'numeric', 'min:0'],
        ]);

        $hospcode = $hospital->hospcode;
        $rows = $validated['records'];

        // ---- เตรียมวันที่ทั้งหมดจาก payload ----
        $dates = collect($rows)->pluck('vstdate')->unique()->values();

        // ---- เช็ควันที่ที่มีอยู่แล้วใน DB (ของ hospcode นี้) ----
        $existing = Opd::query()
            ->where('hospcode', $hospcode)
            ->whereIn('vstdate', $dates)
            ->pluck('vstdate')
            ->all();

        // สร้าง set แบบปลอดภัย (กัน array_flip error หากมีค่า non-scalar)
        $existingSet = [];
        foreach ($existing as $d) {
            if (is_string($d) || is_int($d)) {
                $existingSet[(string)$d] = true;
            }
        }

        // ---- กัน payload ซ้ำวันที่เดียวกัน: อันหลังทับอันแรก ----
        $byDate = [];
        foreach ($rows as $r) {
            $byDate[$r['vstdate']] = $r;
        }

        // ---- แปลงเป็น rows สำหรับ upsert ----
        $now = now();
        $toUpsert = [];
        foreach ($byDate as $vstdate => $row) {
            $toUpsert[] = [
                'hospcode' => $hospcode,
                'vstdate'  => $vstdate,

                // Visits (int)
                'hn_total'            => $row['hn_total'],
                'visit_total'         => $row['visit_total'],
                'visit_total_op'      => $row['visit_total_op'],
                'visit_total_pp'      => $row['visit_total_pp'],
                'visit_endpoint'      => $row['visit_endpoint'],                
                'visit_total_pp'      => $row['visit_total_pp'],
                'visit_ucs_incup'     => $row['visit_ucs_incup'],
                'visit_ucs_inprov'    => $row['visit_ucs_inprov'],
                'visit_ucs_outprov'   => $row['visit_ucs_outprov'],
                'visit_ofc'           => $row['visit_ofc'],
                'visit_bkk'           => $row['visit_bkk'],
                'visit_bmt'           => $row['visit_bmt'],
                'visit_sss'           => $row['visit_sss'],
                'visit_lgo'           => $row['visit_lgo'],
                'visit_fss'           => $row['visit_fss'],
                'visit_stp'           => $row['visit_stp'],
                'visit_pay'           => $row['visit_pay'],
                'visit_ppfs'          => $row['visit_ppfs'],
                'visit_ucs_cr'        => $row['visit_ucs_cr'],
                'visit_ucs_herb'      => $row['visit_ucs_herb'],
                'visit_ucs_healthmed' => $row['visit_ucs_healthmed'],
                'visit_healthmed'     => $row['visit_healthmed'],
                'visit_dent'          => $row['visit_dent'],
                'visit_physic'        => $row['visit_physic'],
                'visit_referout_inprov'      => $row['visit_referout_inprov'],
                'visit_referout_outprov'     => $row['visit_referout_outprov'],

                // Income (float/double)
                'inc_total'            => $row['inc_total'],
                'inc_lab_total'        => $row['inc_lab_total'],
                'inc_drug_total'       => $row['inc_drug_total'],
                'inc_ucs_incup'        => $row['inc_ucs_incup'],
                'inc_lab_ucs_incup'    => $row['inc_lab_ucs_incup'],
                'inc_drug_ucs_incup'   => $row['inc_drug_ucs_incup'],
                'inc_ucs_inprov'       => $row['inc_ucs_inprov'],
                'inc_lab_ucs_inprov'   => $row['inc_lab_ucs_inprov'],
                'inc_drug_ucs_inprov'  => $row['inc_drug_ucs_inprov'],
                'inc_ucs_outprov'      => $row['inc_ucs_outprov'],
                'inc_lab_ucs_outprov'  => $row['inc_lab_ucs_outprov'],
                'inc_drug_ucs_outprov' => $row['inc_drug_ucs_outprov'],
                'inc_ofc'              => $row['inc_ofc'],
                'inc_lab_ofc'          => $row['inc_lab_ofc'],
                'inc_drug_ofc'         => $row['inc_drug_ofc'],
                'inc_bkk'              => $row['inc_bkk'],
                'inc_lab_bkk'          => $row['inc_lab_bkk'],
                'inc_drug_bkk'         => $row['inc_drug_bkk'],
                'inc_bmt'              => $row['inc_bmt'],
                'inc_lab_bmt'          => $row['inc_lab_bmt'],
                'inc_drug_bmt'         => $row['inc_drug_bmt'],
                'inc_sss'              => $row['inc_sss'],
                'inc_lab_sss'          => $row['inc_lab_sss'],
                'inc_drug_sss'         => $row['inc_drug_sss'],
                'inc_lgo'              => $row['inc_lgo'],
                'inc_lab_lgo'          => $row['inc_lab_lgo'],
                'inc_drug_lgo'         => $row['inc_drug_lgo'],
                'inc_fss'              => $row['inc_fss'],
                'inc_lab_fss'          => $row['inc_lab_fss'],
                'inc_drug_fss'         => $row['inc_drug_fss'],
                'inc_stp'              => $row['inc_stp'],
                'inc_lab_stp'          => $row['inc_lab_stp'],
                'inc_drug_stp'         => $row['inc_drug_stp'],
                'inc_pay'              => $row['inc_pay'],
                'inc_lab_pay'          => $row['inc_lab_pay'],
                'inc_drug_pay'         => $row['inc_drug_pay'],
                'inc_ppfs'             => $row['inc_ppfs'],
                'inc_uccr'             => $row['inc_uccr'],
                'inc_herb'             => $row['inc_herb'],

                // timestamps
                'updated_at' => $now,
            ];
        }

        // ---- นับผลลัพธ์ created / updated ให้ถูกต้อง ----
        $payloadDates  = array_keys($byDate);
        $existingDates = array_keys($existingSet);
        $created = count(array_diff($payloadDates, $existingDates));
        $updated = count(array_intersect($payloadDates, $existingDates));

        // ---- ทำ upsert ----
        if (!empty($toUpsert)) {
            DB::beginTransaction();
            try {
                DB::table((new Opd())->getTable())->upsert(
                    $toUpsert,
                    ['hospcode', 'vstdate'], // unique keys
                    [
                        // Visits
                        'hn_total', 'visit_total', 'visit_total_op', 'visit_total_pp',
                        'visit_endpoint','visit_ucs_incup', 'visit_ucs_inprov', 'visit_ucs_outprov',
                        'visit_ofc', 'visit_bkk', 'visit_bmt', 'visit_sss','visit_lgo', 'visit_fss', 
                        'visit_stp', 'visit_pay','visit_ppfs', 'visit_ucs_cr', 'visit_ucs_herb', 
                        'visit_ucs_healthmed','visit_healthmed','visit_dent','visit_physic',
                        'visit_referout_inprov','visit_referout_outprov',
                        // Incomes
                        'inc_total', 'inc_lab_total', 'inc_drug_total',
                        'inc_ucs_incup', 'inc_lab_ucs_incup', 'inc_drug_ucs_incup',
                        'inc_ucs_inprov', 'inc_lab_ucs_inprov', 'inc_drug_ucs_inprov',
                        'inc_ucs_outprov', 'inc_lab_ucs_outprov', 'inc_drug_ucs_outprov',
                        'inc_ofc', 'inc_lab_ofc', 'inc_drug_ofc',
                        'inc_bkk', 'inc_lab_bkk', 'inc_drug_bkk',
                        'inc_bmt', 'inc_lab_bmt', 'inc_drug_bmt',
                        'inc_sss', 'inc_lab_sss', 'inc_drug_sss',
                        'inc_lgo', 'inc_lab_lgo', 'inc_drug_lgo',
                        'inc_fss', 'inc_lab_fss', 'inc_drug_fss',
                        'inc_stp', 'inc_lab_stp', 'inc_drug_stp',
                        'inc_pay', 'inc_lab_pay', 'inc_drug_pay',
                        'inc_ppfs', 'inc_uccr', 'inc_herb',

                        // Timestamp
                        'updated_at',
                    ]
                );

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                // หากล้มเหลวทั้งก้อน ให้ส่งรายละเอียด error กลับ
                return response()->json([
                    'hospcode' => $hospcode,
                    'created'  => 0,
                    'updated'  => 0,
                    'errors'   => [
                        ['message' => $e->getMessage()]
                    ],
                ], 500);
            }
        }

        // 200: สำเร็จทั้งหมด | 207: บางส่วน (ในที่นี้เรา validate ก่อนแล้ว ปกติจะ 200)
        return response()->json([
            'hospcode' => $hospcode,
            'created'  => $created,
            'updated'  => $updated,
            'errors'   => [],
        ], 200);
    }

    /**
     * (ทางเลือก) GET /api/op-insurance/health
     * ใช้เช็คว่า endpoint ใช้งานได้และ token ถูกต้องหรือไม่
     */
    public function health()
    {
        $hospital = Auth::user();
        if (!$hospital || !$hospital->tokenCan('ingest')) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }
        return response()->json(['ok' => true, 'hospcode' => $hospital->hospcode]);
    }
}
