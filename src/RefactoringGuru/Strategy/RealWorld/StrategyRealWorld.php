<?php

namespace RefactoringGuru\Strategy\RealWorld;

/**
 * Strategy Design Pattern
 *
 * Intent: Define a family of algorithms, encapsulate each one, and make them
 * interchangeable. Strategy lets the algorithm vary independently from clients
 * that use it.
 *
 * Example: In this example, the Strategy pattern is used to represent payment
 * methods in an e-commerce application.
 *
 * Each payment method can display a payment form to collect proper payment
 * details from a user and send it to the payment processing company. Then,
 * after the payment processing company redirects the user back to our website,
 * the payment method validates the return parameters and helps to decide
 * whether the order was completed.
 */

/**
 * EN: This is the router and controller of our application. Upon receiving a
 * request, this class decides what behavior should be executed. When the app
 * receives a payment request, the OrderController class also decides which
 * payment method should it use to process the request. Thus, the class acts as
 * the Context and the Client at the same time.
 *
 * RU: Это роутер и контроллер нашего приложения. При получении запроса, он
 * передаст выполнение одному из своих методов. В случае, если приходит запрос
 * на оплату, OrderController дополнительно выберет соотвествующий класс
 * платёжного метода и использует его для оплаты. Таким образом, этот класс
 * одновременно отыгрывает и роль Контекста и роль Клиента.
 */
class OrderController
{
    /**
     * Handle POST requests.
     *
     * @param $url
     * @param $data
     * @throws \Exception
     */
    public function post($url, $data)
    {
        print("Controller: POST request to $url with ".json_encode($data)."\n");

        $path = parse_url($url, PHP_URL_PATH);

        if (preg_match('#^/orders?$#', $path, $matches)) {

            $this->postNewOrder($data);

        } else {
            print("Controller: 404 page\n");
        }
    }

    /**
     * Handle GET requests.
     *
     * @param $url
     * @throws \Exception
     */
    public function get($url)
    {
        print("Controller: GET request to $url\n");

        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $data);

        if (preg_match('#^/orders?$#', $path, $matches)) {

            $this->getAllOrders();

        } elseif (preg_match('#^/order/([0-9]+?)/payment/([a-z]+?)(/return)?$#', $path, $matches)) {

            $order = Order::get($matches[1]);

            // The payment method (strategy) is selected according to the value
            // passed along with the request.
            $paymentMethod = PaymentFactory::getPaymentMethod($matches[2]);

            if (! isset($matches[3])) {
                $this->getPayment($paymentMethod, $order, $data);
            } else {
                $this->getPaymentReturn($paymentMethod, $order, $data);
            }

        } else {
            print("Controller: 404 page\n");
        }
    }

    /**
     * POST /order {data}
     */
    public function postNewOrder(array $data)
    {
        $order = new Order($data);
        print("Controller: Created the order #{$order->id}.\n");
    }

    /**
     * GET /orders
     */
    public function getAllOrders()
    {
        print("Controller: Here's all orders:\n");
        foreach (Order::get() as $order) {
            print(json_encode($order, JSON_PRETTY_PRINT)."\n");
        }
    }

    /**
     * GET /order/123/payment/XX
     */
    public function getPayment(PaymentMethod $method, Order $order, array $data)
    {
        // The actual work is delegated to the payment method object.
        $form = $method->getPaymentForm($order);
        print("Controller: here's the payment form:\n");
        print($form."\n");
    }

    /**
     * GET /order/123/payment/XXX/return?key=AJHKSJHJ3423&success=true
     */
    public function getPaymentReturn(PaymentMethod $method, Order $order, array $data)
    {
        try {
            // Another type of work delegated to the payment method.
            if ($method->validateReturn($order, $data)) {
                print("Controller: Thanks for your order!\n");
                $order->complete();
            }
        } catch (\Exception $e) {
            print("Controller: got an exception (".$e->getMessage().")\n");
        }
    }
}

/**
 * A simplified representation of the Order class.
 */
class Order
{
    /**
     * For the sake of simplicity, we'll store all created orders here.
     *
     * @var array
     */
    private static $orders = [];

    /**
     * ...and access them from here.
     *
     * @param int $orderId
     * @return mixed
     */
    public static function get(int $orderId = null)
    {
        if ($orderId === null) {
            return static::$orders;
        } else {
            return static::$orders[$orderId];
        }
    }

    /**
     * The Order constructor assigns the values of the order's fields. To keep
     * things simple, there is no validation whatsoever.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->id = count(static::$orders);
        $this->status = "new";
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
        static::$orders[$this->id] = $this;
    }

    /**
     * The method to call when an order gets paid.
     */
    public function complete()
    {
        $this->status = "completed";
        print("Order: #{$this->id} is now {$this->status}.");
    }
}

/**
 * This class helps to produce a proper strategy object for handling a payment.
 */
class PaymentFactory
{
    /**
     * Get a payment method by its ID.
     *
     * @param $id
     * @return PaymentMethod
     * @throws \Exception
     */
    public static function getPaymentMethod($id): PaymentMethod
    {
        switch ($id) {
            case "cc":
                return new CreditCardPayment();
            case "paypal":
                return new PayPalPayment();
            default:
                throw new \Exception("Unknown Payment Method");
        }
    }
}

/**
 * The Strategy interface describes how a client can use various Concrete
 * Strategies.
 *
 * Note that in most examples you can find on the Web, strategies tend to do
 * some tiny thing within one method. However, in reality, your strategies can
 * be much more robust, have several methods, etc.
 */
interface PaymentMethod
{
    public function getPaymentForm(Order $order): string;

    public function validateReturn(Order $order, $data): bool;
}

/**
 * This Concrete Strategy provides a payment form and validates returns for
 * credit card payments.
 */
class CreditCardPayment implements PaymentMethod
{
    static private $store_secret_key = "swordfish";

    public function getPaymentForm(Order $order): string
    {
        $returnURL = "https://our-website.com/".
            "order/{$order->id}/payment/cc/return";

        return <<<FORM
<form action="https://my-credit-card-processor.com/charge" method="POST">
    <input type="hidden" id="email" value="{$order->email}">
    <input type="hidden" id="total" value="{$order->total}">
    <input type="hidden" id="returnURL" value="$returnURL">
    <input type="text" id="cardholder-name">
    <input type="text" id="credit-card">
    <input type="text" id="expiration-date">
    <input type="text" id="ccv-number">
    <input type="submit" value="Pay">
</form>
FORM;
    }

    public function validateReturn(Order $order, $data): bool
    {
        print("CreditCardPayment: ...validating... ");

        if ($data['key'] != md5($order->id.static::$store_secret_key)) {
            throw new \Exception("Payment key is wrong.");
        }

        if (! isset($data['success']) || ! $data['success'] || $data['success'] == 'false') {
            throw new \Exception("Payment failed.");
        }

        // ...

        if (floatval($data['total']) < $order->total) {
            throw new \Exception("Payment amount is wrong.");
        }

        print("Done!\n");

        return true;
    }
}

/**
 * This Concrete Strategy provides a payment form and validates returns for
 * PayPal payments.
 */
class PayPalPayment implements PaymentMethod
{
    public function getPaymentForm(Order $order): string
    {
        $returnURL = "https://our-website.com/".
            "order/{$order->id}/payment/paypal/return";

        return <<<FORM
<form action="https://paypal.com/payment" method="POST">
    <input type="hidden" id="email" value="{$order->email}">
    <input type="hidden" id="total" value="{$order->total}">
    <input type="hidden" id="returnURL" value="$returnURL">
    <input type="submit" value="Pay on PayPal">
</form>
FORM;
    }

    public function validateReturn(Order $order, $data): bool
    {
        print("PayPalPayment: ...validating... ");

        // ...

        print("Done!\n");

        return true;
    }
}

/**
 * The client code.
 */

$controller = new OrderController();

print("Client: Let's create some orders\n");

$controller->post("/orders", [
    "email" => "me@example.com",
    "product" => "ABC Cat food (XL)",
    "total" => 9.95,
]);

$controller->post("/orders", [
    "email" => "me@example.com",
    "product" => "XYZ Cat litter (XXL)",
    "total" => 19.95,
]);

print("\nClient: List my orders, please\n");

$controller->get("/orders");

print("\nClient: I'd like to pay for the second, show me the payment form\n");

$controller->get("/order/1/payment/paypal");

print("\nClient: ...pushes the Pay button...\n");
print("\nClient: Oh, I'm redirected to the PayPal.\n");
print("\nClient: ...pays on the PayPal...\n");
print("\nClient: Alright, I'm back with you, guys.\n");

$controller->get("/order/1/payment/paypal/return".
    "?key=c55a3964833a4b0fa4469ea94a057152&success=true&total=19.95");