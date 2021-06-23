<?php

namespace YouPaySDK;


class OrderTotal
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var float
     */
    public $value;

    /**
     * @var int
     */
    public $sort_order;

    /**
     * Create Order Total
     *
     * @param array $fillable [
     *        string    title*,
     *        float     value*,
     *        int     sort_order,
     * ]
     * @return OrderTotal
     */
    public static function create($fillable)
    {
        $total = new self();
        $total->title = $fillable['title'];
        $total->value = $fillable['value'];

        // Option Fields
        if ( ! empty($fillable['sort_order']) ) {
            $total->sort_order = $fillable['sort_order'];
        }

        return $total;
    }
}