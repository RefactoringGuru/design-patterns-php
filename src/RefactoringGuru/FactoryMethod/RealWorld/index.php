<?php

namespace RefactoringGuru\FactoryMethod\RealWorld;

/**
 * EN: Factory Method Design Pattern
 *
 * Intent: Provides an interface for creating objects in a superclass, but
 * allows subclasses to alter the type of objects that will be created.
 *
 * Example: In this example, the Factory Method pattern provides an interface
 * for creating social network connectors, which can be used to log in to the
 * network, create posts and potentially perform other activities—and all of
 * this without coupling the client code to specific classes of the particular
 * social network.
 *
 * RU: Паттерн Фабричный Метод
 *
 * Назначение: Определяет общий интерфейс для создания объектов в суперклассе,
 * позволяя подклассам изменять тип создаваемых объектов.
 *
 * Пример: В этом примере паттерн Фабричный Метод предоставляет интерфейс для
 * создания коннекторов к социальным сетям, которые могут быть использованы для
 * входа в сеть, создания сообщений и, возможно, выполнения других действий, – и
 * всё это без привязки клиентского кода к определённым классам конкретной
 * социальной сети.
 */

/**
 * EN: The Creator declares a factory method that can be used as a substitution
 * for the direct constructor calls of products, for instance:
 *
 * - Before: $p = new FacebookConnector();
 * - After: $p = $this->getSocialNetwork;
 *
 * This allows changing the type of the product being created by
 * SocialNetworkPoster's subclasses.
 *
 * RU: Создатель объявляет фабричный метод, который может быть использован
 * вместо прямых вызовов конструктора продуктов, например:
 *
 * - До: $p = new FacebookConnector();
 * - После: $p = $this->getSocialNetwork;
 *
 * Это позволяет подклассам SocialNetworkPoster изменять тип создаваемого
 * продукта.
 */
abstract class SocialNetworkPoster
{
    /**
     * EN: The actual factory method. Note that it returns the abstract
     * connector. This lets subclasses return any concrete connectors without
     * breaking the superclass' contract.
     *
     * RU: Фактический фабричный метод. Обратите внимание, что он возвращает
     * абстрактный коннектор. Это позволяет подклассам возвращать любые
     * конкретные коннекторы без нарушения контракта суперкласса.
     */
    abstract public function getSocialNetwork(): SocialNetworkConnector;

    /**
     * EN: When the factory method is used inside the Creator's business logic,
     * the subclasses may alter the logic indirectly by returning different
     * types of the connector from the factory method.
     *
     * RU: Когда фабричный метод используется внутри бизнес-логики Создателя,
     * подклассы могут изменять логику косвенно, возвращая из фабричного метода
     * различные типы коннекторов.
     */
    public function post($content): void
    {
        // EN: Call the factory method to create a Product object...
        //
        // RU: Вызываем фабричный метод для создания объекта Продукта...
        $network = $this->getSocialNetwork();
        // EN: ...then use it as you will.
        //
        // RU: ...а затем используем его по своему усмотрению.
        $network->logIn();
        $network->createPost($content);
        $network->logout();
    }
}

/**
 * EN: This Concrete Creator supports Facebook. Remember that this class also
 * inherits the 'post' method from the parent class. Concrete Creators are the
 * classes that the Client actually uses.
 *
 * RU: Этот Конкретный Создатель поддерживает Facebook. Помните, что этот класс
 * также наследует метод post от родительского класса. Конкретные Создатели —
 * это классы, которые фактически использует Клиент.
 */
class FacebookPoster extends SocialNetworkPoster
{
    private $login, $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector
    {
        return new FacebookConnector($this->login, $this->password);
    }
}

/**
 * EN: This Concrete Creator supports LinkedIn.
 *
 * RU: Этот Конкретный Создатель поддерживает LinkedIn.
 */
class LinkedInPoster extends SocialNetworkPoster
{
    private $email, $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector
    {
        return new LinkedInConnector($this->email, $this->password);
    }
}

/**
 * EN: The Product interface declares behaviors of various types of products.
 *
 * RU: Интерфейс Продукта объявляет поведения различных типов продуктов.
 */
interface SocialNetworkConnector
{
    public function logIn(): void;

    public function logOut(): void;

    public function createPost($content): void;
}

/**
 * EN: This Concrete Product implements the Facebook API.
 *
 * RU: Этот Конкретный Продукт реализует API Facebook.
 */
class FacebookConnector implements SocialNetworkConnector
{
    private $login, $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function logIn(): void
    {
        echo "Send HTTP API request to log in user $this->login with " .
            "password $this->password\n";
    }

    public function logOut(): void
    {
        echo "Send HTTP API request to log out user $this->login\n";
    }

    public function createPost($content): void
    {
        echo "Send HTTP API requests to create a post in Facebook timeline.\n";
    }
}

/**
 * EN: This Concrete Product implements the LinkedIn API.
 *
 * RU: А этот Конкретный Продукт реализует API LinkedIn.
 */
class LinkedInConnector implements SocialNetworkConnector
{
    private $email, $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function logIn(): void
    {
        echo "Send HTTP API request to log in user $this->email with " .
            "password $this->password\n";
    }

    public function logOut(): void
    {
        echo "Send HTTP API request to log out user $this->email\n";
    }

    public function createPost($content): void
    {
        echo "Send HTTP API requests to create a post in LinkedIn timeline.\n";
    }
}

/**
 * EN: The client code can work with any subclass of SocialNetworkPoster since
 * it doesn't depend on concrete classes.
 *
 * RU: Клиентский код может работать с любым подклассом SocialNetworkPoster, так
 * как он не зависит от конкретных классов.
 */
function clientCode(SocialNetworkPoster $creator)
{
    // ...
    $creator->post("Hello world!");
    $creator->post("I had a large hamburger this morning!");
    // ...
}

/**
 * EN: During the initialization phase, the app can decide which social network
 * it wants to work with, create an object of the proper subclass, and pass it
 * to the client code.
 *
 * RU: На этапе инициализации приложение может выбрать, с какой социальной сетью
 * оно хочет работать, создать объект соответствующего подкласса и передать его
 * клиентскому коду.
 */
echo "Testing ConcreteCreator1:\n";
clientCode(new FacebookPoster("john_smith", "******"));
echo "\n\n";

echo "Testing ConcreteCreator2:\n";
clientCode(new LinkedInPoster("john_smith@example.com", "******"));
