<?php

namespace Neoxia\GAE\Migrations;

use Illuminate\Database\DatabaseManager;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;

class MigrateController extends BaseController
{
    public function post(Application $application, DatabaseManager $databaseManager, Request $request, Config $config)
    {
        if ($request->input('token') !== $config->get('app.admin_token')) {
            return response('Unauthorized', 401);
        }

        $connection = $config->get('database.default');
        $this->confirmDatabaseExistence($databaseManager, $config, $connection);

        $result = "Database existence confirmed.\n";

        $migrator = $application['migrator'];
        $migrator->setConnection($connection);
        if (! $migrator->repositoryExists()) {
            $migrator->getRepository()->createRepository();
            $result .= "Migration table created successfully.\n";
        }

        $migrator->run(database_path() . '/migrations');
        $result .= str_replace(['<info>', '</info>'], '', implode("\n", $migrator->getNotes())) . "\n";

        return response($result, 200);
    }

    protected function confirmDatabaseExistence(DatabaseManager $databaseManager, Config $config, $default)
    {
        $database = $config->get('database.connections.' . $default . '.database');
        $config->set('database.connections.' . $default . '.database', null);
        $databaseManager->purge($default);
        $connection = $databaseManager->connection($default);
        $connection->statement("CREATE SCHEMA IF NOT EXISTS $database CHARSET 'utf8'");
        $databaseManager->purge($default);
        $config->set('database.connections.' . $default . '.database', $database);
    }
}

if (! function_exists('database_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string $path
     *
     * @return string
     */
    function base_path($path = '')
    {
        return getcwd();
    }
}
