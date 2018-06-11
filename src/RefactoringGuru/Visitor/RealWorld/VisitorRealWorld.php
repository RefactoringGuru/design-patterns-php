<?php

namespace RefactoringGuru\Visitor\RealWorld;

/**
 * Visitor Design Pattern
 *
 * Intent: Represent an operation to be performed over elements of an object
 * structure. The Visitor pattern lets you define a new operation without
 * changing the classes of the elements on which it operates.
 *
 * Example: In this example, the Visitor pattern helps to introduce a reporting
 * feature into an existing class hierarchy:
 *
 * Company > Department > Employee
 *
 * Once the Visitor infrastructure is added to the app, you can easily add other
 * similar behaviors to the app, without changing the existing classes.
 */

/**
 * The Component interface declares a method of accepting visitor objects.
 *
 * In this method, a Concrete Component must call a specific Visitor's method
 * that has the same parameter type as that component.
 */
interface Entity
{
    public function accept(Visitor $visitor);
}

/**
 * The Company Concrete Component.
 */
class Company implements Entity
{
    private $name;

    /**
     * @var Department[]
     */
    private $departments;

    public function __construct($name, $departments)
    {
        $this->name = $name;
        $this->departments = $departments;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDepartments()
    {
        return $this->departments;
    }

    // ...

    public function accept(Visitor $visitor)
    {
        // See, the Company component must call the visitCompany method. The
        // same principle applies to all components.
        return $visitor->visitCompany($this);
    }
}

/**
 * The Department Concrete Component.
 */
class Department implements Entity
{
    private $name;

    /**
     * @var Employee[]
     */
    private $employees;

    public function __construct($name, $employees)
    {
        $this->name = $name;
        $this->employees = $employees;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmployees()
    {
        return $this->employees;
    }

    public function getCost()
    {
        $cost = 0;
        foreach ($this->employees as $employee) {
            $cost += $employee->getSalary();
        }

        return $cost;
    }

    // ...

    public function accept(Visitor $visitor)
    {
        return $visitor->visitDepartment($this);
    }
}

/**
 * The Employee Concrete Component.
 */
class Employee implements Entity
{
    private $name;

    private $position;

    private $salary;

    public function __construct($name, $position, $salary)
    {
        $this->name = $name;
        $this->position = $position;
        $this->salary = $salary;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getSalary()
    {
        return $this->salary;
    }

    // ...

    public function accept(Visitor $visitor)
    {
        return $visitor->visitEmployee($this);
    }
}

/**
 * The Visitor interface declares a set of visiting methods for each of the
 * Concrete Component classes.
 */
interface Visitor
{
    public function visitCompany(Company $company);

    public function visitDepartment(Department $department);

    public function visitEmployee(Employee $employee);
}

/**
 * The Concrete Visitor must provide implementations for every single class of
 * the Concrete Components.
 */
class SalaryReport implements Visitor
{
    public function visitCompany(Company $company)
    {
        $output = "";
        $total = 0;

        foreach ($company->getDepartments() as $department) {
            $total += $department->getCost();
            $output .= "\n--".$this->visitDepartment($department);
        }

        $output = $company->getName().
            " (".money_format("%i", $total).")\n".$output;

        return $output;
    }

    public function visitDepartment(Department $department)
    {
        $output = "";

        foreach ($department->getEmployees() as $employee) {
            $output .= "   ".$this->visitEmployee($employee);
        }

        $output = $department->getName().
            " (".money_format("%i", $department->getCost()).")\n\n".
            $output;

        return $output;
    }

    public function visitEmployee(Employee $employee)
    {
        return money_format("%#6n", $employee->getSalary()).
            " ".$employee->getName().
            " (".$employee->getPosition().")\n";
    }
}

/**
 * The client code.
 */

$mobileDev = new Department("Mobile Development", [
    new Employee("Albert Falmore", "designer", 100000),
    new Employee("Ali Halabay", "programmer", 100000),
    new Employee("Sarah Konor", "programmer", 90000),
    new Employee("Monica Ronaldino", "QA engineer", 31000),
    new Employee("James Smith", "QA engineer", 30000),
]);
$techSupport = new Department("Tech Support", [
    new Employee("Larry Ulbrecht", "supervisor", 70000),
    new Employee("Elton Pale", "operator", 30000),
    new Employee("Rajeet Kumar", "operator", 30000),
    new Employee("John Burnovsky", "operator", 34000),
    new Employee("Sergey Korolev", "operator", 35000),
]);
$company = new Company("SuperStarDevelopment", [$mobileDev, $techSupport]);

setlocale(LC_MONETARY, 'en_US');
$report = new SalaryReport();

print("Client: I can print a report for a whole company:\n\n");
print($company->accept($report));

print("\nClient: ...or just for a single department:\n\n");
print($techSupport->accept($report));

// $export = new JSONExport(); 
// print($company->accept($export));

