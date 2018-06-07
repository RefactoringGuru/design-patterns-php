<?php

namespace RefactoringGuru\ChainOfResponsibility\RealWorld;

/**
 * Chain of Responsibility Design Pattern
 *
 * Intent: Avoid coupling a sender of a request to its receiver by giving more
 * than one object a chance to handle the request. Chain the receiving objects
 * and then pass the request through the chain until some receiver handles it.
 *
 * Example: The most widely known use of the Chain of Responsibility (CoR)
 * pattern in PHP world are request middleware. They are implemented by most
 * popular PHP frameworks and even got standardized as part of PSR-15.
 *
 * It works like this: an HTTP request must pass though a stack of middleware
 * objects in order be handled by the app. Each middleware can either reject the
 * further processing of the request or pass it to the next middleware. Once the
 * request successfully passes all middleware, the primary handler of the app
 * can finally handle it.
 *
 * You might have noticed that this approach is kind of inverse to the original
 * intent of the pattern. Indeed, in the classic implementation a request is
 * only passed along a chain if a current handler CANNOT process it, while a
 * middleware passes the request further down the chain when it thinks that the
 * app CAN handle the request. Nevertheless, since middleware are chained, the
 * whole concept is still considered an example of the CoR pattern.
 */

/**
 * The classic CoR pattern declares a single role for objects that make-up a
 * chain, which is a Handler. In our example, let's differentiate between
 * middleware and a final application's handler, which is executed when a
 * request gets through all the middleware objects.
 *
 * The base Middleware class declares interface for linking middleware objects
 * into a chain.
 */
abstract class Middleware
{
    /**
     * @var Middleware
     */
    private $next;

    /**
     * This method can be used to build a chain of middleware objects.
     */
    public function linkWith(Middleware $next): Middleware
    {
        $this->next = $next;

        return $next;
    }

    /**
     * Subclasses must override this method to provide their own checks. A
     * subclass can fall-back to the parent implementation if it can't process a
     * request.
     */
    public function check(string $email, string $password): bool
    {
        if (! $this->next) {
            return true;
        }

        return $this->next->check($email, $password);
    }
}

/**
 * This Concrete Middleware checks whether a user with given credentials exists.
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
        if (! $this->server->hasEmail($email)) {
            print("UserExistsMiddleware: This email is not registered!\n");

            return false;
        }

        if (! $this->server->isValidPassword($email, $password)) {
            print("UserExistsMiddleware: Wrong password!\n");

            return false;
        }

        return parent::check($email, $password);
    }
}

/**
 * This Concrete Middleware checks whether a user associated with the request
 * has sufficient permissions.
 */
class RoleCheckMiddleware extends Middleware
{
    public function check(string $email, string $password): bool
    {
        if ($email === "admin@example.com") {
            print("RoleCheckMiddleware: Hello, admin!\n");

            return true;
        }
        print("RoleCheckMiddleware: Hello, user!\n");

        return parent::check($email, $password);
    }
}

/**
 * This Concrete Middleware checks whether there are too many failed login
 * requests.
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
     * Please, not that the parent::check call can be inserted both in the
     * beginning of this method and in the end.
     *
     * This gives much more flexibility than a simple loop over all middleware
     * objects. For instance, a middleware can change the order of checks by
     * running its check after all the others.
     */
    public function check(string $email, string $password): bool
    {
        if (time() > $this->currentTime + 60) {
            $this->request = 0;
            $this->currentTime = time();
        }

        $this->request++;

        if ($this->request > $this->requestPerMinute) {
            print("ThrottlingMiddleware: Request limit exceeded!\n");
            die();
        }

        return parent::check($email, $password);
    }
}

/**
 * This is an application's class, that acts as a real handler. The Server class
 * uses the CoR pattern to execute a set of various authentication middleware
 * prior to launching some business logic associated with a request.
 */
class Server
{
    private $users = [];

    /**
     * @var Middleware
     */
    private $middleware;

    /**
     * The client can configure the server with a chained middleware list.
     */
    public function setMiddleware(Middleware $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * The server gets email and password from the client and sends the
     * authorization request to the middleware.
     */
    public function logIn(string $email, string $password)
    {
        if ($this->middleware->check($email, $password)) {
            print("Server: Authorization has been successful!\n");

            // Do something useful for authorized users.

            return true;
        }

        return false;
    }

    public function register(string $email, string $password)
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
 * The client code.
 */
$server = new Server();
$server->register("admin@example.com", "admin_pass");
$server->register("user@example.com", "user_pass");

// All middleware are chained. The client can build various configurations of
// chains depending on its needs.
$middleware = new ThrottlingMiddleware(2);
$middleware
    ->linkWith(new UserExistsMiddleware($server))
    ->linkWith(new RoleCheckMiddleware());

// The server gets a chain from the client code.
$server->setMiddleware($middleware);

// ...

do {
    print("\nEnter your email:\n");
    $email = readline();
    print("Enter your password:\n");
    $password = readline();
    $success = $server->logIn($email, $password);
} while (! $success);
