<?php

namespace Neoxia\GAE\Console;

use Illuminate\Console\Command;
use Illuminate\Config\Repository as Config;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Filesystem\Filesystem;

class CompileViews extends Command
{
    protected $signature = 'gae:compile-views {--keep-files}';
    protected $description = 'Compile all the blade templates';

    protected $config;
    protected $files;
    protected $compiler;

    public function __construct(Config $config, Filesystem $files, BladeCompiler $compiler)
    {
        parent::__construct();

        $this->config = $config;
        $this->files = $files;
        $this->compiler = $compiler;
    }

    public function handle()
    {
        $viewsPath = $this->config->get('view.paths.0');
        $views = $this->files->allFiles($viewsPath);

        foreach ($views as $view) {
            $path = $view->getPathName();
            $content = $this->files->get($path);
            $contentCompiled = $this->compiler->compileString($content);

            $target = str_replace('.blade', '', $path);
            if ($this->option('keep-files')) {
                $this->files->put($target, $contentCompiled);
            } else {
                $this->files->put($path, $contentCompiled);
                $this->files->move($path, $target);
            }
        }

        $this->info('Blade templates compiled!');
    }
}
