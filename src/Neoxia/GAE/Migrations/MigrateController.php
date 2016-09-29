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

        $this->confirmDatabaseExistence($databaseManager, $config);
        $result = 'Database existence confirmed.';

        $migrator = $application['migrator'];
        if (! $migrator->repositoryExists()) {
            $migrator->getRepository()->createRepository();
            $result .= "Migration table created successfully.\n";
        }

        $migrator->run(database_path() . '/migrations');
        $result .= str_replace(['<info>', '</info>'], '', implode("\n", $migrator->getNotes())) . "\n";

        return response($result, 200);
    }

    protected function confirmDatabaseExistence(DatabaseManager $databaseManager, Config $config)
    {
        $default = $config->get('database.default');
        $database = $config->get('database.' . $default . '.database');
        $config->set('database.' . $default . '.database', null);
        $databaseManager->purge();
        $connection = $databaseManager->connection($default);
        $connection->statement("CREATE SCHEMA IF NOT EXISTS $database CHARSET utf8");
        $databaseManager->purge();
        $config->set('database.' . $default . '.database', $database);
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
