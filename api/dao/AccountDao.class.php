<?php
require_once dirname(__FILE__) . "/BaseDao.class.php";

class AccountDao extends BaseDao
{
    /**
     * Constructor for AccountDao class.
     */
    public function __construct()
    {
        parent::__construct("accounts");
    }

    /**
     * Getting accounts from database.
     * @param string search word
     * @param int offset for pagination
     * @param int limit for pagination
     * @param string order key for ordering. default "-id", also can be "+id"
     * @return array which includes query's response.
     */
    public function get_accounts($search, $offset, $limit, $order = "-id")
    {
        list($order_column, $order_direction) = self::parse_order($order);
        return $this->query(
            "SELECT * FROM accounts
                         WHERE LOWER(name) LIKE CONCAT('%', :name, '%')
                         ORDER BY ${order_column} ${order_direction}
                         LIMIT ${limit} OFFSET ${offset}",
            ["name" => strtolower($search)]
        );
    }
}
