<?php

namespace Hammunima\Crudgen\Commands;

use Illuminate\Console\Command;

class CrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:crud 
                            {name : name of controller} 
                            {--dir= : directory namespace}
                            {--pk= : column of primary key}
                            {--fields= : name of fields}
                            {--template= : name of template}
                            {--layout= : define name of layout}
                            {--asset-path= : define path of asset}
                            {--route= : name of route file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $routeName = '';

    protected $directory = '';

    protected $templateName = '';

    protected $pathOfAsset = '';

    protected $optionChecked = 'true';

    protected $pk = '';
    
    protected $fields = '';

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

        $this->routeName = ($this->option('route')) ? $this->option('route') : 'web';
        $this->directory = ($this->option('dir')) ? $this->option('dir') : '';
        $this->templateName = ($this->option('template')) ? $this->option('template') : '';
        $this->directory = ($this->option('dir')) ? $this->option('dir') : '';
        $this->layoutName = ($this->option('layout')) ? $this->option('layout') : '';
        $this->pathOfAsset = ($this->option('asset-path')) ? $this->option('asset-path') : '';

        $this->checkOption('pk');
        $this->checkOption('fields');
        if($this->optionChecked){
            $this->fields = $this->option('fields');
            $this->pk = $this->option('pk');

            $this->call('crud:controller', ['name' => $name, '--dir' => $this->directory, '--route' => $this->routeName]);
            $this->call('crud:model', ['name' => $name, '--pk' => $this->pk]);
            $this->call('generate:view', ['name' => $name, '--dir' => $this->directory, '--fields' => $this->fields, '--pk' => $this->pk, '--template' => $this->templateName, '--layout' => $this->layout, '--asset-path' => $this->pathOfAsset]);
            $this->info('Success...... ');
        }else{
            $this->error('Error......!!!');
        }
    }

    protected function checkOption($option){
        if($this->option($option)){
            if($this->optionChecked == true){
                $this->optionChecked = true;
            }
        }else{
            $this->error('Option --' . $option . ' is required...');
            $this->optionChecked = false;
        }
        return $this;
    }
}
