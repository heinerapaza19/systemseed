<div align="center">
  <h1>🌱 SystemSeed</h1>
  <p><strong>Plataforma moderna de portafolio y gestión de proyectos</strong></p>
  
  [![Laravel 11](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
  [![PHP 8.2](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
  [![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Base%20Datos-336791?style=flat-square&logo=postgresql&logoColor=white)](https://postgresql.org)
</div>

---

## 🎯 Acerca de SystemSeed

SystemSeed es una plataforma profesional construida con Laravel 11 y PostgreSQL, diseñada para presentar trabajos, gestionar proyectos y contenidos digitales. Combina una arquitectura robusta con un frontend moderno, ofreciendo una base sólida para portafolios, demostraciones, plantillas y futuras integraciones multiplataforma.

---

## ✨ Características principales

- 🎨 **Portafolio profesional** - Presenta tus trabajos y proyectos de forma elegante
- 📊 **Gestión de contenidos** - Administra proyectos, recursos y materiales descargables
- 🔐 **Autenticación segura** - Laravel Sanctum para protección de API
- 👤 **Login social** - Integración con redes sociales mediante Socialite
- 📱 **API REST** - Endpoints escalables y bien estructurados
- 🎨 **Interfaz moderna** - Diseño limpio con Tailwind CSS
- ⚡ **Performance optimizado** - Construcción rápida con Vite

---

## 🏗️ Stack tecnológico

**Backend:** PHP 8.2 • Laravel 11 • PostgreSQL • Laravel Sanctum • Laravel Socialite

**Frontend:** Blade Templates • Tailwind CSS • Vite • Axios

**Desarrollo:** Docker Sail • PHPUnit • Laravel Pint • Pail

---

## 🚀 Inicio rápido

### Requisitos
- PHP 8.2 o superior
- Composer
- Node.js & npm
- PostgreSQL
- Docker (opcional)

### Instalación

```bash
# Clonar repositorio
git clone https://github.com/heinerapaza19/systemseed.git
cd systemseed

# Instalar dependencias
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate

# Iniciar desarrollo
composer run dev
```

---

## 📋 Comandos disponibles

```bash
php artisan serve              # Servidor de desarrollo
php artisan migrate            # Ejecutar migraciones
php artisan test               # Correr tests
npm run dev                    # Build de frontend
npm run build                  # Compilar producción
```

---

## 📦 Dependencias principales

**Producción:**
- laravel/framework ^11.31
- laravel/sanctum ^4.3
- laravel/socialite ^5.31
- laravel/tinker ^2.9

**Desarrollo:**
- phpunit/phpunit ^11.0.1
- laravel/sail ^1.26
- laravel/pint ^1.13
- mockery/mockery ^1.6

---

## 🏛️ Estructura del proyecto

```
systemseed/
├── app/                  # Lógica de aplicación
├── database/             # Migraciones y seeders
├── resources/            # Vistas y assets
├── routes/               # Definición de rutas
├── tests/                # Suite de pruebas
├── storage/              # Logs y archivos
└── config/               # Configuración
```

---

## 🔐 Seguridad y autenticación

- **Sanctum:** Autenticación token-based para APIs
- **Socialite:** OAuth2 para login social
- **CORS:** Configuración flexible de dominios
- **Validación:** Protección contra vulnerabilidades comunes

---

## 📚 Documentación

- [Laravel](https://laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Vite](https://vitejs.dev)
- [PostgreSQL](https://www.postgresql.org/docs)

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/mi-feature`)
3. Commit tus cambios (`git commit -m 'Agrego mi feature'`)
4. Push a la rama (`git push origin feature/mi-feature`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver [LICENSE](LICENSE) para más detalles.

---

<div align="center">
  <p><strong>Desarrollado con ❤️ usando Laravel y tecnologías modernas</strong></p>
  <p>⭐ Si te resulta útil, considera darle una estrella</p>
</div>
