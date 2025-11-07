# Word Reverser

Библиотека для разворота букв в словах с сохранением регистра и пунктуации.

## Описание

Класс `WordReverser` разворачивает порядок букв в каждом слове строки, при этом:
- Сохраняет регистр букв по позициям исходного слова
- Сохраняет всю пунктуацию на своих местах
- Дефис (`-`), апостроф (`'`) и бэктик (`` ` ``) считаются разделителями слов

## Примеры использования

```php
use App\Service\WordReverser;

$reverser = new WordReverser();

// Базовые примеры
$reverser->transform('Cat');           // 'Tac'
$reverser->transform('домИК');          // 'кимОД'
$reverser->transform('houSe');          // 'esuOh'

// С пунктуацией
$reverser->transform('cat,');           // 'tac,'
$reverser->transform('is "cold" now');  // 'si "dloc" won'

// Составные слова с разделителями
$reverser->transform('third-part');     // 'driht-trap'
$reverser->transform("can't");          // "nac't"
$reverser->transform('can`t');          // 'nac`t'

// Мультиалфавитные строки
$reverser->transform('Дом cat');        // 'Мод tac'
$reverser->transform('你好 мир');       // '好你 рим'
```

## Требования

- PHP >= 8.1
- Расширение `ext-mbstring` (для работы с Unicode)

## Установка

### Через Composer

```bash
composer install
```

### Через Docker

```bash
docker build -t word-reverser .
```

## Использование

### Базовое использование

```php
<?php

require 'vendor/autoload.php';

use App\Service\WordReverser;

$reverser = new WordReverser();
$result = $reverser->transform('Hello, World!');
echo $result; // 'Olleh, Dlrow!'
```

### Использование интерфейса

```php
use App\Service\WordReverserInterface;
use App\Service\WordReverser;

$reverser = new WordReverser();
// $reverser реализует WordReverserInterface
```

## Запуск тестов

### Локально

```bash
vendor/bin/phpunit
```

### Через Docker

```bash
# Запуск тестов
docker run --rm word-reverser

# Или с монтированием для разработки
docker run --rm -v ${PWD}:/app word-reverser
```

### Через Docker Compose

```bash
docker-compose up
```

## Результаты тестирования

```
PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.1.33
Configuration: /app/phpunit.xml

..........................................                        42 / 42 (100%)

Time: 00:00.634, Memory: 8.00 MB

OK (42 tests, 124 assertions)
```

## Структура проекта

```
.
├── composer.json              # Зависимости проекта
├── composer.lock              # Зафиксированные версии
├── phpunit.xml                # Конфигурация PHPUnit
├── Dockerfile                 # Docker образ
├── docker-compose.yml         # Docker Compose конфигурация
├── .dockerignore              # Исключения для Docker
├── src/
│   └── Service/
│       └── WordReverser.php   # Основной класс
└── tests/
    └── WordReverserTest.php   # Unit-тесты
```

## Технические детали

### Алгоритм работы

1. **Токенизация**: Строка разбивается на токены — группы букв (`\p{L}+`) и всё остальное (пунктуация, пробелы, разделители)
2. **Разворот**: Каждая буквенная группа разворачивается отдельно
3. **Сохранение регистра**: Регистр исходных позиций применяется к развёрнутым символам
4. **Сборка**: Токены объединяются обратно в строку

### Особенности

- **Unicode-совместимость**: Поддержка всех языков (латиница, кириллица, иероглифы и т.д.)
- **Разделители слов**: Дефис, апостроф и бэктик не являются буквами, поэтому автоматически остаются на местах
- **Символы без регистра**: Иероглифы и другие символы без регистра обрабатываются корректно

### Примеры обработки

| Входная строка | Результат | Описание |
|---------------|-----------|----------|
| `'Cat'` | `'Tac'` | Сохранение регистра |
| `'домИК'` | `'кимОД'` | Кириллица |
| `'houSe'` | `'esuOh'` | Смешанный регистр |
| `'third-part'` | `'driht-trap'` | Дефис как разделитель |
| `"can't"` | `"nac't"` | Апостроф как разделитель |
| `'is "cold" now'` | `'si "dloc" won'` | Кавычки сохраняются |
| `'Дом cat'` | `'Мод tac'` | Мультиалфавитная строка |

## Docker

### Сборка образа

```bash
docker build -t word-reverser .
```

### Запуск контейнера

```bash
# Запуск тестов
docker run --rm word-reverser

# Интерактивный режим
docker run --rm -it word-reverser bash

# С монтированием для разработки
docker run --rm -v ${PWD}:/app word-reverser
```

### Docker Compose

```bash
# Запуск тестов
docker-compose up

# Пересборка образа
docker-compose build
```

## Принципы проектирования

Проект следует принципам **SOLID**:

- **SRP** (Single Responsibility Principle): Класс решает одну задачу — трансформацию строки
- **OCP** (Open/Closed Principle): Открыт для расширения через интерфейс
- **LSP** (Liskov Substitution Principle): Реализация соответствует контракту интерфейса
- **ISP** (Interface Segregation Principle): Минимальный интерфейс из одного метода
- **DIP** (Dependency Inversion Principle): Потребители зависят от абстракции (интерфейса)

## Покрытие тестами

Проект включает **42 теста** с **124 проверками**, покрывающих:

- Базовые случаи (латиница, кириллица)
- Сохранение регистра
- Сохранение пунктуации
- Разделители слов (дефис, апостроф, бэктик)
- Мультиалфавитные строки
- Символы без регистра (иероглифы)
- Граничные случаи (пустые строки, только пунктуация)
- Длинные строки (стресс-тесты)
- Специальные Unicode-символы
