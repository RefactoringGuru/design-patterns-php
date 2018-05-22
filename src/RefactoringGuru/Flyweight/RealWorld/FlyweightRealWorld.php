<?php

namespace RefactoringGuru\Flyweight\RealWorld;

/**
 * Flyweight Design Pattern
 *
 * Intent: Use sharing to support a large number of objects that have part of
 * their internal state in common where the other part of state can vary.
 *
 * Example: In this example, the Flyweight pattern is used to minimize the RAM
 * usage of objects in an animal database of a cat-only veterinary clinic. Each
 * record in the database is represented by a Cat object. Its data consists of
 * two parts:
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
 * Flyweight Factory. This class stores both contexts and flyweights objects,
 * effectively hiding any notion of flyweights from a client.
 */
class CatDataBase
{
    /**
     * @var Cat[] List of cat objects (contexts).
     */
    private $cats = [];

    /**
     * @var CatVariation[] List of cat variations (flyweights).
     */
    private $variations = [];

    /**
     * When adding a cat to the database, we look for an existing cat variation.
     */
    public function addCat($name, $age, $owner, $breed, $image, $color, $texture, $fur, $size)
    {
        $variation =
            $this->getVariation($breed, $image, $color, $texture, $fur, $size);
        $this->cats[] = new Cat($name, $age, $owner, $variation);
        print("CatDataBase: Added a cat ($name, $breed).\n");
    }

    /**
     * Return an existing variation by given data or create a new one if it
     * doesn't exist yet.
     *
     * @return CatVariation
     */
    public function getVariation($breed, $image, $color, $texture, $fur, $size)
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
     *
     * @return string
     */
    private function getKey($data)
    {
        return md5(implode("_", $data));
    }

    /**
     * Look for a cat in the database using the given query parameters.
     *
     * @param $query
     * @return Cat
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
 * Flyweight. Stores that data, shared by multiple cats.
 */
class CatVariation
{
    /**
     * Intrinsic state.
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
     * @param $name
     * @param $age
     * @param $owner
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
 * Context. Stores the data, unique for each cat.
 */
class Cat
{
    /**
     * Extrinsic state.
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
     * Context usually delegates most of the work to the Flyweight object.
     */
    public function render()
    {
        $this->variation->renderProfile($this->name, $this->age, $this->owner);
    }
}

/**
 * Client code.
 */
$db = new CatDataBase();

print("Client: Let's see what we have in \"cats.csv\".\n");

// To see the real effect of the pattern, the database should contain several
// million of records.
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