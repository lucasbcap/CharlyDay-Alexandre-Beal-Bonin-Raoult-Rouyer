<?php

namespace custumbox\auth;

use custumbox\db\ConnectionFactory as ConnectionFactory;
use custumbox\user\User;


class Auth
{
    public static function authenticate()
    {
        $username = filter_var($_POST['login']);
        $pass = filter_var($_POST['password']);
        $nom = filter_var($_POST['nom']);
        $prenom = filter_var($_POST['prenom']);
        $tel = filter_var($_POST['telephone']);
        $email = filter_var($_POST['email']);

        $bdd = ConnectionFactory::makeConnection();
        $c1 = $bdd->prepare("Select passwd, token from user where login=:login");
        $c1->bindParam(":login", $username);
        $_SESSION['mdp'] = $pass;
        $c1->execute();
        $mdpbdd = "";
        $token = 1;
        while ($d = $c1->fetch()) {
            $mdpbdd = $d['passwd'];
            $token = $d['token'];
        }
        if (password_verify($pass, $mdpbdd)) {
            if($nom == null){
                echo "ui";
            }else{
                echo "nn";
            }
            $_SESSION['user'] = serialize(new User($nom,$prenom,$tel,$username,$email, $mdpbdd, $token));
        } else {
            $_SESSION['user'] = null;
        }
    }

    /**
     * Ajoute un utilisateur a notre base de donnee
     * @param string $email email de l'utilisateur
     * @param string $pass son mot de passe
     * @return string log pour valide la reussite
     */
    public static function register(string $login, string $pass, string $email): string
    {
        $bdd = ConnectionFactory::makeConnection();
        $token = Auth::generateToken($login);
        if ($token==""){
            $token = $_SESSION['token'];
        }
        if($_SESSION['telephone'] != null){
            $tel = $_SESSION['telephone'];
        }else{
            $tel=null;
        }
        if($_SESSION['nom'] != null){
            $nom = $_SESSION['nom'];
        }else{
            $nom = null;
        }
        if($_SESSION['prenom'] != null){
            $prenom = $_SESSION['prenom'];
        }else{
            $prenom = null;
        }
        $c2 = $bdd->prepare("insert into user values(:login,:pass,:email,:nom,:prenom,:tel,:token, 0)");
        $c2->bindParam(":login", $login,);
        $pass = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);
        $c2->bindParam(":pass", $pass);
        $c2->bindParam(":email",$email);
        $c2->bindParam(":nom",$nom);
        $c2->bindParam(":prenom",$prenom);
        $c2->bindParam(":tel",$tel);

        $c2->bindParam(":token", $token);
        $c2->execute();

        $_POST['login'] = $login;
        $_POST['password'] = $pass;
        Auth::authenticate();
        return "Log";
    }

    /**
     * Verifie si le mot de passe est valide
     * @param string $pass mot de passe
     * @param int $minimumLength taille minimum du mot de passe
     * @return bool mot de passe valide ou non
     */
    public static function checkPasswordStrength(string $pass, int $minimumLength): bool
    {

        $length = (strlen($pass) < $minimumLength); // longueur minimale
        $digit = preg_match("#[\d]#", $pass); // au moins un digit
        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule
        if ($length || !$digit || !$lower || !$upper) return false;
        return true;

    }

    /**
     * Change le mot de passe dans la base de donnee
     * @param string $mailUser email de l'user
     * @param string $newMDP nouveau mot de passe
     * @return string renvoie une chaine vide si le mot de passe n'a pas ete modifie, sinon renvoie une chaine de validation
     */
    public static function changerMDP(string $loginUser, string $newMDP): string
    {
        $bdd = ConnectionFactory::makeConnection();
        $res = "";
        //On verifie la force du mot de passe
        if (self::checkPasswordStrength($newMDP, 4)) {
            $c3 = $bdd->prepare("Update user set passwd =:mdp
                            where login=:login");
            $pass = password_hash($newMDP, PASSWORD_DEFAULT, ['cost' => 12]);
            $c3->bindParam(":login", $loginUser);
            $c3->bindParam(":mdp", $pass);
            $c3->execute();
            session_destroy();
            $res = "<h2>Votre mot de passe a bien été modifié</h2>";
        }
        return $res;
    }

    /**
     * Genere un token aleatoire d'une taille de 50 et l'insere dans la base de donnee à l'email donnee
     * @param string $email email de l'user
     * @return string renvoie le token s'il a bien ete genere
     */
    public static function generateToken(string $login): string
    {
        $token = "";
        $bdd = ConnectionFactory::makeConnection();
        $req1 = $bdd->prepare("select * from user where login=:login");
        $req1->bindParam(":login", $login);
        $req1->execute();
        $verif = false;
        while ($d = $req1->fetch()) {
            $verif = true;
        }
        $res = "";
        //Si l'email existe ou si l'entree est nouveau (pour valide l'inscription)  on genere un token
        if ($verif || $login=="new") {
            $chaine = "a0b1c2d3e4f5g6h7i8j9klmnpqrstuvwxy123456789";
            for ($i = 0; $i < 50; $i++) {
                $token .= $chaine[rand() % strlen($chaine)];
            }
            if ($login == "new"){
                $_SESSION['token'] = $token;
            }
            $res = $token;
            $req2 = $bdd->prepare("update user set token=:token where login=:login");
            $req2->bindParam(":login", $login);
            $req2->bindParam(":token", $token);
            $req2->execute();
        }
        return $res;
    }

    /**
     *  Verifie si le token dans la base de donne et en parametre est le même
     * @param string $token token donnee
     * @return bool vrai si les token sont egaux
     */
    public static function activate(string $token): bool
    {
        $bdd = ConnectionFactory::makeConnection();
        $req1 = $bdd->prepare("select token from user where login=:login");
        $req1->bindParam(":login", $_SESSION['login']);
        $req1->execute();
        $res = false;
        while ($d = $req1->fetch()) {
            if ($d['token'] == $token && $token != "") {
                $res = true;
            }
        }
        if (isset($_SESSION['token']) && $_SESSION['token'] == $token && $token != "") {
            $res = true;
        }
        return $res;
    }
}