<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProjectAccessController extends Controller
{
    public function demo(Request $request, Project $project): JsonResponse
    {
        $user = $this->optionalUser();
        $this->ensureVisibleProject($project, $user);

        abort_unless($project->demo_enabled && filled($project->demo_url), Response::HTTP_NOT_FOUND);

        if (! $project->demo_public && ! $user) {
            abort(Response::HTTP_UNAUTHORIZED, 'No autenticado');
        }

        return $this->urlResponse('Demo disponible', $project->demo_url);
    }

    public function download(Request $request, Project $project): JsonResponse
    {
        return $this->privateResource($project, 'download_enabled', 'download_url', 'Descarga disponible');
    }

    public function sourceCode(Request $request, Project $project): JsonResponse
    {
        return $this->privateResource($project, 'source_code_enabled', 'source_code_url', 'Código fuente disponible');
    }

    private function privateResource(Project $project, string $enabledField, string $urlField, string $message): JsonResponse
    {
        $user = $this->optionalUser();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED, 'No autenticado');
        }

        $this->ensureVisibleProject($project, $user);
        abort_unless($project->{$enabledField} && filled($project->{$urlField}), Response::HTTP_NOT_FOUND);

        return $this->urlResponse($message, $project->{$urlField});
    }

    private function optionalUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::guard('sanctum')->user();

        if ($user && ! $user->is_active) {
            abort(Response::HTTP_FORBIDDEN, 'Cuenta inactiva');
        }

        return $user;
    }

    private function ensureVisibleProject(Project $project, ?User $user): void
    {
        abort_unless($project->status === 'published' || $user?->isAdmin(), Response::HTTP_NOT_FOUND);
    }

    private function urlResponse(string $message, string $url): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => ['url' => $url],
        ]);
    }
}
