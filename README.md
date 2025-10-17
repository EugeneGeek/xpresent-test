# 🚀 Установка и запуск Laravel проекта

## 📋 Требования
Перед началом убедитесь, что у вас установлены:
- PHP 8.2+
- Composer
- Node.js и npm
- MySQL или PostgreSQL
- Git

---

## 🔧 Установка проекта

### 1. Клонирование репозитория
```bash
git clone git@github.com:EugeneGeek/xpresent-test.git
cd xpresent-test
```

### 2. Установка PHP-зависимостей
```bash
composer install
```

### 3. Настройка окружения
```bash
cp .env.example .env
php artisan key:generate
```

Отредактируйте файл `.env`, указав настройки базы данных и других сервисов.

Пример:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
SESSION_DRIVER=database
```

### 4. Запуск миграций и создание таблицы сессий
```bash
php artisan migrate
php artisan session:table
php artisan migrate
```

### 5. Установка npm-зависимостей
```bash
npm install
```

### 6. Сборка фронтенда
Для режима разработки:
```bash
npm run dev
```
Для продакшена:
```bash
npm run build
```

---

## ▶️ Запуск проекта

### Локальный сервер Laravel
```bash
php artisan serve
```
После запуска приложение будет доступно по адресу:
👉 [http://localhost:8000](http://localhost:8000)

---

## ⚙️ Дополнительно

Если используется очередь:
```bash
php artisan queue:work
```

Если требуется кеширование конфигурации:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✅ Готово!
Теперь проект Laravel полностью развернут и готов к работе 🎉
