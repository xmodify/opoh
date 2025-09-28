<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OpInsurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OpInsuranceController extends Controller
{
    // POST /ingest  (ต้องส่ง Authorization: Bearer <token>)
    public function ingest(Request $request)
    {
        $hospital = Auth::user(); // tokenable เป็น Hospital
        if (!$hospital || !$request->user()->tokenCan('ingest')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'records' => ['required', 'array', 'min:1'],
            'records.*.vstdate' => ['required', 'date'],
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

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($validated['records'] as $i => $row) {
            try {
                $data = array_merge($row, ['hospcode' => $hospital->hospcode]);

                // upsert ตาม unique(hospcode, vstdate)
                $model = OpInsurance::updateOrCreate(
                    ['hospcode' => $hospital->hospcode, 'vstdate' => $row['vstdate']],
                    $data
                );

                if ($model->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Throwable $e) {
                $errors[] = [
                    'index' => $i,
                    'vstdate' => $row['vstdate'] ?? null,
                    'message' => $e->getMessage()
                ];
            }
        }

        $status = empty($errors) ? 200 : 207; // 207 Multi-Status ถ้ามีบางเรคอร์ดพลาด
        return response()->json([
            'hospcode' => $hospital->hospcode,
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ], $status);
    }
}
