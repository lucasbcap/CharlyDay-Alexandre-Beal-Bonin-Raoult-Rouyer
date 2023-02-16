<?php

namespace custumbox\action;

use custumbox\db\ConnectionFactory;
use custumbox\user\User;

class AdminAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }


    public function execute(): string
    {
        $bd = ConnectionFactory::makeConnection();
        $res = "";
        $requete = <<<END
                    select * from user 
                    where login = ?;
                    END;
        $requete = $bd->prepare($requete);
        $requete->bindParam(1,$_GET['idlogin']);
        $requete->execute();
        $d = $requete->fetch();
        $user = new User($d['nomUser'],$d['prenomUser'],$d['tel'],$d['login'],$d['email'],$d['passwrd'],$d['token']);
        $user->mettreAdmin();
        return $res;
    }
}