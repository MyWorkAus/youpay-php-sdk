<?php

namespace YouPaySDK;


class Order
{
    /**
     * @var string|null
     */
    public $youpay_id = null;

    /**
     * @var string
     */
    public $order_id;

    /**
     * @var string
     */
    public $store_id;

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var array
     */
    public $order_items = [];

    /**
     * @var array
     */
    public $order_totals = [];

    /**
     * @var Receiver|null
     */
    public $receiver;

    /**
     * Handling, fees, discounts and other line item totals
     *
     * @var float|false
     */
    public $extra_fees;

    /**
     * @var float
     */
    public $sub_total;

    /**
     * @var float
     */
    public $total;

    /**
     * Static way to initialize the class
     *
     * @param array $fillable [order_id, store_id, title, order_items, extra_fees, sub_total, total]
     * @return Order
     */
    public static function create($fillable)
    {
        $self = new self();
        $self->order_id = $fillable['order_id'];
        $self->title = $fillable['title'];
        $self->sub_total = $fillable['sub_total'];
        $self->total = $fillable['total'];

        // Optional
        if ( ! empty( $fillable['store_id'] ) ) {
            $self->store_id = $fillable['store_id'];
        }
        if ( ! empty( $fillable['extra_fees'] ) ) {
            $self->extra_fees = $fillable['extra_fees'];
        }
        if ( ! empty( $fillable['order_items'] ) && is_array( $fillable['order_items'] ) ) {
            foreach ($fillable['order_items'] as $order_item) {
                $self->create_order_item($order_item);
            }
        }

        if ( ! empty( $fillable['order_totals'] ) && is_array( $fillable['order_totals'] ) ) {
            foreach ($fillable['order_totals'] as $order_total) {
                $self->create_order_item($order_total);
            }
        }

        if ( ! empty($fillable['receiver'])) {
            if (! $fillable['receiver'] instanceof Receiver) {
                $fillable['receiver'] = Receiver::create($fillable['receiver']);
            }
            $self->receiver = $fillable['receiver'];
        } else {
            throw new \Exception('Receiver not set.');
        }

        return $self;
    }

    /**
     * Create Order Item
     *
     * @param $item
     * @return $this
     */
    public function create_order_item($item)
    {
        if (! $item instanceof OrderItem) {
            $item = OrderItem::create($item);
        }

        $this->order_items[] = $item;

        return $this;
    }

    /**
     * Create Order Total
     *
     * @param $item
     * @return $this
     */
    public function create_order_total($item)
    {
        if (! $item instanceof OrderTotal) {
            $item = OrderTotal::create($item);
        }

        $this->order_totals[] = $item;

        return $this;
    }

    /**
     * Create Order Item
     *
     * @param OrderItem $order_item
     * @deprecated
     * @return $this
     */
    public function add_order_item($order_item)
    {
        $this->order_items[] = $order_item;

        return $this;
    }

    /**
     * Set ID. Allows for chaining.
     *
     * @param $id
     * @return $this
     */
    public function setYouPayID($id)
    {
        $this->youpay_id = $id;
        return $this;
    }

    /**
     * Set Store ID. Allows for chaining.
     *
     * @param $id
     * @return $this
     */
    public function setStoreID($id)
    {
        $this->store_id = $id;
        return $this;
    }
}
