<?php

use Neoxia\GAE\Console\CompileViews;
use Mockery as m;

class CompileViewsTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->config = m::mock('Illuminate\Config\Repository');
        $this->compiler = m::mock('Illuminate\View\Compilers\BladeCompiler');
        $this->files = m::mock('Illuminate\Filesystem\Filesystem');
        $this->viewFile = m::mock('SplFileInfo');

        $this->command = m::mock('Neoxia\GAE\Console\CompileViews[info]', [$this->config, $this->files, $this->compiler]);
    }

    public function testCompileViews()
    {
        $this->config->shouldReceive('get')->with('view.paths.0')->andReturn('view/path');
        $this->files->shouldReceive('allFiles')->with('view/path')->andReturn([$this->viewFile]);
        $this->viewFile->shouldReceive('getPathname')->andReturn('view/path/index.blade.php');
        $this->files->shouldReceive('get')->with('view/path/index.blade.php')->andReturn('Hello {{ $world }}');
        $this->compiler->shouldReceive('compileString')->with('Hello {{ $world }}')->andReturn('Hello <?php echo $world ?>');
        $this->files->shouldReceive('put')->with('view/path/index.blade.php', 'Hello <?php echo $world ?>');
        $this->files->shouldReceive('move')->with('view/path/index.blade.php', 'view/path/index.php');
        $this->command->shouldReceive('info')->with('Blade templates compiled!');

        $this->command->handle();
    }
}
