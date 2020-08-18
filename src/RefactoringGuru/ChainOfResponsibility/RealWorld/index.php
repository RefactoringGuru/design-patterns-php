<?php

namespace RefactoringGuru\ChainOfResponsibility\RealWorld;

/**
 * EN: Chain of Responsibility Design Pattern
 *
 * Intent: Lets you pass requests along a chain of handlers. Upon receiving a
 * request, each handler decides either to process the request or to pass it to
 * the next handler in the chain.
 *
 * Example: The most widely known use of the Chain of Responsibility (CoR)
 * pattern in the PHP world is found in HTTP request middleware. These are
 * implemented by most popular PHP frameworks and even got standardized as part
 * of PSR-15.
 *
 * It works like this: an HTTP request must pass through a stack of middleware
 * objects in order to be handled by the app. Each middleware can either reject
 * the further processing of the request or pass it to the next middleware. Once
 * the request successfully passes all middleware, the primary handler of the
 * app can finally handle it.
 *
 * You might have noticed that this approach is kind of inverse to the original
 * intent of the pattern. Indeed, in the typical implementation, a request is
 * only passed along a chain if a current handler CANNOT process it, while a
 * middleware passes the request further down the chain when it thinks that the
 * app CAN handle the request. Nevertheless, since middleware are chained, the
 * whole concept is still considered an example of the CoR pattern.
 *
 * RU: Паттерн Цепочка обязанностей
 *
 * Назначение: Позволяет передавать запросы последовательно по цепочке
 * обработчиков. Каждый последующий обработчик решает, может ли он обработать
 * запрос сам и стоит ли передавать запрос дальше по цепи.
 *
 * Пример: Пожалуй, самым известным применением паттерна Цепочка обязанностей
 * (CoR) в мире PHP являются промежуточные обработчики HTTP-запросов, называемые
 * middleware. Они стали настолько популярными, что были реализованы в самом
 * языке как часть PSR-15.
 *
 * Всё это работает следующим образом: HTTP-запрос должен пройти через стек
 * объектов middleware, прежде чем приложение его обработает. Каждое middleware
 * может либо отклонить дальнейшую обработку запроса, либо передать его
 * следующему middleware. Как только запрос успешно пройдёт все middleware,
 * основной обработчик приложения сможет окончательно его обработать.
 *
 * Можно отметить, что такой подход – своего рода инверсия первоначального
 * замысла паттерна. Действительно, в стандартной реализации запрос передаётся
 * по цепочке только в том случае, если текущий обработчик НЕ МОЖЕТ его
 * обработать, тогда как middleware передаёт запрос дальше по цепочке, когда
 * считает, что приложение МОЖЕТ обработать запрос. Тем не менее, поскольку
 * middleware соединены цепочкой, вся концепция по-прежнему считается примером
 * паттерна CoR.
 */

/**
 * EN: The classic CoR pattern declares a single role for objects that make up a
 * chain, which is a Handler. In our example, let's differentiate between
 * middleware and a final application's handler, which is executed when a
 * request gets through all the middleware objects.
 *
 * The base Middleware class declares an interface for linking middleware
 * objects into a chain.
 *
 * RU: Классический паттерн CoR объявляет для объектов, составляющих цепочку,
 * единственную роль – Обработчик. В нашем примере давайте проводить различие
 * между middleware и конечным обработчиком приложения, который выполняется,
 * когда запрос проходит через все объекты middleware.
 *
 * Базовый класс Middleware объявляет интерфейс для связывания объектов
 * middleware в цепочку.
 */
abstract class Middleware
{
    /**
     * @var Middleware
     */
    private $next;

    /**
     * EN: This method can be used to build a chain of middleware objects.
     *
     * RU: Этот метод можно использовать для построения цепочки объектов
     * middleware.
     */
    public function linkWith(Middleware $next): Middleware
    {
        $this->next = $next;

        return $next;
    }

    /**
     * EN: Subclasses must override this method to provide their own checks. A
     * subclass can fall back to the parent implementation if it can't process a
     * request.
     *
     * RU: Подклассы должны переопределить этот метод, чтобы предоставить свои
     * собственные проверки. Подкласс может обратиться к родительской реализации
     * проверки, если сам не в состоянии обработать запрос.
     */
    public function check(string $email, string $password): bool
    {
        if (!$this->next) {
            return true;
        }

        return $this->next->check($email, $password);
    }
}

/**
 * EN: This Concrete Middleware checks whether a user with given credentials
 * exists.
 *
 * RU: Это Конкретное Middleware проверяет, существует ли пользователь с
 * указанными учётными данными.
 */
class UserExistsMiddleware extends Middleware
{
    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function check(string $email, string $password): bool
    {
        if (!$this->server->hasEmail($email)) {
            echo "UserExistsMiddleware: This email is not registered!\n";

            return false;
        }

        if (!$this->server->isValidPassword($email, $password)) {
            echo "UserExistsMiddleware: Wrong password!\n";

            return false;
        }

        return parent::check($email, $password);
    }
}

/**
 * EN: This Concrete Middleware checks whether a user associated with the
 * request has sufficient permissions.
 *
 * RU: Это Конкретное Middleware проверяет, имеет ли пользователь, связанный с
 * запросом, достаточные права доступа.
 */
class RoleCheckMiddleware extends Middleware
{
    public function check(string $email, string $password): bool
    {
        if ($email === "admin@example.com") {
            echo "RoleCheckMiddleware: Hello, admin!\n";

            return true;
        }
        echo "RoleCheckMiddleware: Hello, user!\n";

        return parent::check($email, $password);
    }
}

/**
 * EN: This Concrete Middleware checks whether there are too many failed login
 * requests.
 *
 * RU: Это Конкретное Middleware проверяет, не было ли превышено максимальное
 * число неудачных запросов авторизации.
 */
class ThrottlingMiddleware extends Middleware
{
    private $requestPerMinute;

    private $request;

    private $currentTime;

    public function __construct(int $requestPerMinute)
    {
        $this->requestPerMinute = $requestPerMinute;
        $this->currentTime = time();
    }

    /**
     * EN: Please, note that the parent::check call can be inserted both at the
     * beginning of this method and at the end.
     *
     * This gives much more flexibility than a simple loop over all middleware
     * objects. For instance, a middleware can change the order of checks by
     * running its check after all the others.
     *
     * RU: Обратите внимание, что вызов parent::check можно вставить как в
     * начале этого метода, так и в конце.
     *
     * Это даёт значительно большую свободу действий, чем простой цикл по всем
     * объектам middleware. Например, middleware может изменить порядок
     * проверок, запустив свою проверку после всех остальных.
     */
    public function check(string $email, string $password): bool
    {
        if (time() > $this->currentTime + 60) {
            $this->request = 0;
            $this->currentTime = time();
        }

        $this->request++;

        if ($this->request > $this->requestPerMinute) {
            echo "ThrottlingMiddleware: Request limit exceeded!\n";
            die();
        }

        return parent::check($email, $password);
    }
}

/**
 * EN: This is an application's class that acts as a real handler. The Server
 * class uses the CoR pattern to execute a set of various authentication
 * middleware before launching some business logic associated with a request.
 *
 * RU: Это класс приложения, который осуществляет реальную обработку запроса.
 * Класс Сервер использует паттерн CoR для выполнения набора различных
 * промежуточных проверок перед запуском некоторой бизнес-логики, связанной с
 * запросом.
 */
class Server
{
    private $users = [];

    /**
     * @var Middleware
     */
    private $middleware;

    /**
     * EN: The client can configure the server with a chain of middleware
     * objects.
     *
     * RU: Клиент может настроить сервер с помощью цепочки объектов middleware.
     */
    public function setMiddleware(Middleware $middleware): void
    {
        $this->middleware = $middleware;
    }

    /**
     * EN: The server gets the email and password from the client and sends the
     * authorization request to the middleware.
     *
     * RU: Сервер получает email и пароль от клиента и отправляет запрос
     * авторизации в middleware.
     */
    public function logIn(string $email, string $password): bool
    {
        if ($this->middleware->check($email, $password)) {
            echo "Server: Authorization has been successful!\n";

            // EN: Do something useful for authorized users.
            //
            // RU: Выполняем что-нибудь полезное для авторизованных
            // пользователей.

            return true;
        }

        return false;
    }

    public function register(string $email, string $password): void
    {
        $this->users[$email] = $password;
    }

    public function hasEmail(string $email): bool
    {
        return isset($this->users[$email]);
    }

    public function isValidPassword(string $email, string $password): bool
    {
        return $this->users[$email] === $password;
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
$server = new Server();
$server->register("admin@example.com", "admin_pass");
$server->register("user@example.com", "user_pass");

// EN: All middleware are chained. The client can build various configurations
// of chains depending on its needs.
//
// RU: Все middleware соединены в цепочки. Клиент может построить различные
// конфигурации цепочек в зависимости от своих потребностей.
$middleware = new ThrottlingMiddleware(2);
$middleware
    ->linkWith(new UserExistsMiddleware($server))
    ->linkWith(new RoleCheckMiddleware());

// EN: The server gets a chain from the client code.
//
// RU: Сервер получает цепочку из клиентского кода.
$server->setMiddleware($middleware);

// ...

do {
    echo "\nEnter your email:\n";
    $email = readline();
    echo "Enter your password:\n";
    $password = readline();
    $success = $server->logIn($email, $password);
} while (!$success);
