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
        $res = "<h2>Profil : </h2> ";
        $res .= "<form id='formPro' action='?action=profil' method='POST' >
                 <label><b>Nom :</b></label>
                 <input id='input' type='text' placeholder='Entrer votre nom' name='nom'>
                 <label><b>Prénom :</b></label>       
                 <input id= 'input' type='text' placeholder='Entrer votre prénom' name='prenom'><br>
                
                   
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
            $genrepref = filter_var($_POST['genrepref']);
            $emailUser = unserialize($_SESSION['user'])->email;
            $c2 = $bdd->prepare("update user set nom= ? , prenom=? where email = ?");
            $c2->bindParam(1,$nom);
            $c2->bindParam(2,$prenom);
            $c2->bindParam(3,$genrepref);
            $c2->bindParam(4,$emailUser);
            $c2->execute();
        }
        return $res;
    }
}