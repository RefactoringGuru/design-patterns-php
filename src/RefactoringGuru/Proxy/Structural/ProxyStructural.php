<?php

namespace RefactoringGuru\Proxy\Structural;

/**
 * Proxy Design Pattern
 *
 * Intent: Provide a surrogate or placeholder for another object to control
 * access to the original object or to add other responsibilities.
 */

/**
 * The Subject interface declares common operations for both RealSubject
 * and the Proxy. As long as the client works with RealSubject using this
 * interface, you'll be able to pass it a proxy instead of a real subject.
 */
interface Subject
{
    public function request();
}

/**
 * The RealSubject contains some core business logic. Usually, RealSubjects are
 * capable of doing some useful work which may also be very slow or sensitive -
 * e.g. correcting input data. A Proxy can solve these issues without any 
 * changes to the real subject's code.
 */
class RealSubject implements Subject
{
    public function request()
    {
        print("RealSubject: Handling request.\n");
    }
}

/**
 * The Proxy has an interface identical to the RealSubject.
 */
class Proxy implements Subject
{
    /**
     * @var RealSubject
     */
    private $realSubject;

    /**
     * The Proxy maintains a reference to an object of the RealSubject class. It
     * can be either lazy-loaded or passed to the Proxy by the client.
     */
    public function __construct(RealSubject $realSubject)
    {
        $this->realSubject = $realSubject;
    }

    /**
     * The most common applications of the Proxy pattern are lazy loading,
     * caching, controlling the access, logging, etc. A Proxy can perform one of
     * these things and then, depending on the result, pass the execution to the
     * same method in a linked RealSubject object.
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
        // Some real checks should go here.
        print("Proxy: Checking access prior to firing a real request.\n");

        return true;
    }

    private function logAccess()
    {
        print("Proxy: Logging the time of request.\n");
    }
}

/**
 * The client code is supposed to work with all objects (both subjects and
 * proxies) via the Subject interface in order to support both real subjects and
 * proxies. In real life, however, clients mostly work with their real subjects
 * directly. In this case, to implement the pattern more easily, you can extend
 * your proxy from the real subject's class.
 */
function clientCode(Subject $subject)
{
    // ...

    print($subject->request());

    // ...
}

print("Client: Executing the client code with a real subject:\n");
$realSubject = new RealSubject();
clientCode($realSubject);

print("\n");

print("Client: Executing the same client code with a proxy:\n");
$proxy = new Proxy($realSubject);
clientCode($proxy);