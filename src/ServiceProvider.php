<?php

namespace NimblePHP\Migrations;

use NimblePHP\Framework\Interfaces\CliCommandProviderInterface;
use NimblePHP\Migrations\Commands\MigrationCommand;

class ServiceProvider implements CliCommandProviderInterface
{

    public function getCliCommands(): array
    {
        return [
            MigrationCommand::class
        ];
    }

}