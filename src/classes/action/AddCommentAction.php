<?php
declare(strict_types=1);
namespace custumbox\action;
use \PDO;
//Action qui permet d'ajouter un commentaire
class AddCommentAction extends Action
{

    public function execute(): string
    {
        ConnectionFactory::makeConnection();

        $res = "";
        if ($this->http_method == 'POST') {
            if (isset($_POST['commentaire']) and isset($_POST['note'])) {
                $commentaire = filter_var($_POST['commentaire'], FILTER_SANITIZE_STRING);
                $note = $_POST['note'];
                $id = $_GET['id'];
                //On verifie que l'utilisateur est connecté
                if (isset($_SESSION['user'])) {
                    $user = unserialize($_SESSION['user']);
                    //On verifie que l'utilisateur est verifié
                    if ($user->role != USER::NO_USER) {
                        $email = $user->email;
                        //On regarde si l'utilisateur a deja commenté la serie
                        $query = "select COUNT(*) from commentaire where email= ? and idProduit = ?";
                        $st = ConnectionFactory::$db->prepare($query);
                        $st -> bindParam(1,$email);
                        $st -> bindParam(2,$id);
                        $st->execute();
                        $result = $st->fetch(PDO::FETCH_ASSOC);
                        //On verifie si l'utilisateur n'a pas deja commenté la serie
                        if ($result['COUNT(*)'] == 0) {
                            //On ajoute le commentaire
                            $query2 = "insert into commentaire (comm ,note , email, idProduit) values (?, ?, ?,?)";
                            $st2 = ConnectionFactory::$db->prepare($query2);
                            $st2 -> bindParam(1,$commentaire);
                            $st2 -> bindParam(2,$note);
                            $st2 -> bindParam(3,$email);
                            $st2 -> bindParam(4,$id);
                            $st2->execute();
                            $res = "Commentaire ajouté<br>";
                        } else {
                            $res = "Vous avez deja commenté <br>";
                        }
                        $_SESSION['user'] = serialize($user);
                    } else {
                        $res .= 'Il faut se connecter avant d ajouter un commentaire';
                    }
                }
            }
        }
        return $res;
    }
}