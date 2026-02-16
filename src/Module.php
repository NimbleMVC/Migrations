<?php

namespace NimblePHP\Migrations;

use NimblePHP\Framework\Interfaces\CliCommandProviderInterface;
use NimblePHP\Framework\Module\Interfaces\ModuleInterface;
use NimblePHP\Migrations\Commands\MigrationCommand;

class Module implements ModuleInterface, CliCommandProviderInterface
{

    public function getName(): string
    {
        return 'Nimblephp Migrations';
    }

    public function register(): void
    {
    }

    public function getCliCommands(): array
    {
        return [
            MigrationCommand::class
        ];
    }

}