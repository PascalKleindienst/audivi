<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\DataProvider;
use App\Http\Requests\FetchMetadataRequest;
use App\Library\DataProviders\DataType;
use App\Library\DataProviders\UnsupportedDataTypeError;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

final class MetadataController extends Controller
{
    public function fetch(FetchMetadataRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            return response()->json([
                'items' => DataProvider::provider($data['provider'])
                    ->search($data['query'], DataType::from($data['type']), $data['locale'] ?? app()->getLocale()),
            ]);
        } catch (UnsupportedDataTypeError|InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 400);
        }
    }
}
