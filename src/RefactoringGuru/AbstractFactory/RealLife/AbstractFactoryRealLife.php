<?php

namespace RefactoringGuru\AbstractFactory\RealLife;

/**
 * Abstract Factory Design Pattern
 *
 * Intent: Provide an interface for creating families of related or dependent
 * objects without specifying their concrete classes.
 *
 * Example: Abstract Factory provides interface for creating templates
 * for various elements of a page. A website can support several rendering
 * engines by implementing separate concrete factory classes. They will
 * create different template classes for the same page elements.
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
 * Abstract product. Template of the page title.
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
        return '<h1>{{ title }}</h1>';
    }
}

/**
 * Concrete product. PHPTemplate variant.
 */
class PHPTitleTemplate implements TitleTemplate
{
    public function render(): string
    {
        return '<h1><?php echo $title ?></h1>';
    }
}

/**
 * Abstract product. Template of the whole page.
 */
interface PageTemplate
{
    /**
     * ProductB does its own thing.
     */
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
  <article class="content"><?php echo \$content ?></article>
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

    echo $pageTemplate->render($titleTemplate);
}

/**
 * Client code can be launched with any factory type.
 */
echo "Testing rendering with the Twig factory:\n";
templateRenderer(new TwigTemplateFactory());
echo "\n\n";

echo "Testing rendering with the PHPTemplate factory:\n";
templateRenderer(new PHPTemplateFactory());
