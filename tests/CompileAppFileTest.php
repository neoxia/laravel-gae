<?php

use Neoxia\GAE\Console\CompileAppFile;
use Illuminate\Support\Collection;
use Mockery as m;

class CompileAppFileTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->config = m::mock('Illuminate\Config\Repository');
        $this->viewFactory = m::mock('Illuminate\View\Factory');
        $this->compiler = m::mock('Illuminate\View\Engines\CompilerEngine');
        $this->files = m::mock('Illuminate\Filesystem\Filesystem');
        $this->view = m::mock('Illuminate\View\View');

        $this->command = m::mock('Neoxia\GAE\Console\CompileAppFile[getView,info,error]', [$this->config, $this->files, $this->viewFactory, $this->compiler]);
        $this->command->shouldReceive('getView')->andReturn($this->view);
    }

    public function testCompileAppFile()
    {
        $this->files->shouldReceive('isFile')->with('/.*app.yaml/')->andReturn(true);
        $this->view->shouldReceive('render')->andReturn('Config data');
        $this->files->shouldReceive('put')->with('/.*app.yaml/', 'Config data');
        $this->command->shouldReceive('info')->with('app.yaml compiled!');

        $this->command->handle();
    }

    public function testTryToCompilerAppFileIfFileDoesntExist()
    {
        $this->files->shouldReceive('isFile')->with('/.*app.yaml/')->andReturn(false);
        $this->command->shouldReceive('error')->with('Can\'t find app.yaml file');

        $this->command->handle();
    }

    public function testTryToCompilerAppFileIfErrorDuringRender()
    {
        $this->files->shouldReceive('isFile')->with('/.*app.yaml/')->andReturn(true);
        $this->view->shouldReceive('render')->andThrow('ErrorException', 'Undefined variable: DB_PASSWORD');
        $this->command->shouldReceive('error')->with('Render error: "Undefined variable: DB_PASSWORD"');

        $this->command->handle();
    }
}
