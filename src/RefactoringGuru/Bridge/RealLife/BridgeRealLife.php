<?php

namespace RefactoringGuru\Bridge\RealLife;

/**
 * Bridge Design Pattern
 *
 * Intent: Decouple an abstraction from its implementation so that the two can
 * vary independently.
 *
 *               A
 *            /     \                        A         N
 *          Aa      Ab        ===>        /     \     / \
 *         / \     /  \                 Aa(N) Ab(N)  1   2
 *        Aa1 Aa2  Ab1 Ab2
 *
 * Example: Bridge pattern allows to connect orthogonal class hierarchies
 * instead of merging them together. The Page hierarchy acts as abstraction, it
 * is responsible for assembling various pages from a basic page elements
 * provided by a renderer classes, that play role of implementation. While these
 * hierarchies are separated, one can add a new renderer type without touching
 * page classes and vise versa.
 */

/**
 * Abstraction's interface.
 */
abstract class Page
{
    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * Abstraction usually initialized with one of the implementation objects.
     */
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Bridge pattern allows changing the implementation dynamically.
     */
    public function changeRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * View makes sense only in concrete abstractions, so we leave it abstract.
     */
    abstract public function view();
}

/**
 * Concrete abstraction.
 */
class SimplePage extends Page
{
    protected $title;
    protected $content;

    public function __construct(Renderer $renderer, $title, $content)
    {
        parent::__construct($renderer);
        $this->title = $title;
        $this->content = $content;
    }

    public function view(): string
    {
        return $this->renderer->renderParts([
            $this->renderer->renderHeader(),
            $this->renderer->renderTitle($this->title),
            $this->renderer->renderTextBlock($this->content),
            $this->renderer->renderFooter()
        ]);
    }
}

/**
 * Another concrete abstraction.
 */
class ProductPage extends Page
{
    protected $product;

    public function __construct(Renderer $renderer, Product $product)
    {
        parent::__construct($renderer);
        $this->product = $product;
    }

    public function view(): string
    {
        return $this->renderer->renderParts([
            $this->renderer->renderHeader(),
            $this->renderer->renderTitle($this->product->getTitle()),
            $this->renderer->renderTextBlock($this->product->getDescription()),
            $this->renderer->renderImage($this->product->getImage()),
            $this->renderer->renderLink("/cart/add/" . $this->product->getId(), "Add to cart"),
            $this->renderer->renderFooter()
        ]);
    }
}

/**
 * A helper class for the ProductPage.
 */
class Product
{
    private $id, $title, $description, $image, $price;

    public function __construct($id, $title, $description, $image, $price)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->price = $price;
    }

    public function getId() { return $this->id; }

    public function getTitle() { return $this->title; }

    public function getDescription() { return $this->description; }

    public function getImage() { return $this->image; }

    public function getPrice() { return $this->price; }
}


/**
 * Implementor.
 */
interface Renderer
{
    public function renderTitle(string $title): string;

    public function renderTextBlock(string $text): string;

    public function renderImage(string $url): string;

    public function renderLink(string $url, string $title): string;

    public function renderHeader(): string;

    public function renderFooter(): string;

    public function renderParts(array $parts): string;
}

/**
 * ConcreteImplementor. Renders stuff as HTML.
 */
class HTMLRenderer implements Renderer
{
    public function renderTitle(string $title): string
    {
        return "<h1>$title</h1>";
    }

    public function renderTextBlock(string $text): string
    {
        return "<div class='text'>$text</div>";
    }

    public function renderImage(string $url): string
    {
        return "<img src='$url'>";
    }

    public function renderLink(string $url, string $title): string
    {
        return "<a href='$url'>$title</a>";
    }

    public function renderHeader(): string
    {
        return "<html><body>";
    }

    public function renderFooter(): string
    {
        return "</body></html>";
    }

    public function renderParts(array $parts): string
    {
        return implode("\n", $parts);
    }
}

/**
 * ConcreteImplementor. Renders stuff as JSON strings.
 */
class JsonRenderer implements Renderer
{
    public function renderTitle(string $title): string
    {
        return '"title": "' . $title . '"';
    }

    public function renderTextBlock(string $text): string
    {
        return '"text": "' . $text . '"';
    }

    public function renderImage(string $url): string
    {
        return '"img": "' . $url . '"';
    }

    public function renderLink(string $url, string $title): string
    {
        return '"link": {"href": "' . $title . '", "title": "' . $title . '""}';
    }

    public function renderHeader(): string
    {
        return '';
    }

    public function renderFooter(): string
    {
        return '';
    }

    public function renderParts(array $parts): string
    {
        return "{\n" . implode(",\n", array_filter($parts)) . "\n}";
    }
}

/**
 * Client code. In most cases works only with abstraction.
 */
function clientCode(Page $page)
{
    // ...

    print($page->view());

    // ...
}

/**
 * Client code can be launched with any pre-configured abstraction-
 * implementation combination.
 */
$HTMLRenderer = new HTMLRenderer();
$JSONRenderer = new JsonRenderer();

$page = new SimplePage($HTMLRenderer, "Home", "Welcome to our website!");
print("HTML view of a simple content page:\n");
clientCode($page);
print("\n\n");

/**
 * Abstraction can change the implementation in run time if needed.
 */
$page->changeRenderer($JSONRenderer);
print("JSON view of a simple content page, rendered with a same client code:\n");
clientCode($page);
print("\n\n");


$product = new Product("123", "Star Wars, episode1",
    "A long time ago in a galaxy far, far away...",
    "/images/star-wars.jpeg", 39.95);

$page = new ProductPage($HTMLRenderer, $product);
print("HTML view of a product page, same client code:\n");
clientCode($page);
print("\n\n");

$page->changeRenderer($JSONRenderer);
print("JSON view of a simple content page, with the same client code:\n");
clientCode($page);
