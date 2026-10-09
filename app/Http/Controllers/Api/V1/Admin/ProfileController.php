<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\UpdateProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function show(): ProfileResource
    {
        return new ProfileResource(Profile::main());
    }

    public function update(UpdateProfileRequest $request): ProfileResource
    {
        $profile = Profile::main();
        $profile->update($request->validated());

        return new ProfileResource($profile->refresh());
    }
}
