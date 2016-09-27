<?php

namespace Neoxia\GAE\Console;

use Illuminate\Console\Command;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\View;
use ErrorException;

class CompileAppFile extends Command
{
    protected $signature = 'gae:compile-app-file {src=app.yaml} {dest=app.yaml}';
    protected $description = 'Set variables in app.yaml file';

    protected $config;
    protected $files;
    protected $compiler;
    protected $viewFactory;

    public function __construct(Config $config, Filesystem $files, ViewFactory $viewFactory, BladeCompiler $compiler)
    {
        parent::__construct();

        $this->config = $config;
        $this->files = $files;
        $this->compiler = $compiler;
        $this->viewFactory = $viewFactory;
    }

    public function handle()
    {
        $src = base_path() . $this->argument('src');

        if (! $this->files->isFile($src)) {
            return $this->error('Can\'t find app.yaml file');
        }

        $view = $this->getView($src, $_SERVER);

        try {
            $content = $view->render();
        } catch (ErrorException $e) {
            return $this->error('Render error: "' . $e->getMessage() . '"');
        }

        $this->files->put($this->argument('dest'), $content);

        return $this->info('app.yaml compiled!');
    }

    public function getView($path, $data)
    {
        $engine = new CompilerEngine($this->compiler);

        return new View($this->viewFactory, $engine, $path, $path, $data);
    }
}

if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string  $path
     * @return string
     */
    function base_path($path = '')
    {
        return getcwd();
    }
}
