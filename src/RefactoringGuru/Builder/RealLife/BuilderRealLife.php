<?php

namespace RefactoringGuru\Builder\RealLife;

/**
 * Builder Design Pattern
 *
 * Intent: Separate the construction of a complex object from its representation
 * so that the same construction process can create different representations.
 *
 * Example: Builder can be used to construct complex product in several steps
 * without exposing its incomplete state between the construction steps.
 * Application may have several builder classes with a common interface that
 * create different types of products.
 *
 * Query builder is a common example of Builder. Builder interface has common
 * steps to build a generic SQL query. Concrete builders correspond to different
 * SQL dialects and produce SQL queries that can be executed in particular
 * database engine.
 */

/**
 * Builder interface.
 *
 * Construction steps are supposed to return same query builder object to allow
 * chained calls.
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
 * Concrete builder. Builds SQL queries compatible with MySQL.
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

    public function limit(int $start, int $offset): SQLQueryBuilder
    {
        if (!in_array($this->query->type, ['select'])) {
            throw new \Exception("LIMIT can only be added to SELECT");
        }
        $this->query->limit = " LIMIT " . $start . ", " . $offset;

        return $this;
    }

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
 * Concrete builder. Builds SQL queries compatible with PostgresSQL. Postgres is
 * very much like Mysql, with few differences. That's why we extend it from the
 * Mysql builder.
 */
class PostgresQueryBuilder extends MysqlQueryBuilder
{
    /**
     * Among the other things, PostgresSQL has slightly different LIMIT syntax.
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
 * Client code uses builder directly. Since all query builders create same
 * product type (string), we can completely rely on builder interface to
 * interact with concrete builders. Query builders are used without a director
 * class, because client code needs unique query almost every time, so the
 * sequence of the construction steps can not be easily reused.
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
 * Application picks proper query builder, depending on current configuration.
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