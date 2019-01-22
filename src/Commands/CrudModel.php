<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class CrudModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:model 
                            {name : name of model}
                            {--pk= : column of primary key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate File Model';

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
        $primaryKey = $this->option('pk');

        $stub = $this->getStub('Model');
        
        $this->replaceClassName($stub, ucfirst($name))
                                ->replaceTableName($stub, $name)
                                ->replacePrimaryKey($stub, $primaryKey)
                                ->createFile(ucfirst($name), $stub);

        $this->Info('Model ' . ucfirst($name) . ' has been successfully added');

    }

    protected function getStub($type){
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    protected function createFile($name, $template){
        if(!File::isDirectory(app_path("/Models"))){
            File::makeDirectory(app_path("/Models"), 0755, true);
        }
        
        file_put_contents(app_path("/Models/{$name}.php"), $template);
    }
    
    protected function replaceClassName(&$stub, $className){
        $stub = str_replace('{{className}}', $className, $stub);
        return $this;
    }

    protected function replacePrimaryKey(&$stub, $primaryKey){
        $stub = str_replace('{{primaryKey}}', $primaryKey, $stub);
        return $this;
    }
    
    protected function replaceTableName(&$stub, $tableName){
        $stub = str_replace('{{tableName}}', $tableName, $stub);
        return $this;
    }
}
