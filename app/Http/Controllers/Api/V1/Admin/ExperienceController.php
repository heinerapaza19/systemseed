<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreExperienceRequest;
use App\Http\Requests\Api\V1\Admin\UpdateExperienceRequest;
use App\Http\Resources\Api\V1\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ExperienceController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ExperienceResource::collection(Experience::query()->orderBy('sort_order')->orderByDesc('start_date')->get());
    }

    public function store(StoreExperienceRequest $request): JsonResponse
    {
        return (new ExperienceResource(Experience::create($request->validated())))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Experience $experience): ExperienceResource
    {
        return new ExperienceResource($experience);
    }

    public function update(UpdateExperienceRequest $request, Experience $experience): ExperienceResource
    {
        $experience->update($request->validated());

        return new ExperienceResource($experience->refresh());
    }

    public function destroy(Experience $experience): JsonResponse
    {
        $experience->delete();

        return response()->json(['message' => 'Experiencia eliminada correctamente']);
    }
}
