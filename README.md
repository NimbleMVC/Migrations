# <h1 align="center">NimblePHP - Migrations</h1>
Pakiet migracyjny dla frameworka NimblePHP umożliwia automatyczną aktualizację schematu bazy danych za pomocą plików SQL.
Dzięki temu rozwiązaniu możesz łatwo i bezpiecznie wprowadzać zmiany w strukturze bazy danych, co jest nieocenione w 
środowiskach produkcyjnych oraz podczas ciągłej integracji. Pakiet zapewnia efektywne zarządzanie wersjami bazy danych, 
minimalizując ryzyko błędów i maksymalizując produktywność.

**Dokumentacja** projektu dostępna jest pod linkiem:
https://nimblemvc.github.io/documentation/extension/migrations/start/#

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

$migrations = new \NimblePHP\Migrations\Migrations(__DIR__);
$migrations->runMigrations();
```
3. W folderze pliku `migrations` możemy utworzyć pliku `.sql` o nazwie `timestamp` (bash: `date +%s`)
4. Uruchamiamy plik `update.php` w konsoli (`php update.php`)

## Współtworzenie
Zachęcamy do współtworzenia! Masz sugestie, znalazłeś błędy, chcesz pomóc w rozwoju? Otwórz issue lub prześlij pull request.

## Pomoc
Wszelkie problemy oraz pytania należy zadawać przez zakładkę discussions w github pod linkiem:
https://github.com/NimbleMVC/Migrations/discussions