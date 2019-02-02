<?php

namespace Hammunima\Crudgen\Commands;

use Illuminate\Console\Command;
use File;

class CrudView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:view 
                            {name : name of view}
                            {--fields= : name of fields}
                            {--pk= : name of primaryKey}
                            {--template= : name of template}
                            {--layout= : define name of layout}
                            {--asset-path= : define path of asset}
                            {--dir= : directory of file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate File View';

    protected $directory = '';

    protected $viewDir = '';
    
    protected $optionChecked = 'true';

    protected $defaultNamespace = '';

    protected $templateName = '';

    protected $formFields = [];

    protected $pathOfAsset = '';

    protected $formFieldsHtml = '';

    protected $tableColumnJavascript = '';

    protected $tableColumn = '';

    protected $typeLookup = [
        'string' => 'text',
        'char' => 'text',
        'varchar' => 'text',
        'text' => 'textarea',
        'mediumtext' => 'textarea',
        'longtext' => 'textarea',
        'json' => 'textarea',
        'jsonb' => 'textarea',
        'binary' => 'textarea',
        'password' => 'password',
        'email' => 'email',
        'number' => 'number',
        'integer' => 'number',
        'bigint' => 'number',
        'mediumint' => 'number',
        'tinyint' => 'number',
        'smallint' => 'number',
        'decimal' => 'number',
        'double' => 'number',
        'float' => 'number',
        'date' => 'date',
        'datetime' => 'datetime-local',
        'timestamp' => 'datetime-local',
        'time' => 'time',
        'radio' => 'radio',
        'boolean' => 'radio',
        'enum' => 'select',
        'select' => 'select',
        'file' => 'file',
    ];
    
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
        $this->directory = ($this->option('dir')) ? $this->option('dir') . '/' : '';
        $this->templateName = ($this->option('template')) ? $this->option('template') : '';
        $this->layoutName = ($this->option('layout')) ? $this->option('layout') : '';
        $this->pathOfAsset = ($this->option('asset-path')) ? $this->option('asset-path') . '/' : '';
        
        $this->checkOption('pk');
        $this->checkOption('fields');

        if($this->optionChecked){

            $fields = $this->option('fields');
            $fieldsArray = explode(';', $fields);

            $this->formFields = [];
            $x = 0;
            foreach ($fieldsArray as $item) {
                $itemArray = explode('>>', $item);
                $this->formFields[$x]['name'] = trim($itemArray[0]);
                $this->formFields[$x]['type'] = trim($itemArray[1]);
                $x++;
            }

            foreach ($this->formFields as $item) {
                $this->formFieldsHtml .= $this->createField($item);

                $this->tableColumnJavascript .= $this->createTableColumnJs($item);
                
                $this->tableColumn .= $this->createTableColumn($item);
            }
            //create column menu
            $this->tableColumn .= $this->createTableColumnMenu();

            //create button save
            $this->formFieldsHtml .= $this->createButtonSave($name);

            //create column menu in JS
            $this->tableColumnJavascript .= $this->createTableColumnMenuJs();

            $stub = $this->getStub('View');
        
            $this->replaceClassName($stub, ucfirst($name))
                        ->replaceVarName($stub, $name)
                        ->replaceTableColumn($stub, $this->tableColumn)
                        ->replaceTableColumnJavascript($stub, $this->tableColumnJavascript)
                        ->replaceFormField($stub, $this->formFieldsHtml)
                        ->replaceLayoutName($stub, $this->layoutName)
                        ->replacePathOfAsset($stub, $this->pathOfAsset)
                        ->createFile($name, $stub);

            $this->Info('View ' . ucfirst($name) . ' has been successfully added');

        }
        
    }

    protected function getStub($type){
        if($this->templateName == ''){
            $tmp = '';
        }else{
            $tmp = '_' . $this->templateName;
        }
        return file_get_contents(resource_path("stubs/$type" . $tmp . ".stub"));
    }

    protected function getFormStub($type){
        if($this->templateName == ''){
            $tmp = '';
        }else{
            $tmp = '/' . $this->templateName;
        }
        return file_get_contents(resource_path("stubs/views" . $tmp . "/$type.stub"));
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

    protected function createFile($name, $template){
        if(!File::isDirectory(resource_path("views/{$this->directory}"))){
            File::makeDirectory(resource_path("views/{$this->directory}"), 0755, true);
        }
        
        file_put_contents(resource_path("views/{$this->directory}/{$name}.blade.php"), $template);
    }
    
    protected function replaceVarName(&$stub, $varName){
        $stub = str_replace('<<varName>>', $varName, $stub);
        return $this;
    }

    protected function replaceClassName(&$stub, $className){
        $stub = str_replace('<<className>>', $className, $stub);
        return $this;
    }

    protected function replaceTableColumn(&$stub, $tableColumn){
        $stub = str_replace('<<tableColumn>>', $tableColumn, $stub);
        return $this;
    }
    
    protected function replaceTableColumnJavascript(&$stub, $tableColumnJavascript){
        $stub = str_replace('<<tableColumnJavascript>>', $tableColumnJavascript, $stub);
        return $this;
    }

    protected function replaceFormField(&$stub, $formField){
        $stub = str_replace('<<formField>>', $formField, $stub);
        return $this;
    }

    protected function replacePathOfAsset(&$stub, $path){
        $stub = str_replace('<<pathOfAsset>>', $path, $stub);
        return $this;
    }

    protected function replaceLayoutName(&$stub, $name){
        $stub = str_replace('<<layoutName>>', $name, $stub);
        return $this;
    }

    protected function wrapField($item, $field)
    {
        if($this->templateName == ''){
            $tmp = '';
        }else{
            $tmp = '_' . $this->templateName;
        }
        $inputForm = $this->getFormStub('wrap-div' . $tmp . '.blade');
        return sprintf($inputForm, $item['name'], $field);
    }

    protected function createField($item)
    {
        if($this->templateName == ''){
            $tmp = '';
        }else{
            $tmp = '_' . $this->templateName;
        }
        switch ($this->typeLookup[$item['type']]) {
            case 'password':
                return $this->createPasswordField($item);
            case 'datetime-local':
            case 'time':
                return $this->createInputField($item);
            case 'radio':
                return $this->createRadioField($item);
            case 'textarea':
                return $this->createTextareaField($item);
            case 'select':
            case 'enum':
                return $this->createSelectField($item);
            default: // text
                return $this->createFormField($item, 'text-field' . $tmp . '.blade');
        }
    }

    protected function createTableColumnJs($item)
    {
        $fields = "{data: '" . $item['name'] . "', name: '" . $item['name'] . "'},";
        return $fields;
    }

    protected function createTableColumnMenuJs()
    {
        $fields = "{data: 'menu', orderable: false, searchable: false}";
        return $fields;
    }

    protected function createTableColumn($item)
    {
        $fields = "<td>" . ucfirst($item['name']) . "</td>";
        return $fields;
    }

    protected function createTableColumnMenu()
    {
        $fields = "<td>Menu</td>";
        return $fields;
    }

    protected function createFormField($item, $file)
    {
        $inputForm = $this->getFormStub($file);
        $inputForm = str_replace('<<itemName>>', $item['name'], $inputForm);
        $inputForm = str_replace('<<fieldType>>', $this->typeLookup[$item['type']], $inputForm);
        return $this->wrapField(
            $item,
            $inputForm
        );
    }

    protected function createPasswordField($item)
    {
        $start = $this->delimiter[0];
        $end = $this->delimiter[1];
        $required = $item['required'] ? 'required' : '';
        $markup = File::get($this->viewDirectoryPath . 'form-fields/password-field.blade.stub');
        $markup = str_replace($start . 'required' . $end, $required, $markup);
        $markup = str_replace($start . 'itemName' . $end, $item['name'], $markup);
        $markup = str_replace($start . 'crudNameSingular' . $end, $this->crudNameSingular, $markup);
        return $this->wrapField(
            $item,
            $markup
        );
    }

    protected function createInputField($item)
    {
        $start = $this->delimiter[0];
        $end = $this->delimiter[1];
        $required = $item['required'] ? 'required' : '';
        $markup = File::get($this->viewDirectoryPath . 'form-fields/input-field.blade.stub');
        $markup = str_replace($start . 'required' . $end, $required, $markup);
        $markup = str_replace($start . 'fieldType' . $end, $this->typeLookup[$item['type']], $markup);
        $markup = str_replace($start . 'itemName' . $end, $item['name'], $markup);
        $markup = str_replace($start . 'crudNameSingular' . $end, $this->crudNameSingular, $markup);
        return $this->wrapField(
            $item,
            $markup
        );
    }

    protected function createRadioField($item)
    {
        $start = $this->delimiter[0];
        $end = $this->delimiter[1];
        $markup = File::get($this->viewDirectoryPath . 'form-fields/radio-field.blade.stub');
        $markup = str_replace($start . 'itemName' . $end, $item['name'], $markup);
        $markup = str_replace($start . 'crudNameSingular' . $end, $this->crudNameSingular, $markup);
        return $this->wrapField(
            $item,
            $markup
        );
    }

    protected function createTextareaField($item)
    {
        $start = $this->delimiter[0];
        $end = $this->delimiter[1];
        $required = $item['required'] ? 'required' : '';
        $markup = File::get($this->viewDirectoryPath . 'form-fields/textarea-field.blade.stub');
        $markup = str_replace($start . 'required' . $end, $required, $markup);
        $markup = str_replace($start . 'fieldType' . $end, $this->typeLookup[$item['type']], $markup);
        $markup = str_replace($start . 'itemName' . $end, $item['name'], $markup);
        $markup = str_replace($start . 'crudNameSingular' . $end, $this->crudNameSingular, $markup);
        return $this->wrapField(
            $item,
            $markup
        );
    }

    protected function createSelectField($item)
    {
        $start = $this->delimiter[0];
        $end = $this->delimiter[1];
        $required = $item['required'] ? 'required' : '';
        $markup = File::get($this->viewDirectoryPath . 'form-fields/select-field.blade.stub');
        $markup = str_replace($start . 'required' . $end, $required, $markup);
        $markup = str_replace($start . 'options' . $end, $item['options'], $markup);
        $markup = str_replace($start . 'itemName' . $end, $item['name'], $markup);
        $markup = str_replace($start . 'crudNameSingular' . $end, $this->crudNameSingular, $markup);
        return $this->wrapField(
            $item,
            $markup
        );
    }

    protected function createButtonSave($name)
    {
        if($this->templateName == ''){
            $tmp = '';
        }else{
            $tmp = '_' . $this->templateName;
        }
        $inputForm = $this->getFormStub('button-save' . $tmp . '.blade');
        $inputForm = str_replace('<<varName>>', $name, $inputForm);
        return $inputForm;
    }
}
