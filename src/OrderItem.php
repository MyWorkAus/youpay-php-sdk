<?php

namespace YouPaySDK;


class OrderItem
{
    /**
     * @var string
     */
    public $product_id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string Product Image URL.
     */
    public $src;

    /**
     * @var string Product Image URL.
     */
    public $order_item_id;

    /**
     * @var array
     */
    public $variants = [];

    /**
     * @var array
     */
    public $variants_display = '';

    /**
     * @var float
     */
    public $price;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var float
     */
    public $total;

    /**
     * Create Order Items
     *
     * @param array $fillable [
     *        string    title*,
     *        string    product_id*,
     *        string    src*,
     *        float     price*,
     *        int       quantity*,
     *        float     total*,
     *        string    description,
     *        array     variants,
     *        string    variants_display
     * ]
     * @return OrderItem
     */
    public static function create($fillable)
    {
        $item = new self();
        $item->order_item_id = $fillable['order_item_id'];
        $item->product_id = $fillable['product_id'];
        $item->title = $fillable['title'];
        $item->src = $fillable['src'];
        $item->price = $fillable['price'];
        $item->quantity = $fillable['quantity'];
        $item->total = $fillable['total'];

        // Option Fields
        if ( ! empty($fillable['description']) ) {
            $item->description = $fillable['description'];
        }
        if ( ! empty($fillable['variants'])) {
            $item->variants = $fillable['variants'];
        }
        if ( ! empty($fillable['variants_display'])) {
            $item->variants_display = $fillable['variants_display'];
        }

        return $item;
    }
}