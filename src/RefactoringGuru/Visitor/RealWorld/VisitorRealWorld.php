<?php

namespace RefactoringGuru\Visitor\RealWorld;

/**
 * Visitor Design Pattern
 *
 * Intent: Represent an operation to be performed on the elements of an object
 * structure. Visitor lets you define a new operation without changing the
 * classes of the elements on which it operates.
 *
 * Example: In this example the Visitor pattern helps to introduces a reporting
 * feature into an existing object hierarchy Company > Department > Employee.
 * Other similar behaviors can be added into the program without changing the
 * actual classes of the hierarchy.
 */

/**
 * Component Interface.
 */
interface Entity
{
    public function accept(Visitor $visitor);
}

/**
 * Concrete Component.
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
        return $visitor->visitCompany($this);
    }
}

/**
 * Concrete Component.
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
 * Concrete Component.
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
 * Visitor Interface.
 */
interface Visitor
{
    public function visitCompany(Company $company);

    public function visitDepartment(Department $department);

    public function visitEmployee(Employee $employee);
}

/**
 * Concrete Visitor.
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
 * Client code.
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

