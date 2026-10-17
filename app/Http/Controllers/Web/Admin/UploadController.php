<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Subida de imágenes desde el panel admin (iconos, portadas, galería).
 * Devuelve una URL pública que luego se guarda en los campos existentes
 * (icon, cover_image, image_url), sin cambiar los contratos de la API.
 */
class UploadController extends Controller
{
    public const FOLDERS = ['technologies', 'skills', 'projects', 'gallery'];

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'folder' => ['required', Rule::in(self::FOLDERS)],
        ], [
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'Formatos permitidos: JPG, PNG, WEBP o GIF.',
            'image.max' => 'La imagen no debe pesar más de 4 MB.',
        ]);

        $path = $request->file('image')->store('uploads/'.$validated['folder'], 'public');

        abort_unless($path, Response::HTTP_INTERNAL_SERVER_ERROR, 'No se pudo guardar la imagen.');

        return response()->json([
            'message' => 'Imagen subida correctamente.',
            'data' => [
                'path' => $path,
                'url' => '/storage/'.$path,
            ],
        ], Response::HTTP_CREATED);
    }
}
