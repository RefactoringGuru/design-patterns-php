<?php

namespace RefactoringGuru\Interpreter\RealWorld;

/**
 * EN: Interpreter Design Pattern
 *
 * Defines a representation for a grammar as well as a mechanism to understand and act upon the grammar.
 *
 * RU: Паттерн Интерпретатор
 *
 * Определяет грамматику простого языка, представляет предложения на этом языке и интерпретирует их.
 */
class Context
{
    private $poolVariable;

    public function lookUp(string $name): bool
    {
        if (!key_exists($name, $this->poolVariable)) {
            die("No exist variable: $name");
        }

        return $this->poolVariable[$name];
    }

    public function assign(VariableExp $variable, $val)
    {
        $this->poolVariable[$variable->getName()] = $val;
    }
}

abstract class AbstractExp
{
    abstract public function interpret(Context $context): bool;
}

class VariableExp extends AbstractExp
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function interpret(Context $context): bool
    {
        return $context->lookUp($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class AndExp extends AbstractExp
{
    private $first;
    private $second;

    public function __construct(AbstractExp $first, AbstractExp $second)
    {
        $this->first  = $first;
        $this->second = $second;
    }

    public function interpret(Context $context): bool
    {
        return (bool)$this->first->interpret($context) && $this->second->interpret($context);
    }
}

class OrExp extends AbstractExp
{
    private $first;
    private $second;

    public function __construct(AbstractExp $first, AbstractExp $second)
    {
        $this->first  = $first;
        $this->second = $second;
    }

    public function interpret(Context $context): bool
    {
        return $this->first->interpret($context) || $this->second->interpret($context);
    }
}

/**
 * EN: The client code.
 *
 * RU: Клиентский код.
 */

$context = new Context();

$a = new VariableExp('A');
$b = new VariableExp('B');
$c = new VariableExp('C');

// example 1:
// A ∧ (B ∨ C)
$exp = new AndExp(
    $a,
    new OrExp($b, $c)
);

$context->assign($a, true);
$context->assign($b, true);
$context->assign($c, false);

$result = $exp->interpret($context) ? 'true' : 'false';

echo 'boolean expression A ∧ (B ∨ C) = ' . $result . ', with variables A=true, B=true, C=false' . PHP_EOL;


// example 2:
// B ∨ (A ∧ (B ∨ C))
$exp = new OrExp(
    $b,
    new AndExp(
        $a,
        new OrExp($b, $c)
    )
);

$context->assign($a, false);
$context->assign($b, false);
$context->assign($c, true);

$result2 = $exp->interpret($context) ? 'true' : 'false';

echo 'boolean expression B ∨ (A ∧ (B ∨ C)) = ' . $result2 . ', with variables A=false, B=false, C=true';
