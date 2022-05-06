<?php
require_once dirname(__FILE__) . '/BaseService.class.php';
require_once dirname(__FILE__) . '/../dao/LetterDao.class.php';

class LetterService extends BaseService
{

    /**
     * Constructor for LetterService class.
     */
    public function __construct()
    {
        $this->dao = new LetterDao();
    }

    /**
     * Adding letter in database.
     * @param int id of account
     * @param object letter that need to be inserted in database
     * @return array which includes query's response.
     */
    public function add_letter($account_id, $letter)
    {
        try {
            $data = [
                "title" => $letter["title"],
                "body" => $letter["body"],
                "created_at" => date(Config::DATE_FORMAT),
                "send_at" => $letter["send_at"],
                "account_id" => $account_id
            ];
            return parent::add($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Getting letter id with title from database.
     * @param string title of letter
     * @return array which includes query's response.
     */
    public function get_letter_id_by_title($title)
    {
        return $this->dao->get_letter_id_by_title($title);
    }

    /**
     * Getting letter title with id from database.
     * @param int id of letter
     * @return array which includes query's response.
     */
    public function get_letter_title_by_id($id)
    {
        return $this->dao->get_letter_title_by_id($id);
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
        return $this->dao->get_letter($account_id, $offset, $limit, $search, $order);
    }

    /**
     * Getting letter with account id and letter id from database.
     * @param int id of account
     * @param int id of letter
     * @return array which includes query's response.
     */
    public function get_letter_with_account_and_letter_id($account_id, $id)
    {
        return $this->dao->get_letter_with_account_and_letter_id($account_id, $id);
    }

    /**
     * First updating letter
     * @param object person that needs to be update letter
     * @param int id of letter
     * @param object letter that needs to be updated
     * @return array which includes query's response.
     */
    public function update_letter($person, $id, $letter)
    {
        $db_template = $this->dao->get_by_id($id);
        if ($db_template['account_id'] != $person['aid']) {
            throw new Exception("Invalid letter", 403);
        }
        return $this->update($id, $letter);
    }

    /**
     * Second updating letter
     * @param int id of letter
     * @param string title of letter
     * @param string body of letter
     * @param string sending date of letter
     * @return array which includes query's response.
     */
    public function update_letter_new($id, $title, $body, $send_at)
    {
        return $this->dao->update_letter_new($id, $title, $body, $send_at);
    }

    /**
     * Counting how many letters for account
     * @param int id of account
     * @return array which includes query's response.
     */
    public function how_many_letters($account_id)
    {
        return $this->dao->how_many_letters($account_id);
    }
}
