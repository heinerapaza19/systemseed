<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TechnologyResource;
use App\Models\Technology;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TechnologyController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TechnologyResource::collection(
            Technology::query()->orderBy('name')->get(),
        );
    }
}
