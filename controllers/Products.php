<?php namespace AWME\Stockist\Controllers;

use Backend\Classes\Controller;
use AWME\Stockist\Models\Product;
use BackendMenu;
use AWME\Stockist\Classes\ImportProducts;
use Redirect;
use Flash;
use Backend;

class Products extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController',
        'Backend\Behaviors\ImportExportController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $importExportConfig = 'config_import_export.yaml';


    public $requiredPermissions = [
        'awme.stockist.usage' 
    ];

    public $bodyClass = 'compact-container';
    protected $assetsPath = '/plugins/awme/stockist/assets';

    public function __construct()
    {
        parent::__construct();
        
        BackendMenu::setContext('AWME.Stockist', 'stockist', 'stockist-products');

        $this->addCss($this->assetsPath.'/css/modal-form.css');
        $this->addJs($this->assetsPath.'/js/product-form.js');
    }
}