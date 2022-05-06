<?php

class BaseService
{

    protected $dao;

    /**
     * General method for getting any data with id
     * @param int id of data
     * @return array which includes query's response.
     */
    public function get_by_id($id)
    {
        return $this->dao->get_by_id($id);
    }

    /**
     * General method for adding any data to database
     * @param object data that need to be inserted in database
     * @return array which includes query's response.
     */
    public function add($data)
    {
        return $this->dao->add($data);
    }

    /**
     * General method for updating any data to database
     * @param int id of data
     * @param object data that need to be updated in database
     * @return array which includes query's response.
     */
    public function update($id, $data)
    {
        $this->dao->update($id, $data);
        return $this->dao->get_by_id($id);
    }
}
