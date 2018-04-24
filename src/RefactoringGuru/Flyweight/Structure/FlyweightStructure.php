<?php

namespace RefactoringGuru\Flyweight\Structure;

/**
 * Flyweight Design Pattern
 *
 * Intent: Use sharing to support a large number of objects that have part of
 * their internal state in common where the other part of state can vary.
 */

/**
 * FlyweightFactory creates and manages flyweight objects. It ensures that
 * flyweights are shared properly. When a client requests a flyweight, the
 * factory object supplies an existing instance creates one, if none exists.
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
     * Return a string hash for a given state.
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
     * Return an existing flyweight with a given state or create a new one.
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
 * Flyweight stores portion of the state that belongs to multiple real business
 * entities (also called intrinsic state). The rest of the state, which is
 * unique for each entity, it accepts via parameters of specific methods.
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
 * Client code.
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