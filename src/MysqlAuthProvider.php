<?php

class MysqlAuthProvider extends \Thruway\Authentication\AbstractAuthProviderClient
{
    /** @var \React\MySQL\Connection */
    private $mysqlConnection;

    /**
     * @inheritDoc
     */
    public function onSessionStart($session, $transport)
    {
        parent::onSessionStart($session, $transport);

        $connection = new React\MySQL\Connection($this->getLoop(), array(
            'dbname' => 'thruway_auth_example',
            'user'   => 'root',
            'passwd' => 'root',
            'port'   => 8889
        ));
        //connecting to mysql server, not required.
        $connection->connect(function () {});

        $this->mysqlConnection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function preProcessAuthenticate(array $args)
    {
        if (!isset($args[0]) && !is_object($args[0])) {
            return ["FAILURE"];
        }

        $authInfo = $args[0];
        if (isset($authInfo->authid) && isset($authInfo->signature)) {
            // lookup the user
            $query = (new \React\MySQL\Query("SELECT id FROM users WHERE login = ? AND password = ?"))
                ->bindParams($authInfo->authid, $authInfo->signature);

            $deferred = new \React\Promise\Deferred();

            $this->mysqlConnection->query($query->getSql(), function ($command, $conn) use ($deferred) {
                if ($command->hasError()) { //test whether the query was executed successfully
                    //error
                    $error = $command->getError();// get the error object, instance of Exception.
                    $this->resolve(['FAILURE']);
                } else {
                    $results = $command->resultRows; //get the results
                    $fields  = $command->resultFields; // get table fields

                    if (count($results) > 0) {
                        $deferred->resolve(['SUCCESS']);
                    } else {
                        $deferred->resolve(['FAILURE']);
                    }
                }
            });

            return $deferred->promise();
        }


        return ["FAILURE"];
    }

    /**
     * @inheritDoc
     */
    public function getMethodName()
    {
        return "mysql_auth";
    }
}
