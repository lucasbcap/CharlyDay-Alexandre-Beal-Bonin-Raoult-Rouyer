<?php

namespace custumbox\action;
use custumbox\action\Action;
use custumbox\db\ConnectionFactory;

class AjoutAction extends Action
{

    /**
     * methode magique
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Methode execute qui permet d executer les methodes suppSQL et addSQL pour rajouter des series favoris
     * @return string retourne une chaine comportant les informations à mettre sur le site
     */
    public function execute(): string
    {
        $bdd = ConnectionFactory::makeConnection();
        $query = "select * from panier where login=:login and idProduit=:id";
        $c = $bdd->prepare($query);
        $c->bindParam(":id", $_GET['id']);
        $login = unserialize($_SESSION['user'])->login;
        $c->bindParam(":login",$login );
        $c->execute();
        $produit = $c->fetch();


        if($produit['idProduit'] ==""){
            $bdd2 = ConnectionFactory::makeConnection();
            $query2 = "INSERT INTO panier(login,idProduit,qte) VALUES (:login,:id,:qte)";
            $c1 = $bdd2->prepare($query2);
            $login2 = unserialize($_SESSION['user'])->login;
            $c1->bindParam(":id", $_GET['id']);
            $login = unserialize($_SESSION['user'])->login;
            $c1->bindParam(":login",$login );
            $c1->bindParam(":qte",$_POST['qte'] );
            $c1->execute();

        }
        header("Location:?action=display-catalogue&page=1");
        return "";
    }
}