<?php

namespace NimblePHP\Migrations;

use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\Condition;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use krzysztofzylka\DatabaseManager\Enum\DatabaseType;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\File\File;
use NimblePHP\Framework\Exception\DatabaseException;
use NimblePHP\Framework\Exception\NimbleException;
use NimblePHP\Framework\Kernel;
use NimblePHP\Framework\Request;
use NimblePHP\Framework\Routes\Route;
use NimblePHP\Migrations\Exceptions\MigrationException;
use NimblePHP\Migrations\Resource\MigrationController;
use Throwable;

/**
 * Migrations
 */
class Migrations
{

    /**
     * Project path
     * @var string
     */
    protected string $projectPath;

    /**
     * Migrations path
     * @var string
     */
    protected string $migrationsPath;

    /**
     * Migration list
     * @var array
     */
    protected array $migrationList = [];

    /**
     * Migration table instance
     * @var Table
     */
    protected Table $migrationTable;

    /**
     * Initialize migrations
     * @param false|string $projectPath
     * @param string|null $migrationsPath
     * @throws NimbleException
     * @throws Throwable
     */
    public function __construct(false|string $projectPath, ?string $migrationsPath = null)
    {
        $this->projectPath = $projectPath;
        $this->migrationsPath = $migrationsPath ?? ($projectPath . '/migrations');

        if ($projectPath) {
            $this->projectAutoloader();

            if (!file_exists($this->migrationsPath)) {
                File::mkdir($this->migrationsPath);
            }

            $request = new Request();
            $route = new Route($request);
            $kernel = new Kernel($route);
            Kernel::$projectPath = $projectPath;
            $kernel->bootstrap();
            $kernel->loadConfiguration();

            if (!$_ENV['DATABASE']) {
                throw new NimbleException('Database is disabled');
            }

            $this->connectDatabase();
        }

        $this->initTable();
    }

    /**
     * Run migration
     * @return void
     * @throws DatabaseManagerException
     * @throws MigrationException
     * @throws NimbleException
     */
    public function runMigrations(): void
    {
        $this->checkMigrations();
        $this->generateMigrationList();

        foreach ($this->migrationList as $timestamp => $path) {
            if ($this->migrationTable->findIsset(['migrations.timestamp' => $timestamp])) {
                continue;
            }

            $this->migrationTable->setId(null)->insert([
                'timestamp' => $timestamp,
                'status' => Status::PENDING->value
            ]);

            try {
                switch (File::getExtension($this->migrationsPath . '/' . $path)) {
                    case 'sql':
                        $content = file_get_contents($this->migrationsPath . '/' . $path);
                        DatabaseManager::$connection->getConnection()->query($content);

                        break;
                    case 'php':
                        /** @var AbstractMigration $class */
                        $class = include($this->migrationsPath . '/' . $path);

                        if (!$class instanceof AbstractMigration) {
                            throw new MigrationException('Failed load class');
                        }

                        $class->controller = new MigrationController();
                        $class->run();

                        break;
                }

                $this->migrationTable->updateValue('status', Status::FINISHED->value);
            } catch (Throwable $exception) {
                $message = $exception->getMessage();

                if (method_exists($exception, 'getHiddenmessage')) {
                    $message = $exception->getHiddenmessage();
                }

                if ($exception->getPrevious()) {
                    $message = $exception->getPrevious()->getMessage();

                    if (method_exists($exception->getPrevious(), 'getHiddenmessage')) {
                        $message = $exception->getPrevious()->getHiddenmessage();
                    }
                }

                $this->migrationTable->update([
                    'status' => Status::FAILED->value,
                    'error' => $message
                ]);

                throw new MigrationException('Failed update');
            }
        }
    }

    /**
     * Check migrations
     * @return void
     * @throws DatabaseManagerException
     * @throws NimbleException
     */
    protected function checkMigrations(): void
    {
        $conditions = [
            new Condition('migrations.status', 'IN', ['pending', 'failed'])
        ];

        if ($this->migrationTable->findIsset($conditions)) {
            throw new NimbleException("The migration process has been halted due to a migration with a 'pending' or 'failed' status.");
        }
    }

    /**
     * Generate migration list
     * @return void
     */
    protected function generateMigrationList(): void
    {
        foreach (File::scanDir($this->migrationsPath) as $migrationPath) {
            $timestamp = str_replace(['.sql', '.php'], '', basename($migrationPath));

            $this->migrationList[$timestamp] = $migrationPath;
        }

        ksort($this->migrationList);
    }

    /**
     * Init migration table instance
     * @return void
     * @throws NimbleException
     */
    protected function initTable(): void
    {
        $this->migrationTable = new Table('migrations');

        if ($this->migrationTable->exists()) {
            return;
        }

        $createTable = new CreateTable();
        $createTable->setName('migrations');
        $createTable->addIdColumn();
        $createTable->addSimpleVarcharColumn('timestamp', 50);
        $createTable->addSimpleVarcharColumn('status');
        $createTable->addColumn((new Column('error'))->setType(ColumnType::text));
        $createTable->addDateModifyColumn();
        $createTable->addDateCreatedColumn();

        try {
            $createTable->execute();
        } catch (DatabaseManagerException $exception) {
            throw new NimbleException($exception->getHiddenMessage());
        }
    }

    /**
     * Connect database
     * @return void
     * @throws NimbleException
     */
    protected function connectDatabase(): void
    {
        try {
            $connect = DatabaseConnect::create();
            $connect->setType(DatabaseType::mysql);

            switch ($_ENV['DATABASE_TYPE']) {
                case 'mysql':
                    $connect->setType(DatabaseType::mysql);
                    $connect->setHost($_ENV['DATABASE_HOST']);
                    $connect->setDatabaseName($_ENV['DATABASE_NAME']);
                    $connect->setUsername($_ENV['DATABASE_USERNAME']);
                    $connect->setPassword($_ENV['DATABASE_PASSWORD']);
                    $connect->setPort($_ENV['DATABASE_PORT']);
                    break;
                case 'sqlite':
                    $connect->setType(DatabaseType::sqlite);
                    $connect->setSqlitePath(Kernel::$projectPath . DIRECTORY_SEPARATOR . $_ENV['DATABASE_PATH']);
                    break;
                default:
                    throw new DatabaseException('Invalid database type');
            }

            $connect->setCharset($_ENV['DATABASE_CHARSET']);

            $manager = new DatabaseManager();
            $manager->connect($connect);
        } catch (DatabaseManagerException $exception) {
            throw new NimbleException($exception->getHiddenMessage());
        }
    }

    /**
     * Project autoloader
     * @return void
     */
    protected function projectAutoloader(): void
    {
        spl_autoload_register(function ($className) {
            $className = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $className);
            $file = $this->projectPath . '/' . $className . '.php';

            if (file_exists($file)) {
                require($file);
            }
        });
    }

}