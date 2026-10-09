<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CertificateResource;
use App\Models\Certificate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CertificateController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CertificateResource::collection(
            Certificate::query()
                ->where('visible', true)
                ->orderBy('sort_order')
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->get(),
        );
    }

    public function show(Certificate $certificate): CertificateResource
    {
        abort_unless($certificate->visible, 404);

        return new CertificateResource($certificate);
    }
}
