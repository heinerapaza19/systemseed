<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreEducationRequest;
use App\Http\Requests\Api\V1\Admin\UpdateEducationRequest;
use App\Http\Resources\Api\V1\EducationResource;
use App\Models\Education;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class EducationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return EducationResource::collection(Education::query()->orderBy('sort_order')->orderByDesc('start_date')->get());
    }

    public function store(StoreEducationRequest $request): JsonResponse
    {
        return (new EducationResource(Education::create($request->validated())))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Education $education): EducationResource
    {
        return new EducationResource($education);
    }

    public function update(UpdateEducationRequest $request, Education $education): EducationResource
    {
        $education->update($request->validated());

        return new EducationResource($education->refresh());
    }

    public function destroy(Education $education): JsonResponse
    {
        $education->delete();

        return response()->json(['message' => 'Educación eliminada correctamente']);
    }
}
