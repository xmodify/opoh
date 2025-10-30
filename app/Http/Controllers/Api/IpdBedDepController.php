<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IpdBedDepController extends Controller
{
    public function ingest(Request $request)
    {
        $hospital = Auth::user();
        if (!$hospital || !$hospital->tokenCan('ingest')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'records' => ['required', 'array', 'min:1'],
            'records.*.bed_code'   => ['required', 'string', 'max:6'],
            'records.*.bed_qty'    => ['required', 'integer', 'min:0'],
            'records.*.bed_use'    => ['required', 'integer', 'min:0'],
        ]);

        $hospcode = $hospital->hospcode;
        $rows = $validated['records'];
        $now = now();

        // ---- กันข้อมูลซ้ำ bed_code ใน payload ----
        $byBed = [];
        foreach ($rows as $r) {
            $byBed[$r['bed_code']] = $r;
        }

        // ---- เตรียมข้อมูล upsert ----
        $toUpsert = [];
        foreach ($byBed as $bed_code => $r) {
            $toUpsert[] = [
                'hospcode'   => $hospcode,
                'bed_code'   => $bed_code,
                'bed_qty'    => $r['bed_qty'],
                'bed_use'    => $r['bed_use'],
                'updated_at' => $now,
            ];
        }

        // ---- ทำ upsert ----
        try {
            DB::table('ipd_bed_dep')->upsert(
                $toUpsert,
                ['hospcode', 'bed_code'],
                ['bed_qty', 'bed_use', 'updated_at']
            );

            return response()->json([
                'hospcode' => $hospcode,
                'count'    => count($toUpsert),
                'message'  => 'Success',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'hospcode' => $hospcode,
                'message'  => 'Database Error',
                'error'    => $e->getMessage(),
            ], 500);
        }
    }
//############################################################################################################################
    public function get(Request $request)
    {
        $hospital = Auth::user();
        if (!$hospital || !$hospital->tokenCan('ingest')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $hospcode = $hospital->hospcode;
        $superHospcodes = ['00025']; // ผู้ดูแลพิเศษ

        $query = DB::table('ipd_bed_dep');
        if (!in_array($hospcode, $superHospcodes)) {
            $query->where('hospcode', $hospcode);
        }

        $data = $query->orderBy('bed_code')->get();

        return response()->json([
            'ok' => true,
            'hospcode' => $hospcode,
            'super' => in_array($hospcode, $superHospcodes),
            'count' => $data->count(),
            'data' => $data,
        ]);
    }
}
