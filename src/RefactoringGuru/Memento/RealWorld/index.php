<?php

namespace RefactoringGuru\Memento\RealWorld;

/**
 * Memento Design Pattern
 *
 * Intent: Lets you save and restore the previous state of an object without
 * revealing the details of its implementation.
 *
 * This real-world example demonstrates how a configuration management system
 * implements backup and restore functionality using the Memento pattern.
 */

/**
 * The Originator holds some important state that may change over time. It also
 * defines a method for saving the state inside a memento and another method for
 * restoring the state from it.
 *
 * In this example, the ConfigManager acts as the Originator. It manages
 * application configuration settings that can be modified during runtime.
 * The configuration might include database settings, feature flags, UI themes,
 * SEO settings, and other application parameters.
 *
 * The ConfigManager's business logic may affect its internal state. Therefore,
 * the client should backup the state before launching methods of the
 * business logic via the save() method.
 */
class ConfigManager
{
    /**
     * @var array For simplicity, the configuration state is stored inside an array.
     * In real applications, this might be a more complex structure with validation,
     * type checking, and nested configurations.
     */
    private $config;

    /**
     * Constructor initializes the configuration manager with default settings.
     *
     * @param array $initialConfig The initial configuration values
     */
    public function __construct(array $initialConfig)
    {
        $this->config = $initialConfig;
        echo "ConfigManager: Initialized with " . count($initialConfig) . " config items.\n";
    }

    /**
     * The ConfigManager's business logic may affect its internal state. Therefore,
     * the client should backup the state before launching methods of the
     * business logic via the save() method.
     *
     * This method simulates updating configuration values, which is a common
     * operation in web applications (admin panels, user preferences, etc.).
     *
     * @param array $newValues New configuration values to merge with existing config
     */
    public function updateConfig(array $newValues): void
    {
        echo "ConfigManager: Updating configuration with new values...\n";
        $this->config = array_merge($this->config, $newValues);
        echo "ConfigManager: Configuration updated. Current config has " . count($this->config) . " items.\n";
    }

    /**
     * Retrieves the current configuration state.
     *
     * @return array The current configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Saves the current state inside a memento.
     *
     * This method creates a snapshot of the current configuration state
     * and returns it wrapped in a memento object. The memento contains
     * all the information needed to restore the configuration to its
     * current state later.
     *
     * @return ConfigSnapshot A memento containing the current config state
     */
    public function save(): ConfigSnapshot
    {
        echo "ConfigManager: Saving current configuration state...\n";
        return new ConfigSnapshot($this->config);
    }

    /**
     * Restores the ConfigManager's state from a memento object.
     *
     * This method takes a memento and restores the configuration to the
     * state that was saved in that memento. This is useful for implementing
     * undo functionality or rolling back failed configuration changes.
     *
     * @param ConfigSnapshot $snapshot The memento to restore from
     */
    public function restore(ConfigSnapshot $snapshot): void
    {
        $this->config = $snapshot->getState();
        echo "ConfigManager: Configuration restored from snapshot.\n";
    }

    /**
     * Additional business method: Reset to defaults
     *
     * Resets the configuration to default values. This is another operation
     * that might benefit from creating a backup before execution.
     */
    public function resetToDefaults(): void
    {
        echo "ConfigManager: Resetting configuration to defaults...\n";
        $this->config = [
            'maintenance_mode' => false,
            'theme' => 'light',
            'debug' => false
        ];
        echo "ConfigManager: Configuration reset to defaults.\n";
    }

    /**
     * Additional business method: Enable maintenance mode
     *
     * Quickly enables maintenance mode across the application.
     */
    public function enableMaintenanceMode(): void
    {
        echo "ConfigManager: Enabling maintenance mode...\n";
        $this->config['maintenance_mode'] = true;
        $this->config['maintenance_message'] = 'System under maintenance. Please try again later.';
        echo "ConfigManager: Maintenance mode enabled.\n";
    }
}

/**
 * The Memento interface provides a way to retrieve the memento's metadata, such
 * as creation date or name. However, it doesn't expose the Originator's state.
 *
 * This interface ensures that external classes can work with mementos without
 * having direct access to the stored state. Only the originator should be able
 * to extract the actual state data.
 */
interface ConfigMemento
{
    /**
     * Returns a user-friendly name for this memento.
     *
     * @return string A descriptive name for this snapshot
     */
    public function getName(): string;

    /**
     * Returns the date when this memento was created.
     *
     * @return string The creation timestamp
     */
    public function getDate(): string;
}

/**
 * The Concrete Memento contains the infrastructure for storing the Originator's
 * state.
 *
 * This class stores a snapshot of the configuration state along with metadata
 * about when the snapshot was created. The actual state is stored privately
 * and can only be accessed by the originator through the getState() method.
 */
class ConfigSnapshot implements ConfigMemento
{
    /**
     * @var array The configuration state at the time this snapshot was created
     */
    private $state;

    /**
     * @var string The timestamp when this snapshot was created
     */
    private $date;

    /**
     * Constructor stores the provided state and records the current timestamp.
     *
     * @param array $state The configuration state to store
     */
    public function __construct(array $state)
    {
        $this->state = $state;
        $this->date = date('Y-m-d H:i:s');
        echo "ConfigSnapshot: Created snapshot with " . count($state) . " config items.\n";
    }

    /**
     * The Originator uses this method when restoring its state.
     *
     * This method provides access to the stored state data. It should only
     * be called by the ConfigManager when restoring configuration.
     *
     * @return array The stored configuration state
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * The rest of the methods are used by the Caretaker to display metadata.
     *
     * Returns a descriptive name that includes the timestamp and a preview
     * of the configuration content.
     *
     * @return string A user-friendly name for this snapshot
     */
    public function getName(): string
    {
        $configCount = count($this->state);
        $maintenanceStatus = $this->state['maintenance_mode'] ?? 'unknown';
        return $this->date . " / ({$configCount} items, maintenance: {$maintenanceStatus})";
    }

    /**
     * Returns the creation date of this snapshot.
     *
     * @return string The timestamp when this snapshot was created
     */
    public function getDate(): string
    {
        return $this->date;
    }
}

/**
 * The Caretaker doesn't depend on the Concrete Memento class. Therefore, it
 * doesn't have access to the originator's state, stored inside the memento. It
 * works with all mementos via the base Memento interface.
 *
 * The ConfigHistory class manages a collection of configuration snapshots and
 * provides undo functionality. It demonstrates how the caretaker can manage
 * mementos without knowing their internal structure.
 */
class ConfigHistory
{
    /**
     * @var ConfigSnapshot[] Array of stored configuration snapshots
     */
    private $snapshots = [];

    /**
     * @var ConfigManager Reference to the configuration manager
     */
    private $configManager;

    /**
     * Constructor establishes the relationship with the originator.
     *
     * @param ConfigManager $configManager The configuration manager to work with
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
        echo "ConfigHistory: History manager initialized.\n";
    }

    /**
     * Creates a backup of the current configuration state.
     *
     * This method asks the originator to create a memento and stores it
     * in the history. This should be called before making changes that
     * might need to be undone.
     */
    public function backup(): void
    {
        echo "\nConfigHistory: Creating backup of current configuration...\n";
        $this->snapshots[] = $this->configManager->save();
        echo "ConfigHistory: Backup created. Total backups: " . count($this->snapshots) . "\n";
    }

    /**
     * Restores the configuration to the most recent backup.
     *
     * This method retrieves the most recent memento from the history and
     * asks the originator to restore its state from that memento.
     */
    public function undo(): void
    {
        if (!count($this->snapshots)) {
            echo "ConfigHistory: No backups available for undo.\n";
            return;
        }

        $memento = array_pop($this->snapshots);

        echo "ConfigHistory: Restoring configuration to: " . $memento->getName() . "\n";
        try {
            $this->configManager->restore($memento);
            echo "ConfigHistory: Undo completed successfully.\n";
        } catch (\Exception $e) {
            echo "ConfigHistory: Undo failed, trying previous backup...\n";
            $this->undo();
        }
    }

    /**
     * Displays the history of all saved configuration snapshots.
     *
     * This method shows all available backups using only the information
     * available through the memento interface, without accessing the
     * actual configuration data.
     */
    public function showHistory(): void
    {
        echo "\nConfigHistory: Available configuration backups:\n";
        if (empty($this->snapshots)) {
            echo "No backups available.\n";
        } else {
            foreach ($this->snapshots as $index => $memento) {
                echo "[{$index}] " . $memento->getName() . "\n";
            }
        }
        echo "\n";
    }

    /**
     * Clears all stored backups.
     *
     * This method removes all mementos from the history, which might be
     * useful when starting fresh or to free up memory.
     */
    public function clearHistory(): void
    {
        $count = count($this->snapshots);
        $this->snapshots = [];
        echo "ConfigHistory: Cleared {$count} backups from history.\n";
    }

    /**
     * Gets the number of available backups.
     *
     * @return int The number of stored backups
     */
    public function getBackupCount(): int
    {
        return count($this->snapshots);
    }
}



/**
 * ============================================================================
 * CLIENT CODE - DEMONSTRATION AND USAGE EXAMPLES
 * ============================================================================
 */

echo "=== Configuration Manager with Memento Pattern Demo ===\n\n";

/**
 * Example 1: Basic configuration management with backup/restore
 */
echo "--- Example 1: Basic Configuration Management ---\n";

// Initialize configuration manager with default settings
$config = new ConfigManager([
    'maintenance_mode' => false,
    'theme' => 'light',
    'seo' => ['title' => 'My Website', 'description' => 'Welcome to my site!'],
    'debug' => false,
    'max_users' => 1000
]);

// Create history manager
$history = new ConfigHistory($config);

echo "\nInitial configuration:\n";
print_r($config->getConfig());

/**
 * Example 2: Making changes with backups
 */
echo "\n--- Example 2: Making Changes with Backups ---\n";

// Create backup before making changes
$history->backup();

// Update theme settings
$config->updateConfig([
    'theme' => 'dark',
    'theme_options' => ['sidebar' => 'collapsed', 'font_size' => 'large']
]);

echo "\nAfter theme update:\n";
print_r($config->getConfig());

// Create another backup
$history->backup();

// Enable maintenance mode
$config->enableMaintenanceMode();

echo "\nAfter enabling maintenance mode:\n";
print_r($config->getConfig());

/**
 * Example 3: Demonstrating undo functionality
 */
echo "\n--- Example 3: Undo Functionality ---\n";

// Show current history
$history->showHistory();

// Undo last change (maintenance mode)
echo "Undoing maintenance mode activation...\n";
$history->undo();

echo "\nAfter first undo:\n";
print_r($config->getConfig());

// Undo theme changes
echo "\nUndoing theme changes...\n";
$history->undo();

echo "\nAfter second undo (back to original):\n";
print_r($config->getConfig());

/**
 * Example 4: Multiple configuration scenarios
 */
echo "\n--- Example 4: Multiple Configuration Scenarios ---\n";

// Scenario A: SEO Configuration
$history->backup();
echo "\nScenario A: Updating SEO settings...\n";
$config->updateConfig([
    'seo' => [
        'title' => 'Best Products Online',
        'description' => 'Find the best products at great prices!',
        'keywords' => 'products, online, shopping, deals'
    ],
    'analytics' => ['google_id' => 'GA-123456', 'facebook_pixel' => 'FB-789012']
]);

echo "SEO configuration updated:\n";
print_r($config->getConfig());

// Scenario B: Performance Settings
$history->backup();
echo "\nScenario B: Updating performance settings...\n";
$config->updateConfig([
    'cache_enabled' => true,
    'cache_duration' => 3600,
    'compression' => 'gzip',
    'max_users' => 2000
]);

echo "Performance settings updated:\n";
print_r($config->getConfig());

// Scenario C: Emergency rollback
echo "\nScenario C: Emergency rollback to SEO-only changes...\n";
$history->undo(); // Remove performance changes
echo "Rolled back performance changes:\n";
print_r($config->getConfig());

/**
 * Example 5: Reset and recovery
 */
echo "\n--- Example 5: Reset and Recovery ---\n";

// Save current state before reset
$history->backup();

// Reset configuration
$config->resetToDefaults();

echo "\nAfter reset to defaults:\n";
print_r($config->getConfig());

// Restore previous configuration
echo "\nRestoring previous configuration...\n";
$history->undo();

echo "\nAfter restoration:\n";
print_r($config->getConfig());

/**
 * Example 6: History management
 */
echo "\n--- Example 6: History Management ---\n";

// Show complete history
$history->showHistory();

echo "Total backups available: " . $history->getBackupCount() . "\n";

// Clear history
echo "\nClearing history...\n";
$history->clearHistory();

// Show history after clearing
$history->showHistory();

/**
 * Example 7: Real-world workflow simulation
 */
echo "\n--- Example 7: Real-World Workflow Simulation ---\n";

// Simulate a typical configuration update workflow
echo "Simulating typical admin workflow...\n\n";

// Step 1: Admin wants to update site for promotion
$history->backup();
echo "Step 1: Preparing for Black Friday promotion...\n";
$config->updateConfig([
    'promotion_banner' => 'Black Friday Sale - 50% Off!',
    'theme' => 'dark',
    'special_offers' => ['discount' => 50, 'code' => 'BLACKFRIDAY50']
]);

// Step 2: Update SEO for promotion
$history->backup();
echo "\nStep 2: Updating SEO for promotion visibility...\n";
$config->updateConfig([
    'seo' => [
        'title' => 'Black Friday Sale - 50% Off Everything!',
        'description' => 'Huge Black Friday discounts on all products. Limited time offer!',
        'keywords' => 'black friday, sale, discount, deals, promotion'
    ]
]);

// Step 3: Something goes wrong, need to rollback SEO changes only
echo "\nStep 3: SEO changes caused issues, rolling back SEO only...\n";
$history->undo();

echo "Final configuration after workflow:\n";
print_r($config->getConfig());

echo "\nFinal history state:\n";
$history->showHistory();
