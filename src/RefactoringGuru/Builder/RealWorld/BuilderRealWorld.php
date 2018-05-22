<?php

namespace RefactoringGuru\Builder\RealWorld;

/**
 * Builder Design Pattern
 *
 * Intent: Separate the construction of a complex object from its representation
 * so that the same construction process can create different representations.
 *
 * Example: One of the best applications of the Builder pattern is a SQL query
 * builder. The Builder interface defines the common steps required to build a
 * generic SQL query. On the other hand Concrete Builders, corresponding to
 * different SQL dialects, implement these steps by returning parts of SQL
 * queries that can be executed in particular database engine.
 */

/**
 * The Builder interface. All of the construction steps are returning the
 * builder object to allow chaining: $builder->select(...)->where(...)
 */
interface SQLQueryBuilder
{
    public function select(string $table, array $fields): SQLQueryBuilder;

    public function where(string $field, string $value, string $operator = '='): SQLQueryBuilder;

    public function limit(int $start, int $offset): SQLQueryBuilder;

    // +100 other SQL syntax methods...

    public function getSQL(): string;
}

/**
 * Concrete Builder. Builds SQL queries compatible with MySQL.
 */
class MysqlQueryBuilder implements SQLQueryBuilder
{
    protected $query;

    protected function reset()
    {
        $this->query = new \stdClass();
    }

    /**
     * Build a base SELECT query.
     */
    public function select(string $table, array $fields): SQLQueryBuilder
    {
        $this->reset();
        $this->query->base = "SELECT " . implode(", ", $fields) . " FROM " . $table;
        $this->query->type = 'select';

        return $this;
    }

    /**
     * Add a WHERE condition.
     */
    public function where(string $field, string $value, string $operator = '='): SQLQueryBuilder
    {
        if (!in_array($this->query->type, ['select', 'update'])) {
            throw new \Exception("WHERE can only be added to SELECT OR UPDATE");
        }
        $this->query->where[] = "$field $operator '$value'";

        return $this;
    }

    /**
     * Add a LIMIT constraint.
     */
    public function limit(int $start, int $offset): SQLQueryBuilder
    {
        if (!in_array($this->query->type, ['select'])) {
            throw new \Exception("LIMIT can only be added to SELECT");
        }
        $this->query->limit = " LIMIT " . $start . ", " . $offset;

        return $this;
    }

    /**
     * Get the final query string.
     */
    public function getSQL(): string
    {
        $query = $this->query;
        $sql = $query->base;
        if (!empty($query->where)) {
            $sql .= " WHERE " . implode(' AND ', $query->where);
        }
        if (isset($query->limit)) {
            $sql .= $query->limit;
        }
        $sql .= ";";
        return $sql;
    }
}

/**
 * Concrete Builder. Builds SQL queries compatible with PostgresSQL. Postgres is
 * very similar to Mysql, but still has a few differences. That's why we extend
 * it from the MySQL builder.
 */
class PostgresQueryBuilder extends MysqlQueryBuilder
{
    /**
     * Among other things, PostgresSQL has slightly different LIMIT syntax.
     */
    public function limit(int $start, int $offset): SQLQueryBuilder
    {
        parent::limit($start, $offset);

        $this->query->limit = " LIMIT " . $start . " OFFSET " . $offset;

        return $this;
    }

    // + tons of other overrides...
}


/**
 * Note that the client code uses the builder directly. A designated Director
 * class is not necessary in this case, because the client code needs a
 * different query almost every time, so the sequence of the construction steps
 * can not be easily reused.
 *
 * Since all of the query builders create the same product type (which is a
 * string) we can rely on the Builder interface when interacting with all
 * Concrete Builders.
 */
function clientCode(SQLQueryBuilder $queryBuilder)
{
    // ...

    $query = $queryBuilder
        ->select("users", ["name", "email", "password"])
        ->where("age", 18, ">")
        ->where("age", 30, "<")
        ->limit(10, 20)
        ->getSQL();

    print($query);

    // ...
}


/**
 * The application selects a proper query builder type depending on a current
 * configuration.
 */
// if ($_ENV['database_type'] == 'postgres') {
//     $builder = new PostgresQueryBuilder(); } else {
//     $builder = new MysqlQueryBuilder(); }
//
// clientCode($builder);


print("Testing MySQL query builder:\n");
clientCode(new MysqlQueryBuilder());

print("\n\n");

print("Testing PostgresSQL query builder:\n");
clientCode(new PostgresQueryBuilder());