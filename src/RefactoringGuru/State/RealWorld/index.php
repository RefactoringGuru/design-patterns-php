<?php

namespace RefactoringGuru\State\RealWorld;

/**
 * EN: State Design Pattern
 *
 * Intent: Lets an object alter its behavior when its internal state changes. It
 * appears as if the object changed its class.
 */

/**
 * EN: The base State class declares methods that all Concrete State should
 * implement and also provides a backreference to the Context object, associated
 * with the State. This backreference can be used by States to transition the
 * Context to another State.
 */

abstract class State
{

    /**
     * @var Article
     */
    protected $article;

    public function setArticle(Article $article): void
    {
        $this->article = $article;
    }

    abstract public function getState(): void;
}

/**
 * EN: Concrete States (Pending / Published) implement various behaviors,
 * associated with a state of the Article.
 */

class Pending extends State
{
    public function getState(): void
    {
        echo "Pending";
    }
}


class Published extends State
{
    public function getState(): void
    {
        echo "Published";
    }
}

/**
 * EN: The Article allows changing the State object at runtime.
 */

class Article
{

    /**
     * $state
     *
     * @var State
     */
    protected $state;

    /**
     *
     * @param   State  $state  the concrete state object
     *
     */
    public function __construct(State $state)
    {
        $this->setState($state);
    }

    /**
     * setStatus - Set the article status
     *
     * @param   State  $state  state object
     *
     * @return  void
     */

    public function setState(State $state): void
    {
        echo "Context: Transition to " . get_class($state) . ".\n";
        $this->state = $state;
        $this->state->setArticle($this);
    }
    /**
     * getStatus - get the current status
     *
     * @return  string  the current status
     */
    public function getState()
    {
        return $this->state->getState();
    }
}

/**
 * EN: the client code
 */

$post = new Article(new Pending());

$post->getState(); // "Pending"

$post->setState(new Published());
$post->getState(); // "Published"
