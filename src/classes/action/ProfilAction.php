<?php

namespace custumbox\action;
use custumbox\action\Action;
use custumbox\db\ConnectionFactory;
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
            $res .= $this->enregistrerProfil();
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
        $c1->bindParam(":login", $user->login);
        $c1->execute();
        $d = $c1->fetch();
        //$userN = User::getUser($user->login);

        $res = "<h2>Profil : </h2> ";
        $res .= "<form id='formPro' action='?action=profil' method='POST' >
                 <label><b>Nom : </b></label>
                 <input value='".$d['nom']."' id='input' type='text' placeholder='Entrer votre nom' name='nom'>
                 <label><b>Prénom :</b></label>       
                 <input value='".$d['prenom']."' id= 'input' type='text' placeholder='Entrer votre prénom' name='prenom'><br>
              <center>   <label><b>Téléphone :</b></label>       
                 <input value='".$d['tel']."' id= 'input' type='text' placeholder='Entrer votre numéro de télélphone' name='telephone'><br></center>
                
                   
                 <input type='submit' value='Sauvegarder'>";


        $res .= "</form>";

        return $res;
    }

    /**
     * Methode enregisrtrerProfil qui permet d'enregistrer les informations que l utilisateur a mis sur le formulaire
     * @return string retourne les informations souhaitées
     */
    function enregistrerProfil() : string{
        $res ="";
        if(isset($_POST['nom']) && isset($_POST['prenom'])){
            $bdd = ConnectionFactory::makeConnection();
            $nom = filter_var($_POST['nom']);
            $prenom = filter_var($_POST['prenom']);
            $telephone = filter_var($_POST['telephone']);
            $loginUser = unserialize($_SESSION['user'])->login;
            $c2 = $bdd->prepare("update user set nom= ? , prenom=?,telephone = ? where email = ?");
            $c2->bindParam(1,$nom);
            $c2->bindParam(2,$prenom);
            $c2->bindParam(3,$telephone);
            $c2->bindParam(4,$loginUser);
            $c2->execute();
        }
        return $res;
    }
}