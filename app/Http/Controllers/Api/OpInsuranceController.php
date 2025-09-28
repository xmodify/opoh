<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OpInsurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class OpInsuranceController extends Controller
{
    // POST /ingest  (ต้องส่ง Authorization: Bearer <token> ที่มี ability: ingest)
    public function ingest(Request $request)
    {
        $hospital = Auth::user(); // tokenable เป็น Hospital

        // ปิดช่องโหว่: เช็คที่ $hospital แทนการอ้าง $request->user() ซ้ำ
        if (!$hospital || !$hospital->tokenCan('ingest')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'records' => ['required', 'array', 'min:1'],
            'records.*.vstdate' => ['required','date_format:Y-m-d'],
            // 'records.*.vstdate' => ['required', 'date'], // ถ้าข้อมูลเป็น YYYY-MM-DD ตายตัว แนะนำใช้: date_format:Y-m-d
            'records.*.total_visit' => ['required','integer','min:0'],
            'records.*.endpoint' => ['required','integer','min:0'],
            'records.*.ofc_visit' => ['required','integer','min:0'],
            'records.*.ofc_edc' => ['required','integer','min:0'],
            'records.*.non_authen' => ['required','integer','min:0'],
            'records.*.non_hmain' => ['required','integer','min:0'],
            'records.*.uc_anywhere' => ['required','integer','min:0'],
            'records.*.uc_anywhere_endpoint' => ['required','integer','min:0'],
            'records.*.uc_cr' => ['required','integer','min:0'],
            'records.*.uc_cr_endpoint' => ['required','integer','min:0'],
            'records.*.uc_herb' => ['required','integer','min:0'],
            'records.*.uc_herb_endpoint' => ['required','integer','min:0'],
            'records.*.ppfs' => ['required','integer','min:0'],
            'records.*.ppfs_endpoint' => ['required','integer','min:0'],
            'records.*.uc_healthmed' => ['required','integer','min:0'],
            'records.*.uc_healthmed_endpoint' => ['required','integer','min:0'],
        ]);

        $hospcode = $hospital->hospcode;
        $rows     = $validated['records'];

        // เตรียม key (vstdate) ทั้งหมด
        $dates = collect($rows)->pluck('vstdate')->unique()->values();

        // ดึงว่ามีรายการไหนอยู่แล้วใน DB (เพื่อจะได้นับ created/updated)
        $existing = OpInsurance::query()
            ->where('hospcode', $hospcode)
            ->whereIn('vstdate', $dates)
            ->pluck('vstdate')
            ->all();

        $existingSet = array_flip($existing); // ใช้เช็คเร็ว ๆ

        $toUpsert = [];
        $created = 0;
        $updated = 0;
        $errors  = [];

        // แปลงข้อมูลสำหรับ upsert ทีเดียว
        foreach ($rows as $i => $row) {
            try {
                $record = [
                    'hospcode' => $hospcode,
                    'vstdate'  => $row['vstdate'],

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

                    // เผื่อใช้ timestamps
                    'updated_at' => now(),
                ];

                // ถ้าเป็นรายการใหม่ เพิ่ม created_at ด้วย (บาง DB จะตั้ง default เองก็ได้)
                if (!isset($existingSet[$row['vstdate']])) {
                    $record['created_at'] = now();
                    $created++;
                } else {
                    $updated++;
                }

                $toUpsert[] = $record;
            } catch (\Throwable $e) {
                $errors[] = [
                    'index'   => $i,
                    'vstdate' => $row['vstdate'] ?? null,
                    'message' => $e->getMessage(),
                ];
            }
        }

        // ถ้ามี error บางเรคอร์ด ก็ยัง upsert ส่วนที่พร้อมได้
        if (!empty($toUpsert)) {
            DB::beginTransaction();
            try {
                // ใช้ Query Builder เพื่อ upsert ทีเดียว
                // uniqueBy ตามคีย์ร่วม (hospcode, vstdate)
                // update columns: ระบุเฉพาะฟิลด์ข้อมูลสรุป (ตัดคีย์และ created_at ออก)
                $updateColumns = [
                    'total_visit','endpoint','ofc_visit','ofc_edc','non_authen','non_hmain',
                    'uc_anywhere','uc_anywhere_endpoint','uc_cr','uc_cr_endpoint','uc_herb','uc_herb_endpoint',
                    'ppfs','ppfs_endpoint','uc_healthmed','uc_healthmed_endpoint','updated_at'
                ];

                DB::table((new OpInsurance())->getTable())
                    ->upsert($toUpsert, ['hospcode','vstdate'], $updateColumns);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                // ถ้า upsert ล้มเหลวทั้งหมด ปรับ counter กลับ 0 แล้วบันทึกข้อผิดพลาดรวม
                $errors[] = ['index' => null, 'vstdate' => null, 'message' => $e->getMessage()];
                $created = 0;
                $updated = 0;
            }
        }

        $status = empty($errors) ? 200 : 207; // 207 = บางรายการพลาด
        return response()->json([
            'hospcode' => $hospcode,
            'created'  => $created,
            'updated'  => $updated,
            'errors'   => $errors,
        ], $status);
    }
}
