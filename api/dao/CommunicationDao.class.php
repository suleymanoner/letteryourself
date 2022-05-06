<?php
require_once dirname(__FILE__) . "/BaseDao.class.php";

class CommunicationDao extends BaseDao
{

    /**
     * Constructor for CommunicationDao class.
     */
    public function __construct()
    {
        parent::__construct("communication");
    }

    /**
     * Getting receiver id with letter id from database
     * @param int id of the letter
     * @return array which includes query's response.
     */
    public function get_receiver_id_by_letter_id($letter_id)
    {
        return $this->query_unique("SELECT receiver_id FROM communication WHERE letter_id = :id", ["id" => $letter_id]);
    }

    /**
     * Getting communication from database
     * @param int id of the account
     * @param int offset for pagination
     * @param int limit for pagination
     * @return array which includes query's response.
     */
    public function get_comm_new($account_id, $offset, $limit)
    {

        $params = [];

        $query = "SELECT *
              FROM communication
              WHERE 1 = 1 ";

        if ($account_id) {
            $params["account_id"] = $account_id;
            $query .= "AND account_id = :account_id ";
        }

        $query .= "LIMIT ${limit} OFFSET ${offset}";

        return $this->query($query, $params);
    }
}
