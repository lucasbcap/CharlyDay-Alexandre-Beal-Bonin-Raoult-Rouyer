<?php

namespace custumbox\user;

class User
{
    protected string $email, $passwrd, $token;

    /**
     * Constructeur
     * @param string $email email de l'user
     * @param string $passwrd mot de passe de l'user
     * @param string $token token associe à l'user
     */
    public function __construct(string $email, string $passwrd, string $token)
    {
        $this->email = $email;
        $this->passwrd = $passwrd;
        $this->token = $token;
    }
}