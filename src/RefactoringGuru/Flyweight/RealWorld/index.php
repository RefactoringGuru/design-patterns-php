<?php

namespace RefactoringGuru\Flyweight\RealWorld;

/**
 * EN: Flyweight Design Pattern
 *
 * Intent: Lets you fit more objects into the available amount of RAM by sharing
 * common parts of state between multiple objects, instead of keeping all of the
 * data in each object.
 *
 * Example: Before we begin, please note that real applications for the
 * Flyweight pattern in PHP are pretty rare. This stems from the single-thread
 * nature of PHP, where you're not supposed to be storing ALL of your
 * application's objects in memory at the same time, in the same thread. While
 * the idea for this example is only half-serious, and the whole RAM problem
 * might be solved by structuring the app differently, it still demonstrates the
 * concept of the pattern as it works in the real world. All right, I've given
 * you the disclaimer. Now, let's begin.
 *
 * In this example, the Flyweight pattern is used to minimize the RAM usage of
 * objects in an animal database of a cat-only veterinary clinic. Each record in
 * the database is represented by a Cat object. Its data consists of two parts:
 *
 * 1. Unique (extrinsic) data such as a pet's name, age, and owner info.
 * 2. Shared (intrinsic) data such as a breed name, color, texture, etc.
 *
 * The first part is stored directly inside the Cat class, which acts as a
 * context. The second part, however, is stored separately and can be shared by
 * multiple cats. This shareable data resides inside the CatVariation class. All
 * cats that have similar features are linked to the same CatVariation class,
 * instead of storing the duplicate data in each of their objects.
 *
 * RU: Паттерн Легковес
 *
 * Назначение: Позволяет вместить бóльшее количество объектов в отведённую
 * оперативную память. Легковес экономит память, разделяя общее состояние
 * объектов между собой, вместо хранения одинаковых данных в каждом объекте.
 *
 * Пример: Прежде чем мы начнём, обратите внимание, что реальное применение
 * паттерна Легковес на PHP встречается довольно редко. Это связано с
 * однопоточным характером PHP, где вы не должны хранить ВСЕ объекты вашего
 * приложения в памяти одновременно в одном потоке. Хотя замысел этого примера
 * только наполовину серьёзен, и вся проблема с ОЗУ может быть решена, если
 * приложение структурировать по-другому, он всё же наглядно показывает
 * концепцию паттерна, как он работает в реальном мире. Итак, я вас предупредил.
 * Теперь давайте начнём.
 *
 * В этом примере паттерн Легковес применяется для минимизации использования
 * оперативной памяти объектами в базе данных животных ветеринарной клиники
 * только для кошек. Каждую запись в базе данных представляет объект-Кот. Его
 * данные состоят из двух частей:
 *
 * 1. Уникальные (внешние) данные: имя кота, возраст и инфо о владельце.
 * 2. Общие (внутренние) данные: название породы, цвет, текстура и т.д.
 *
 * Первая часть хранится непосредственно внутри класса Кот, который играет роль
 * контекста. Вторая часть, однако, хранится отдельно и может совместно
 * использоваться разными объектами котов. Эти совместно используемые данные
 * находятся внутри класса РазновидностиКотов. Все коты, имеющие схожие
 * признаки, привязаны к одному и тому же классу РазновидностейКотов, вместо
 * того чтобы хранить повторяющиеся данные в каждом из своих объектов.
 */

/**
 * EN: Flyweight objects represent the data shared by multiple Cat objects. This
 * is the combination of breed, color, texture, etc.
 *
 * RU: Объекты Легковеса представляют данные, разделяемые несколькими объектами
 * Кошек. Это сочетание породы, цвета, текстуры и т.д.
 */
class CatVariation
{
    /**
     * EN: The so-called "intrinsic" state.
     *
     * RU: Так называемое «внутреннее» состояние.
     */
    public $breed;

    public $image;

    public $color;

    public $texture;

    public $fur;

    public $size;

    public function __construct(
        string $breed,
        string $image,
        string $color,
        string $texture,
        string $fur,
        string $size
    ) {
        $this->breed = $breed;
        $this->image = $image;
        $this->color = $color;
        $this->texture = $texture;
        $this->fur = $fur;
        $this->size = $size;
    }

    /**
     * EN: This method displays the cat information. The method accepts the
     * extrinsic  state as arguments. The rest of the state is stored inside
     * Flyweight's fields.
     *
     * You might be wondering why we had put the primary cat's logic into the
     * CatVariation class instead of keeping it in the Cat class. I agree, it
     * does sound confusing.
     *
     * Keep in mind that in the real world, the Flyweight pattern can either be
     * implemented from the start or forced onto an existing application
     * whenever the developers realize they've hit upon a RAM problem.
     *
     * In the latter case, you end up with such classes as we have here. We kind
     * of "refactored" an ideal app where all the data was initially inside the
     * Cat class. If we had implemented the Flyweight from the start, our class
     * names might be different and less confusing. For example, Cat and
     * CatContext.
     *
     * However, the actual reason why the primary behavior should live in the
     * Flyweight class is that you might not have the Context class declared at
     * all. The context data might be stored in an array or some other more
     * efficient data structure. You won't have another place to put your
     * methods in, except the Flyweight class.
     *
     * RU: Этот метод отображает информацию о кошке. Метод принимает внешнее
     * состояние в качестве аргументов. Остальная часть состояния хранится
     * внутри полей Легковеса.
     *
     * Возможно, вы удивлены, почему мы поместили основную логику кошки в класс
     * РазновидностейКошек вместо того, чтобы держать её в классе Кошки. Я
     * согласен, это звучит странно.
     *
     * Имейте в виду, что в реальной жизни паттерн Легковес может быть либо
     * реализован с самого начала, либо принудительно применён к существующему
     * приложению, когда разработчики понимают, что они столкнулись с проблемой
     * ОЗУ.
     *
     * Во втором случае вы получаете такие же классы, как у нас. Мы как бы
     * «отрефакторили» идеальное приложение, где все данные изначально
     * находились внутри класса Кошки. Если бы мы реализовывали Легковес с
     * самого начала, названия наших классов могли бы быть другими и более
     * определёнными. Например, Кошка и КонтекстКошки.
     *
     * Однако действительная причина, по которой основное поведение должно
     * проживать в классе Легковеса, заключается в том, что у вас может вообще
     * не быть объявленного класса Контекста. Контекстные данные могут храниться
     * в массиве или какой-то другой, более эффективной структуре данных.
     */
    public function renderProfile(string $name, string  $age, string $owner)
    {
        echo "= $name =\n";
        echo "Age: $age\n";
        echo "Owner: $owner\n";
        echo "Breed: $this->breed\n";
        echo "Image: $this->image\n";
        echo "Color: $this->color\n";
        echo "Texture: $this->texture\n";
    }
}

/**
 * EN: The context stores the data unique for each cat.
 *
 * A designated class for storing context is optional and not always viable. The
 * context may be stored inside a massive data structure within the Client code
 * and passed to the flyweight methods when needed.
 *
 * RU: Контекст хранит данные, уникальные для каждой кошки.
 *
 * Создавать отдельный класс для хранения контекста необязательно и не всегда
 * целесообразно. Контекст может храниться внутри громоздкой структуры данных в
 * коде Клиента и при необходимости передаваться в методы легковеса.
 */
class Cat
{
    /**
     * EN: The so-called "extrinsic" state.
     *
     * RU: Так называемое «внешнее» состояние.
     */
    public $name;

    public $age;

    public $owner;

    /**
     * @var CatVariation
     */
    private $variation;

    public function __construct(string $name, string $age, string $owner, CatVariation $variation)
    {
        $this->name = $name;
        $this->age = $age;
        $this->owner = $owner;
        $this->variation = $variation;
    }

    /**
     * EN: Since the Context objects don't own all of their state, sometimes,
     * for the sake of convenience, you may need to implement some helper
     * methods (for example, for comparing several Context objects.)
     *
     * RU: Поскольку объекты Контекста не владеют всем своим состоянием, иногда
     * для удобства вы можете реализовать несколько вспомогательных методов
     * (например, для сравнения нескольких объектов Контекста между собой).
     */
    public function matches(array $query): bool
    {
        foreach ($query as $key => $value) {
            if (property_exists($this, $key)) {
                if ($this->$key != $value) {
                    return false;
                }
            } elseif (property_exists($this->variation, $key)) {
                if ($this->variation->$key != $value) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * EN: The Context might also define several shortcut methods, that delegate
     * execution to the Flyweight object. These methods might be remnants of
     * real methods, extracted to the Flyweight class during a massive
     * refactoring to the Flyweight pattern.
     *
     * RU: Кроме того, Контекст может определять несколько методов быстрого
     * доступа, которые делегируют исполнение объекту-Легковесу. Эти методы
     * могут быть остатками реальных методов, извлечённых в класс Легковеса во
     * время массивного рефакторинга к паттерну Легковес.
     */
    public function render(): string
    {
        $this->variation->renderProfile($this->name, $this->age, $this->owner);
    }
}

/**
 * EN: The Flyweight Factory stores both the Context and Flyweight objects,
 * effectively hiding any notion of the Flyweight pattern from the client.
 *
 * RU: Фабрика Легковесов хранит объекты Контекст и Легковес, эффективно скрывая
 * любое упоминание о паттерне Легковес от клиента.
 */
class CatDataBase
{
    /**
     * EN: The list of cat objects (Contexts).
     *
     * RU: Список объектов-кошек (Контексты).
     */
    private $cats = [];

    /**
     * EN: The list of cat variations (Flyweights).
     *
     * RU: Список вариаций кошки (Легковесы).
     */
    private $variations = [];

    /**
     * EN: When adding a cat to the database, we look for an existing cat
     * variation first.
     *
     * RU: При добавлении кошки в базу данных мы сначала ищем существующую
     * вариацию кошки.
     */
    public function addCat(
        string $name,
        string $age,
        string $owner,
        string $breed,
        string $image,
        string $color,
        string $texture,
        string $fur,
        string $size
    ) {
        $variation =
            $this->getVariation($breed, $image, $color, $texture, $fur, $size);
        $this->cats[] = new Cat($name, $age, $owner, $variation);
        echo "CatDataBase: Added a cat ($name, $breed).\n";
    }

    /**
     * EN: Return an existing variation (Flyweight) by given data or create a
     * new one if it doesn't exist yet.
     *
     * RU: Возвращаем существующий вариант (Легковеса) по указанным данным или
     * создаём новый, если он ещё не существует.
     */
    public function getVariation(
        string $breed,
        string $image, $color,
        string $texture,
        string $fur,
        string $size
    ): CatVariation {
        $key = $this->getKey(get_defined_vars());

        if (!isset($this->variations[$key])) {
            $this->variations[$key] =
                new CatVariation($breed, $image, $color, $texture, $fur, $size);
        }

        return $this->variations[$key];
    }

    /**
     * EN: This function helps to generate unique array keys.
     *
     * RU: Эта функция помогает генерировать уникальные ключи массива.
     */
    private function getKey(array $data): string
    {
        return md5(implode("_", $data));
    }

    /**
     * EN: Look for a cat in the database using the given query parameters.
     *
     * RU: Ищем кошку в базе данных, используя заданные параметры запроса.
     */
    public function findCat(array $query)
    {
        foreach ($this->cats as $cat) {
            if ($cat->matches($query)) {
                return $cat;
            }
        }
        echo "CatDataBase: Sorry, your query does not yield any results.";
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */
$db = new CatDataBase();

echo "Client: Let's see what we have in \"cats.csv\".\n";

// EN: To see the real effect of the pattern, you should have a large database
// with several millions of records. Feel free to experiment with code to see
// the real extent of the pattern.
//
// RU: Чтобы увидеть реальный эффект паттерна, вы должны иметь большую базу
// данных с несколькими миллионами записей. Не стесняйтесь экспериментировать с
// кодом, чтобы увидеть реальные масштабы паттерна.
$handle = fopen(__DIR__ . "/cats.csv", "r");
$row = 0;
$columns = [];
while (($data = fgetcsv($handle)) !== false) {
    if ($row == 0) {
        for ($c = 0; $c < count($data); $c++) {
            $columnIndex = $c;
            $columnKey = strtolower($data[$c]);
            $columns[$columnKey] = $columnIndex;
        }
        $row++;
        continue;
    }

    $db->addCat(
        $data[$columns['name']],
        $data[$columns['age']],
        $data[$columns['owner']],
        $data[$columns['breed']],
        $data[$columns['image']],
        $data[$columns['color']],
        $data[$columns['texture']],
        $data[$columns['fur']],
        $data[$columns['size']],
    );
    $row++;
}
fclose($handle);

// ...

echo "\nClient: Let's look for a cat named \"Siri\".\n";
$cat = $db->findCat(['name' => "Siri"]);
if ($cat) {
    $cat->render();
}

echo "\nClient: Let's look for a cat named \"Bob\".\n";
$cat = $db->findCat(['name' => "Bob"]);
if ($cat) {
    $cat->render();
}
