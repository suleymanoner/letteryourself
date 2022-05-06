<?php
require_once dirname(__FILE__) . "/BaseDao.class.php";

class LetterDao extends BaseDao
{

    /**
     * Constructor for LetterDao class.
     */
    public function __construct()
    {
        parent::__construct("letter");
    }

    /**
     * Getting letters from database.
     * @param int id of account
     * @param int offset for pagination
     * @param int limit for pagination
     * @param string search word
     * @param string order key for ordering. default "-id", also can be "+id"
     * @return array which includes query's response.
     */
    public function get_letter($account_id, $offset, $limit, $search, $order)
    {
        list($order_column, $order_direction) = self::parse_order($order);

        $params = [];

        $query = "SELECT *
              FROM letter
              WHERE 1 = 1 ";

        if ($account_id) {
            $params["account_id"] = $account_id;
            $query .= "AND account_id = :account_id ";
        }

        if (isset($search)) {
            $query .= "AND ( LOWER(title) LIKE CONCAT('%', :search, '%') OR send_at LIKE CONCAT('%', :search, '%'))";
            $params['search'] = strtolower($search);
        }

        $query .= "ORDER BY ${order_column} ${order_direction} ";
        $query .= "LIMIT ${limit} OFFSET ${offset}";

        return $this->query($query, $params);
    }

    /**
     * Getting letter with account id and letter id from database.
     * @param int id of account
     * @param int id of letter
     * @return array which includes query's response.
     */
    public function get_letter_with_account_and_letter_id($account_id, $id)
    {
        return $this->query_unique("SELECT * FROM letter WHERE account_id = :acc_id AND id = :id", ["acc_id" => $account_id, "id" => $id]);
    }

    /**
     * Getting letter title with letter id from database.
     * @param int id of letter
     * @return array which includes query's response.
     */
    public function get_letter_title_by_id($id)
    {
        return $this->query_unique("SELECT title FROM letter WHERE id = :id", ["id" => $id]);
    }

    /**
     * Getting letter id with title from database.
     * @param string title of letter
     * @return array which includes query's response.
     */
    public function get_letter_id_by_title($title)
    {
        return $this->query_unique("SELECT id FROM letter WHERE title = :title", ["title" => $title]);
    }

    /**
     * Updating letter
     * @param int id of letter
     * @param string title of letter
     * @param string body of letter
     * @param string sending date of letter
     * @return array which includes query's response.
     */
    public function update_letter_new($id, $title, $body, $send_at)
    {
        return $this->query_unique("UPDATE letter SET title = :title, body = :body, send_at = :send_at
       WHERE id = :id", ["title" => $title, "body" => $body, "send_at" => $send_at, "id" => $id]);
    }

    /**
     * Counting how many letters for account
     * @param int id of account
     * @return array which includes query's response.
     */
    public function how_many_letters($account_id)
    {
        return $this->query_unique("SELECT COUNT(*) as 'total' FROM letter WHERE account_id = :account_id", ["account_id" => $account_id]);
    }
}
