<?php

namespace custumbox\render;

use custumbox\Catalogue\Produit;
use custumbox\db\ConnectionFactory;
use custumbox\Render\Render;
use custumbox\Render\ProduitRender;


/**
 * Render du catalogue
 */
class CatalogueRender extends Render
{
    protected Produit $produit;


    public function __construct(Produit $produit)
    {
        $this->produit = $produit;
    }


    /**
     * Render de la série
     * @param int $selector selecteur de quel type d'affichage nous voulons, 1 pour le catalogue, 2 pour en cours et 3 pour les favories et fini
     * @return string affichage de la serie
     */
    public function render(int $selector = 1): string
    {
        $bdd = ConnectionFactory::makeConnection();

        $res = "";
        $id = $this->produit->id;

        //Affichage de la série dans le catalogue principale
        if($selector===1) {

            //Calcul de la moyenne
            $c2 = $bdd->prepare("select AVG(note) as moyenne from commentaire where  idProduit=?");
            $c2->bindParam(1, $id);
            $c2 ->execute();
            $moyenne = $c2->fetch()['moyenne'];
            if($moyenne === null) $moyenne = "Non notée";
            else $moyenne =round($moyenne,2) . " <img id ='stars' src='img/stars.png'>/ 5";

            //Affichage
            $produitRender = new ProduitRender($this->produit);
            $res = $produitRender->render(1);

            //En favori ou non

            $array = unserialize($_SESSION['user'])->getSQL("favori");
            $trouve = false;
            if($array!=null) {
                foreach ($array as $serie) {
                    if ($this->serie->id === $serie->id) $trouve = true;
                }
            }
             if($trouve){
                 $res .= "<center><a href='?action=prefere&fav=oui&id=" . $this->produit->id . "'><img src='img/coeurplein.png' width='70' height='70'></a></center>";

             }else {
                 $res .= "<center><a href='?action=prefere&fav=non&id=" . $this->produit->id . "'><img src='img/coeurvide.png' width='70' height='70'></a></center>";
             }
        }

        //Affichage de la série dans en cours
        if($selector===2){

            $query = "select max(idEpisode) as epCourant from encours where idSerie=:numeroSerie";
            $c = $bdd->prepare($query);
            $id = $this->serie->id;
            $c->bindParam(":numeroSerie", $id);
            $c->execute();
            $numEp = $c->fetch()['epCourant'];

            $IdEp = Episode::chercherEpisodeNumero($numEp,$id);

            //Affichage
            $res = "<div class='liste'><a href='?action=display-serie&id=" . $id . "'>";
            $res .= "<h4>" . $this->serie->titre . "</h4>";
            $res .= "<a href='?action=display-episode&id=" . $IdEp . "' id='lien'><div class=zoom>
                    <div class=image>
                    <img src='Image/" . $this->serie->img . "' width='200' height='120'></a>
                    </div>
                    </div>
                    </div>";
        }

        //Affichage de la série dans favories ou fini, la différence est le lien qui ne sera pas le même
        if($selector===3){

            //Affichage
            $res = "<div class='liste'><a href='?action=display-serie&id=" . $id . "'>";
            $res .= "<h4>" . $this->serie->titre . "</h4>";
            $res .= "<a href='?action=display-serie&id=" . $id . "' id='lien'><div class=zoom>
                    <div class=image>
                    <img src='Image/" . $this->serie->img . "' width='200' height='120'></a>
                    </div>
                    </div>
                    </div>";
        }

        //On creer un bouton pour changer de page
        $res .= "<div class='bouton'><a href='?action=display-catalogue&page=" . ($_GET['page']-1) . "'>Page précédente</a></div>";
        $res .= "<div class='bouton'><a href='?action=display-catalogue&page=" . ($_GET['page']+1) . "'>Page suivante</a></div>";

        return $res;
    }
}