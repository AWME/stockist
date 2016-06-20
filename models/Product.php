<?php namespace AWME\Stockist\Models;

use Model;

/**
 * Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'categories'    => ['AWME\Stockist\Models\Category', 'key' => 'category_id'],
    ];

    public $belongsToMany = [
        'products' => ['AWME\Stockist\Models\Product', 'table' => 'awme_octomain_sales_products']
    ];



    /**
     * @var array Validation rules
     */
    protected $rules = [
        'name' => ['required', 'between:1,255'],
        'sku' => ['required', 'unique:awme_stockist_products'],
        'slug' => [
            'unique:awme_stockist_products',
            'required',
            'alpha_dash',
            'between:1,255',
        ],
        'stock' => ['required','numeric'],
        'price_cost' => ['required','numeric','min:00.10', 'max:99999999.99'],
        'price_sale' => ['required','numeric','min:00.10', 'max:99999999.99'],
        'iva' => ['numeric', 'max:99.99'],
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awme_stockist_products';
}