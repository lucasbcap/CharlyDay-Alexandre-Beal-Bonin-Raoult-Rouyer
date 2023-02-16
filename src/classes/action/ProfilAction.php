<?php

namespace custumbox\action;
use custumbox\action\Action;
use custumbox\auth\Auth;
use custumbox\db\ConnectionFactory;
use custumbox\user\User;

class ProfilAction extends Action
{
    /**
     * methode magique
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Methode execute qui permet d executer les methodes creees dans cette classe
     * @return string retourne une chaine comportant les informations à mettre dans le html
     */
    public function execute(): string
    {
        $res ="";
        if($this->http_method == 'GET') $res = $this->modifProfil();
        else if ($this->http_method == 'POST') {
            $res = $this->modifProfil();
            $this->enregistrerProfil();
            $res .="<h3 style='color:green'>Profil enregistré</h3>";
        }
        return $res;
    }

    /**
     * Methode register qui permet de creer un formulaire et donc de rentrer les informations du profil sur le site
     * @return string retourne une chaine avec les informations permettant d afficher le formulaire sur la page html
     */
    function modifProfil() : string{
        $user = unserialize($_SESSION['user']);
        $bdd = ConnectionFactory::makeConnection();
        $c1 = $bdd->prepare("select * from user where login = :login");
        $login = $user->login;
        $c1->bindParam(":login",$login );
        $c1->execute();
        $d = $c1->fetch();

        $res = "<h2>Profil : </h2> ";
        $res .= "<form id='formPro' action='?action=profil' method='POST' >
                 <label><b>Nom : </b></label>
                 <input value='".$d['nomUser']."' id='input' type='text' placeholder='Entrer votre nom' name='nom'>
                 <label><b>Prénom :</b></label>       
                 <input value='".$d['prenomUser']."' id= 'input' type='text' placeholder='Entrer votre prénom' name='prenom'><br>
                 <label><b>Téléphone :</b></label>       
                 <input value='".$d['tel']."' id= 'input' type='text' placeholder='Entrer votre numéro de télélphone' name='telephone'>
                 <label><b>Mot de passe :</b></label>       
                 <input id= 'input' type='password' placeholder='Entrer votre nouveau mot de passe' name='mdp'><br>
                
                   
                 <input type='submit' value='Sauvegarder'>";


        $res .= "</form>";

        return $res;
    }

    /**
     * Methode enregisrtrerProfil qui permet d'enregistrer les informations que l utilisateur a mis sur le formulaire
     * @return string retourne les informations souhaitées
     */
    function enregistrerProfil() : string{
        $res ="oui";
        if(isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['telephone'])){
            $user = unserialize($_SESSION['user']);
            $bdd = ConnectionFactory::makeConnection();
            $c1 = $bdd->prepare("select * from user where login = :login");
            $c1->bindParam(":login", $user->login);
            $c1->execute();
            $d = $c1->fetch();
            $nom = filter_var($_POST['nom']);
            if($nom == "")$nom=$d['nom'];
            $prenom = filter_var($_POST['prenom']);
            if($prenom == "") $prenom=$d['prenom'];
            $telephone = filter_var($_POST['telephone']);
            if($telephone == "") $telephone=$d['tel'];
            $mdp = filter_var($_POST['mdp']);
            if($mdp=="") $mdp =$d['mdp'];

                $mdp = password_hash($mdp, PASSWORD_DEFAULT, ['cost' => 12]);

                $loginUser = unserialize($_SESSION['user'])->login;
                $c2 = $bdd->prepare("update user set nomUser= ? , prenomUser=?,tel = ?, passwd = ? where login = ?");
                $c2->bindParam(1, $nom);
                $c2->bindParam(2, $prenom);
                $c2->bindParam(3, $telephone);

                $c2->bindParam(4, $mdp);
                $c2->bindParam(5, $loginUser);
                $c2->execute();

        }
        return $res;
    }
}