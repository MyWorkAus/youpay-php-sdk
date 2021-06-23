<?php
namespace YouPaySDK;

/**
 * YouPay Client
 *
 * @package YouPaySDK
 */
class Client
{
    /**
     * @var string API Token
     */
    private $token;

    /**
     * @var string Store ID
     */
    private $store_id;

    /**
     * @var string API Url
     */
    public $api_url = 'https://v1.youpay.ai/';

    /**
     * @var string APP Url
     */
    public $app_url = 'https://app.youpay.ai/';

    public function __construct()
    {
        if (defined('YOUPAY_API_URL')) {
            $this->api_url = YOUPAY_API_URL;
        }
        if (defined('YOUPAY_APP_URL')) {
            $this->app_url = YOUPAY_APP_URL;
        }
    }

    /**
     * Create the client
     *
     * @param string $token
     * @param string $store_id
     * @return Client
     */
    public static function create($token, $store_id = '')
    {
        $self = new self();
        return $self
            ->setToken($token)
            ->setStoreID($store_id);
    }

    /**
     * Static method for login and API Token generation
     *
     * This will create the store if it doesn't already exist
     *
     * @see login
     * @param string $email
     * @param string $password
     * @param string $domain
     * @param string $store_type
     *
     * @return object [status_code, access_token, store_id] || [status_code, message, error]
     * @throws \Exception Bad Data.
     */
    public static function auth($email, $password, $domain, $store_type)
    {
        $self = new self();
        return $self->login($email, $password, $domain, $store_type);
    }

    /**
     * Login and get API Token
     *
     * This will create the store if it doesn't already exist
     *
     * @param $email
     * @param $password
     * @param $domain
     * @param $store_type
     *
     * @return object [status_code, access_token, store_id] || [status_code, message, error]
     * @throws \Exception Bad Data.
     */
    public function login($email, $password, $domain, $store_type)
    {
        return json_decode(
            $this->client()->post('/v1/login', [
                'json' => [
                    'email'      => $email,
                    'password'   => $password,
                    'domain'     => $domain,
                    'store_type' => $store_type
                ]
            ])->getBody()->getContents()
        );
    }

    /**
     * Fetch Bearer Token
     *
     * @param $email
     * @param $password
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchToken($email, $password)
    {
        return json_decode(
            $this->client()->post('/v1/fetch-token', [
                'json' => [
                    'email'      => $email,
                    'password'   => $password
                ]
            ])->getBody()->getContents()
        );
    }

    /**
     * List All Store Orders
     *
     * @param string
     * @return array
     * @throws \Exception Bad Response Exception.
     */
    public function listOrders($limit = 10)
    {
        return $this->handleResponse(
            $this->client()->get('/v1/order?store_id=' . $this->store_id . '&limit=' . $limit)
        );
    }

    /**
     * List All Store Orders
     *
     * @return array
     * @throws \Exception Bad Response Exception.
     */
    public function listAllOrders($limit = 10)
    {
        return $this->handleResponse(
            $this->client()->post('/v1/orders?limit=' . $limit)
        );
    }

	/**
	 * List All Store Payments
	 *
	 * @param string
	 * @return array
	 * @throws \Exception Bad Response Exception.
	 */
	public function listPayments($limit = 10)
	{
		return $this->handleResponse(
			$this->client()->get('/v1/payments?limit=' . $limit)
		);
	}

	/**
	 * List All Store Payments
	 *
	 * @return array
	 * @throws \Exception Bad Response Exception.
	 */
	public function listAllPayments($limit = 10)
	{
		return $this->handleResponse(
			$this->client()->get('/v1/payments?limit=' . $limit)
		);
	}

    /**
     * Create or Update an Order
     *
     * @param Order $order
     * @param mixed $params Extra data to pass to the request
     * @return mixed
     * @deprecated
     * @throws \Exception Bad Response Exception.
     */
    public function postOrder(Order $order, $params = null)
    {
        if ($order->youpay_id) {
            return  $this->updateOrder($order, $params);
        }
        return $this->createOrder($order, $params);
    }

    /**
     * Create a new order
     *
     * @param  Order  $order
     * @param  null  $params
     *
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrder(Order $order, $params = null)
    {
        return $this->handleResponse(
            $this->client()
                 ->post('/v1/order', [
                     'json' => [
                         'order' => $order,
                         'store_id' => $this->store_id,
                         'params' => $params
                     ]
                 ])
        );
    }

    /**
     * Update existing order
     *
     * @param  Order  $order
     * @param  null  $params
     *
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateOrder(Order $order, $params = null)
    {
        return $this->handleResponse(
            $this->client()
                 ->put("/v1/order/$order->youpay_id", [
                     'json' => [
                         'order' => $order,
                         'store_id' => $this->store_id,
                         'params' => $params
                     ]
                 ])
        );
    }

    /**
     * Get the Order by ID
     *
     * @param $id
     * @return mixed
     * @throws \Exception Bad Response Exception.
     */
    public function getOrder($id)
    {
        return $this->handleResponse(
            $this->client()->get('/v1/order/' . $id)
        );
    }

	/**
	 * Cancel Order
	 *
	 * @param $id
	 *
	 * @return mixed|null
	 * @throws \Exception
	 */
    public function cancelOrder($id)
    {
	    return $this->handleResponse(
		    $this->client()->get('/v1/order/' . $id . '/cancel')
	    );
    }

    /**
     * Get the Store by ID
     *
     * @param $id
     * @return mixed
     * @throws \Exception Bad Response Exception.
     */
    public function getStore($id = false)
    {
    	if (!$id) {
    		$id = $this->store_id;
	    }
        return $this->handleResponse(
            $this->client()->get('/v1/store/' . $id)
        );
    }

    /**
     * Get the Stores
     *
     * @return mixed
     * @throws \Exception Bad Response Exception.
     */
    public function listStores()
    {
        return $this->handleResponse(
            $this->client()->get('/v1/stores')
        );
    }

    /**
     * Get the Store by Domain name
     *
     * @param string $domain eg. google.com
     * @return mixed
     */
    public function findStore($domain)
    {
        return $this->handleResponse(
            $this->client()
                ->post('/v1/stores/find', [
                    'json' => [
                        'domain' => $domain
                    ]
                ])
        );
    }

    /**
     * Update Store Details
     *
     * @param $title
     * @param string $logo
     * @param string $description
     * @return mixed|null
     * @throws \Exception Bad Response Exception.
     */
    public function updateStore($title, $logo = '', $description = '')
    {
        return $this->handleResponse(
            $this->client()
                ->put('/v1/store/' . $this->store_id, [
                    'json' => [
                        'title' => $title,
                        'logo' => $logo,
                        'description' => $description,
                    ]
                ])
        );
    }

    /**
     * Get the Guzzle Client
     *
     * @return \GuzzleHttp\Client
     */
    public function client()
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        if (!empty($this->token)) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        return new \GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => $this->api_url,
            // You can set any number of default request options.
            'timeout' => 60.0,
            'headers' => $headers
        ]);
    }

    /**
     * Handle Response and return data
     * TODO: Handle validation errors better
     *
     * @param $response
     * @return mixed|null
     */
    public function handleResponse($response)
    {
        $content = $response->getBody()->getContents();
        $data = json_decode($content);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Unknown response data: ' . $content);
        }
        return $data;
    }

    /**
     * Set Token
     *
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Set Store ID
     *
     * @param $store_id
     * @return $this
     */
    public function setStoreID($store_id)
    {
        $this->store_id = $store_id;

        return $this;
    }

    /**
     * Create Order class via the client.
     *
     * @see Order::create()
     * @param $fillable
     * @return Order
     * @throws \Exception Bad Response Exception.
     */
    public function createOrderClass($fillable, $youpay_id = null)
    {
        $order = Order::create($fillable)
            ->setStoreID($this->store_id);
        if ($youpay_id) {
            $order->setYouPayID($youpay_id);
        }
        return $order;
    }

    /**
     * Create Order and Post to API
     *
     * @param $fillable
     * @param null $youpay_id
     * @return mixed
     * @throws \Exception Bad Response Exception.
     */
    public function createOrderFromArray($fillable, $youpay_id = null)
    {
        return $this->postOrder(
	        $this->createOrderClass($fillable, $youpay_id),
	        (!empty($fillable['params'])) ? $fillable['params'] : null
        );
    }

	/**
	 * Magic Login Link for URL
	 *
	 * @param $url
	 *
	 * @return mixed|null
	 * @throws \Exception
	 */
    public function link($url = '/dashboards/main')
    {
	    return $this->handleResponse(
		    $this->client()->post('/v1/magic-link', [
			    'json' => [
				    'url' => $url
			    ]
		    ])
	    )->url;
    }

    /**
     * Get Checkout JS Url
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCheckoutJSUrl($unique_id = false)
    {
	    if ($unique_id) {
		    return  rtrim($this->api_url, '/') . '/checkout.js?unique=' . $unique_id;
	    }
	    return  rtrim($this->api_url, '/') . '/checkout.js?random=' . time();
    }

    /**
     * Get Checkout JS Url
     *
     * @return mixed
     * @throws \Exception
     */
    public static function checkoutUrl($unique_id = false)
    {
        $self = new self();
        return $self->getCheckoutJSUrl($unique_id);
    }
}
