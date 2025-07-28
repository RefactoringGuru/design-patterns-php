<?php

namespace RefactoringGuru\Memento\RealWorld;

/**
 * Memento Design Pattern
 *
 * Intent: Lets you save and restore the previous state of an object without
 * revealing the details of its implementation.
 */


/**
 * The Memento interface provides a way to retrieve metadata about the memento.
 *
 * This interface defines the contract that all order memento implementations
 * must follow. It provides methods for accessing metadata about the saved
 * state but does NOT provide direct access to the order's internal state.
 *
 * The interface serves as a protective barrier between the caretaker and
 * the actual state data, ensuring proper encapsulation.
 */
interface OrderMemento
{
    /**
     * Get the timestamp when this memento was created
     *
     * @return string The creation timestamp
     */
    public function getTimestamp(): string;

    /**
     * Get a description of this memento's state
     *
     * @return string A description of the saved state
     */
    public function getDescription(): string;

    /**
     * Get the complete state data from this memento
     *
     * This method should only be called by the originator when restoring state.
     *
     * @return array The complete order state data
     */
    public function getState(): array;
}


/**
 * The Concrete Memento stores the complete order state.
 *
 * This class implements the OrderMemento interface and provides storage
 * for all aspects of an order's state. It captures a complete snapshot
 * that allows the order to be restored to exactly the same state later.
 *
 * The memento is immutable - once created, its state cannot be changed.
 */
class OrderSnapshot implements OrderMemento
{
    /**
     * Stored order state data
     *
     * @var array
     */
    private $orderState;

    /**
     * Creation timestamp
     *
     * @var string
     */
    private $timestamp;

    /**
     * Constructor
     *
     * Creates a new order snapshot with all the provided state information.
     *
     * @param string $orderId Order identifier
     * @param array $items Order items
     * @param array $customer Customer information
     * @param string $status Current order status
     * @param float $total Order total amount
     * @param array $payments Payment information
     */
    public function __construct(
        string $orderId,
        array $items,
        array $customer,
        string $status,
        float $total,
        array $payments
    ) {
        $this->orderState = [
            'orderId' => $orderId,
            'items' => $items,
            'customer' => $customer,
            'status' => $status,
            'total' => $total,
            'payments' => $payments
        ];
        
        $this->timestamp = date('Y-m-d H:i:s');
    }

    /**
     * Get the creation timestamp
     *
     * @return string When this memento was created
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * Get a description of this memento
     *
     * @return string Human-readable description
     */
    public function getDescription(): string
    {
        $orderId = $this->orderState['orderId'];
        $status = $this->orderState['status'];
        $total = number_format($this->orderState['total'], 2);
        
        return "Order {$orderId} - Status: {$status} - Total: \${$total}";
    }

    /**
     * Get the complete state data
     *
     * @return array The complete order state
     */
    public function getState(): array
    {
        return $this->orderState;
    }
}

/**
 * The Originator (Order) holds important state that changes during processing.
 *
 * This class represents an e-commerce order that goes through various
 * processing steps. Each step modifies the order's state, and any step
 * might fail, requiring a rollback to a previous consistent state.
 *
 * The Order can create snapshots of its state and restore from them
 * without exposing the internal structure to external systems.
 */
class Order
{
    /**
     * Unique order identifier
     *
     * @var string
     */
    private $orderId;

    /**
     * Order items with details
     *
     * @var array
     */
    private $items;

    /**
     * Customer information
     *
     * @var array
     */
    private $customer;

    /**
     * Current processing status
     *
     * @var string
     */
    private $status;

    /**
     * Order total amount
     *
     * @var float
     */
    private $total;

    /**
     * Payment information
     *
     * @var array
     */
    private $payments;

    /**
     * Constructor
     *
     * Creates a new order with basic information.
     * The order starts in 'pending' status.
     *
     * @param string $orderId Unique identifier for this order
     * @param array $items Order items
     * @param array $customer Customer information
     */
    public function __construct(string $orderId, array $items, array $customer)
    {
        $this->orderId = $orderId;
        $this->items = $items;
        $this->customer = $customer;
        $this->status = 'pending';
        $this->total = $this->calculateTotal();
        $this->payments = [];

        echo "Order {$this->orderId} created with " . count($items) . " items\n";
    }

    /**
     * Business logic method: Validate order
     *
     * Performs order validation and updates status.
     */
    public function validateOrder(): void
    {
        echo "Validating order {$this->orderId}...\n";
        
        // Update item details during validation
        foreach ($this->items as &$item) {
            $item['validated'] = true;
            $item['validated_at'] = date('Y-m-d H:i:s');
        }
        
        $this->status = 'validated';
        echo "Order {$this->orderId} validated successfully\n";
    }

    /**
     * Business logic method: Process payment
     *
     * Processes payment for the order.
     *
     * @param array $paymentInfo Payment details
     */
    public function processPayment(array $paymentInfo): void
    {
        echo "Processing payment for order {$this->orderId}...\n";
        
        $payment = [
            'method' => $paymentInfo['method'],
            'amount' => $this->total,
            'status' => 'completed',
            'transaction_id' => 'TXN_' . uniqid(),
            'processed_at' => date('Y-m-d H:i:s')
        ];
        
        $this->payments[] = $payment;
        $this->status = 'paid';
        
        echo "Payment of $" . number_format($this->total, 2) . " processed\n";
    }

    /**
     * Business logic method: Ship order
     *
     * Prepares order for shipping.
     */
    public function shipOrder(): void
    {
        echo "Shipping order {$this->orderId}...\n";
        
        $this->status = 'shipped';
        
        echo "Order {$this->orderId} has been shipped\n";
    }

    /**
     * Calculate order total
     *
     * @return float The calculated total
     */
    private function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    /**
     * Creates a memento with the current order state
     *
     * This is the key method of the Memento pattern. It creates a snapshot
     * of the current order state and returns it as a memento object.
     *
     * @return OrderMemento A memento containing the current state
     */
    public function saveToMemento(): OrderMemento
    {
        return new OrderSnapshot(
            $this->orderId,
            $this->items,
            $this->customer,
            $this->status,
            $this->total,
            $this->payments
        );
    }

    /**
     * Restores the order's state from a memento object
     *
     * This method accepts a memento and restores the order's state to
     * match the state stored in the memento.
     *
     * @param OrderMemento $memento The memento to restore from
     */
    public function restoreFromMemento(OrderMemento $memento): void
    {
        $state = $memento->getState();
        
        $this->items = $state['items'];
        $this->customer = $state['customer'];
        $this->status = $state['status'];
        $this->total = $state['total'];
        $this->payments = $state['payments'];
        
        echo "Order {$this->orderId} restored to status: {$this->status}\n";
    }

    /**
     * Get order ID
     *
     * @return string The order ID
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * Get current status
     *
     * @return string The current order status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get order summary
     *
     * @return array Order summary information
     */
    public function getOrderSummary(): array
    {
        return [
            'order_id' => $this->orderId,
            'status' => $this->status,
            'total' => $this->total,
            'item_count' => count($this->items),
            'payment_count' => count($this->payments)
        ];
    }
}


/**
 * The Caretaker manages order snapshots and provides rollback functionality.
 *
 * This class represents the Caretaker in the Memento pattern. It manages
 * a collection of order snapshots and provides methods to save states
 * and rollback when needed.
 *
 * The caretaker doesn't access the order's internal state directly - it
 * works through the memento interface, maintaining proper encapsulation.
 */
class OrderHistory
{
    /**
     * Collection of saved snapshots
     *
     * @var array
     */
    private $snapshots = [];

    /**
     * Reference to the order being managed
     *
     * @var Order
     */
    private $order;

    /**
     * Constructor
     *
     * Creates a new history manager for the given order.
     *
     * @param Order $order The order to manage history for
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Save current order state
     *
     * Creates a snapshot of the current order state and saves it.
     *
     * @param string $label Optional label for this snapshot
     */
    public function saveState(string $label = ''): void
    {
        $memento = $this->order->saveToMemento();
        
        $snapshotKey = count($this->snapshots);
        if ($label) {
            $snapshotKey = $label;
        }
        
        $this->snapshots[$snapshotKey] = $memento;
        
        echo "State saved";
        if ($label) {
            echo " as '{$label}'";
        }
        echo " for order {$this->order->getOrderId()}\n";
    }

    /**
     * Restore to a previous state
     *
     * Restores the order to a previously saved state.
     *
     * @param string|int $snapshotKey The snapshot to restore to
     * @return bool True if restore successful, false otherwise
     */
    public function restoreState($snapshotKey): bool
    {
        if (!isset($this->snapshots[$snapshotKey])) {
            echo "Snapshot '{$snapshotKey}' not found\n";
            return false;
        }
        
        $memento = $this->snapshots[$snapshotKey];
        $this->order->restoreFromMemento($memento);
        
        echo "Restored to snapshot '{$snapshotKey}'\n";
        return true;
    }

    /**
     * Get list of available snapshots
     *
     * @return array List of snapshot information
     */
    public function getSnapshots(): array
    {
        $snapshotList = [];
        foreach ($this->snapshots as $key => $memento) {
            $snapshotList[$key] = [
                'key' => $key,
                'description' => $memento->getDescription(),
                'timestamp' => $memento->getTimestamp()
            ];
        }
        return $snapshotList;
    }

    /**
     * Show all snapshots
     */
    public function showSnapshots(): void
    {
        echo "\n=== Order Snapshots ===\n";
        if (empty($this->snapshots)) {
            echo "No snapshots available\n";
        } else {
            foreach ($this->snapshots as $key => $memento) {
                echo "[{$key}] {$memento->getDescription()} - {$memento->getTimestamp()}\n";
            }
        }
        echo "========================\n\n";
    }

    /**
     * Clear all snapshots
     */
    public function clearSnapshots(): void
    {
        $this->snapshots = [];
        echo "All snapshots cleared for order {$this->order->getOrderId()}\n";
    }
}



/**
 * ============================================================================
 * USAGE EXAMPLE AND DEMONSTRATION
 * ============================================================================
 *
 * The following code demonstrates how to use the Memento pattern implementation
 * with various scenarios that show all the key features of the pattern:
 * - Creating and restoring mementos
 * - Undo/redo functionality
 * - History management
 */

echo "=== Simple Order Processing with Memento Pattern ===\n\n";

// Create order
$customer = [
    'id' => 'CUST_001',
    'name' => 'John Doe',
    'email' => 'john@example.com'
];

$items = [
    [
        'product_id' => 'PROD_001',
        'name' => 'Laptop',
        'price' => 999.99,
        'quantity' => 1
    ],
    [
        'product_id' => 'PROD_002',
        'name' => 'Mouse',
        'price' => 29.99,
        'quantity' => 1
    ]
];

$order = new Order('ORD_001', $items, $customer);
$history = new OrderHistory($order);

echo "Initial order: " . json_encode($order->getOrderSummary()) . "\n\n";

// Save initial state
$history->saveState('initial');

// Process order step by step with snapshots
echo "--- Step 1: Validate Order ---\n";
$order->validateOrder();
$history->saveState('after_validation');
echo "Order status: " . $order->getStatus() . "\n\n";

echo "--- Step 2: Process Payment ---\n";
$order->processPayment(['method' => 'credit_card']);
$history->saveState('after_payment');
echo "Order status: " . $order->getStatus() . "\n\n";

echo "--- Step 3: Ship Order ---\n";
$order->shipOrder();
$history->saveState('after_shipping');
echo "Order status: " . $order->getStatus() . "\n\n";

// Show all snapshots
$history->showSnapshots();

// Demonstrate rollback
echo "--- Rollback Demonstration ---\n";
echo "Rolling back to after_validation state...\n";
$history->restoreState('after_validation');
echo "Current order status: " . $order->getStatus() . "\n\n";

// Process payment again
echo "--- Reprocess from validation state ---\n";
$order->processPayment(['method' => 'debit_card']);
echo "Order status after reprocessing: " . $order->getStatus() . "\n\n";

// Show final snapshots
$history->showSnapshots();
