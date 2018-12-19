<?php

namespace App\Console\Commands;

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
    protected $description = 'Generate File Controller';

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

        $this->controller($name);

        File::append(base_path('routes/web.php'), "Route::get('" . $name . "/{id?}', ['uses' => '{$this->defaultNamespace}{$name}Controller@{$name}Link', 'as' => '{$name}-link']);
            Route::get('" . $name . "-data', ['uses' => '{$this->defaultNamespace}{$name}Controller@{$name}Data', 'as' => '{$name}-data']);
            Route::post('" . $name . "-simpan', ['uses' => '{$this->defaultNamespace}{$name}Controller@{$name}Simpan', 'as' => '{$name}-simpan']);
            Route::post('" . $name . "-hapus', ['uses' => '{$this->defaultNamespace}{$name}Controller@{$name}Hapus', 'as' => '{$name}-hapus']);
            ");

    }

    protected function getStub($type){
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    protected function controller($name){
        $controllerTemplate = str_replace([
            '{{modelName}}',
            '{{pathView}}',
        ],
        [
            $name,
            $this->viewDir . $name,
        ],
        $this->getStub('Controller'));

        if(!File::isDirectory(app_path("/Http/Controllers/{$this->controllerDir}"))){
            File::makeDirectory(app_path("/Http/Controllers/{$this->controllerDir}"), 0755, true);
        }
        
        file_put_contents(app_path("/Http/Controllers/{$this->controllerDir}{$name}Controller.php"), $controllerTemplate);
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
}
