<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreTechnologyRequest;
use App\Http\Requests\Api\V1\Admin\UpdateTechnologyRequest;
use App\Http\Resources\Api\V1\TechnologyResource;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TechnologyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TechnologyResource::collection(
            Technology::query()->orderBy('name')->get(),
        );
    }

    public function store(StoreTechnologyRequest $request): JsonResponse
    {
        return (new TechnologyResource(Technology::create($request->validated())))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Technology $technology): TechnologyResource
    {
        return new TechnologyResource($technology);
    }

    public function update(UpdateTechnologyRequest $request, Technology $technology): TechnologyResource
    {
        $technology->update($request->validated());

        return new TechnologyResource($technology->refresh());
    }

    public function destroy(Technology $technology): JsonResponse
    {
        $technology->delete();

        return response()->json([
            'message' => 'Tecnología eliminada correctamente',
        ]);
    }
}
