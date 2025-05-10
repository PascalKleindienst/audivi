<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DataProviderType;
use App\Exceptions\UnsupportedDataTypeError;
use App\Facades\DataProvider;
use App\Http\Requests\FetchMetadataRequest;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

final class FetchMetadataController extends Controller
{
    public function __invoke(FetchMetadataRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            return response()->json([
                'items' => DataProvider::provider($data['provider'])
                    ->search($data['query'], DataProviderType::from($data['type']), $data['locale'] ?? app()->getLocale()),
            ]);
        } catch (UnsupportedDataTypeError|InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 400);
        }
    }
}
