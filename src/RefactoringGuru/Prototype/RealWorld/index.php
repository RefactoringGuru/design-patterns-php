<?php

namespace RefactoringGuru\Prototype\RealWorld;

/**
 * EN: Prototype Design Pattern
 *
 * Intent: Lets you copy existing objects without making your code dependent on
 * their classes.
 *
 * Example: The Prototype pattern provides a convenient way to replicate
 * existing objects instead of reconstructing them and copying over all of their
 * fields directly. The direct approach not only couples you to the classes of
 * the objects being cloned, but also doesn't allow you to copy the contents of
 * the private fields. The Prototype pattern lets you perform the cloning within
 * the context of the cloned class, where the access to the class' private
 * fields is not restricted.
 *
 * This example shows you how to clone a complex Page object using the Prototype
 * pattern. The Page class has lots of private fields, which will be carried
 * over to the cloned object thanks to the Prototype pattern.
 *
 * RU: Паттерн Прототип
 *
 * Назначение: Позволяет копировать объекты, не вдаваясь в подробности их
 * реализации.
 *
 * Пример: Паттерн Прототип предоставляет удобный способ репликации существующих
 * объектов вместо их восстановления и копирования всех полей напрямую. Прямое
 * копирование не только связывает вас с классами клонируемых объектов, но и не
 * позволяет копировать содержимое приватных полей. Паттерн Прототип позволяет
 * выполнять клонирование в контексте клонированного класса, где доступ к
 * приватным полям класса не ограничен.
 *
 * В этом примере показано, как клонировать сложный объект Страницы, используя
 * паттерн Прототип. Класс Страница имеет множество приватных полей, которые
 * будут перенесены в клонированный объект благодаря паттерну Прототип.
 */

/**
 * EN: Prototype.
 *
 * RU: Прототип.
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

    // EN: +100 private fields.
    //
    // RU: +100 приватных полей.

    public function __construct(string $title, string $body, Author $author)
    {
        $this->title = $title;
        $this->body = $body;
        $this->author = $author;
        $this->author->addToPage($this);
        $this->date = new \DateTime();
    }

    public function addComment(string $comment): void
    {
        $this->comments[] = $comment;
    }

    /**
     * EN: You can control what data you want to carry over to the cloned
     * object.
     *
     * For instance, when a page is cloned:
     * - It gets a new "Copy of ..." title.
     * - The author of the page remains the same. Therefore we leave the
     * reference to the existing object while adding the cloned page to the list
     * of the author's pages.
     * - We don't carry over the comments from the old page.
     * - We also attach a new date object to the page.
     *
     * RU: Вы можете контролировать, какие данные вы хотите перенести в
     * клонированный объект.
     *
     * Например, при клонировании страницы:
     * - Она получает новый заголовок «Копия ...».
     * - Автор страницы остаётся прежним. Поэтому мы оставляем ссылку на
     * существующий объект, добавляя клонированную страницу в список страниц
     * автора.
     * - Мы не переносим комментарии со старой страницы.
     * - Мы также прикрепляем к странице новый объект даты.
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

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addToPage(Page $page): void
    {
        $this->pages[] = $page;
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
function clientCode()
{
    $author = new Author("John Smith");
    $page = new Page("Tip of the day", "Keep calm and carry on.", $author);

    // ...

    $page->addComment("Nice tip, thanks!");

    // ...

    $draft = clone $page;
    echo "Dump of the clone. Note that the author is now referencing two objects.\n\n";
    print_r($draft);
}

clientCode();
