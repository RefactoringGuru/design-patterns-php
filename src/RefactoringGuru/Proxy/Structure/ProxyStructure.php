<?php

namespace RefactoringGuru\Proxy\Structure;

/**
 * Proxy Design Pattern
 *
 * Intent: Provide a surrogate or placeholder for another object to control
 * access to it or add other responsibilities.
 */

/**
 * The common interface for RealSubject and Proxy. It allows passing Proxy
 * objects to any method, which expects a RealSubject.
 */
interface Subject
{
    public function request();
}

/**
 * Contains a core business logic. Usually, real subjects do some useful, but
 * very slow or insecure work. Proxies can solve these issues without
 * any changes to a real subject's code.
 */
class RealSubject implements Subject
{
    public function request()
    {
        echo "REAL_SUBJECT: Handling request.\n";
    }
}

/**
 * Proxy has an interface identical to Subject's.
 */
class Proxy implements Subject
{
    /**
     * @var RealSubject
     */
    private $realSubject;

    /**
     * Proxy object maintains a reference that lets the proxy access the
     * object of real subject.
     */
    public function __construct(RealSubject $realSubject)
    {
        $this->realSubject = $realSubject;
    }

    /**
     * The most common applications of proxy are: lazy loading, caching,
     * controlling the access, logging, etc. Proxy does on of these things
     * inside its subject methods and then executes the same method in a linked
     * real subject object.
     */
    public function request()
    {
        if ($this->checkAccess()) {
            $this->realSubject->request();
            $this->logAccess();
        }
    }

    private function checkAccess()
    {
        // Some real checks should go here, bu we just:
        echo "PROXY: Checking access prior to firing a real request.\n";
        return true;
    }

    private function logAccess()
    {
        echo "PROXY: Logging the time of request.\n";
    }

}

/**
 * Client code is suppose to work with a common subject interface in order to
 * support both real subjects and proxies. In real life, clients usually
 * already work with real subjects directly, so you create proxy by extending
 * the real subject class.
 */
function clientCode(Subject $subject)
{
    // ...

    echo $subject->request();

    // ...
}

echo "Executing client code with real subject:\n";
$realSubject = new RealSubject();
clientCode($realSubject);

echo "\n";

echo "Executing the same client code with a proxy:\n";
$proxy = new Proxy($realSubject);
clientCode($proxy);