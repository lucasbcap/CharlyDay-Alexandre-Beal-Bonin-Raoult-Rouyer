<?php

namespace custumbox\user;

use custumbox\Catalogue\Produit;
use custumbox\db\ConnectionFactory as ConnectionFactory;

use custumbox\Catalogue\Categorie;

/**
 * Classe User
 */
class User
{
    protected string $login, $nom, $prenom, $tel, $email, $passwrd, $token;

    /**
     * Constructeur
     * @param string $email email de l'user
     * @param string $passwrd mot de passe de l'user
     * @param string $token token associe à l'user
     */
    public function __construct(string $nom, string $prenom, string $telephone, string $login, string $email, string $passwrd, string $token)
    {
        $this->nom=$nom;
        $this->prenom = $prenom;
        $this->tel=$telephone;
        $this->login=$login;
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
        $c1 = $bdd->prepare("select idProduit from $table where login = :login");
        $c1->bindParam(":login", $this->login);
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

    /**
     * Supprime une ligne dans une table de notre base de donnee grace à son ID (ne fonctionne que pour estfini/encours/favori)
     * @param int $id id de la ligne que l'on veut supprimer
     * @param string $table table que l'on veut modifie
     * @return void
     */
    function suppSQL(int $id, string $table)
    {
        $bdd = ConnectionFactory::makeConnection();
        $c1 = $bdd->prepare("DELETE FROM $table WHERE login=:login AND idProduit=:id;");
        $c1->bindParam(":login", $this->login);
        $c1->bindParam(":id", $id);
        $c1->execute();
    }

    /**
     * Inserer dans une table un nouvel element grace a son id (ne fonctionne que pour estfini/encours/favori)
     * @param int $id id d'une serie que l'on veut inserer
     * @param string $table table ou l'on veut ajoute
     * @return void
     */
    function addSQL(int $id, string $table): void
    {
        $bdd = ConnectionFactory::makeConnection();

        $query = "Select * from $table where login=:login and idProduit=:id";
        $insert = "insert into $table values (:login,:id)";

        $c = $bdd->prepare($query);
        $c->bindParam(":login", $this->login);
        $c->bindParam(":id", $id);

        $c->execute();


        $verif = true;
        //On verifie que la donné que l'on veut inserer n'existe pas deja
        while ($d = $c->fetch()) {
            $verif = false;
        }

        //Si elle n'existe pas on l'insert
        if ($verif) {
            $c1 = $bdd->prepare($insert);
            $c1->bindParam(":login", $this->login);
            $c1->bindParam(":id", $id);

            $c1->execute();
        }
    }

    static function getUser(string $login): User
    {
        $bdd = ConnectionFactory::makeConnection();
        $c1 = $bdd->prepare("select * from user where login = :login");
        $c1->bindParam(":login", $login);
        $c1->execute();
        $user = null;
        while ($d = $c1->fetch()) {
            $user=new User($d['nom'],$d['prenom'],$d['telephone'],$d['login'],$d['email'],$d['passwrd'],$d['token']);
        }
        return $user;
    }

    public function __get(string $at):mixed {
        if (property_exists($this, $at)) {
            return $this->$at;
        }else {
            throw new \Exception("$at: invalid property");
        }
    }

    public function addPanier(int $idProduite, int $qte){
        $db = ConnectionFactory::makeConnection();
        $query = $db->prepare("INSERT INTO favorite VALUES(?, ?, ?)");
        $query->bindParam(1, $this->login);
        $query->bindParam(2, $idProduite);
        $query->bindParam(3, $qte);
        return $query->execute();
    }

    public function mettreAdmin(){
        $db = ConnectionFactory::makeConnection();
        $query = $db->prepare("update user set privilige = 1 where login = ?");
        $query->bindParam(1, $this->login);
        return $query->execute();
    }
}