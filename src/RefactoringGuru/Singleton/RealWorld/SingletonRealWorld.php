<?php

namespace RefactoringGuru\Singleton\RealWorld;

/**
 * Singleton Design Pattern
 *
 * Intent: Ensure that a class has a single instance, and provide a global point
 * of access to it.
 *
 * Example: The Singleton pattern is notorious for limiting code reuse and
 * complicating unit testing. However it is still very useful in some cases. In
 * particular, it's handy when you need control some shared resources. For
 * example, a global logging object that have to control the access to a log
 * file. Another good example: a shared runtime configuration storage.
 */

/**
 * If you need to support several types of Singletons in your app, you can
 * define the basic features of the Singleton in a base class, while moving the
 * actual business logic (like logging) to subclasses.
 */
class Singleton
{
    /**
     * The actual singleton's instance almost always reside inside a static
     * field. In this case, the static field is an array, where each subclasses
     * of the Singleton stores its own instance.
     */
    private static $instances = array();

    /**
     * Singleton's constructor should not be public. But it can't be private
     * either, if we want to allow subclassing.
     */
    protected function __construct() { }

    /**
     * Cloning and unserialization is not permitted for singletons.
     */
    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * The method you use to get the Singleton's instance.
     */
    public static function getInstance()
    {
        $subclass = get_called_class();
        if (!isset(self::$instances[$subclass])) {
            // Note that here we use the "static" keyword instead of the actual
            // class name. In this context the "static" keyword means "the name
            // of the current class". That detail is important because when the
            // method is called on the subclass, we want an instance of that
            // subclass to be created here.
            self::$instances[$subclass] = new static;
        }
        return self::$instances[$subclass];
    }
}

/**
 * The logging class is most known and appreciated application of the Singleton
 * pattern. In most cases, you need a single logging object that writes to a
 * single log file (control over shared resource). You also need a convenient
 * way to access that instance from any context of you app (global access
 * point).
 */
class Logger extends Singleton
{
    /**
     * A file pointer resource of the log file.
     */
    private $fileHandle;

    /**
     * Since the Singleton's constructor is called only once, just a single file
     * resource will be opened at all times.
     *
     * Note, for the sake of simplicity we open the console stream instead of
     * the actual file here.
     */
    protected function __construct()
    {
        $this->fileHandle = fopen('php://stdout', 'w');
    }

    /**
     * Write a log entry to the opened file resource.
     */
    public function writeLog(string $message)
    {
        $date = date('Y-m-d');
        fwrite($this->fileHandle, "$date: $message\n");
    }

    /**
     * Just a handy shortcut to reduce the amount of code needed to log messages
     * from the client code.
     */
    public static function log(string $message)
    {
        $logger = static::getInstance();
        $logger->writeLog($message);
    }
}

/**
 * Applying the Singleton pattern to the configuration storage is also a common
 * practice. Often you need to access application configurations from a lot of
 * different places of the program. Singleton gives you that comfort.
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
 * The client code.
 */
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