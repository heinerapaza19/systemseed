# Storage en producción

Los controladores de SystemSeed usan el disco definido por `FILESYSTEM_DISK`; no contienen rutas absolutas ni nombres de discos fijos.

## Desarrollo local

```dotenv
FILESYSTEM_DISK=public
APP_URL=http://127.0.0.1:8000
```

Ejecutar una vez:

```bash
php artisan storage:link
```

## Amazon S3 o almacenamiento compatible

```dotenv
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_URL=
AWS_ENDPOINT=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

No se deben guardar credenciales reales en el repositorio. Después de cambiar variables de entorno, ejecutar `php artisan config:clear`.

Cloudinary requerirá instalar y registrar un driver Laravel compatible. Cuando exista ese disk, bastará con definirlo en `config/filesystems.php` y cambiar `FILESYSTEM_DISK`, sin modificar los controladores.
