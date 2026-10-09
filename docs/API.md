# SystemSeed API v1

Base local: `http://127.0.0.1:8000/api/v1`

Las solicitudes protegidas usan `Authorization: Bearer {token}` y `Accept: application/json`. Los roles disponibles son `user` y `admin`. Una cuenta con `is_active=false` recibe `403` en rutas protegidas. La verificación de correo está disponible, pero todavía no es obligatoria para usar la aplicación.

## Autenticación y seguridad

| Método | URL | Auth / rol | Body esperado | Respuesta principal | Errores comunes |
|---|---|---|---|---|---|
| POST | `/auth/register` | Pública | `name`, `email`, `password`, `password_confirmation` | `201`, usuario y token; envía verificación | `422`, `429` |
| POST | `/auth/login` | Pública | `email`, `password` | Token Bearer y usuario | `422` credenciales, `403` inactivo, `429` |
| POST | `/auth/logout` | Bearer / activo | — | Revoca el token actual | `401`, `403` |
| GET | `/auth/me` | Bearer / activo | — | Usuario autenticado | `401`, `403` |
| POST | `/auth/forgot-password` | Pública | `email` | Mensaje genérico tanto si existe como si no | `422`, `429` |
| POST | `/auth/reset-password` | Pública | `email`, `token`, `password`, `password_confirmation` | Cambia contraseña y revoca todos los tokens | `422`, `429` |
| PATCH | `/user/password` | Bearer / activo | `current_password`, `password`, `password_confirmation` | Cambia contraseña y revoca todos los tokens | `401`, `403`, `422` |
| GET | `/auth/email/verify/{id}/{hash}` | Bearer / activo, URL firmada | — | Marca el correo como verificado | `401`, `403`, `429` |
| POST | `/auth/email/verification-notification` | Bearer / activo | — | Reenvía enlace o informa que ya fue verificado | `401`, `403`, `429` |
| GET | `/auth/email/verification-status` | Bearer / activo | — | `verified`, `email_verified_at` | `401`, `403` |
| GET | `/auth/google/redirect` | Pública | — | Redirección OAuth a Google | `302`, error de configuración |
| GET | `/auth/google/callback` | Pública | Parámetros OAuth de Google | Usuario y token | `422` callback inválido |

Las contraseñas nuevas requieren al menos ocho caracteres, letras y números. El enlace de verificación debe corresponder al usuario autenticado y conservar su firma completa.

## API pública

| Método | URL | Auth / rol | Body o parámetros | Respuesta principal | Errores comunes |
|---|---|---|---|---|---|
| GET | `/status` | Pública | — | Estado y versión | — |
| GET | `/profile` | Pública | — | Perfil profesional principal | — |
| GET | `/projects` | Pública | Query: `featured`, `technology`, `per_page` | Proyectos publicados paginados | `422` filtros |
| GET | `/projects/{slug}` | Pública | — | Proyecto publicado | `404` |
| GET | `/technologies` | Pública | — | Tecnologías | — |
| GET | `/experiences` | Pública | — | Experiencias visibles | — |
| GET | `/education` | Pública | — | Educación visible | — |
| GET | `/skills` | Pública | — | Skills visibles | — |
| GET | `/certificates` | Pública | — | Certificados visibles | — |
| GET | `/certificates/{certificate}` | Pública | — | Certificado visible | `404` |
| POST | `/contact` | Pública | `name`, `email`, `subject`, `message` | `201`, mensaje registrado | `422`, `429` |
| GET | `/projects/{project}/demo` | Pública o Bearer | — | URL si el demo es público o el usuario tiene permiso | `401`, `403`, `404` |
| GET | `/projects/{project}/updates` | Pública | — | Novedades visibles del proyecto publicado | `404` |

`demo_url`, `download_url` y `source_code_url` se omiten de los recursos JSON cuando el solicitante no tiene permiso.

## Usuario autenticado

| Método | URL | Auth / rol | Body | Respuesta principal | Errores comunes |
|---|---|---|---|---|---|
| GET | `/user/dashboard` | Bearer / activo | — | Usuario, contadores y novedades recientes | `401`, `403` |
| GET | `/user/favorites` | Bearer / activo | — | Favoritos publicados | `401`, `403` |
| POST | `/user/favorites/{project}` | Bearer / activo | — | `201`, agrega sin duplicar | `401`, `403`, `404` |
| DELETE | `/user/favorites/{project}` | Bearer / activo | — | Retira favorito | `401`, `403` |
| GET | `/user/templates` | Bearer / activo | — | Plantillas guardadas | `401`, `403` |
| POST | `/user/templates/{project}` | Bearer / activo | — | `201`, guarda plantilla sin duplicar | `401`, `403`, `422` |
| DELETE | `/user/templates/{project}` | Bearer / activo | — | Retira plantilla | `401`, `403` |
| GET | `/user/following` | Bearer / activo | — | Proyectos seguidos | `401`, `403` |
| POST | `/user/following/{project}` | Bearer / activo | — | `201`, sigue sin duplicar | `401`, `403`, `404` |
| DELETE | `/user/following/{project}` | Bearer / activo | — | Deja de seguir | `401`, `403` |
| GET | `/user/project-updates` | Bearer / activo | — | Novedades visibles de proyectos seguidos | `401`, `403` |
| GET | `/projects/{project}/demo` | Bearer / activo | — | URL de demo público o privado habilitado | `401`, `403`, `404` |
| GET | `/projects/{project}/download` | Bearer / activo | — | URL de descarga habilitada | `401`, `403`, `404` |
| GET | `/projects/{project}/source-code` | Bearer / activo | — | URL de código habilitado | `401`, `403`, `404` |
| PATCH | `/user/password` | Bearer / activo | `current_password`, `password`, `password_confirmation` | Actualiza password y revoca tokens | `401`, `403`, `422` |

## Administración

Todas las rutas siguientes requieren Bearer, usuario activo y rol `admin`.

### Usuarios y panel

| Método | URL | Body / filtros | Respuesta principal | Errores comunes |
|---|---|---|---|---|
| GET | `/admin/dashboard` | — | Panel administrador | `401`, `403` |
| GET | `/admin/users` | Paginación | Usuarios sin password | `401`, `403` |
| GET | `/admin/users/{user}` | — | Usuario | `401`, `403`, `404` |
| PATCH | `/admin/users/{user}` | `role`, `is_active` | Usuario actualizado | `401`, `403`, `422` |
| DELETE | `/admin/users/{user}` | — | Elimina usuario | `401`, `403`, `422` |

### Proyectos, tecnologías e imágenes

| Método | URL | Body / filtros | Respuesta principal | Errores comunes |
|---|---|---|---|---|
| GET | `/admin/projects` | Paginación | Todos los proyectos | `401`, `403` |
| POST | `/admin/projects` | `title`, descripciones, estado, URLs, permisos, tecnologías | `201`, proyecto | `401`, `403`, `422` |
| GET | `/admin/projects/{project}` | — | Proyecto completo | `401`, `403`, `404` |
| PUT/PATCH | `/admin/projects/{project}` | Campos editables del proyecto | Proyecto actualizado | `401`, `403`, `422` |
| DELETE | `/admin/projects/{project}` | — | Elimina proyecto y relaciones | `401`, `403`, `404` |
| POST | `/admin/projects/{project}/images` | `image_url`, `alt_text`, `sort_order` | `201`, imagen | `401`, `403`, `422` |
| DELETE | `/admin/projects/{project}/images/{image}` | — | Elimina imagen | `401`, `403`, `404` |
| GET | `/admin/technologies` | — | Tecnologías | `401`, `403` |
| POST | `/admin/technologies` | `name`, `category`, `icon` | `201`, tecnología | `401`, `403`, `422` |
| GET | `/admin/technologies/{technology}` | — | Tecnología | `401`, `403`, `404` |
| PUT/PATCH | `/admin/technologies/{technology}` | Campos editables | Tecnología actualizada | `401`, `403`, `422` |
| DELETE | `/admin/technologies/{technology}` | — | Elimina tecnología | `401`, `403` |

Los permisos de proyecto son `demo_enabled`, `demo_public`, `is_template`, `download_enabled`, `download_url`, `source_code_enabled` y `source_code_url`. Todas las URLs aceptan solo HTTP/HTTPS.

### Novedades

| Método | URL | Body | Respuesta principal | Errores comunes |
|---|---|---|---|---|
| GET | `/admin/projects/{project}/updates` | — | Todas las novedades | `401`, `403`, `404` |
| POST | `/admin/projects/{project}/updates` | `title`, `content`, `published_at`, `visible` | `201`, novedad | `401`, `403`, `422` |
| GET | `/admin/projects/{project}/updates/{update}` | — | Novedad | `401`, `403`, `404` |
| PATCH | `/admin/projects/{project}/updates/{update}` | Campos editables | Novedad actualizada | `401`, `403`, `404`, `422` |
| DELETE | `/admin/projects/{project}/updates/{update}` | — | Elimina novedad | `401`, `403`, `404` |

### Perfil y CV

| Método | URL | Body | Respuesta principal | Errores comunes |
|---|---|---|---|---|
| GET | `/admin/profile` | — | Perfil principal | `401`, `403` |
| PUT/PATCH | `/admin/profile` | Datos profesionales y redes | Perfil actualizado | `401`, `403`, `422` |
| POST | `/admin/profile/cv` | Multipart `cv` PDF, máximo 10 MB | URL del CV | `401`, `403`, `422` |
| DELETE | `/admin/profile/cv` | — | Elimina CV | `401`, `403` |

### Certificados y currículum

Los CRUD usan `GET` para listar/ver, `POST` para crear, `PUT/PATCH` para actualizar y `DELETE` para eliminar.

| Recurso | URLs | Body principal | Respuesta / errores |
|---|---|---|---|
| Certificados | `/admin/certificates`, `/admin/certificates/{certificate}` | título, institución, fechas, visibilidad y archivo opcional PDF/JPG/JPEG/PNG/WEBP | Resource; `401`, `403`, `404`, `422` |
| Experiencia | `/admin/experiences`, `/admin/experiences/{experience}` | cargo, empresa, fechas, descripción, visibilidad y orden | Resource; `401`, `403`, `404`, `422` |
| Educación | `/admin/education`, `/admin/education/{education}` | institución, título, fechas, descripción, visibilidad y orden | Resource; `401`, `403`, `404`, `422` |
| Skills | `/admin/skills`, `/admin/skills/{skill}` | nombre, categoría, nivel, visibilidad y orden | Resource; `401`, `403`, `404`, `422` |

### Mensajes de contacto

| Método | URL | Body / filtros | Respuesta principal | Errores comunes |
|---|---|---|---|---|
| GET | `/admin/messages` | Query `read` | Mensajes paginados | `401`, `403`, `422` |
| GET | `/admin/messages/{message}` | — | Mensaje | `401`, `403`, `404` |
| PATCH | `/admin/messages/{message}/read` | — | Marca leído | `401`, `403`, `404` |
| PATCH | `/admin/messages/{message}/unread` | — | Marca no leído | `401`, `403`, `404` |
| DELETE | `/admin/messages/{message}` | — | Elimina mensaje | `401`, `403`, `404` |

## Rate limiting

- `auth`: 10 solicitudes por minuto por correo e IP para login y registro.
- `password-reset`: 6 por minuto por correo e IP.
- `verification`: 6 por minuto por usuario e IP.
- `contact`: 10 por minuto por IP.

Al superar un límite se devuelve HTTP `429 Too Many Requests`.

## CORS y clientes

Los orígenes se configuran con `CORS_ALLOWED_ORIGINS`, separados por comas. Las credenciales están habilitadas, por lo que no se usa `*` para los orígenes. Sanctum puede configurarse mediante `SANCTUM_STATEFUL_DOMAINS`. Los clientes móviles pueden usar tokens Bearer sin depender de cookies.
