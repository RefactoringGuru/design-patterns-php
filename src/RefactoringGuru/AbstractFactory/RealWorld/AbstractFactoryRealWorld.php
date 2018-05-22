<?php

namespace RefactoringGuru\AbstractFactory\RealWorld;

/**
 * EN: Abstract Factory Design Pattern
 *
 * Intent: Provide an interface for creating families of related or dependent
 * objects without specifying their concrete classes.
 *
 * Example: In this example the Abstract Factory pattern provides an
 * infrastructure for creating various types of templates for different elements
 * of a web page.
 *
 * A web application can support different rendering engines at the same time,
 * but only if its classes are independent from concrate classes of rendering
 * engines. Hence, the application's objects must communicate with template
 * objects only via their abstract interfaces. Your code should not create the
 * template objects directly, but delegate the creation to special factory
 * objects. Finally, your code should not depend on the factory objects either,
 * but work with them via abstract factory interface.
 *
 * As a result, you will be able to provide the app with the factory object that
 * corresponds to one of the rendering engines. All templates, created in the
 * app, will be created by that factory and their type will match the type of
 * the factory. If you decide to change the rendering engine, you'll be able to
 * pass a different factory and all the code will remain functional.
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
 * интерфейсы, не привязываясь к их конкретным классам. В то же время, объекты
 * приложения не должны создавать шаблоны напрямую, а поручать это спецальным
 * объектам фабрик, с которыми тоже надо работать через абстрактный интерфейс.
 *
 * Благодаря этому, вы можете подать в приложение фабрику, соотвествующую одному
 * из движков рендеринга, зная что с этого момента, все шаблоны будут
 * порождаться именно этой фабрикой, и будут соотвествовать движку рендеринга
 * этой фабрики. Если вы захотите сменить движок рендеринга, то всё что нужно
 * будет сделать — это подать в приложение объект фабрики другого типа и ничего
 * при этом не сломается.
 */

/**
 * Abstract Factory.
 */
interface TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate;

    public function createPageTemplate(): PageTemplate;
}

/**
 * Concrete Factory. Creates Twig variant of products.
 */
class TwigTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new TwigTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new TwigPageTemplate();
    }
}

/**
 * Concrete Factory. Creates PHPTemplate variant of products.
 */
class PHPTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new PHPTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new PHPPageTemplate();
    }
}

/**
 * Abstract product: the page title template.
 */
interface TitleTemplate
{
    public function render(): string;
}

/**
 * Concrete product. Twig variant.
 */
class TwigTitleTemplate implements TitleTemplate
{
    public function render(): string
    {
        return "<h1>{{ title }}</h1>";
    }
}

/**
 * Concrete product. PHPTemplate variant.
 */
class PHPTitleTemplate implements TitleTemplate
{
    public function render(): string
    {
        return "<h1><?php print(\$title) ?></h1>";
    }
}

/**
 * Abstract product: the whole page template.
 */
interface PageTemplate
{
    public function render(TitleTemplate $titleTemplate): string;
}

/**
 * Concrete product. Twig variant.
 */
class TwigPageTemplate implements PageTemplate
{
    public function render(TitleTemplate $titleTemplate): string
    {
        $title = $titleTemplate->render();
        return <<<EOF
<div class="page">
  $title
  <article class="content">{{ content }}</article>
</div>
EOF;
    }
}

/**
 * Concrete product. PHPTemplate variant.
 */
class PHPPageTemplate implements PageTemplate
{
    public function render(TitleTemplate $titleTemplate): string
    {
        $title = $titleTemplate->render();
        return <<<EOF
<div class="page">
  $title
  <article class="content"><?php print(\$content) ?></article>
</div>
EOF;
    }
}

/**
 * Client code.
 */
function templateRenderer(TemplateFactory $factory)
{
    $titleTemplate = $factory->createTitleTemplate();
    $pageTemplate = $factory->createPageTemplate();

    print($pageTemplate->render($titleTemplate));
}

/**
 * The client code can be accept a factory object of any type.
 */
print("Testing rendering with the Twig factory:\n");
templateRenderer(new TwigTemplateFactory());
print("\n\n");

print("Testing rendering with the PHPTemplate factory:\n");
templateRenderer(new PHPTemplateFactory());
