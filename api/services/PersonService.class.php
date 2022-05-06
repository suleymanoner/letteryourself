<?php
require_once dirname(__FILE__) . '/BaseService.class.php';
require_once dirname(__FILE__) . '/../dao/PersonDao.class.php';
require_once dirname(__FILE__) . '/../dao/AccountDao.class.php';
require_once dirname(__FILE__) . '/../dao/CommunicationDao.class.php';
require_once dirname(__FILE__) . '/../clients/SMTPClient.class.php';


class PersonService extends BaseService
{

    private $accountDao;
    private $smtpClient;
    private $communicationDao;

    /**
     * Constructor for PersonService class.
     */
    public function __construct()
    {
        $this->dao = new PersonDao();
        $this->accountDao = new AccountDao();
        $this->smtpClient = new SMTPClient();
        $this->communicationDao = new CommunicationDao();
    }

    /**
     * Getting person with account id
     * @param int id of account
     * @return array which includes query's response.
     */
    public function get_person_by_account_id($id)
    {
        return $this->dao->get_person_by_account_id($id);
    }

    /**
     * Getting persons from database.
     * @param string role of account, admin or user
     * @param int offset for pagination
     * @param int limit for pagination
     * @param string search word
     * @param string order key for ordering. default "-id", also can be "+id"
     * @return array which includes query's response.
     */
    public function get_all_persons($role, $offset, $limit, $search, $order)
    {
        return $this->dao->get_all_persons($role, $offset, $limit, $search, $order);
    }

    /**
     * Reset token for person
     * @param object person that need to be update token 
     * @return array which includes query's response.
     */
    public function reset($person)
    {
        $db_person = $this->dao->get_person_by_token($person['token']);
        if (!isset($db_person['id'])) throw new Exception("Invalid token", 400);

        if (strtotime(date(Config::DATE_FORMAT)) - strtotime($db_person['token_created_at']) > 300) throw new Exception("Token time expired");

        $this->dao->update($db_person['id'], ['password' => md5($person['password']), 'token' => NULL]);

        return $db_person;
    }

    /**
     * Forgot password for person
     * @param object person that forgot to password
     * @return array which includes query's response.
     */
    public function forgot($person)
    {
        $db_person = $this->dao->get_person_by_email($person['email']);
        if (!isset($db_person['id'])) throw new Exception("Person doesn't exist", 400);

        if (strtotime(date(Config::DATE_FORMAT)) - strtotime($db_person['created_at']) < 300) throw new Exception("Be patient token on his way.");

        $db_person = $this->update($db_person['id'], ['token' => md5(random_bytes(16)), 'token_created_at' => date(Config::DATE_FORMAT)]);

        $this->smtpClient->send_person_recovery_token($db_person);
    }

    /**
     * Login for person
     * @param object person that login
     * @return array which includes query's response.
     */
    public function login($person)
    {
        $db_person = $this->dao->get_person_by_email($person['email']);

        if (!isset($db_person['id'])) throw new Exception("Person doesn't exist", 400);
        if ($db_person['status'] != 'ACTIVE') throw new Exception("Person doesn't active.");

        $account = $this->accountDao->get_by_id($db_person['account_id']);
        if (!isset($account['id']) || $account['status'] != 'ACTIVE') throw new Exception("Account doesn't exist", 400);
        if ($db_person['password'] != md5($person['password'])) throw new Exception("Invalid password.", 400);

        //$jwt = \Firebase\JWT\JWT::encode(["exp" => (time() + Config::JWT_TOKEN_TIME),"id" => $person["id"], "aid" => $person["account_id"], "r" => $person["role"]], Config::JWT_SECRET);
        //return ["token" => $jwt];

        return $db_person;
    }

    /**
     * Register for person
     * @param object person that login
     * @return array which includes query's response.
     */
    public function register($person)
    {
        if (!isset($person['account'])) throw new Exception("Account is required!");

        try {
            $this->dao->beginTransaction();

            $account = $this->accountDao->add([
                "name" => $person['account'],
                "status" => "PENDING",
                "created_at" => date(Config::DATE_FORMAT)
            ]);

            $person = parent::add([
                "account_id" => $account['id'],
                "name" => $person['name'],
                "surname" => $person['surname'],
                "email" => $person['email'],
                "password" => md5($person['password']),
                "status" => "PENDING",
                "created_at" => date(Config::DATE_FORMAT),
                "token" => md5(random_bytes(16))
            ]);
            $this->dao->commit();
        } catch (\Exception $e) {
            $this->dao->rollBack();
            if (str_contains($e->getMessage(), 'persons.uq_person_email')) {
                throw new Exception("Person with same email already exist : " . $person['email'], 400, $e);
            } else {
                throw $e;
            }
        }

        $this->smtpClient->send_register_token($person);
        return $person;
    }

    /**
     * Confirm account for person
     * @param string token that used for confirmation
     * @return array which includes query's response.
     */
    public function confirm($token)
    {
        $person = $this->dao->get_person_by_token($token);

        if (!isset($person['id'])) throw new Exception("Invalid token", 400);

        $this->dao->update($person['id'], ["status" => "ACTIVE", "token" => NULL]);
        $this->accountDao->update($person['account_id'], ["status" => "ACTIVE"]);

        return $person;
    }
}
