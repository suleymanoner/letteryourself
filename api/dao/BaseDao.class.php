<?php
require_once dirname(__FILE__) . "/../config.php";

/**
 * This BaseDao class will contact to database.
 *
 * Other dao classes will be child of this BaseDao class.
 *
 * @author Suleyman Oner
 */

class BaseDao
{
    protected $connection;
    private $table;

    /**
     * Begin transaction for PDO.
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    /**
     * Commit transaction for PDO.
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * Rollback for PDO.
     */
    public function rollBack()
    {
        $this->connection->rollBack();
    }

    /**
     * Constructor for BaseDao class.
     * @param object for initialize the table object 
     */
    public function __construct($table)
    {
        $this->table = $table;
        try {
            $this->connection = new PDO("mysql:host=" . Config::DB_HOST() . ";port=" . Config::DB_PORT() . ";dbname=" . Config::DB_SCHEME(), Config::DB_USERNAME(), Config::DB_PASSWORD());
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //$this->connection->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Parsing order and returns column and direction
     * @param string order string should start "+" or "-"
     * @return array which includes order column and direction.
     */
    public static function parse_order($order)
    {
        switch (substr($order, 0, 1)) {
            case '-':
                $order_direction = "ASC";
                break;
            case '+':
                $order_direction = "DESC";
                break;
            default:
                throw new Exception("Invalid order format! Use '-' or '+'");
                break;
        };

        $order_column = substr($order, 1);
        //$order_column = trim($this->connection->quote(substr($order, 1)),"'");
        return [$order_column, $order_direction];
    }

    /**
     * Inserting data to database table
     * @param string table name that need to be inserted
     * @param object entity that need to be inserted in table
     * @return array which includes inserted entity.
     */
    public function insert($table, $entity)
    {
        $query = "INSERT INTO ${table} (";
        foreach ($entity as $column => $value) {
            $query .= $column . ", ";
        }
        $query = substr($query, 0, -2);
        $query .= ") VALUES (";
        foreach ($entity as $column => $value) {
            $query .= ":" . $column . ", ";
        }
        $query = substr($query, 0, -2);
        $query .= ")";

        $stmt = $this->connection->prepare($query);
        $stmt->execute($entity);
        $entity['id'] = $this->connection->lastInsertId();
        return $entity;
    }

    /**
     * Updating data in database table
     * @param string table name that need to be updated
     * @param int id which id of the entity
     * @param object entity that need to be updated in table
     * @param string id column which default "id", it could change according to need
     */
    public function execute_update($table, $id, $entity, $id_column = "id")
    {
        $query = "UPDATE ${table} SET ";
        foreach ($entity as $name => $value) {
            $query .= $name . "= :" . $name . ", ";
        }
        $query = substr($query, 0, -2);
        $query .= " WHERE ${id_column} = :id";

        $stmt = $this->connection->prepare($query);
        $entity['id'] = $id;
        $stmt->execute($entity);
    }

    /**
     * Query method which executes SQL queries
     * @param string query which includes SQL commands
     * @param array params for SQL query
     * @return array which includes response of query.
     */
    public function query($query, $params)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Query method only for unique response
     * @param string query which includes SQL commands
     * @param array params for SQL query
     * @return array which includes first response of query.
     */
    public function query_unique($query, $params)
    {
        $results = $this->query($query, $params);
        return reset($results);
    }

    /**
     * Adding data with usage of insert method
     * @param object entity that need to be inserted in table
     * @return array which includes inserted entity.
     */
    public function add($entity)
    {
        return $this->insert($this->table, $entity);
    }

    /**
     * Updating data with usage of execute update method
     * @param object entity that need to be updated in table
     */
    public function update($id, $entity)
    {
        $this->execute_update($this->table, $id, $entity);
    }

    /**
     * Getting data with id
     * @param string id that need to retrieve data
     * @return array which includes data from database.
     */
    public function get_by_id($id)
    {
        return $this->query_unique("SELECT * FROM " . $this->table . " WHERE id = :id", ["id" => $id]);
    }

    /**
     * Getting data with offset, limit and order
     * @param int offset for pagination, default 0
     * @param int limit for pagination, default 25
     * @param string order key for ordering. default "-id", also can be "+id"
     * @return array which includes data from database.
     */
    public function get_all($offset = 0, $limit = 25, $order = "-id")
    {
        list($order_column, $order_direction) = self::parse_order($order);
        return $this->query("SELECT *
                         FROM " . $this->table . "
                         ORDER BY ${order_column} ${order_direction}
                         LIMIT ${limit} OFFSET {$offset}", []);
    }
}
