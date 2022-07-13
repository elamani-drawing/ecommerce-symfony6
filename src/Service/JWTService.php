<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    /**
     * Generation du JWT
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        //10800 seconde, par defaut le token sera valide 3 h
        if($validity > 0)
        {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;
            
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;    
        }
        //si c'est inferrieur on ne fait rien, pratique pour la verification du token, on a pas a regenerer des dates dexpirations et de creation

        //encore en base 64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        //on "nettoie" les valeurs encodées (retrait des +,/ et = )
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        //on genere la signature
        $secret  = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        //on cree le token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    /**
     * Verifie que le token est valide (correctement formé)
     */
    public function isValid(string $token) : bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/', //maj, min, nbr et - _ = + . etc.
            $token
        )===1 ;
    }

    /**
     * On reccupere le payload
     */
    public function getPayload(string $token) : array
    {
        //on démonte le token 
        $array = explode('.', $token);

        //on decode le payload 
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }
    /**
     * On reccupere le header
     */
    public function getHeader(string $token) : array
    {
        //on démonte le token 
        $array = explode('.', $token);

        //on decode le header 
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    /**
     * Verifie si le token a expirer
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();
        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * on verifie la signature du token 
     */
    public function check(string $token,  string $secret):bool
    {
        //on reccupere le header et le payload
        $payload = $this->getPayload($token);
        $header = $this->getHeader($token);

        //recreer le token et on les compares
        $verifToken = $this->generate($header, $payload, $secret, 0);
        return $token === $verifToken;
    }
}