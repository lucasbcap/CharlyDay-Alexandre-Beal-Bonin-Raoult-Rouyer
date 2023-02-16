<?php

namespace custumbox\action;

use custumbox\auth\Auth;
use custumbox\action\Action;

class SigninAction extends Action
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
        if($this->http_method == 'GET') $res = $this->register();
        else if ($this->http_method == 'POST') $res = $this->signin();
        return $res;
    }


    /**
     * Methode register qui permet de creer un formulaire et donc de se connecter sur le site
     * @return string retourne une chaine avec les informations permettant d afficher le formulaire sur la page html
     */
    function register(): string
    {
        //affichage du formulaire de connexion
        $res = "
    <form id='sign' action='?action=sign-in' method='POST'>
        <h1>Connexion</h1>

        <label><b>Login</b></label>
        <input type='text' placeholder='Entrer votre login' name='login' required><br>

        <label><b>Mot de passe</b></label>
        <input type='password' placeholder='Entrer le mot de passe' name='password' required><br>

        <a href='?action=mdpoub' id='mdpoublier'>Mot de passe oublié</a>
        <input type='submit' id='log' value='LOGIN'>
        ";

        //verifie que si il rentre de mauvaises informations il renvoie une erreur
        if (isset($_GET['error'])) {
            $res .= "<p style='color:red'>Utilisateur ou mot de passe incorrect</p><br>";
        }

        $res .= "</form>";
        return $res;

    }

    /**
     * Methode signin qui permet si le mot de passe est valide et le mail aussi de le mettre dans la page d accueil sinon une erreur
     * @return string retourne les informations souhaitées
     */
    function signin(): string
    {
        $res = "";
        if (isset($_POST['login']) && isset($_POST['password'])) {
            Auth::authenticate();
            if (unserialize($_SESSION['user']) == null) {
                header("Location: ?action=sign-in&error=1");
            } else {
                header('location: ./');
            }
        }
        return $res;
    }

}