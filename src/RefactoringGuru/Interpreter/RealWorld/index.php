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

// A ∧ (B ∨ C)
$exp = new AndExp(
    $a,
    new OrExp($b, $c)
);

$context->assign($a, true);
$context->assign($b, true);
$context->assign($c, false);

$result = $exp->interpret($context) ? 'true' : 'false';

echo 'boolean expression A ∧ (B ∨ C) = ' . $result;
