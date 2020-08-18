<?php

namespace RefactoringGuru\Singleton\RealWorld;

/**
 * EN: Singleton Design Pattern
 *
 * Intent: Lets you ensure that a class has only one instance, while providing a
 * global access point to this instance.
 *
 * Example: The Singleton pattern is notorious for limiting code reuse and
 * complicating unit testing. However, it is still very useful in some cases. In
 * particular, it's handy when you need to control some shared resources. For
 * example, a global logging object that has to control the access to a log
 * file. Another good example: a shared runtime configuration storage.
 *
 * RU: Паттерн Одиночка
 *
 * Назначение: Гарантирует, что у класса есть только один экземпляр, и
 * предоставляет к нему глобальную точку доступа.
 *
 * Пример: Паттерн Одиночка печально известен тем, что ограничивает повторное
 * использование кода и усложняет модульное тестирование. Несмотря на это, он
 * всё же очень полезен в некоторых случаях. В частности, он удобен, когда
 * необходимо контролировать некоторые общие ресурсы. Например, глобальный
 * объект логирования, который должен управлять доступом к файлу журнала. Еще
 * один хороший пример: совместно используемое хранилище конфигурации среды
 * выполнения.
 */

/**
 * EN: If you need to support several types of Singletons in your app, you can
 * define the basic features of the Singleton in a base class, while moving the
 * actual business logic (like logging) to subclasses.
 *
 * RU: Если вам необходимо поддерживать в приложении несколько типов Одиночек,
 * вы можете определить основные функции Одиночки в базовом классе, тогда как
 * фактическую бизнес-логику (например, ведение журнала) перенести в подклассы.
 */
class Singleton
{
    /**
     * EN: The actual singleton's instance almost always resides inside a static
     * field. In this case, the static field is an array, where each subclass of
     * the Singleton stores its own instance.
     *
     * RU: Реальный экземпляр одиночки почти всегда находится внутри
     * статического поля. В этом случае статическое поле является массивом, где
     * каждый подкласс Одиночки хранит свой собственный экземпляр.
     */
    private static $instances = [];

    /**
     * EN: Singleton's constructor should not be public. However, it can't be
     * private either if we want to allow subclassing.
     *
     * RU: Конструктор Одиночки не должен быть публичным. Однако он не может
     * быть приватным, если мы хотим разрешить создание подклассов.
     */
    protected function __construct() { }

    /**
     * EN: Cloning and unserialization are not permitted for singletons.
     *
     * RU: Клонирование и десериализация не разрешены для одиночек.
     */
    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * EN: The method you use to get the Singleton's instance.
     *
     * RU: Метод, используемый для получения экземпляра Одиночки.
     */
    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            // EN: Note that here we use the "static" keyword instead of the
            // actual class name. In this context, the "static" keyword means
            // "the name of the current class". That detail is important because
            // when the method is called on the subclass, we want an instance of
            // that subclass to be created here.
            //
            // RU: Обратите внимание, что здесь мы используем ключевое слово
            // "static"  вместо фактического имени класса. В этом контексте
            // ключевое слово "static" означает «имя текущего класса». Эта
            // особенность важна, потому что, когда метод вызывается в
            // подклассе, мы хотим, чтобы экземпляр этого подкласса был создан
            // здесь.

            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}

/**
 * EN: The logging class is the most known and praised use of the Singleton
 * pattern. In most cases, you need a single logging object that writes to a
 * single log file (control over shared resource). You also need a convenient
 * way to access that instance from any context of your app (global access
 * point).
 *
 * RU: Класс ведения журнала является наиболее известным и похвальным
 * использованием паттерна Одиночка.
 */
class Logger extends Singleton
{
    /**
     * EN: A file pointer resource of the log file.
     *
     * RU: Ресурс указателя файла файла журнала.
     */
    private $fileHandle;

    /**
     * EN: Since the Singleton's constructor is called only once, just a single
     * file resource is opened at all times.
     *
     * Note, for the sake of simplicity, we open the console stream instead of
     * the actual file here.
     *
     * RU: Поскольку конструктор Одиночки вызывается только один раз, постоянно
     * открыт всего лишь один файловый ресурс.
     *
     * Обратите внимание, что для простоты мы открываем здесь консольный поток
     * вместо фактического файла.
     */
    protected function __construct()
    {
        $this->fileHandle = fopen('php://stdout', 'w');
    }

    /**
     * EN: Write a log entry to the opened file resource.
     *
     * RU: Пишем запись в журнале в открытый файловый ресурс.
     */
    public function writeLog(string $message): void
    {
        $date = date('Y-m-d');
        fwrite($this->fileHandle, "$date: $message\n");
    }

    /**
     * EN: Just a handy shortcut to reduce the amount of code needed to log
     * messages from the client code.
     *
     * RU: Просто удобный ярлык для уменьшения объёма кода, необходимого для
     * регистрации сообщений из клиентского кода.
     */
    public static function log(string $message): void
    {
        $logger = static::getInstance();
        $logger->writeLog($message);
    }
}

/**
 * EN: Applying the Singleton pattern to the configuration storage is also a
 * common practice. Often you need to access application configurations from a
 * lot of different places of the program. Singleton gives you that comfort.
 *
 * RU: Применение паттерна Одиночка в хранилище настроек – тоже обычная
 * практика. Часто требуется получить доступ к настройкам приложений из самых
 * разных мест программы. Одиночка предоставляет это удобство.
 */
class Config extends Singleton
{
    private $hashmap = [];

    public function getValue(string $key): string
    {
        return $this->hashmap[$key];
    }

    public function setValue(string $key, string $value): void
    {
        $this->hashmap[$key] = $value;
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
Logger::log("Started!");

// EN: Compare values of Logger singleton.
//
// RU: Сравниваем значения одиночки-Логгера.
$l1 = Logger::getInstance();
$l2 = Logger::getInstance();
if ($l1 === $l2) {
    Logger::log("Logger has a single instance.");
} else {
    Logger::log("Loggers are different.");
}

// EN: Check how Config singleton saves data...
//
// RU: Проверяем, как одиночка-Конфигурация сохраняет данные...
$config1 = Config::getInstance();
$login = "test_login";
$password = "test_password";
$config1->setValue("login", $login);
$config1->setValue("password", $password);
// EN: ...and restores it.
//
// RU: ...и восстанавливает их.
$config2 = Config::getInstance();
if ($login == $config2->getValue("login") &&
    $password == $config2->getValue("password")
) {
    Logger::log("Config singleton also works fine.");
}

Logger::log("Finished!");
