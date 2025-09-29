<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OpInsurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpInsuranceController extends Controller
{
    /**
     * POST /api/op-insurance/ingest
     * Header: Authorization: Bearer <token>  (ต้องมี ability: ingest)
     *
     * Body ตัวอย่าง:
     * {
     *   "records": [
     *     {
     *       "vstdate": "2025-08-01",
     *       "total_visit": 524,
     *       "endpoint": 2,
     *       "ofc_visit": 69,
     *       "ofc_edc": 58,
     *       "non_authen": 0,
     *       "non_hmain": 0,
     *       "uc_anywhere": 8,
     *       "uc_anywhere_endpoint": 1,
     *       "uc_cr": 19,
     *       "uc_cr_endpoint": 0,
     *       "uc_herb": 25,
     *       "uc_herb_endpoint": 0,
     *       "ppfs": 5,
     *       "ppfs_endpoint": 0,
     *       "uc_healthmed": 1,
     *       "uc_healthmed_endpoint": 0
     *     }
     *   ]
     * }
     */
    public function ingest(Request $request)
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
            'records.*.total_visit' => ['required', 'integer', 'min:0'],
            'records.*.endpoint' => ['required', 'integer', 'min:0'],
            'records.*.ofc_visit' => ['required', 'integer', 'min:0'],
            'records.*.ofc_edc' => ['required', 'integer', 'min:0'],
            'records.*.non_authen' => ['required', 'integer', 'min:0'],
            'records.*.non_hmain' => ['required', 'integer', 'min:0'],
            'records.*.uc_anywhere' => ['required', 'integer', 'min:0'],
            'records.*.uc_anywhere_endpoint' => ['required', 'integer', 'min:0'],
            'records.*.uc_cr' => ['required', 'integer', 'min:0'],
            'records.*.uc_cr_endpoint' => ['required', 'integer', 'min:0'],
            'records.*.uc_herb' => ['required', 'integer', 'min:0'],
            'records.*.uc_herb_endpoint' => ['required', 'integer', 'min:0'],
            'records.*.ppfs' => ['required', 'integer', 'min:0'],
            'records.*.ppfs_endpoint' => ['required', 'integer', 'min:0'],
            'records.*.uc_healthmed' => ['required', 'integer', 'min:0'],
            'records.*.uc_healthmed_endpoint' => ['required', 'integer', 'min:0'],
        ]);

        $hospcode = $hospital->hospcode;
        $rows = $validated['records'];

        // ---- เตรียมวันที่ทั้งหมดจาก payload ----
        $dates = collect($rows)->pluck('vstdate')->unique()->values();

        // ---- เช็ควันที่ที่มีอยู่แล้วใน DB (ของ hospcode นี้) ----
        $existing = OpInsurance::query()
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
                'hospcode'              => $hospcode,
                'vstdate'               => $vstdate,

                'total_visit'           => $row['total_visit'],
                'endpoint'              => $row['endpoint'],
                'ofc_visit'             => $row['ofc_visit'],
                'ofc_edc'               => $row['ofc_edc'],
                'non_authen'            => $row['non_authen'],
                'non_hmain'             => $row['non_hmain'],
                'uc_anywhere'           => $row['uc_anywhere'],
                'uc_anywhere_endpoint'  => $row['uc_anywhere_endpoint'],
                'uc_cr'                 => $row['uc_cr'],
                'uc_cr_endpoint'        => $row['uc_cr_endpoint'],
                'uc_herb'               => $row['uc_herb'],
                'uc_herb_endpoint'      => $row['uc_herb_endpoint'],
                'ppfs'                  => $row['ppfs'],
                'ppfs_endpoint'         => $row['ppfs_endpoint'],
                'uc_healthmed'          => $row['uc_healthmed'],
                'uc_healthmed_endpoint' => $row['uc_healthmed_endpoint'],

                // timestamps (optional ถ้าตารางมีคอลัมน์)
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
                DB::table((new OpInsurance())->getTable())->upsert(
                    $toUpsert,
                    ['hospcode', 'vstdate'], // unique keys
                    [
                        'total_visit', 'endpoint', 'ofc_visit', 'ofc_edc', 'non_authen', 'non_hmain',
                        'uc_anywhere', 'uc_anywhere_endpoint', 'uc_cr', 'uc_cr_endpoint',
                        'uc_herb', 'uc_herb_endpoint', 'ppfs', 'ppfs_endpoint',
                        'uc_healthmed', 'uc_healthmed_endpoint', 'updated_at',
                    ] // columns to update
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
