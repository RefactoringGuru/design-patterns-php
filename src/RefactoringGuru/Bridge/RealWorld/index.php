<?php

namespace RefactoringGuru\Bridge\RealWorld;

/**
 * EN: Bridge Design Pattern
 *
 * Intent: Lets you split a large class or a set of closely related classes into
 * two separate hierarchies—abstraction and implementation—which can be
 * developed independently of each other.
 *
 * Example: In this example, the Page hierarchy acts as the Abstraction, and the
 * Renderer hierarchy acts as the Implementation. Objects of the Page class can
 * assemble web pages of a particular kind using basic elements provided by a
 * Renderer object attached to that page. Since both of the class hierarchies
 * are separate, you can add a new Renderer class without changing any of the
 * Page classes and vice versa.
 *
 * RU: Паттерн Мост
 *
 * Назначение: Разделяет один или несколько классов на две отдельные иерархии —
 * абстракцию и реализацию, позволяя изменять их независимо друг от друга.
 *
 * Пример: В этом примере иерархия Страницы выступает как Абстракция, а иерархия
 * Рендера как Реализация. Объекты класса Страница монтируют веб-страницы
 * определённого типа, используя базовые элементы, которые предоставляются
 * объектом Рендер, прикреплённым к этой странице. Обе иерархии классов
 * разделены, поэтому можно добавить новый класс Рендер без изменения классов
 * страниц и наоборот.
 */

/**
 * EN: The Abstraction.
 *
 * RU: Абстракция.
 */
abstract class Page
{
    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * EN: The Abstraction is usually initialized with one of the Implementation
     * objects.
     *
     * RU: Обычно Абстракция инициализируется одним из объектов Реализации.
     */
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * EN: The Bridge pattern allows replacing the attached Implementation
     * object dynamically.
     *
     * RU: Паттерн Мост позволяет динамически заменять присоединённый объект
     * Реализации.
     */
    public function changeRenderer(Renderer $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * EN: The "view" behavior stays abstract since it can only be provided by
     * Concrete Abstraction classes.
     *
     * RU: Поведение «вида» остаётся абстрактным, так как оно предоставляется
     * только классами Конкретной Абстракции.
     */
    abstract public function view(): string;
}

/**
 * EN: This Concrete Abstraction represents a simple page.
 *
 * RU: Эта Конкретная Абстракция создаёт простую страницу.
 */
class SimplePage extends Page
{
    protected $title;
    protected $content;

    public function __construct(Renderer $renderer, string $title, string $content)
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
 * EN: This Concrete Abstraction represents a more complex page.
 *
 * RU: Эта Конкретная Абстракция создаёт более сложную страницу.
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
            $this->renderer->renderTextBlock('$'.number_format($this->product->getPrice(), 2)),
            $this->renderer->renderLink("/cart/add/".$this->product->getId(), "Add to cart"),
            $this->renderer->renderFooter()
        ]);
    }
}

/**
 * EN: A helper class for the ProductPage class.
 *
 * RU: Вспомогательный класс для класса ProductPage.
 */
class Product
{
    private $id, $title, $description, $image, $price;

    public function __construct(
        string $id,
        string $title,
        string $description,
        string $image,
        float $price
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->price = $price;
    }

    public function getId(): string { return $this->id; }

    public function getTitle(): string { return $this->title; }

    public function getDescription(): string { return $this->description; }

    public function getImage(): string { return $this->image; }

    public function getPrice(): float { return $this->price; }
}


/**
 * EN: The Implementation declares a set of "real", "under-the-hood", "platform"
 * methods.
 *
 * In this case, the Implementation lists rendering methods that can be used to
 * compose any web page. Different Abstractions may use different methods of the
 * Implementation.
 *
 * RU: Реализация объявляет набор «реальных», «под капотом», «платформенных»
 * методов.
 *
 * В этом случае Реализация перечисляет методы рендеринга, которые используются
 * для создания веб-страниц. Разные Абстракции могут использовать разные методы
 * Реализации.
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
 * EN: This Concrete Implementation renders a web page as HTML.
 *
 * RU: Эта Конкретная Реализация отображает веб-страницу в виде HTML.
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
 * EN: This Concrete Implementation renders a web page as JSON strings.
 *
 * RU: Эта Конкретная Реализация отображает веб-страницу в виде строк JSON.
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
        return '"link": {"href": "' . $url . '", "title": "' . $title . '"}';
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
 * EN: The client code usually deals only with the Abstraction objects.
 *
 * RU: Клиентский код имеет дело только с объектами Абстракции.
 */
function clientCode(Page $page)
{
    // ...

    echo $page->view();

    // ...
}

/**
 * EN: The client code can be executed with any pre-configured combination of
 * the Abstraction+Implementation.
 *
 * RU: Клиентский код может выполняться с любой предварительно
 * сконфигурированной комбинацией Абстракция+Реализация.
 */
$HTMLRenderer = new HTMLRenderer();
$JSONRenderer = new JsonRenderer();

$page = new SimplePage($HTMLRenderer, "Home", "Welcome to our website!");
echo "HTML view of a simple content page:\n";
clientCode($page);
echo "\n\n";

/**
 * EN: The Abstraction can change the linked Implementation at runtime if
 * needed.
 *
 * RU: При необходимости Абстракция может заменить связанную Реализацию во время
 * выполнения.
 */
$page->changeRenderer($JSONRenderer);
echo "JSON view of a simple content page, rendered with the same client code:\n";
clientCode($page);
echo "\n\n";


$product = new Product("123", "Star Wars, episode1",
    "A long time ago in a galaxy far, far away...",
    "/images/star-wars.jpeg", 39.95);

$page = new ProductPage($HTMLRenderer, $product);
echo "HTML view of a product page, same client code:\n";
clientCode($page);
echo "\n\n";

$page->changeRenderer($JSONRenderer);
echo "JSON view of a simple content page, with the same client code:\n";
clientCode($page);
