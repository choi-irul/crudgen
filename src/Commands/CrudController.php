<?php

namespace Hammunima\Crudgen\Command;

use Illuminate\Console\Command;
use File;

class CrudController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:controller 
                            {name : name of controller} 
                            {--dir= : directory namespace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate File Controller...';

    protected $directory = '';

    protected $viewDir = '';
    
    protected $controllerDir = '';

    protected $defaultNamespace = '';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->directory = ($this->option('dir')) ? $this->option('dir') : '';
        $this->getControllerDirectory();
        $this->getViewDirectory();
        $this->getDefaultNamespace();

        $stub = $this->getStub('Controller');
        
        $this->replaceClassName($stub, ucfirst($name))
            ->replaceFunctionName($stub, $name)
            ->replaceVarName($stub, $name)
            ->replacePathView($stub, $this->viewDir . $name)
            ->createRoute($name)
            ->createFile(ucfirst($name), $stub);
    }

    protected function getStub($type){
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    protected function createRoute($name){
        File::append(base_path('routes/web.php'), "Route::get('" . $name . "/{id?}', ['uses' => '{$this->defaultNamespace}{$name}Controller@{$name}Link', 'as' => '{$name}-link']);
            Route::get('" . $name . "-data', ['uses' => '{$this->defaultNamespace}{$name}Controller@{$name}Data', 'as' => '{$name}-data']);
            Route::post('" . $name . "-simpan', ['uses' => '{$this->defaultNamespace}{$name}Controller@{$name}Simpan', 'as' => '{$name}-simpan']);
            Route::post('" . $name . "-hapus', ['uses' => '{$this->defaultNamespace}{$name}Controller@{$name}Hapus', 'as' => '{$name}-hapus']);
            ");
        $this->Info('Route ' . ucfirst($name) . ' has been successfully added');
        return $this;
    }

    protected function createFile($name, $template){
        if(!File::isDirectory(app_path("/Http/Controllers/{$this->controllerDir}"))){
            File::makeDirectory(app_path("/Http/Controllers/{$this->controllerDir}"), 0755, true);
        }
        
        file_put_contents(app_path("/Http/Controllers/{$this->controllerDir}{$name}Controller.php"), $template);

        $this->Info('Controller ' . ucfirst($name) . ' has been successfully added');
    }

    protected function getControllerDirectory(){
        if($this->directory != ''){
            $this->controllerDir = $this->directory . '/';
        }
        return $this;
    }

    protected function getViewDirectory(){
        if($this->directory != ''){
            $this->viewDir = $this->directory . '.';
        }
        return $this;
    }

    protected function getDefaultNamespace(){
        if($this->directory != ''){
            $this->defaultNamespace = $this->directory . '\\';
        }
        return $this;
    }

    protected function replaceClassName(&$stub, $className){
        $stub = str_replace('{{className}}', $className, $stub);
        return $this;
    }

    protected function replaceFunctionName(&$stub, $functionName){
        $stub = str_replace('{{functionName}}', $functionName, $stub);
        return $this;
    }
    
    protected function replaceVarName(&$stub, $varName){
        $stub = str_replace('{{varName}}', $varName, $stub);
        return $this;
    }

    protected function replacePathView(&$stub, $pathView){
        $stub = str_replace('{{pathView}}', $pathView, $stub);
        return $this;
    }
}
