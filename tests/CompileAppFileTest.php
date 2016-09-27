<?php

use Mockery as m;

class CompileAppFileTest extends PHPUnit_Framework_TestCase
{
    /** @var m\Mock|\Neoxia\GAE\Console\CompileAppFile */
    private $command;

    /** @var m\Mock|\Illuminate\Config\Repository */
    private $config;

    /** @var m\Mock|\Illuminate\View\Compilers\BladeCompiler */
    private $compiler;

    /** @var m\Mock|\Illuminate\Filesystem\Filesystem */
    private $files;

    /** @var m\Mock|\Illuminate\View\View */
    private $view;

    /** @var m\Mock|\Illuminate\View\Factory */
    private $viewFactory;

    protected function setUp()
    {
        parent::setUp();

        $this->config = m::mock(Illuminate\Config\Repository::class);
        $this->viewFactory = m::mock(Illuminate\View\Factory::class);
        $this->compiler = m::mock(Illuminate\View\Compilers\BladeCompiler::class);
        $this->files = m::mock(Illuminate\Filesystem\Filesystem::class);
        $this->view = m::mock(Illuminate\View\View::class);

        $inject = [$this->config, $this->files, $this->viewFactory, $this->compiler];
        $this->command = m::mock(Neoxia\GAE\Console\CompileAppFile::class . '[getView,info,error,argument]', $inject);
        $this->command->shouldReceive('getView')->andReturn($this->view);
    }

    public function testCompileAppFileWithDefaultArgs()
    {
        $this->command->shouldReceive('argument')->with('src')->andReturn('app.yaml');
        $this->command->shouldReceive('argument')->with('dest')->andReturn('app.yaml');
        $this->files->shouldReceive('isFile')->with('/app.yaml$/')->andReturn(true);
        $this->view->shouldReceive('render')->andReturn('Config data');
        $this->files->shouldReceive('put')->with('/.*app.yaml/', 'Config data');
        $this->command->shouldReceive('info')->with('app.yaml compiled!');

        $this->command->handle();
    }

    public function testCompileAppFileWithCustomFileSourceAndCustomDest()
    {
        $this->command->shouldReceive('argument')->with('src')->andReturn('app.blade.yaml');
        $this->command->shouldReceive('argument')->with('dest')->andReturn('app.yaml');
        $this->files->shouldReceive('isFile')->with('/app.blade.yaml$/')->andReturn('Config data');
        $this->view->shouldReceive('render')->andReturn('Config data');
        $this->files->shouldReceive('put')->with('/app.yaml$/', 'Config data');
        $this->command->shouldReceive('info')->with('app.yaml compiled!');

        $this->command->handle();
    }

    public function testCompileAppFileWithNonExistingFile()
    {
        $this->command->shouldReceive('argument')->with('src')->andReturn('app.yaml');
        $this->files->shouldReceive('isFile')->with('/.*app.yaml/')->andReturn(false);
        $this->command->shouldReceive('error')->with('Can\'t find app.yaml file');

        $this->command->handle();
    }

    public function testCompileAppFileWithRenderError()
    {
        $this->command->shouldReceive('argument')->with('src')->andReturn('app.yaml');
        $this->files->shouldReceive('isFile')->with('/.*app.yaml/')->andReturn(true);
        $this->view->shouldReceive('render')->andThrow('ErrorException', 'Undefined variable: DB_PASSWORD');
        $this->command->shouldReceive('error')->with('Render error: "Undefined variable: DB_PASSWORD"');

        $this->command->handle();
    }
}
