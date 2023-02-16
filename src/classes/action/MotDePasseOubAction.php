<?php

namespace custumbox\action;
use custumbox\action\Action;
use custumbox\auth\Auth;
class MotDePasseOubAction extends Action
{
    /**
     * Execute une methode selon si la methode http si c'est un GET ou un POST
     * @return string
     */
    public function execute(): string
    {
        $res = "";
        if ($this->http_method == 'GET') {
            //Si le token est exacte on envoie le formulaire du mot de passe, sinon on demande le mail
            if (isset($_GET['token']) && Auth::activate($_GET['token'])) {
                $res = $this->NewMdp();
            } else {
                $res = $this->Email();
            }
        } else if ($this->http_method == 'POST')
            //On change le mot de passe si le token est exacte sinon on lui envoie le lien
            if (isset($_SESSION['mail'])) {
                if (isset($_GET['token']) && Auth::activate($_GET['token'])) {
                    $res = $this->changerMdp();
                }
            } else {
                $res = $this->envoieToken($_POST['mail']);
                $_SESSION['mail'] = $_POST['mail'];
            }
        return $res;
    }

    /**
     * Formulaire de mot de passe oublie
     */
    function Email(): string
    {
        //On detruit au cas ou les sessions en cours si il fait plusieurs demande de mot de passe oublie
        session_destroy();
        $res = "
        <form id='sign' action='?action=mdpoub' method='POST'>
        <h1>Mot de passe oublie</h1>
        <label><b>Email</b></label>
        <input type='email' placeholder='Entrer votre mail' name='mail' required><br>
        <input type='submit' id='log' value='Envoyer un mail'>
        ";

        //on ajoute une ligne s'il y a des erreurs
        if (isset($_GET['error'])) {
            $res .= "<p style='color:red'>Cette email n'existe pas</p><br>";
        }
        if (isset($_GET['token'])) {
            $res .= "<p style='color:red'>Mauvais Token</p><br>";
        }

        $res .= "</form>";
        return $res;
    }

    /**
     * Envoie le lien avec le token
     * @param string $email email de l'utilisateur du token que l'on veut generer
     * @return string lien de token
     */
    public function envoieToken(string $email): string
    {
        $res = Auth::generateToken($email);
        //Si aucun token n'a ete creer, on renvoie vers le formulair et une erreur
        if ($res == "") {
            header("Location: ?action=mdpoub&error=1");
        } else {
            $res = "<a href='?action=mdpoub&token=$res'>Lien de reset mot de passe</a>";
        }
        return $res;
    }

    /**
     * Change le mot de passe
     * @return string validation du mot de passe sinon redirection vers le formulaire du nouveau mot de passe
     */
    function changerMdp():string{
        //On verifie le mot de passe
        if ($_POST['mdp'] == $_POST['verifmdp']) {
            $res = Auth::changerMDP($_SESSION['mail'], $_POST['mdp']);
            //s'il est vide c'est que le mot de passe n'a pas passé le check
            if ($res == "") {
                header("location: ?action=mdpoub&token=" . $_GET['token'] . "&error=2");
            }
        }else{
            header("location: ?action=mdpoub&token=".$_GET['token']."&error=3");
        }
        return $res;
    }

    /**
     * Formulaire du nouveau mot de passe
     * @return string formulaire
     */
    function NewMdp(): string
    {

        //formulaire
        $res = "
    <form id='sign' action='?action=mdpoub&token=".$_GET['token']."' method='POST'>
        <h1>Changement</h1>

        <label><b>Nouveau mot de passe :</b></label>
        <input type='password' placeholder='Mot de passe' name='mdp' required><br>
        <input type='password' placeholder='Entre a nouveau votre mot de passe' name='verifmdp' required><br>
        <input type='submit' id='log' value='Changer'>
        ";
        //on ajoute une ligne s'il y a des erreurs
        if (isset($_GET['error'])) {
            switch ($_GET['error']) {
                case 2:
                    $res .= "<p style='color:red'>Votre mot de passe doit faire au moins 5 caractères avec un nombre, une minuscule et une majuscule</p><br>";
                    break;

                case 3:
                    $res .= "<p style='color:red'>Votre mot de passe est different entre les 2 champs</p><br>";
                    break;
            }
        }
        $res .= "</form>";
        return $res;

    }
}