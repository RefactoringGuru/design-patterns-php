<?php

namespace RefactoringGuru\Flyweight\RealWorld;

/**
 * Flyweight Design Pattern
 *
 * Intent: Use sharing to fit more objects into the available amount of RAM by
 * sharing common parts of the object state among multiple objects, instead of
 * keeping the entire state in each object.
 *
 * Example: Before we begin, please note that real applications for the
 * Flyweight pattern in PHP are pretty rare. This comes from a single-thread
 * nature of PHP, where you're not supposed to be storing ALL of your
 * application's objects in memory at the same time, in the same thread. While
 * the idea for this example is only half-serious, and the whole RAM problem
 * might be solved by structuring the app in a different way, it still
 * demonstrates the concept of the pattern quite close to the real world.
 * Alright, I've said it. Now, let's begin.
 *
 * In this example, the Flyweight pattern is used to minimize the RAM usage of
 * objects in an animal database of a cat-only veterinary clinic. Each record in
 * the database is represented by a Cat object. Its data consists of two parts:
 *
 * 1. Unique (extrinsic) data such as a pet's name, age and owner info.
 * 2. Shared (intrinsic) data such as a breed name, color, texture, etc.
 *
 * The first part is stored directly inside the Cat class that serves as a
 * context. The second part, however, is stored separately and can be shared by
 * multiple cats. This shareable data resides inside the CatVariation class. Two
 * cats that have similar features, reference to the same CatVariation class
 * instead of storing the duplicate data in each of their object.
 */

/**
 * Flyweight objects represent the data, shared by multiple Cat objects. This is
 * the combination of breed, color, texture, etc.
 */
class CatVariation
{
    /**
     * The so called "intrinsic" state.
     */
    public $breed;

    public $image;

    public $color;

    public $texture;

    public $fur;

    public $size;

    public function __construct($breed, $image, $color, $texture, $fur, $size)
    {
        $this->breed = $breed;
        $this->image = $image;
        $this->color = $color;
        $this->texture = $texture;
        $this->fur = $fur;
        $this->size = $size;
    }

    /**
     * Display the cat information. The method accepts extrinsic state as
     * arguments. The rest of the state is stored inside Flyweight's fields.
     *
     * You might be wondering why we had put the primary cat logic into the
     * CatVariation class instead of keeping it in the Cat class. I agree, it
     * does sound confusing.
     *
     * Keep in mind that in real world, the Flyweight pattern can either be
     * implemented from the start or forced onto an existing application
     * whenever the developers realize they've hit by a RAM problem.
     *
     * In later case, you end-up with the classes like we have here. We kind of
     * "refactored" an ideal app where all the data was initially inside the Cat
     * class. If we had implemented the Flyweight from the start, our class
     * names might be different and less confusing, for example, Cat and
     * CatContext.
     *
     * But the actual reason why the primary behavior should live in the
     * Flyweight class is because you might not have the Context class declared
     * at all. The context data might be stored in an array or some other more
     * efficient data structure. You won't have an other place to put your
     * methods in, except the Flyweight class.
     */
    public function renderProfile($name, $age, $owner)
    {
        print("= $name =\n");
        print("Age: $age\n");
        print("Owner: $owner\n");
        print("Breed: $this->breed\n");
        print("Image: $this->image\n");
        print("Color: $this->color\n");
        print("Texture: $this->texture\n");
    }
}

/**
 * The context stores the data, unique for each cat.
 *
 * A designated class for storing context is optional and not always viable. The
 * context may be stored inside a massive data structure within the Client code
 * and passed to the flyweight methods when needed.
 */
class Cat
{
    /**
     * The so called "extrinsic" state.
     */
    public $name;

    public $age;

    public $owner;

    /**
     * @var CatVariation
     */
    private $variation;

    public function __construct($name, $age, $owner, CatVariation $variation)
    {
        $this->name = $name;
        $this->age = $age;
        $this->owner = $owner;
        $this->variation = $variation;
    }

    /**
     * Since the Context objects don't own all of their state, sometimes you'll
     * need to provide some convenience method, for example for comparing their
     * data with other objects.
     *
     * @param $query
     * @return bool
     */
    public function matches($query): bool
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
     * The Context might also define several shortcut methods, that delegate
     * execution to the Flyweight object. These methods might be remnants of
     * real methods, extracted to the Flyweight class during a massive
     * refactoring to the Flyweight pattern.
     */
    public function render()
    {
        $this->variation->renderProfile($this->name, $this->age, $this->owner);
    }
}

/**
 * The Flyweight Factory stores both the Context and Flyweight objects,
 * effectively hiding any notion of the Flyweight pattern from the client.
 */
class CatDataBase
{
    /**
     * The list of cat objects (Contexts).
     */
    private $cats = [];

    /**
     * The list of cat variations (Flyweights).
     */
    private $variations = [];

    /**
     * When adding a cat to the database, we look for an existing cat variation
     * first.
     */
    public function addCat($name, $age, $owner, $breed, $image, $color, $texture, $fur, $size)
    {
        $variation =
            $this->getVariation($breed, $image, $color, $texture, $fur, $size);
        $this->cats[] = new Cat($name, $age, $owner, $variation);
        print("CatDataBase: Added a cat ($name, $breed).\n");
    }

    /**
     * Return an existing variation (Flyweight) by given data or create a new
     * one if it doesn't exist yet.
     */
    public function getVariation($breed, $image, $color, $texture, $fur, $size): CatVariation
    {
        $key = $this->getKey(get_defined_vars());

        if (! isset($this->variations[$key])) {
            $this->variations[$key] =
                new CatVariation($breed, $image, $color, $texture, $fur, $size);
        }

        return $this->variations[$key];
    }

    /**
     * This function helps to generate unique array keys.
     */
    private function getKey($data): string
    {
        return md5(implode("_", $data));
    }

    /**
     * Look for a cat in the database using the given query parameters.
     */
    public function findCat($query)
    {
        foreach ($this->cats as $cat) {
            if ($cat->matches($query)) {
                return $cat;
            }
        }
        print("CatDataBase: Sorry, your query does not yield any results.");
    }
}

/**
 * The client code.
 */
$db = new CatDataBase();

print("Client: Let's see what we have in \"cats.csv\".\n");

// To see the real effect of the pattern, the database should contain several
// million of records. Feel free to experiment with code to see the real extent
// of the pattern.
$handle = fopen(__DIR__."/cats.csv", "r");
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
        $data[$columns['size']]
    );
    $row++;
}
fclose($handle);

// ...

print("\nClient: Let's look for a cat named \"Siri\".\n");
$cat = $db->findCat(['name' => "Siri"]);
if ($cat) {
    $cat->render();
}

print("\nClient: Let's look for a cat named \"Bob\".\n");
$cat = $db->findCat(['name' => "Bob"]);
if ($cat) {
    $cat->render();
}