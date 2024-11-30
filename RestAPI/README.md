# RestAPI Laravel для Аутентификации пользователей

## Описание
Небольшое API и админ-панель для управления пользователями 

### Используемые технологии
- PHP 8.4
- Laravel 11
- PostgreSQL

### Функционал

- Swagger `api/documentation`
- Регистрация `api/register`
- Редактирование `api/update`
- Удаление `api/delete`
- Просмотр информации `api/user`
- Админ-панель `admin/`

### Запуск проекта

- Установите зависимости `composer install`
- Создайте `.env` файл и настройте там:
  - DB_CONNECTION=pgsql
  - DB_HOST= *Адрес БД*
  - DB_PORT=*Порт*
  - DB_DATABASE=*Название БД*
  - DB_USERNAME=*Имя пользователя*
  - DB_PASSWORD=*Пароль*
  - APP_URL=*Адрес приложения, например http://localhost:8000*


- Запустите следующие команды 
```
php artisan migrate
php artisan db:seed
npm install
npm run build
```

После этого будет создан пользователь admin@admin.com с паролем admin, чтобы войти в админ-панель.

Для запуска пропишите 
```
composer run dev --timeout 0
```
