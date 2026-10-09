<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreCvRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class ProfileCvController extends Controller
{
    public function store(StoreCvRequest $request): JsonResponse
    {
        $profile = Profile::main();
        $oldPath = $profile->cv_url;
        $disk = config('filesystems.default');
        $newPath = $request->file('cv')->store('cv', $disk);

        if (! $newPath) {
            throw new RuntimeException('No se pudo almacenar el CV.');
        }

        $profile->update(['cv_url' => $newPath]);

        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk($disk)->delete($oldPath);
        }

        return (new ProfileResource($profile->refresh()))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(): JsonResponse
    {
        $profile = Profile::main();

        if ($profile->cv_url) {
            Storage::disk(config('filesystems.default'))->delete($profile->cv_url);
            $profile->update(['cv_url' => null]);
        }

        return response()->json([
            'message' => 'CV eliminado correctamente',
        ]);
    }
}
