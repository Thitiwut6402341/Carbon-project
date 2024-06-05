<?php

namespace App\Http\Libraries\JWT;

use App\Http\Libraries\JWT\JWT;
use App\Http\Libraries\JWT\Key;

// define("PRIVATE_KEY", "<Secret Key>");
// define("PRIVATE_KEY", env('JWT_SECRET'));
define("PRIVATE_KEY", "-----BEGIN RSA PRIVATE KEY-----
MIIBOwIBAAJBAIsw0/uIjzPfGt04yAPgWsh8DlDz5oyU1ZIm9fLc0IvXW29w/pzB
qRAWl2QYqxipMROQy5wdFiU4qxfHHBPaO9sCAwEAAQJAW82rsxYhpUu8czZVLcFW
/y5bXudPI1+y8T+DLliXr/MpctNbgqVGEEmb8UXQi6yy80/wImKndAbY3MhjIwxl
GQIhAP5VambEElUFdj8LP6ctQ7ZPouYy1DA9Z6t85G2pMUy1AiEAjBpJmSrIqRjc
LD4vogeReuI8QQrQ9OKrABfFj5TfUE8CIQDf2qhbKUqFYNhR5wGwkEuf5HoZqTVP
/EwKCVQ5HQkSXQIgX0ldtxO3J/LlhB3DTcMx+c62xlHx7ivfu49vaYkKHNcCIQC0
Cvb/bE3X1/eCWmn3qvECJrR18pj7zGAMn+S+RaaHrA==
-----END RSA PRIVATE KEY-----");


class JWTUtils
{
    public function generateToken($payload)
    {
        $token = JWT::encode($payload, PRIVATE_KEY, 'HS256');
        return $token;
    }

    public function verifyToken($header)
    {
        $token = null;
        // extract the token from the header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        // check if token is null or empty
        if (is_null($token) || empty($token)) {
            return (object)['state' => false, 'msg' => 'Access denied', 'decoded' => []];
        }

        try {
            $decoded = JWT::decode($token, new Key(PRIVATE_KEY, 'HS256'));
            return (object)['state' => true, 'msg' => 'OK', 'decoded' => $decoded];
        } catch (\Exception $e) {
            return (object)['state' => false, 'msg' => $e->getMessage(), 'decoded' => []];
        }
    }
}
