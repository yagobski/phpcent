<?php
namespace phpcent;

class Client
{
    protected $projectSecret;
    private   $host;
    private   $projectKey;
    /**
     * @var ITransport $transport
     */
    private $transport;
    private $_su = false;

    public function __construct($host = "http://localhost:8000")
    {
        $this->host = $host;

    }

    public function getHost()
    {
        return $this->host;
    }

    public function setProject($projectKey, $projectSecret)
    {
        $this->projectSecret = $projectSecret;
        $this->projectKey = $projectKey;

        return $this;
    }

    /**
     * send message into channel of namespace. data is an actual information you want to send into channel
     *
     * @param       $channel
     * @param array $data
     * @return mixed
     */
    public function publish($channel, $data = [])
    {
        return $this->send("publish", ["channel" => $channel, "data" => $data]);
    }

    /**
     * unsubscribe user with certain ID from channel.
     *
     * @param $channel
     * @param $userId
     * @return mixed
     */
    public function unsubscribe($channel, $userId)
    {
        return $this->send("unsubscribe", ["channel" => $channel, "user" => $userId]);
    }

    /**
     * disconnect user by user ID.
     *
     * @param $userId
     * @return mixed
     */
    public function disconnect($userId)
    {
        return $this->send("disconnect", ["user" => $userId]);
    }

    /**
     * get channel presence information (all clients currently subscribed on this channel).
     *
     * @param $channel
     * @return mixed
     */
    public function presence($channel)
    {
        return $this->send("presence", ["channel" => $channel]);
    }

    /**
     * get channel history information (list of last messages sent into channel).
     *
     * @param $channel
     * @return mixed
     */
    public function history($channel)
    {
        return $this->send("presence", ["channel" => $channel]);
    }

    /**
     * @param string $method
     * @param array  $params
     * @return mixed
     * @throws \Exception
     */
    public function send($method, $params = [])
    {
        if (empty($params)) {
            $params = new \StdClass();
        }
        $data = json_encode(["method" => $method, "params" => $params]);

        return
            $this->getTransport()
                 ->communicate(
                     $this->host,
                     $this->projectKey,
                     ["data" => $data, "sign" => Sign::api($this->projectKey, $this->projectSecret, $data)]
                 );
    }

    /**
     * @param $data
     * @return string $hash
     * @throws \Exception if required data not specified
     * @deprecated
     */
    public function buildSign($data)
    {
        if (empty($this->projectKey) || empty($this->projectSecret)) {

            throw new \BadMethodCallException(
                "Project key and Project secret should nod be empty"
            );
        }

        $ctx = hash_init("sha256", HASH_HMAC, $this->projectSecret);
        hash_update($ctx, ($this->_su) ? "_" : $this->projectKey);
        hash_update($ctx, $data);

        return hash_final($ctx);
    }

    /**
     * @return ITransport
     */
    private function getTransport()
    {
        if ($this->transport == null) {
            $this->setTransport(new Transport());
        }

        return $this->transport;
    }

    /**
     * @param ITransport $transport
     */
    public function setTransport(ITransport $transport)
    {
        $this->transport = $transport;
    }

}
