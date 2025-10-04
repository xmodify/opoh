<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HospitalUpdateController  extends Controller
{
     /**
     * PUT /api/hospital/update
     * อัปเดต bed_qty และ bed_use ของโรงพยาบาล
     */
  public function update(Request $request)
    {
        // ✅ ตรวจสอบสิทธิ์
        $hospital = Auth::user();
        if (!$hospital || !$hospital->tokenCan('ingest')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // ✅ ตรวจสอบข้อมูลที่ส่งมา
        $validated = $request->validate([
            'bed_qty' => ['required', 'integer', 'min:0'],
            'bed_use' => ['required', 'integer', 'min:0'],
        ]);

        $hospcode = $hospital->hospcode;

        try {
            // ✅ อัปเดตหรือสร้างข้อมูลใน hospital_config
            $affected = DB::table('hospital_config') 
                ->updateOrInsert(
                    ['hospcode' => $hospcode],
                    [
                        'bed_qty' => $validated['bed_qty'],
                        'bed_use' => $validated['bed_use'],
                        'updated_at' => now(),
                    ]
                );

            return response()->json([
                'ok' => true,
                'hospcode' => $hospcode,
                'updated' => $affected,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
