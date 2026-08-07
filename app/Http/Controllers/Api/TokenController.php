<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\SecurityLogEvent;
use App\Services\Security\SecurityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Personal access token management (Sanctum preparation).
 */
final class TokenController extends Controller
{
    public function __construct(
        private readonly SecurityLogService $securityLogService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->get(['id', 'name', 'abilities', 'last_used_at', 'created_at']);

        return response()->json(['tokens' => $tokens]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['nullable', 'array'],
        ]);

        $token = $request->user()->createToken(
            $validated['name'],
            $validated['abilities'] ?? ['*'],
        );

        $this->securityLogService->log(
            SecurityLogEvent::ApiTokenCreated,
            $request->user(),
            'API token created: '.$validated['name'],
            $request,
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'id' => $token->accessToken->id,
        ], 201);
    }

    public function destroy(Request $request, int $tokenId): JsonResponse
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        $this->securityLogService->log(
            SecurityLogEvent::ApiTokenRevoked,
            $request->user(),
            'API token revoked',
            $request,
            ['token_id' => $tokenId],
        );

        return response()->json(['message' => 'Token revoked.']);
    }
}
