# How to run it

# 1. Install the dependencies

```bash
composer update
```

# 2. Copy the .env.example file to .env and set the database credentials

```bash
cp .env.example .env
```

# 3. Generate the application key

```bash
php artisan key:generate
```

# 4. Generate the JWT secret

```bash
php artisan jwt:secret
```

# 5. Run the migrations

```bash
php artisan migrate
```

# 6. Run the server

```bash
php artisan serve
```
