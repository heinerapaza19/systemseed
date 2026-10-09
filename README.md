<div align="center">
  <img src="https://img.icons8.com/color/96/000000/laravel.png" alt="Laravel Logo" width="96" height="96" />
  <h1>🌱 SystemSeed</h1>
  <p><strong>A Modern Laravel Application with Full Stack Technologies</strong></p>
  
  [![Laravel Version](https://img.shields.io/badge/Laravel-11.31-red?style=for-the-badge&logo=laravel)](https://laravel.com)
  [![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)](https://php.net)
  [![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
</div>

---

## 🎯 About SystemSeed

SystemSeed is a modern full-stack Laravel application built with cutting-edge technologies for building scalable, efficient web applications. It combines powerful backend frameworks with modern frontend tools for a seamless development experience.

---

## 🏗️ Architecture & Tech Stack

### 🔵 Backend

<table>
  <tr>
    <td align="center" width="50%">
      <img src="https://img.icons8.com/color/64/000000/laravel.png" alt="Laravel" width="48" height="48" />
      <br />
      <strong>Laravel 11.31</strong>
    </td>
    <td align="center" width="50%">
      <img src="https://img.icons8.com/color/64/000000/php.png" alt="PHP" width="48" height="48" />
      <br />
      <strong>PHP 8.2+</strong>
    </td>
  </tr>
  <tr>
    <td><strong>Description:</strong> Powerful, elegant PHP framework for web applications</td>
    <td><strong>Description:</strong> Modern programming language with improved performance</td>
  </tr>
</table>

#### Backend Features:
- ⚡ **Fast Routing** - Simple and fast routing engine
- 💉 **Dependency Injection** - Powerful IoC container
- 🗄️ **Eloquent ORM** - Expressive database abstraction
- 🔐 **Authentication** - Built-in Laravel Sanctum for API security
- 👥 **Social Login** - Laravel Socialite integration
- 🔍 **REPL** - Laravel Tinker for interactive debugging
- 📦 **Queue System** - Background job processing
- 🧪 **Testing** - PHPUnit integration with Pest support

---

### 🎨 Frontend

<table>
  <tr>
    <td align="center" width="50%">
      <img src="https://img.icons8.com/color/64/000000/tailwindcss.png" alt="Tailwind CSS" width="48" height="48" />
      <br />
      <strong>Tailwind CSS 3.4</strong>
    </td>
    <td align="center" width="50%">
      <img src="https://img.icons8.com/color/64/000000/javascript.png" alt="JavaScript" width="48" height="48" />
      <br />
      <strong>Vite</strong>
    </td>
  </tr>
  <tr>
    <td><strong>Description:</strong> Utility-first CSS framework for rapid UI development</td>
    <td><strong>Description:</strong> Next generation frontend build tool</td>
  </tr>
</table>

#### Frontend Tools:
- 🎯 **Tailwind CSS** - Utility-first CSS framework
- 🚀 **Vite** - Lightning-fast build tool
- 📡 **Axios** - HTTP client for API requests
- 🔄 **PostCSS** - CSS transformations
- 🤖 **Autoprefixer** - Automatic vendor prefixes
- ⚙️ **Laravel Vite Plugin** - Seamless integration

---

### 🛠️ Development Tools

<table>
  <tr>
    <td align="center" width="33%">
      <img src="https://img.icons8.com/color/64/000000/docker.png" alt="Docker" width="48" height="48" />
      <br />
      <strong>Docker</strong>
    </td>
    <td align="center" width="33%">
      <img src="https://img.icons8.com/color/64/000000/database.png" alt="Database" width="48" height="48" />
      <br />
      <strong>Database</strong>
    </td>
    <td align="center" width="33%">
      <img src="https://img.icons8.com/color/64/000000/test-passed.png" alt="Testing" width="48" height="48" />
      <br />
      <strong>Testing</strong>
    </td>
  </tr>
</table>

- 🐳 **Laravel Sail** - Docker development environment
- 📊 **Database Migrations** - Schema versioning & management
- 🧪 **PHPUnit 11** - Unit & feature testing
- 🎭 **Mockery** - Mocking library for testing
- 🚨 **Collision** - Beautiful error handling
- ✨ **Pint** - Laravel code style fixer
- 📋 **Pail** - Real-time log monitoring
- 🔮 **Faker** - Generate fake data for testing

---

## 📦 Dependencies

### Production
```json
{
  "php": "^8.2",
  "laravel/framework": "^11.31",
  "laravel/sanctum": "^4.3",
  "laravel/socialite": "^5.31",
  "laravel/tinker": "^2.9"
}
```

### Development
```json
{
  "fakerphp/faker": "^1.23",
  "laravel/pail": "^1.1",
  "laravel/pint": "^1.13",
  "laravel/sail": "^1.26",
  "mockery/mockery": "^1.6",
  "nunomaduro/collision": "^8.1",
  "phpunit/phpunit": "^11.0.1"
}
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm
- Docker (optional, for Laravel Sail)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/heinerapaza19/systemseed.git
   cd systemseed
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   ```

6. **Development mode**
   ```bash
   composer run dev
   ```
   This starts:
   - 🖥️ Laravel development server
   - 📦 Queue listener
   - 📋 Real-time logs
   - 🎨 Frontend build watcher

---

## 📖 Available Commands

### Backend Commands
```bash
php artisan serve              # Start development server
php artisan migrate            # Run database migrations
php artisan tinker             # Interactive shell
php artisan queue:listen       # Listen to queued jobs
php artisan pail               # Monitor logs in real-time
```

### Frontend Commands
```bash
npm run dev                    # Start Vite dev server
npm run build                  # Build for production
```

### Combined Development
```bash
composer run dev               # Run everything concurrently
```

---

## 🏛️ Project Structure

```
systemseed/
├── app/                       # Application logic
│   ├── Http/                  # Controllers & middleware
│   ├── Models/                # Eloquent models
│   └── Services/              # Business logic
├── database/                  # Database files
│   ├── migrations/            # Schema migrations
│   ├── seeders/               # Data seeders
│   └── factories/             # Model factories for testing
├── resources/                 # Frontend assets
│   ├── css/                   # Tailwind CSS styles
│   ├── js/                    # JavaScript files
│   └── views/                 # Blade templates
├── routes/                    # Application routes
├── tests/                     # Test suites
├── storage/                   # Logs & uploads
└── config/                    # Configuration files
```

---

## 🔐 Key Features

### Authentication & Authorization
- 🔒 **Laravel Sanctum** - API token authentication
- 👤 **Social Login** - OAuth integration via Socialite
- 🛡️ **Authorization** - Built-in authorization system

### Database
- 🗄️ **Eloquent ORM** - Expressive database queries
- 📝 **Migrations** - Version control for database schema
- 🌱 **Seeders** - Database population for development
- 🏭 **Factories** - Generate model instances for testing

### Testing
- ✅ **PHPUnit** - Unit and feature tests
- 🎭 **Mockery** - Mock objects for testing
- 🔮 **Faker** - Generate realistic test data

### Development Experience
- ⚡ **Tinker REPL** - Interactive code evaluation
- 🚨 **Collision** - Enhanced error pages
- 📋 **Pail** - Real-time log streaming
- 🎨 **Pint** - Automatic code formatting

---

## 🔄 Development Workflow

### Start Development
```bash
composer run dev
```

This command runs concurrently:
- 🖥️ `php artisan serve` - Laravel server (port 8000)
- 📦 `php artisan queue:listen --tries=1` - Queue processor
- 📋 `php artisan pail --timeout=0` - Log monitor
- 🎨 `npm run dev` - Vite build watcher

### Run Tests
```bash
php artisan test
```

### Format Code
```bash
php artisan pint
```

---

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Vite Guide](https://vitejs.dev)
- [Sanctum Auth](https://laravel.com/docs/sanctum)
- [Socialite OAuth](https://laravel.com/docs/socialite)

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## ✨ Author

**Heinera Paza**  
GitHub: [@heinerapaza19](https://github.com/heinerapaza19)

---

<div align="center">
  <p><strong>Built with ❤️ using Laravel & Modern Web Technologies</strong></p>
  <p>⭐ If you find this useful, please consider giving it a star!</p>
</div>
