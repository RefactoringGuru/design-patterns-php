<?php

namespace RefactoringGuru\Prototype\RealWorld;

/**
 * Prototype Design Pattern
 *
 * Intent: Specify the kinds of objects to create using a prototypical instance,
 * and create new objects by copying this prototype.
 *
 * Example: Prototype provides a convenient way to replicate existing objects
 * instead of re-constructing them and copying over all of their fields.
 * Prototype allows cloning even private field since the copying is performed
 * within the cloned class.
 */

/**
 * Prototype.
 */
class Page
{
    private $title;

    private $body;

    /**
     * @var Author
     */
    private $author;

    private $comments = [];

    /**
     * @var \DateTime
     */
    private $date;

    // +100 private fields.

    public function __construct($title, $body, $author)
    {
        $this->title = $title;
        $this->body = $body;
        $this->author = $author;
        $this->author->addToPage($this);
        $this->date = new \DateTime();
    }

    public function addComment($comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * When a page is cloned, it should get a new default title and empty
     * comments.
     *
     * Author of the page remains the same, therefore we leave the reference to
     * existing object. But we add the clone to list of his pages.
     *
     * A clone also gets a new date object.
     */
    public function __clone()
    {
        $this->title = "Copy of " . $this->title;
        $this->author->addToPage($this);
        $this->comments = [];
        $this->date = new \DateTime();
    }
}

class Author
{
    private $name;

    /**
     * @var Page[]
     */
    private $pages = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function addToPage($page)
    {
        $this->pages[] = $page;
    }
}

/**
 * Client code.
 */
function clientCode()
{
    $author = new Author("John Smith");
    $page = new Page("Tip of the day", "Keep calm and carry on.", $author);

    // ...

    $page->addComment("Nice tip, thanks!");

    // ...

    $draft = clone $page;
    print("Dump of the clone. Note that the author is now referencing two objects.\n\n");
    print_r($draft);
}

clientCode();
