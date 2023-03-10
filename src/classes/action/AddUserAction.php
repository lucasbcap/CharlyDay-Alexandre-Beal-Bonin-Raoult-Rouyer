<?php

namespace custumbox\action;

use custumbox\auth\Auth;
use custumbox\db\ConnectionFactory as ConnectionFactory;

class AddUserAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $res = "";
        if ($this->http_method == 'GET') {
            if (isset($_GET['valide'])) {
                $res = $this->confirmerInscrit();
            } else if (isset($_GET['token']) && Auth::activate($_GET['token'])) {
                $res = $this->inscrit();
            } else {
                $res = $this->inscription();

            }
        }else
            if ($this->http_method == 'POST') {
                switch ($this->verifInscription()) {
                    case "LoginExist":
                        header("Location: ?action=add-user&error=1");
                        break;

                    case "MdpWrong":
                        header("Location: ?action=add-user&error=2");
                        break;
                    case "NotSameMdp":
                        header("Location: ?action=add-user&error=3");
                        break;
                    case "Log":
                        header("Location: ?action=add-user&valide=1");;
                        break;
                    case "EmailExist":
                        header("Location: ?action=add-user&error=4");;
                        break;
                }
            }
        return $res;
    }

    /**
     * Ajoute le compte a la base de donnee
     * @return string balise qui valide l'inscription
     */
    function inscrit(): string
    {
        $email = filter_var($_SESSION['email'], FILTER_SANITIZE_EMAIL);
        $pass = $_SESSION['pass'];
        $login = $_SESSION['login'];
        Auth::register($login, $pass, $email);

        $_SESSION['token'] = null;
        session_destroy();
        Auth::generateToken($login);
        return "<h2> Vous êtes Inscrit ! </h2>";
    }

    /**
     * Verifie si le compte cree a un mot de passe correct, s'il n'a deja pas de compte, et renvoie s'il n'y a aucun probleme
     * renvoie log
     * @return string resultat de ce que l'utilisateur a entree
     */
    function verifInscription(): string
    {
        $r = "Log";
        //On garde en session ce que l'utilisateur a ecrit pour plus tard
        $_SESSION['telephone'] = $_POST['telephone'];
        $_SESSION['nom'] = $_POST['nom'];
        $_SESSION['prenom'] = $_POST['prenom'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['login'] = $_POST['login'];
        $_SESSION['pass'] = $_POST['pass'];
        $_SESSION['pass2'] = $_POST['pass2'];
        //On verifie que toutes les donnees entre sont bonnes
        if ($_SESSION['pass'] == $_SESSION['pass2']) {
            if (Auth::checkPasswordStrength($_SESSION['pass'], 4)) {
                $bdd = ConnectionFactory::makeConnection();
                $c1 = $bdd->prepare("Select * from user where login=:login");
                $c1->bindParam(":login", $_SESSION['login']);
                $c1->execute();
                while ($d = $c1->fetch()) {
                    return "LoginExist";
                }
                $c2 = $bdd->prepare("Select * from user where email=:email");
                $c2->bindParam(":email", $_SESSION['email']);
                $c2->execute();
                $verif = true;
                while ($f = $c2->fetch()) {
                    $verif = false;
                }

                if ($verif) {

                } else {
                    $r = "EmailExist";
                }
            } else {
                $r = "MdpWrong";
            }
        } else {
            $r = "NotSameMdp";
        }
        return $r;
    }

    /**
     * Renvoie un lien pour que l'utilisateur creer son compte
     * @return string lien de validation
     */
    function confirmerInscrit(): string
    {
        $res = Auth::generateToken("new");
        return "<a href='?action=add-user&token=" . $res . "'>Confirmer compte</a>";
    }


    /**
     * Formulaire d'inscription
     * @return string le formulaire
     */
    function inscription(): string
    {
        //Formulaire
        $res = "
<form id='sign' method='post' action='?action=add-user'>
<h1>Inscription</h1>
                    <label><b>Login</b><input type='text' name='login' placeholder='Login' required></label>
                    <label><b>Mot de passe</b> <input type='password' name='pass' placeholder='Mot de passe'required></label>
                    <label><b>Entrer à nouveau votre mot de passe</b> <input type='password' name='pass2' placeholder='Entrer à nouveau votre mot de passe'required></label>
                    <label><b>Email</b><input type='email' name='email' placeholder='Email'required> </label>
                    <label><b>Téléphone</b><input type='text' name='telephone' placeholder='Téléphone (facultatif)'> </label>
                    <label><b>Nom</b><input type='text' name='nom' placeholder='Nom (facultatif)'> </label>
                    <label><b>Prénom</b><input type='text' name='prenom' placeholder='Prénom (facultatif)'> </label>

                    <input type='submit' id='log' value='INSCRIPTION'>";

        //S'il y a des erreurs on ajoutera une ligne supplementaire selon la nature de l'erreur renvoye
        if (isset($_GET['error'])) {
            switch ($_GET['error']) {
                case 1:
                    $res .= "<p style='color:red'>Vous avez déjà un compte avec ce login</p><br>";
                    break;

                case 2:
                    $res .= "<p style='color:red'>Votre mot de passe doit faire au moins 5 caractères avec un nombre, une minuscule et une majuscule</p><br>";
                    break;

                case 3:
                    $res .= "<p style='color:red'>Votre mot de passe est different entre les 2 champs</p><br>";
                    break;
                case 4:
                    $res .= "<p style='color:red'>Vous avez déjà un compte avec ce mail</p><br>";
                    break;
            }
        }
        $res .= "</form>";
        return $res;
    }

}