<?php
require_once dirname(__FILE__) . '/BaseService.class.php';
require_once dirname(__FILE__) . '/../dao/ReceiverDao.class.php';

class ReceiverService extends BaseService
{

    /**
     * Constructor for ReceiverService class.
     */
    public function __construct()
    {
        $this->dao = new ReceiverDao();
    }

    /**
     * Getting receiver email with id
     * @param int id of receiver
     * @return array which includes query's response.
     */
    public function get_receiver_email_with_id($receiver_id)
    {
        return $this->dao->get_receiver_email_with_id($receiver_id);
    }

    /**
     * Adding receiver in database
     * @param object receiver that needs to be inserted in database
     * @return array which includes query's response.
     */
    public function add_receiver($receiver)
    {
        try {
            $data = [
                "receiver_email" => $receiver,
            ];
            return parent::add($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Getting receiver id with email
     * @param string email of receiver
     * @return array which includes query's response.
     */
    public function get_receiver_id_by_email($receiver_email)
    {
        return $this->dao->get_receiver_id_by_email($receiver_email);
    }

    /**
     * Updating receiver email with id
     * @param string email of receiver
     * @param int id of row from receiver table 
     * @return array which includes query's response.
     */
    public function update_receiver_email($receiver_email, $id)
    {
        return $this->dao->update_receiver_email($receiver_email, $id);
    }
}
