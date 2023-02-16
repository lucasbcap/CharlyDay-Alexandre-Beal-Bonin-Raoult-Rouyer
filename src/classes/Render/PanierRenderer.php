<?php

namespace custumbox\Render;

use custumbox\db\ConnectionFactory;

class PanierRenderer extends Render
{

    public function render(int $selector): string
    {
        $prixTot = 0;
        $res = <<<END
         <h1>Votre panier</h1>
            <table>
                <tr>
                    <td>Nom du produit : </td>
                    <td>Prix : </td>
                    <td>Quantité : </td>
                    <td>sous total : </td>
                </tr>
        END;
        $bd = ConnectionFactory::makeConnection();
        $user = unserialize($_SESSION['user']);

        $requete = <<<END
                    select * from user 
                    inner join panier on user.login = panier.login
                    inner join produit on panier.idProduit = produit.id
                    where user.login = ?;
                    END;
        $requete = $bd->prepare($requete);
        $requete->bindParam(1, $user->login);

        $requete->execute();
        while ($data = $requete->fetch()) {
            $res .= <<<END
                    <tr>
                        <td><img src="{$data['img']}" width="100" height="100">  {$data['nomProd']}</td>
                        <td>{$data['prix']}</td>
                    END;
            if ($data['poids'] != 0){
                $poid = $data['qte'] * $data['poids'];
                $res.= "<td>$poid grammes</td>";
            } else {
                $res.= "<td>{$data['qte']}</td>";
            }
            $sousTot = $data['qte'] * $data['prix'];
            $prixTot += $sousTot;
            $res.= "<td>$sousTot</td></tr>";

        }
        $res .= "</table><br><h1>Prix total : $prixTot</h1>";

        return $res;
    }
}