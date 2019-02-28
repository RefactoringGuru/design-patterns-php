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
        return new TwigTitleTemplate;
    }

    public function createPageTemplate(): PageTemplate
    {
        return new TwigPageTemplate($this->createTitleTemplate());
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
        return new PHPTemplateTitleTemplate;
    }

    public function createPageTemplate(): PageTemplate
    {
        return new PHPTemplatePageTemplate($this->createTitleTemplate());
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
 * EN: The client code. Note that it accepts the Abstract Factory class as the
 * parameter, which allows the client to work with any concrete factory type.
 *
 * RU: Клиентский код. Обратите внимание, что он принимает класс Абстрактной
 * Фабрики в качестве параметра, что позволяет клиенту работать с любым типом
 * конкретной фабрики.
 */
function templateRenderer(TemplateFactory $factory)
{
    $pageTemplate = $factory->createPageTemplate();

    echo $pageTemplate->getTemplateString();

    // EN: Here's how would you use the template further in real life:
    //
    // RU: Вот как вы бы использовали этот шаблон в дальнейшем:

    // Twig::render($pageTemplate->getTemplateString(), [
    //     'title' => $page->title,
    //     'content' => $page->content, ]);
}

/**
 * EN: Now, in other parts of the app, the client code can accept factory
 * objects of any type.
 *
 * RU: Теперь в других частях приложения клиентский код может принимать
 * фабричные объекты любого типа.
 */
echo "Testing rendering with the Twig factory:\n";
templateRenderer(new TwigTemplateFactory);
echo "\n\n";

echo "Testing rendering with the PHPTemplate factory:\n";
templateRenderer(new PHPTemplateFactory);
