<?php

namespace custumbox\action;

use custumbox\action\Action;
use custumbox\Catalogue\Produit;
use  custumbox\db\ConnectionFactory;
use  custumbox\Render\ProduitRender;


/**
 * class qui gere la gestion des episodes donc sont affichage mais aussi les commentaires
 */

class DisplayProduitAction extends Action
{

    /**
     * Constructeur classique
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * la methode appeller par le dispatcher
     * @return string se qu il faut afficher
     */
    public function execute(): string
    {
        // on regarde si un commentaire est poster ou pas si oui on l enregistre
        if ($this->http_method == 'POST') {
            $this->enregistrerCom();
        }
        // dans tout les cas on affiche l episode
        $res = $this->affiche();
        return $res;

    }

    /**
     * Permet d afficher un episode et la zone de commentaire en dessous
     * @return string
     */
    public function affiche() : string{
        $bdd = ConnectionFactory::makeConnection();
        $res="";

        if(isset($_GET["id"])){
            // on cherche l episode suivant l id / voir fonction dans episode
            $produit = Produit::creerProduit($_GET["id"]);

            // on affiche l episode puis la zone de saisie des commentaires
            $produitRender = new ProduitRender($produit);
            $res .= $produitRender->render(2);
            $res = $this->afficheCom($res);
        }

        return $res;
    }


    /**
     * fonction qui permet d afficher la zone ou taper son commentaire
     * @param string $res l affichage d avant
     * @return string la zone de commentaire
     */
    public function afficheCom(string $res):string{
        $res .= "
            <form id='formPro' action='?action=display-article&id=".$_GET["id"]."' method='POST'>
            <label><b>Note</b></label>
            <select name='note'>
        <option value='tiret'>-</option>
        <option value='1'>1</option>
        <option value='2'>2</option>
        <option value='3'>3</option>
        <option value='4'>4</option>
        <option value='5'>5</option>
         </select>
          <br>
          <br>

            <label><b>Commentaire</b></label>
            <input id='input2' type='text' placeholder='Entrer votre commentaire' name='commentaire' height='100'><br>
    
            <input type='submit' id='log' value='Envoyer'>
            ";
        return $res;
    }

}