<?php

namespace NimblePHP\Migrations\Commands;

use Exception;
use Krzysztofzylka\Console\Prints;
use NimblePHP\Framework\CLI\Attributes\ConsoleCommand;
use NimblePHP\Framework\Kernel;
use NimblePHP\Migrations\Migrations;

class MigrationCommand
{

    #[ConsoleCommand(command: 'migration:run', description: 'Uruchom migracje bazy danych')]
    public function execute(array $arguments = []): void
    {
        $dir = $arguments['dir'] ?? 'migrations';

        foreach (explode(',', $dir) as $path) {
            try {
                Prints::print('Running migrations from directory: ' . $path);
                $migration = new Migrations(Kernel::$projectPath, $path, $arguments['group'] ?? 'app');
                $migration->runMigrations();
                Prints::print('Migrations from directory ' . $path . ' completed successfully.', color: 'green');
            } catch (\Throwable $e) {
                Prints::print('Error running migrations from directory ' . $path . ': ' . $e->getMessage(), color: 'red');
                return;
            }

        }
    }

}