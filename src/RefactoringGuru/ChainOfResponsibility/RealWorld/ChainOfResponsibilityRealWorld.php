<?php

namespace RefactoringGuru\ChainOfResponsibility\RealWorld;

/**
 * Chain of Responsibility Design Pattern
 *
 * Intent: Avoid coupling the sender of a request to its receiver by giving more
 * than one object a chance to handle the request. Chain the receiving objects
 * and pass the request along the chain until an object handles it.
 *
 * Example: In this example, the Chain of Responsibility pattern helps to
 * structure authentication and authorization as a chain and execute them one by
 * one.
 */

/**
 * Base Handler.
 */
abstract class Middleware
{
    /**
     * @var Middleware
     */
    private $next;

    /**
     * Builds chains of middleware objects.
     */
    public function linkWith(Middleware $next): Middleware
    {
        $this->next = $next;

        return $next;
    }

    /**
     * Subclasses will implement this method with concrete checks.
     */
    public abstract function check(string $email, string $password): bool;

    /**
     * Runs check on the next object in chain or ends traversing if we're in
     * last object in chain.
     */
    protected function checkNext(string $email, string $password): bool
    {
        if (! $this->next) {
            return true;
        }

        return $this->next->check($email, $password);
    }
}

/**
 * Concrete Handler. Checks whether a user with the given credentials exists.
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

        return $this->checkNext($email, $password);
    }
}

/**
 * Concrete Handler. Checks a user's role.
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

        return $this->checkNext($email, $password);
    }
}

/**
 * Concrete Handler. Checks whether there are too many failed login requests.
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
     * Please, not that checkNext() call can be inserted both in the beginning
     * of this method and in the end.
     *
     * This gives much more flexibility than a simple loop over all middleware
     * objects. For instance, an element of a chain can change the order of
     * checks by running its check after all other checks.
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

        return $this->checkNext($email, $password);
    }
}

/**
 * Client code. Server class uses the Chain of Responsibility pattern to execute
 * various authentication handlers one by one.
 */
class Server
{
    /**
     * @var array
     */
    private $users = [];

    /**
     * @var Middleware
     */
    private $middleware;

    /**
     * Client passes a chain of object to server. This improves flexibility and
     * makes testing the server class easier.
     */
    public function setMiddleware(Middleware $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * Server gets email and password from client and sends the authorization
     * request to the chain.
     */
    public function logIn(string $email, string $password)
    {
        if ($this->middleware->check($email, $password)) {
            print("Server: Authorization has been successful!\n");

            // Do something useful here for authorized users.

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
 * Client code.
 */
$server = new Server();
$server->register("admin@example.com", "admin_pass");
$server->register("user@example.com", "user_pass");

// All checks are linked. Client can build various chains using the same
// components.
$middleware = new ThrottlingMiddleware(2);
$middleware
    ->linkWith(new UserExistsMiddleware($server))
    ->linkWith(new RoleCheckMiddleware());

// Server gets a chain from client code.
$server->setMiddleware($middleware);

// ...

do {
    print("\nEnter your email:\n");
    $email = readline();
    print("Enter your password:\n");
    $password = readline();
    $success = $server->logIn($email, $password);
} while (! $success);
