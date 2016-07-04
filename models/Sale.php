<?php namespace AWME\Stockist\Models;

use Flash;
use Model;
use Request;
use BackendAuth;

use AWME\Stockist\Classes\Calculator as Calc;

use AWME\Stockist\Models\Sale;
use AWME\Stockist\Models\Settings;
use AWME\Stockist\Models\SaleProduct;

/**
 * Model
 */
class Sale extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awme_stockist_sales';
    
    protected $jsonable = ['tax'];
    
    /*
     * Validation
     */
    protected $rules = [
        'fullname' => ['between:2,255'],
        'invoice' => [
            'between:1,25',
            'unique:awme_stockist_sales'
        ],
        'dni' => ['digits_between:6,16','numeric'],
        'phone' => ['digits_between:6,16','numeric'],
        'email' => ['email'],
    ];


    /**
     * @var array Relations
     */
    public $belongsTo = [
        'seller' => [
            'Backend\Models\User',
            'key' => 'seller_id',
        ],
    ];
    public $belongsToMany = [
        'products_pivot_model' => [
            'AWME\Stockist\Models\Product',
            'table' => 'awme_stockist_sales_products',
            'key'   => 'sale_id',
            'pivot' => ['quantity','price','subtotal'],
            'timestamps' => true,
            'pivotModel' => 'AWME\Stockist\Models\SaleProductPivot',
        ],
        'pay_methods' => [
            'AWME\Stockist\Models\PayMethod',
            'table' => 'awme_stockist_sales_pay_methods',
            'key'   => 'sale_id',
            'pivot' => ['concept','total'],
            'timestamps' => true,
            'pivotModel' => 'AWME\Stockist\Models\SalePayMethodPivot',
        ],
        
    ];


/**
 * ===================================
 * EVENTS
 * ===================================
 *
 */

    public function beforeSave()
    {
        $this->setTaxes();      # Guarda los Sale.tax fields.
        $this->setSubtotal();   # Operación del subtotal de la venta.
        $this->setTotal();      # Operación del total de la venta.

        $this->seller_id = BackendAuth::getUser()->id;
    }

    public function afterDelete()
    {
        SaleProduct::where('sale_id', $this->id)->delete();
    }


/**
 * ===================================
 * FUNCTIONS
 * ===================================
 *
 */
    /**
     * setTaxes()
     * Aplica el tax attr segun "taxes %/$"
     * para aplicar al subtotal
     */
    public function setTaxes()
    {   
        $taxes = Request::input('Sale.tax');
        $this->tax = $taxes;
    }


    /**
     * setSaleSubtotal()
     * Aplica el subtotal de la venta.
     * Operación (suma $sale_prices)
     */
    public function setSubtotal()
    {   
        $sales = SaleProduct::where('sale_id', $this->id)->get()->toArray();
        $sale_prices = array_column($sales, 'subtotal');

        $this->subtotal = Calc::suma($sale_prices);
    }

    /**
     * setTotal()
     * setea el Total de la venta
     * Sumatoria y operación final de la venta
     * 
     * @return $this->total 
     */
    public function setTotal()
    {
        $total = $this->subtotal;

        if($this->tax['type'] == "$"){
            
            $total = Calc::suma([$this->subtotal, $this->tax['amount']]); 

        }else if($this->tax['type'] == "%"){
            $total = Calc::suma([$this->subtotal, Calc::percent($this->tax['amount'], $this->subtotal)]); 
        }

        $this->total = $total;
    }


/**
 * ===================================
 * SCOPES
 * ===================================
 *
 */
    public function scopeShowToday($query)
    {
        $show = date("Y-m-d");
        $date = $show.' 00:00:00';
        return $query->where('created_at','>=', $date);
    }


    public function scopeShowDate($query)
    {

        $show = date("Y-m-d");
        $date = $show.' 00:00:00';
        return $query->where('created_at','>=', $date);
    }

}