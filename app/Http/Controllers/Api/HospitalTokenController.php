<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HospitalTokenController extends Controller
{
    // GET /hospitals/{hospcode}/tokens
    public function index(string $hospcode)
    {
        $hospital = Hospital::where('hospcode', $hospcode)->first();

        if (!$hospital) {
            return response()->json(['message' => 'Hospital not found'], 404);
        }

        $tokens = $hospital->tokens()
            ->select('id', 'name', 'last_used_at', 'created_at', 'abilities')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'hospcode' => $hospital->hospcode,
            'tokens' => $tokens,
        ]);
    }

    // POST /hospitals/{hospcode}/tokens
    public function issue(Request $request, string $hospcode)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'abilities' => ['sometimes', 'array'],
            'abilities.*' => ['string'],
        ]);

        $hospital = Hospital::firstOrCreate(
            ['hospcode' => $hospcode],
            ['name' => null, 'is_active' => true]
        );

        if (!$hospital->is_active) {
            return response()->json(['message' => 'Hospital is inactive'], 403);
        }

        $abilities = $validated['abilities'] ?? ['ingest']; // จำกัดความสามารถโทเค็น
        $token = $hospital->createToken($validated['name'], $abilities);

        return response()->json([
            'hospcode' => $hospital->hospcode,
            'token_id' => $token->accessToken->id ?? null, // laravel 12: object มี id
            'plain_text_token' => $token->plainTextToken,  // แสดงครั้งเดียว!
            'abilities' => $abilities,
        ], 201);
    }

    // DELETE /hospitals/{hospcode}/tokens/{tokenId}
    public function revoke(string $hospcode, string $tokenId)
    {
        $hospital = Hospital::where('hospcode', $hospcode)->first();

        if (!$hospital) {
            return response()->json(['message' => 'Hospital not found'], 404);
        }

        $deleted = $hospital->tokens()->where('id', $tokenId)->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        return response()->json(['message' => 'Token revoked']);
    }
}
