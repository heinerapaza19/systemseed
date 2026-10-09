<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreSkillRequest;
use App\Http\Requests\Api\V1\Admin\UpdateSkillRequest;
use App\Http\Resources\Api\V1\SkillResource;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class SkillController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SkillResource::collection(Skill::query()->orderBy('sort_order')->orderBy('name')->get());
    }

    public function store(StoreSkillRequest $request): JsonResponse
    {
        return (new SkillResource(Skill::create($request->validated())))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Skill $skill): SkillResource
    {
        return new SkillResource($skill);
    }

    public function update(UpdateSkillRequest $request, Skill $skill): SkillResource
    {
        $skill->update($request->validated());

        return new SkillResource($skill->refresh());
    }

    public function destroy(Skill $skill): JsonResponse
    {
        $skill->delete();

        return response()->json(['message' => 'Habilidad eliminada correctamente']);
    }
}
