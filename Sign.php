<?php
namespace phpcent;

class Sign
{

    public static function api($projectKey, $projectSecret, $data)
    {
        $ctx = hash_init("sha256", HASH_HMAC, $projectSecret);
        hash_update($ctx, $projectKey);
        hash_update($ctx, $data);

        return hash_final($ctx);
    }

    public static function token($projectSecret, $projectKey, $user, $timeStamp, $info = [])
    {
        throw new \Exception('Not implemented');
    }

    public static function channel($project_secret, $client, $channel, $info = "")
    {
        throw new \Exception('Not implemented');
    }

}