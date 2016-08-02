<?php

namespace Neoxia\GAE\Migrations;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;

class MigrateController extends BaseController
{
    public function post(Application $app, Request $request, Config $config)
    {
        if ($request->input('token') !== $config->get('app.admin_token')) {
            return response('Unauthorized', 401);
        }

        $migrator = $app['migrator'];
        $result = '';

        if (! $migrator->repositoryExists()) {
            $migrator->getRepository()->createRepository();
            $result .= "Migration table created successfully.\n";
        }

        $migrator->run($app->databasePath() . '/migrations');
        $result .= str_replace(['<info>', '</info>'], '', implode("\n", $migrator->getNotes())) . "\n";

        return response($result, 200);
    }
}
