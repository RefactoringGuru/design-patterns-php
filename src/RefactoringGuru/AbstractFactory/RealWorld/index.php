<?php

namespace RefactoringGuru\AbstractFactory\RealWorld;

/**
 * EN: Abstract Factory Design Pattern
 *
 * Intent: Lets you produce families of related objects without specifying their
 * concrete classes.
 *
 * Example: In this example, the Abstract Factory pattern provides an
 * infrastructure for creating various types of templates for different elements
 * of a web page.
 *
 * A web application can support different rendering engines at the same time,
 * but only if its classes are independent of the concrete classes of rendering
 * engines. Hence, the application's objects must communicate with template
 * objects only via their abstract interfaces. Your code should not create the
 * template objects directly, but delegate their creation to special factory
 * objects. Finally, your code should not depend on the factory objects either
 * but, instead, should work with them via the abstract factory interface.
 *
 * As a result, you will be able to provide the app with the factory object that
 * corresponds to one of the rendering engines. All templates, created in the
 * app, will be created by that factory and their type will match the type of
 * the factory. If you decide to change the rendering engine, you'll be able to
 * pass a new factory to the client code, without breaking any existing code.
 *
 * RU: Паттерн Абстрактная Фабрика
 *
 * Назначение: Предоставляет интерфейс для создания семейств связанных или
 * зависимых объектов, без привязки к их конкретным классам.
 *
 * Пример: В этом примере паттерн Абстрактная Фабрика предоставляет
 * инфраструктуру для создания нескольких разновидностей шаблонов для одних и
 * тех же элементов веб-страницы.
 *
 * Чтобы веб-приложение могло поддерживать сразу несколько разных движков
 * рендеринга страниц, его классы должны работать с шаблонами только через
 * интерфейсы, не привязываясь к конкретным классам. Чтобы этого достичь,
 * объекты приложения не должны создавать шаблоны напрямую, а поручать создание
 * специальным объектам-фабрикам, с которыми тоже надо работать через
 * абстрактный интерфейс.
 *
 * Благодаря этому, вы можете подать в приложение фабрику, соответствующую
 * одному из движков рендеринга, зная, что с этого момента, все шаблоны будут
 * порождаться именно этой фабрикой, и будут соответствовать движку рендеринга
 * этой фабрики. Если вы захотите сменить движок рендеринга, то всё что нужно
 * будет сделать — это подать в приложение объект фабрики другого типа и ничего
 * при этом не сломается.
 */

/**
 * EN: The Abstract Factory interface declares creation methods for each
 * distinct product type.
 *
 * RU: Интерфейс Абстрактной фабрики объявляет создающие методы для каждого
 * определённого типа продукта.
 */
interface TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate;

    public function createPageTemplate(): PageTemplate;

    public function getRenderer(): TemplateRenderer;
}

/**
 * EN: Each Concrete Factory corresponds to a specific variant (or family) of
 * products.
 *
 * This Concrete Factory creates Twig templates.
 *
 * RU: Каждая Конкретная Фабрика соответствует определённому варианту (или
 * семейству) продуктов.
 *
 * Эта Конкретная Фабрика создает шаблоны Twig.
 */
class TwigTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new TwigTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new TwigPageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer
    {
        return new TwigRenderer();
    }
}

/**
 * EN: And this Concrete Factory creates PHPTemplate templates.
 *
 * RU: А эта Конкретная Фабрика создает шаблоны PHPTemplate.
 */
class PHPTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new PHPTemplateTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new PHPTemplatePageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer
    {
        return new PHPTemplateRenderer();
    }
}

/**
 * EN: Each distinct product type should have a separate interface. All variants
 * of the product must follow the same interface.
 *
 * For instance, this Abstract Product interface describes the behavior of page
 * title templates.
 *
 * RU: Каждый отдельный тип продукта должен иметь отдельный интерфейс. Все
 * варианты продукта должны соответствовать одному интерфейсу.
 *
 * Например, этот интерфейс Абстрактного Продукта описывает поведение шаблонов
 * заголовков страниц.
 */
interface TitleTemplate
{
    public function getTemplateString(): string;
}

/**
 * EN: This Concrete Product provides Twig page title templates.
 *
 * RU: Этот Конкретный Продукт предоставляет шаблоны заголовков страниц Twig.
 */
class TwigTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1>{{ title }}</h1>";
    }
}

/**
 * EN: And this Concrete Product provides PHPTemplate page title templates.
 *
 * RU: А этот Конкретный Продукт предоставляет шаблоны заголовков страниц
 * PHPTemplate.
 */
class PHPTemplateTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1><?= \$title; ?></h1>";
    }
}

/**
 * EN: This is another Abstract Product type, which describes whole page
 * templates.
 *
 * RU: Это еще один тип Абстрактного Продукта, который описывает шаблоны целых
 * страниц.
 */
interface PageTemplate
{
    public function getTemplateString(): string;
}

/**
 * EN: The page template uses the title sub-template, so we have to provide the
 * way to set it in the sub-template object. The abstract factory will link the
 * page template with a title template of the same variant.
 *
 * RU: Шаблон страниц использует под-шаблон заголовков, поэтому мы должны
 * предоставить способ установить объект для этого под-шаблона. Абстрактная
 * фабрика позаботится о том, чтобы подать сюда под-шаблон подходящего типа.
 */
abstract class BasePageTemplate implements PageTemplate
{
    protected $titleTemplate;

    public function __construct(TitleTemplate $titleTemplate)
    {
        $this->titleTemplate = $titleTemplate;
    }
}

/**
 * EN: The Twig variant of the whole page templates.
 *
 * RU: Вариант шаблонов страниц Twig.
 */
class TwigPageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
        <div class="page">
            $renderedTitle
            <article class="content">{{ content }}</article>
        </div>
        HTML;
    }
}

/**
 * EN: The PHPTemplate variant of the whole page templates.
 *
 * RU: Вариант шаблонов страниц PHPTemplate.
 */
class PHPTemplatePageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
        <div class="page">
            $renderedTitle
            <article class="content"><?= \$content; ?></article>
        </div>
        HTML;
    }
}

/**
 * EN: The renderer is responsible for converting a template string into the
 * actual HTML code. Each renderer behaves differently and expects its own type
 * of template strings passed to it. Baking templates with the factory let you
 * pass proper types of templates to proper renders.
 *
 * RU: Классы отрисовки отвечают за преобразовании строк шаблонов в конечный
 * HTML код. Каждый такой класс устроен по-раному и ожидает на входе шаблоны
 * только своего типа. Работа с шаблонами через фабрику позволяет вам избавиться
 * от риска подать в отрисовщик шаблон не того типа.
 */
interface TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string;
}

/**
 * EN: The renderer for Twig templates.
 *
 * RU: Отрисовщик шаблонов Twig.
 */
class TwigRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        return \Twig::render($templateString, $arguments);
    }
}

/**
 * EN: The renderer for PHPTemplate templates. Note that this implementation is
 * very basic, if not crude. Using the `eval` function has many security
 * implications, so use it with caution in real projects.
 *
 * RU: Отрисовщик шаблонов PHPTemplate. Оговорюсь, что эта реализация очень
 * простая, если не примитивная. В реальных проектах используйте `eval` с
 * осмотрительностью, т.к. неправильное использование этой функции может
 * привести к дырам безопасности.
 */
class PHPTemplateRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        extract($arguments);

        ob_start();
        eval(' ?>' . $templateString . '<?php ');
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}

/**
 * EN: The client code. Note that it accepts the Abstract Factory class as the
 * parameter, which allows the client to work with any concrete factory type.
 *
 * RU: Клиентский код. Обратите внимание, что он принимает класс Абстрактной
 * Фабрики в качестве параметра, что позволяет клиенту работать с любым типом
 * конкретной фабрики.
 */
class Page
{

    public $title;

    public $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    // EN: Here's how would you use the template further in real life. Note that
    // the page class does not depend on any concrete template classes.
    //
    // RU: Вот как вы бы использовали этот шаблон в дальнейшем. Обратите
    // внимание, что класс страницы не зависит ни от классов шаблонов, ни от
    // классов отрисовки.
    public function render(TemplateFactory $factory): string
    {
        $pageTemplate = $factory->createPageTemplate();

        $renderer = $factory->getRenderer();

        return $renderer->render($pageTemplate->getTemplateString(), [
            'title' => $this->title,
            'content' => $this->content
        ]);
    }
}

/**
 * EN: Now, in other parts of the app, the client code can accept factory
 * objects of any type.
 *
 * RU: Теперь в других частях приложения клиентский код может принимать
 * фабричные объекты любого типа.
 */
$page = new Page('Sample page', 'This is the body.');

echo "Testing actual rendering with the PHPTemplate factory:\n";
echo $page->render(new PHPTemplateFactory());


// EN: Uncomment the following if you have Twig installed.
//
// RU: Можете убрать комментарии, если у вас установлен Twig.

// echo "Testing rendering with the Twig factory:\n";
// echo $page->render(new TwigTemplateFactory());
