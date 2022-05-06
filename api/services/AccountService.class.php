<?php
require_once dirname(__FILE__) . '/BaseService.class.php';
require_once dirname(__FILE__) . '/../dao/AccountDao.class.php';

class AccountService extends BaseService
{

    /**
     * Constructor for AccountService class.
     */
    public function __construct()
    {
        $this->dao = new AccountDao();
    }

    /**
     * Getting accounts from database.
     * @param string search word
     * @param int offset for pagination
     * @param int limit for pagination
     * @param string order key for ordering. default "-id", also can be "+id"
     * @return array which includes query's response.
     */
    public function get_accounts($search, $offset, $limit, $order)
    {
        if ($search) {
            return $this->dao->get_accounts($search, $offset, $limit, $order);
        } else {
            return $this->dao->get_all($offset, $limit, $order);
        }
    }

    /**
     * Adding account in database.
     * @param object account that need to be inserted in database
     * @return array which includes query's response.
     */
    public function add($account)
    {
        if (!isset($account['name'])) throw new Exception("Name is missing!");

        $account['created_at'] = date(Config::DATE_FORMAT);

        return parent::add($account);
    }
}
