<?php

namespace RefactoringGuru\Flyweight\Structural;

/**
 * Flyweight Design Pattern
 *
 * Intent: Use sharing to fit more objects into the available amount of RAM by
 * sharing common parts of the object state among multiple objects, instead of
 * keeping the entire state in each object.
 */

/**
 * The Flyweight Factory creates and manages the Flyweight objects. It ensures
 * that flyweights are shared correctly. When the client requests a flyweight,
 * the factory either returns an existing instance or creates a new one, if it
 * doesn't exist yet.
 */
class FlyweightFactory
{
    /**
     * @var Flyweight[]
     */
    private $flyweights = [];

    public function __construct(array $initialFlyweights)
    {
        foreach ($initialFlyweights as $state) {
            $this->flyweights[$this->getKey($state)] = new Flyweight($state);
        }
    }

    /**
     * Returns a Flyweight's string hash for a given state.
     *
     * @param array $state
     * @return string
     */
    private function getKey(array $state)
    {
        ksort($state);

        return implode("_", $state);
    }

    /**
     * Returns an existing Flyweight with a given state or creates a new one.
     *
     * @param $sharedState
     * @return Flyweight
     */
    public function getFlyweight(array $sharedState)
    {
        $key = $this->getKey($sharedState);

        if (! isset($this->flyweights[$key])) {
            print("FlyweightFactory: Can't find a flyweight, crating new one.\n");
            $this->flyweights[$key] = new Flyweight($sharedState);
        } else {
            print("FlyweightFactory: Reusing existing flyweight.\n");
        }

        return $this->flyweights[$key];
    }

    public function listFlyweights()
    {
        $count = count($this->flyweights);
        print("\nFlyweightFactory: I have $count flyweights:\n");
        foreach ($this->flyweights as $key => $flyweight) {
            print($key."\n");
        }
    }
}

/**
 * The Flyweight stores a common portion of the state (also called intrinsic
 * state) that belongs to multiple real business entities. The Flyweight accepts
 * the rest of the state (extrinsic state, unique for each entity) via its
 * method parameters.
 */
class Flyweight
{
    private $sharedState;

    public function __construct($sharedState)
    {
        $this->sharedState = $sharedState;
    }

    public function operation($uniqueState)
    {
        $s = json_encode($this->sharedState);
        $u = json_encode($uniqueState);
        print("Flyweight: Displaying shared ($s) and unique ($u) state.\n");
    }
}

/**
 * The client code usually creates a bunch of pre-populated flyweights in the 
 * initialization stage of the application.
 */
$factory = new FlyweightFactory([
    ["Chevrolet", "Camaro2018", "pink"],
    ["Mercedes Benz", "C300", "black"],
    ["Mercedes Benz", "C500", "red"],
    ["BMW", "M5", "red"],
    ["BMW", "X6", "white"],
    // ...
]);
$factory->listFlyweights();

// ...

function addCarToPoliceDatabase(
    FlyweightFactory $ff, $plates, $owner,
    $brand, $model, $color
) {
    print("\nClient: Adding a car to database.\n");
    $flyweight = $ff->getFlyweight([$brand, $model, $color]);

    // The client code either stores or calculates extrinsic state and passes it
    // to the flyweight's methods.
    $flyweight->operation([$plates, $owner]);
}

addCarToPoliceDatabase($factory,
    "CL234IR",
    "James Doe",
    "BMW",
    "M5",
    "red");

addCarToPoliceDatabase($factory,
    "CL234IR",
    "James Doe",
    "BMW",
    "X1",
    "red");

$factory->listFlyweights();