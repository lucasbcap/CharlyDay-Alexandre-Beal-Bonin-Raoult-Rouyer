<?php

namespace custumbox\Render;

use custumbox\db\ConnectionFactory;

class MembreRenderer extends Render
{
    public function render(int $selector): string
    {
        $bd = ConnectionFactory::makeConnection();
        $res = "";

        if($selector == 0){
            $requete = <<<END
                    select * from user 
                    where privilege = 0;
                    END;
            $requete = $bd->prepare($requete);
            $requete->execute();
            while ($data = $requete->fetch()){
                $login = $data['login'];
                $res .= <<<END
                        {$data['login']} : 
                        <form id='ajout' action='?action=userAvancer&idlogin=$login' method='POST'>
                            
                            <div id='btn-modif'><div><input type='submit' value='modification'><br></div></div>
                        </form>
                        END;

            }
        } else {
            $requete = <<<END
                    select * from user 
                    where login = ?;
                    END;
            $requete = $bd->prepare($requete);
            $requete->bindParam(1, $_GET['idlogin']);
            $requete->execute();
            while ($data = $requete->fetch()){
                if($data['nomUser'] != null){
                    $nom = $data['nomUser'];
                } else {
                    $nom = 'pas encore initialisé';
                }
                if($data['prenomUser'] != null){
                    $prenom = $data['prenomUser'];
                } else {
                    $prenom = 'pas encore initialisé';
                }
                if($data['tel'] != null){
                    $tel = $data['tel'];
                } else {
                    $tel = 'pas encore initialisé';
                }
                $login = $data['login'];
                $res .= <<<END
                        <p>login : {$data['login']}, <br>nom : {$nom}, <br>prenom : {$prenom}<br>
                        email : {$data['email']}, telephone : {$tel} </p>
                        <form id='ajout' action='?action=droit&idlogin=$login' method='POST'>
                             
                            <div id='btn-modif'><div><input type='submit' value='donner les droits'><br></div></div>
                        </form>
                        END;

            }
        }
        return $res;


    }
}