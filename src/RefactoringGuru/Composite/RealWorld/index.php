<?php

namespace RefactoringGuru\Composite\RealWorld;

/**
 * EN: Composite Design Pattern
 *
 * Intent: Lets you compose objects into tree structures and then work with
 * these structures as if they were individual objects.
 *
 * Example: The Composite pattern can streamline the work with any tree-like
 * recursive structures. The HTML DOM tree is an example of such a structure.
 * For instance, while the various input elements can act as leaves, the complex
 * elements like forms and fieldsets play the role of composites.
 *
 * Bearing that in mind, you can use the Composite pattern to apply various
 * behaviors to the whole HTML tree in the same way as to its inner elements
 * without coupling your code to concrete classes of the DOM tree. Examples of
 * such behaviors might be rendering the DOM elements, exporting it into various
 * formats, validating its parts, etc.
 *
 * With the Composite pattern, you don't need to check whether it's the simple
 * or complex type of element before executing the behavior. Depending on the
 * element's type, it either gets executed right away or passed all the way down
 * to all element's children.
 *
 * RU: Паттерн Компоновщик
 *
 * Назначение: Позволяет сгруппировать объекты в древовидную структуру, а затем
 * работать с ними так, как будто это единичный объект.
 *
 * Пример: Паттерн Компоновщик может упростить работу с любыми древовидными
 * рекурсивными структурами. Примером такой структуры является DOM-дерево HTML.
 * Например, в то время как различные входные элементы могут служить листьями,
 * сложные элементы, такие как формы и наборы полей, играют роль контейнеров.
 *
 * Имея это в виду, вы можете использовать паттерн Компоновщик для применения
 * различных типов поведения ко всему дереву HTML точно так же, как и к его
 * внутренним элементам, не привязывая ваш код к конкретным классам дерева DOM.
 * Примерами такого поведения может быть рендеринг элементов DOM, их экспорт в
 * различные форматы, проверка достоверности их частей и т.д.
 *
 * С паттерном Компоновщик вам не нужно проверять, является ли тип элемента
 * простым или сложным, перед реализацией поведения. В зависимости от типа
 * элемента, оно либо сразу же выполняется, либо передаётся всем дочерним
 * элементам.
 */

/**
 * EN: The base Component class declares an interface for all concrete
 * components, both simple and complex.
 *
 * In our example, we'll be focusing on the rendering behavior of DOM elements.
 *
 * RU: Базовый класс Компонент объявляет интерфейс для всех конкретных
 * компонентов, как простых, так и сложных.
 *
 * В нашем примере мы сосредоточимся на поведении рендеринга элементов DOM.
 */
abstract class FormElement
{
    /**
     * EN: We can anticipate that all DOM elements require these 3 fields.
     *
     * RU: Можно предположить, что всем элементам DOM будут нужны эти 3 поля.
     */
    protected $name;
    protected $title;
    protected $data;

    public function __construct(string $name, string $title)
    {
        $this->name = $name;
        $this->title = $title;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * EN: Each concrete DOM element must provide its rendering implementation,
     * but we can safely assume that all of them are returning strings.
     *
     * RU: Каждый конкретный элемент DOM должен предоставить свою реализацию
     * рендеринга, но мы можем с уверенностью предположить, что все они будут
     * возвращать строки.
     */
    abstract public function render(): string;
}

/**
 * EN: This is a Leaf component. Like all the Leaves, it can't have any
 * children.
 *
 * RU: Это компонент-Лист. Как и все Листья, он не может иметь вложенных
 * компонентов.
 */
class Input extends FormElement
{
    private $type;

    public function __construct(string $name, string $title, string $type)
    {
        parent::__construct($name, $title);
        $this->type = $type;
    }

    /**
     * EN: Since Leaf components don't have any children that may handle the
     * bulk of the work for them, usually it is the Leaves who do the most of
     * the heavy-lifting within the Composite pattern.
     *
     * RU: Поскольку у компонентов-Листьев нет вложенных компонентов, которые
     * могут выполнять за них основную часть работы, обычно Листья делают
     * большую часть тяжёлой работы внутри паттерна Компоновщик.
     */
    public function render(): string
    {
        return "<label for=\"{$this->name}\">{$this->title}</label>\n" .
            "<input name=\"{$this->name}\" type=\"{$this->type}\" value=\"{$this->data}\">\n";
    }
}

/**
 * EN: The base Composite class implements the infrastructure for managing child
 * objects, reused by all Concrete Composites.
 *
 * RU: Базовый класс Контейнер реализует инфраструктуру для управления дочерними
 * объектами, повторно используемую всеми Конкретными Контейнерами.
 */
abstract class FieldComposite extends FormElement
{
    /**
     * @var FormElement[]
     */
    protected $fields = [];

    /**
     * EN: The methods for adding/removing sub-objects.
     *
     * RU: Методы добавления/удаления подобъектов.
     */
    public function add(FormElement $field): void
    {
        $name = $field->getName();
        $this->fields[$name] = $field;
    }

    public function remove(FormElement $component): void
    {
        $this->fields = array_filter($this->fields, function ($child) use ($component) {
            return $child != $component;
        });
    }

    /**
     * EN: Whereas a Leaf's method just does the job, the Composite's method
     * almost always has to take its sub-objects into account.
     *
     * In this case, the composite can accept structured data.
     *
     * @param array $data
     *
     * RU: В то время как метод Листа просто выполняет эту работу, метод
     * Контейнера почти всегда должен учитывать его подобъекты.
     *
     * В этом случае контейнер может принимать структурированные данные.
     *
     * @param array $data
     */
    public function setData($data): void
    {
        foreach ($this->fields as $name => $field) {
            if (isset($data[$name])) {
                $field->setData($data[$name]);
            }
        }
    }

    /**
     * EN: The same logic applies to the getter. It returns the structured data
     * of the composite itself (if any) and all the children data.
     *
     * RU: Та же логика применима и к получателю. Он возвращает
     * структурированные данные самого контейнера (если они есть), а также все
     * дочерние данные.
     */
    public function getData(): array
    {
        $data = [];
        
        foreach ($this->fields as $name => $field) {
            $data[$name] = $field->getData();
        }
        
        return $data;
    }

    /**
     * EN: The base implementation of the Composite's rendering simply combines
     * results of all children. Concrete Composites will be able to reuse this
     * implementation in their real rendering implementations.
     *
     * RU: Базовая реализация рендеринга Контейнера просто объединяет результаты
     * всех дочерних элементов. Конкретные Контейнеры смогут повторно
     * использовать эту реализацию в своих реальных реализациях рендеринга.
     */
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
 * EN: The fieldset element is a Concrete Composite.
 *
 * RU: Элемент fieldset представляет собой Конкретный Контейнер.
 */
class Fieldset extends FieldComposite
{
    public function render(): string
    {
        // EN: Note how the combined rendering result of children is
        // incorporated into the fieldset tag.
        //
        // RU: Обратите внимание, как комбинированный результат рендеринга
        // потомков включается в тег fieldset.
        $output = parent::render();
        
        return "<fieldset><legend>{$this->title}</legend>\n$output</fieldset>\n";
    }
}

/**
 * EN: And so is the form element.
 *
 * RU: Так же как и элемент формы.
 */
class Form extends FieldComposite
{
    protected $url;

    public function __construct(string $name, string $title, string $url)
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
 * EN: The client code gets a convenient interface for building complex tree
 * structures.
 *
 * RU: Клиентский код получает удобный интерфейс для построения сложных
 * древовидных структур.
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
 * EN: The form structure can be filled with data from various sources. The
 * Client doesn't have to traverse through all form fields to assign data to
 * various fields since the form itself can handle that.
 *
 * RU: Структура формы может быть заполнена данными из разных источников. Клиент
 * не должен проходить через все поля формы, чтобы назначить данные различным
 * полям, так как форма сама может справиться с этим.
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
 * EN: The client code can work with form elements using the abstract interface.
 * This way, it doesn't matter whether the client works with a simple component
 * or a complex composite tree.
 *
 * RU: Клиентский код может работать с элементами формы, используя абстрактный
 * интерфейс. Таким образом, не имеет значения, работает ли клиент с простым
 * компонентом или сложным составным деревом.
 */
function renderProduct(FormElement $form)
{
    // ..

    echo $form->render();

    // ..
}

$form = getProductForm();
loadProductData($form);
renderProduct($form);
