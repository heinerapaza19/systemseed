<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreCertificateRequest;
use App\Http\Requests\Api\V1\Admin\UpdateCertificateRequest;
use App\Http\Resources\Api\V1\CertificateResource;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CertificateController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CertificateResource::collection(
            Certificate::query()
                ->orderBy('sort_order')
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->get(),
        );
    }

    public function store(StoreCertificateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $file = Arr::pull($validated, 'file');
        $path = null;

        try {
            if ($file instanceof UploadedFile) {
                $path = $this->storeFile($file);
                $validated['file_url'] = $path;
                $validated['file_type'] = strtolower($file->getClientOriginalExtension());
            }

            $certificate = Certificate::create($validated);
        } catch (Throwable $exception) {
            if ($path) {
                Storage::disk(config('filesystems.default'))->delete($path);
            }

            throw $exception;
        }

        return (new CertificateResource($certificate))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Certificate $certificate): CertificateResource
    {
        return new CertificateResource($certificate);
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate): CertificateResource
    {
        $validated = $request->validated();
        $file = Arr::pull($validated, 'file');
        $oldPath = $certificate->file_url;
        $newPath = null;

        try {
            if ($file instanceof UploadedFile) {
                $newPath = $this->storeFile($file);
                $validated['file_url'] = $newPath;
                $validated['file_type'] = strtolower($file->getClientOriginalExtension());
            }

            $certificate->update($validated);
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk(config('filesystems.default'))->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk(config('filesystems.default'))->delete($oldPath);
        }

        return new CertificateResource($certificate->refresh());
    }

    public function destroy(Certificate $certificate): JsonResponse
    {
        $path = $certificate->file_url;
        $certificate->delete();

        if ($path) {
            Storage::disk(config('filesystems.default'))->delete($path);
        }

        return response()->json([
            'message' => 'Certificado eliminado correctamente',
        ]);
    }

    private function storeFile(UploadedFile $file): string
    {
        $path = $file->store('certificates', config('filesystems.default'));

        if (! $path) {
            throw new RuntimeException('No se pudo almacenar el certificado.');
        }

        return $path;
    }
}
