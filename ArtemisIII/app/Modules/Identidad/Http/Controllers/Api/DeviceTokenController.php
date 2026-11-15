<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\IssueDeviceToken;
use App\Modules\Identidad\Http\Requests\DeviceTokenRequest;
use App\Modules\Identidad\Http\Resources\TokenResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/auth/device-token: una máquina pide su access token.
 */
final class DeviceTokenController
{
    public function __invoke(DeviceTokenRequest $request, IssueDeviceToken $issue): JsonResponse
    {
        $result = $issue->execute($request->toCredentials());

        return ApiResponse::ok(new TokenResource($result->accessToken, $result->profile));
    }
}
