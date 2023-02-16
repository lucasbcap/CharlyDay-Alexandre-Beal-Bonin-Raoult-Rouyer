<?php

namespace custumbox\user;

use custumbox\Catalogue\Produit;
use custumbox\db\ConnectionFactory as ConnectionFactory;
use iutnc\netvod\video\Serie;


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


    static function TrieSQL(string $nomAttribut = null): array
    {
        $bdd = ConnectionFactory::makeConnection();
        if ($nomAttribut != null) $query = "select id from produit order by $nomAttribut";
        else $query = "select id from produit";


        $c1 = $bdd->prepare($query);
        $c1->execute();
        $array = null;
        while ($d = $c1->fetch()) {
            $produit = Produit::creerProduit($d["id"]);
            if ($produit != null) {
                $array[] = $produit;
            }
        }
        return $array;
    }

    function getSQL(string $table): ?array
    {
        $bdd = ConnectionFactory::makeConnection();
        $c1 = $bdd->prepare("select idProduit from $table where email = :email");
        $c1->bindParam(":email", $this->email);
        $c1->execute();
        $array = null;
        while ($d = $c1->fetch()) {
            $produit = Produit::creerProduit($d['idProduit']);
            if ($produit != null) {
                $array[] = $produit;
            }
        }
        return $array;
    }
}