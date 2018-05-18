<?php

namespace RefactoringGuru\Singleton\RealLife;

/**
 * Singleton Design Pattern
 *
 * Intent: Ensure that class has a single instance, and provide a global point
 * of access to it.
 *
 * Example: Singleton pattern is notorious for limiting code reuse and
 * complicating unit testing. But it still very useful in some cases. In
 * particular, it's useful to control some shared resources. For example, a
 * global logging object controls access to a log file. Another good example: a
 * shared runtime configuration storage.
 */

/**
 * Singleton.
 */
class Singleton
{
    private static $instances = array();

    protected function __construct() { }

    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    public static function getInstance()
    {
        $cls = get_called_class();
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }
}

/**
 * A logging class is most known and appreciated application of singleton. In
 * most cases, you need a single logging object that writes to one log file. You
 * also need global and convenient way to access that instance.
 */
class Logger extends Singleton
{
    /**
     * Handle to opened file resource.
     */
    private $fileHandle;

    /**
     * Singleton's constructor is called just once, therefore just one file
     * handle will be opened at all times.
     *
     * We open the console stream instead of file for the sake of simplicity.
     */
    protected function __construct()
    {
        $this->fileHandle = fopen('php://stdout', 'w');
    }

    /**
     * Write log entry to the opened stream.
     */
    public function writeLog(string $message)
    {
        $date = date('Y-m-d');
        fwrite($this->fileHandle, "$date: $message\n");
    }

    /**
     * Handy shortcut to reduce amount of code needed to log a message from
     * client code.
     */
    public static function log(string $message)
    {
        $logger = static::getInstance();
        $logger->writeLog($message);
    }
}

/**
 * A configuration singleton can is also justifiable solution. Often you need to
 * access application configurations from different places in program. Singleton
 * gives you that comfort.
 */
class Config extends Singleton
{
    private $hashmap = [];

    public function getValue($key)
    {
        return $this->hashmap[$key];
    }

    public function setValue($key, $value)
    {
        $this->hashmap[$key] = $value;
    }
}

/**
 * Client code.
 */
function clientCode()
{
    Logger::log("Started!");

    // Compare values of Logger singleton.
    $l1 = Logger::getInstance();
    $l2 = Logger::getInstance();
    if ($l1 === $l2) {
        Logger::log("Logger has a single instance.");
    } else {
        Logger::log("Loggers are different.");
    }

    // Check how Config singleton saves data...
    $config1 = Config::getInstance();
    $login = "test_login";
    $password = "test_password";
    $config1->setValue("login", $login);
    $config1->setValue("password", $password);
    // ...and restores it.
    $config2 = Config::getInstance();
    if ($login == $config2->getValue("login") &&
        $password == $config2->getValue("password")
    ) {
        Logger::log("Config singleton also works fine.");
    }

    Logger::log("Finished!");
}

clientCode();