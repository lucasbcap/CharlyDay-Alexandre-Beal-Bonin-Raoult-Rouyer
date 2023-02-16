<?php

namespace custumbox\action;

use custumbox\db\ConnectionFactory;
use custumbox\Render\CatalogueRender;
use custumbox\user\User;
use custumbox\Catalogue\Produit;


/**
 * class qui gere la gestion de l affichage du catalogue
 */
class DisplayCatalogueAction extends \custumbox\action\Action
{

    /**
     * Constructeur
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
        $res = "";
        if ($this->http_method == "GET") {

            // il y a 3 cas
            //Soit user cherche quelque chose
            //soit user veut trier
            //soit user veut filtrer
            //sinon le ctalogue est afficher

            if (isset($_GET['search'])) {
                $res = $this->afficherCatalogue($_GET['search']);
            }
            elseif(isset($_GET['trie'])){
                $res=$this->Trie($_GET['trie']);
            }elseif(isset($_GET['genre']) && isset($_GET['type'])) {
                $res = $this->Filtre($_GET['type'],$_GET['genre']);
            }else{
                $res .= $this->afficherCatalogue();
            }

        } else if ($this->http_method == "POST") {
            if(isset($_POST['search'])){
                header('Location: ?action=display-catalogue&search='.$_POST['search']);
            }

            if(isset($_POST['trie'])){
                header('Location: ?action=display-catalogue&trie='.$_POST['trie']);
            }

            if(isset($_POST['filtre1']) && isset($_POST['filtre2'])){
                header('Location: ?action=display-catalogue&type='.$_POST['filtre1'].'&genre='.$_POST['filtre2']);
            }
        }

        return $res;
    }

    /**
     * methode qui affiche les series
     * si un parametre alors il doit afficher les series qui contiennent $search
     * @param string $search
     * @return string
     */
    public function afficherCatalogue(string $search =""): string
    {
        $res = "";
        if ($this->http_method == "GET") {
            $res = "<h1>Catalogue : </h1>";
            $array = User::TrieSQL();

            if ($array!=null) {
                for($id = ($_GET['page']-1)*5; $id<($_GET['page']-1)*5+5;$id++) {
                    if($id<sizeof($array)) {
                        $produitCourantRenderer = new CatalogueRender($array[$id]);
                        $res .= $produitCourantRenderer->render(1);
                    }
                }
            }
            if ($search != "") {
                $res = $this->rechercher($search);
            }
        }
        return $res;
    }

    /**
     * methode qui affiche les series suivant le filtre appliquer
     * @param string $type
     * @param string $genre
     * @return string
     */
    public function Filtre(string $type ="" , string $genre=""): string
    {

        $res = "";
        if ($this->http_method == "GET") {
            $res = "<h2>Catalogue : </h2>";

            // on fait 3 cas
            // si le genre est definie
            // si le type est definie
            // si les 2 sont definie

            if($genre !=="genreF" && $type !=="public viseF")$array = Serie::SerieArgs($genre,$type);
            elseif ($genre !=="genreF") $array = Serie::SerieArgs($genre);
            elseif ($type !=="public viseF")$array = Serie::SerieArgs("",$type);


            if ($array!=null) {
                foreach ($array as $d) {
                    $serieCouranteRenderer = new CatalogueRender($d);
                    $res .= $serieCouranteRenderer->render(1);
                }
            }
            else{
                header('Location: ?action=display-catalogue');
            }
        }
        return $res;
    }

    /**
     * methode qui trie les series
     * @param string $trie
     * @return string
     */
    public function Trie(string $trie =""): string
    {
        // on convertie la selection
        if($trie==="date ajout") $trie = "date_ajout";
        if($trie==="public vise") $trie = "publicvise";
        if($trie==="---") $trie = null;

        $res = "";
        if ($this->http_method == "GET") {
            $res = "<h2>Catalogue : </h2>";
            // si le trie est different de moyenne on appelle la fonction de trie
            if($trie!=="moyenne") {
                $array = User::TrieSQL($trie);
            }
            // sinon on trie nous meme
            else{
                // on regarde les series trier suivant leurs moyenne de notes
                $query = "select idSerie from commentaire group by idSerie ORDER BY avg(note) DESC ";
                $bdd = ConnectionFactory::makeConnection();
                $c1 = $bdd->prepare($query);
                $c1->execute();
                $array = null;
                while ($d = $c1->fetch()) {
                    $serie = Serie::creerSerie($d["idSerie"]);
                    if ($serie!=null) {
                        $array[] = $serie;
                    }
                }

                // On va chercher les series non note et on les ajoutes en dessous
                $query = "select id from serie where id not IN (select idSerie from commentaire); ";
                $c1 = $bdd->prepare($query);
                $c1->execute();
                while ($d = $c1->fetch()) {
                    $serie = Serie::creerSerie($d["id"]);
                    if ($serie!=null) {
                        $array[] = $serie;
                    }
                }
            }
            // si rien est retourne on affiche le catalogue classique
            if ($array!=null) {
                foreach ($array as $d) {
                    $serieCouranteRenderer = new CatalogueRender($d);
                    $res .= $serieCouranteRenderer->render(1);
                }
            }
        }
        return $res;
    }

    /**
     * Methode qui affiche les series suivant la recherche
     * si aucune recherche alors on affiche un texte
     * @param string $search
     * @return string
     */
    public function rechercher(string $search):string{
        $bdd = ConnectionFactory::makeConnection();
        $c1 = $bdd->prepare("SELECT * from serie where titre like :s");
        $search = "%".$search."%";
        $c1->bindParam(":s",$search);
        $c1->execute();
        $rendu = "";
        while ($d = $c1->fetch()) {
            $serie = new Serie($d['titre'], $d['img'], $d['genre'], $d['publicVise'], $d['descriptif'], $d['annee'], $d['date_ajout'], $d['id']);
            $render = new CatalogueRender($serie);
            $rendu .= $render->render();
        }
        if ($rendu == "") {
            $rendu = "<h3>Aucune série n'existe sous ce nom</h3>";
        }
        return $rendu;
    }
}