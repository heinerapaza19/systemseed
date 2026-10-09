<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EducationResource;
use App\Models\Education;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EducationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return EducationResource::collection(Education::query()->where('visible', true)->orderBy('sort_order')->orderByDesc('start_date')->get());
    }
}
