<?php

namespace RefactoringGuru\Composite\RealWorld;

/**
 * Composite Design Pattern
 *
 * Intent: Compose objects into tree structures to represent part-whole
 * hierarchies. Composite lets clients treat individual objects and compositions
 * of objects uniformly.
 *
 * Example: The Composite pattern can streamline the work with any tree-like
 * recursive structures. The HTML DOM tree is an example of such structure. For
 * instance, while the various input elements can act as leafs, the complex
 * elements like forms and fieldsets play role of composites.
 *
 * Having that in mind, you can use the Composite pattern to apply various
 * behaviors to the whole HTML tree in the same way as to its inner elements
 * without coupling your code to concrete classes of DOM tree. The example of
 * such behaviors might be rendering the DOM elements, exporting it into various
 * formats, validating its parts, etc.
 *
 * With the Composite pattern, you don't need to check whether it's the simple
 * or complex type of element prior to executing the behavior. Depending on the
 * element's type, it's either get executed right away or passed all the way
 * down to all element's children.
 */

/**
 * Base Component.
 */
abstract class FormElement
{
    protected $name;
    protected $title;
    protected $data;

    public function __construct($name, $title)
    {
        $this->name = $name;
        $this->title = $title;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public abstract function render(): string;
}

/**
 * Leaf.
 */
class Input extends FormElement
{
    private $type;

    public function __construct($name, $title, $type)
    {
        parent::__construct($name, $title);
        $this->type = $type;
    }

    public function render(): string
    {
        return "<label for=\"{$this->name}\">{$this->title}</label>\n" .
            "<input name=\"{$this->name}\" type=\"{$this->type}\" value=\"{$this->data}\">\n";
    }
}

/**
 * Base Composite.
 */
abstract class FieldComposite extends FormElement
{
    /**
     * @var FormElement[]
     */
    protected $fields = [];

    public function add(FormElement $field)
    {
        $name = $field->getName();
        $this->fields[$name] = $field;
    }

    public function remove(FormElement $component)
    {
        $this->fields = array_filter($this->fields, function ($child) use ($component) {
            return $child == $component;
        });
    }

    public function setData($data)
    {
        foreach ($this->fields as $name => $field) {
            if (isset($data[$name])) {
                $field->setData($data[$name]);
            }
        }
    }

    public function getData()
    {
        $data = [];
        foreach ($this->fields as $name => $field) {
            $data[$name] = $field->getData();
        }
        return $data;
    }


    public function render(): string
    {
        $output = "";
        foreach ($this->fields as $name => $field) {
            $output .= $field->render();
        }
        return $output;
    }
}

/**
 * Fieldset is a composite.
 */
class Fieldset extends FieldComposite
{
    public function render(): string
    {
        $output = parent::render();
        return "<fieldset><legend>{$this->title}</legend>\n$output</fieldset>\n";
    }
}

/**
 * So as the complete form.
 */
class Form extends FieldComposite
{
    protected $url;

    public function __construct($name, $title, $url)
    {
        parent::__construct($name, $title);
        $this->url = $url;
    }

    public function render(): string
    {
        $output = parent::render();
        return "<form action=\"{$this->url}\">\n<h3>{$this->title}</h3>\n$output</form>\n";
    }
}

/**
 * Client code gets convenient interface to build complex tree structure.
 */
function getProductForm(): FormElement
{
    $form = new Form('product', "Add product", "/product/add");
    $form->add(new Input('name', "Name", 'text'));
    $form->add(new Input('description', "Description", 'text'));

    $picture = new Fieldset('photo', "Product photo");
    $picture->add(new Input('caption', "Caption", 'text'));
    $picture->add(new Input('image', "Image", 'file'));
    $form->add($picture);

    return $form;
}

/**
 * Form structure can be filled with data from various sources. Client doesn't
 * have to traverse through form fields to assign data to various fields, the
 * form itself will handle that.
 */
function loadProductData(FormElement $form)
{
    $data = [
        'name' => 'Apple MacBook',
        'description' => 'A decent laptop.',
        'photo' => [
            'caption' => 'Front photo.',
            'image' => 'photo1.png',
        ],
    ];

    $form->setData($data);
}

/**
 * Client code works with form elements using a base interface. This way it
 * doesn't matter whether it is simple component or a complex composite tree.
 */
function renderProduct(FormElement $form)
{
    // ..

    print($form->render());

    // ..
}

$form = getProductForm();
loadProductData($form);
renderProduct($form);