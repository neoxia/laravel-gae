<?php

namespace Neoxia\GAE\Console;

use ErrorException;
use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\View;

class CompileAppFile extends Command
{
    protected $signature = 'gae:compile-app-file {--src=app.yaml} {--dest=app.yaml} {--target=master}';

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
        $src = base_path() . '/' . trim($filename = $this->option('src'), '/');

        if (! $this->files->isFile($src)) {
            return $this->error("Cannot find $filename file.");
        }

        $env = $this->getEnv();
        $view = $this->getView($src, $env);

        try {
            $content = $view->render();
        } catch (ErrorException $e) {
            return $this->error('Render error: "' . $e->getMessage() . '"');
        }

        $this->files->put(base_path() . '/' . trim($dest = $this->option('dest'), '/'), $content);

        return $this->info("$dest compiled!");
    }

    public function error($message, $verbosity = null)
    {
        parent::error($message);

        return 1;
    }

    private function getEnv()
    {
        $env = $_SERVER;
        $target = Str::upper($this->option('target'));

        foreach ($env as $key => $value) {
            if (! Str::contains($key, '__')) {
                continue;
            }

            if (Str::startsWith($key, $target . '__')) {
                $env[Str::substr($key, Str::length($target) + 2)] = $value;
            }

            unset($env[$key]);
        }

        return $env;
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
     * @param  string $path
     *
     * @return string
     */
    function base_path($path = '')
    {
        return getcwd();
    }
}
