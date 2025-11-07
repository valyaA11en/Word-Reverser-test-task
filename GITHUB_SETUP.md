# Инструкция по загрузке проекта в GitHub

## Шаги для загрузки проекта

### 1. Создайте репозиторий на GitHub

Перейдите на https://github.com/valyaA11en и создайте новый репозиторий с именем `word-reverser` (или любым другим).

### 2. Загрузите код в GitHub

Выполните следующие команды:

```bash
# Проверьте статус
git status

# Если есть незакоммиченные изменения, добавьте их
git add .

# Создайте коммит (если еще не создан)
git commit -m "Initial commit: Word Reverser project"

# Убедитесь, что ветка называется main
git branch -M main

# Добавьте remote (если еще не добавлен)
git remote add origin https://github.com/valyaA11en/word-reverser.git

# Или если remote уже существует, обновите URL
git remote set-url origin https://github.com/valyaA11en/word-reverser.git

# Загрузите код в GitHub
git push -u origin main
```

### 3. Если возникнут проблемы с авторизацией

GitHub больше не поддерживает пароли для HTTPS. Используйте один из вариантов:

#### Вариант 1: Personal Access Token (PAT)
1. Перейдите в Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Создайте новый токен с правами `repo`
3. Используйте токен вместо пароля при push

#### Вариант 2: SSH ключ
```bash
# Измените remote на SSH
git remote set-url origin git@github.com:valyaA11en/word-reverser.git

# Затем push
git push -u origin main
```

### 4. Проверка

После успешной загрузки проверьте репозиторий:
https://github.com/valyaA11en/word-reverser

## Структура проекта

Проект содержит:
- ✅ Исходный код (`src/`)
- ✅ Тесты (`tests/`)
- ✅ Docker конфигурацию
- ✅ README.md с документацией
- ✅ Composer зависимости (composer.json, composer.lock)
- ✅ PHPUnit конфигурацию

## Что будет загружено

- `src/Service/WordReverser.php` - основной класс
- `tests/WordReverserTest.php` - unit-тесты
- `composer.json` и `composer.lock` - зависимости
- `Dockerfile` и `docker-compose.yml` - Docker конфигурация
- `phpunit.xml` - конфигурация тестов
- `README.md` - документация
- `.gitignore` - исключения для git
- `.dockerignore` - исключения для Docker

## Что НЕ будет загружено (благодаря .gitignore)

- `/vendor/` - зависимости Composer (устанавливаются через `composer install`)
- IDE файлы
- Временные файлы

