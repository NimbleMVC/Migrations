# Migracje
Szczegółowa dokumentacja:
[https://nimblemvc.github.io/documentation/](https://nimblemvc.github.io/documentation/extension/migrations/start/#)

## Instalacja
```shell
composer require nimblephp/migrations
```

## Użycie
1. W folderze projektu tworzymi plik `update.php` oraz folder `migrations`
2. w pliku update.php wpisujemy następujący kod:
```php
<?php

include('../vendor/autoload.php');

$migrations = new \Nimblephp\migrations\Migrations(__DIR__);
$migrations->runMigrations();
```
3. W folderze pliku `migrations` możemy utworzyć pliku `.sql` o nazwie `timestamp` (bash: `date +%s`)
4. Uruchamiamy plik `update.php` w konsoli (`php update.php`)