<?php

namespace RefactoringGuru\FactoryMethod\RealLife;

/**
 * Factory Method Design Pattern
 *
 * Intent: Define an interface for creating an object, but let subclasses decide
 * which class to instantiate. Factory Method lets a class defer instantiation
 * to subclasses.
 *
 * Example: Factory method provides interface for creating social network
 * connectors that are used to log in and create posts in various social
 * networks. To add new social network support, you need to create a new
 * creator's subclass and create a different social connector object in it.
 */

/**
 * Creator.
 */
abstract class SocialNetworkPoster
{
    /**
     * Factory Method.
     */
    public abstract function getSocialNetwork(): SocialNetworkConnector;

    /**
     * Primary business logic. Will be reused by all subclasses.
     */
    public function post($content)
    {
        // Call the factory method to create a Product object.
        $network = $this->getSocialNetwork();
        $network->logIn();
        $network->createPost($content);
        $network->logout();
    }
}

/**
 * ConcreteCreator.
 */
class FacebookPoster extends SocialNetworkPoster
{
    private $login, $password;

    public function __construct($login, $password)
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
 * ConcreteCreator.
 */
class LinkedInPoster extends SocialNetworkPoster
{
    private $email, $password;

    public function __construct($email, $password)
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
 * Product.
 */
interface SocialNetworkConnector
{
    public function logIn();

    public function logOut();

    public function createPost($content);
}

/**
 * ConcreteProduct.
 */
class FacebookConnector implements SocialNetworkConnector
{
    private $login, $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function logIn()
    {
        print("Send HTTP API request to log in user $this->login with " .
            "password $this->password\n");
    }

    public function logOut()
    {
        print("Send HTTP API request to log out user $this->login\n");
    }

    public function createPost($content)
    {
        print("Send HTTP API requests to create a post in Facebook timeline.\n");
    }
}

/**
 * ConcreteProduct.
 */
class LinkedInConnector implements SocialNetworkConnector
{
    private $email, $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function logIn()
    {
        print("Send HTTP API request to log in user $this->email with " .
            "password $this->password\n");
    }

    public function logOut()
    {
        print("Send HTTP API request to log out user $this->email\n");
    }

    public function createPost($content)
    {
        print("Send HTTP API requests to create a post in LinkedIn timeline.\n");
    }
}

/**
 * Client code.
 */
function clientCode(SocialNetworkPoster $creator)
{
    // ...
    $creator->post("Hello world!");
    $creator->post("I had a large burger this morning!");
    // ...
}

/**
 * Application initialization.
 */
print("Testing ConcreteCreator1:\n");
clientCode(new FacebookPoster("john_smith", "******"));
print("\n\n");

print("Testing ConcreteCreator2:\n");
clientCode(new LinkedInPoster("john_smith@example.com", "******"));
