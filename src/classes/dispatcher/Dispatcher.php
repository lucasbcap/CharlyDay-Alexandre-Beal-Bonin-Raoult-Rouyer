<?php


namespace custumbox\dispatcher;
use custumbox\action\AddCommentAction;
use custumbox\action\AdminAction;
use custumbox\action\DisplayCatalogueAction;
use custumbox\action\AddUserAction;
use custumbox\action\DisplayMembreAction;
use custumbox\action\DisplayPanierAction;
use custumbox\action\DisplayPrincipaleAction;
use custumbox\action\PanierAction;
use custumbox\action\SigninAction;
use custumbox\action\DeconnexionAction;
use custumbox\action\MotDePasseOubAction;
use custumbox\action\ProfilAction;
use custumbox\action\PrefereAction;
use custumbox\action\AjoutAction;
use custumbox\db\ConnectionFactory;
use custumbox\action\DisplayProduitAction;

class Dispatcher
{
    protected ?string $action = null;

    /**
     * @param string|null $action
     */
    public function __construct()
    {
        $this->action = $_GET['action'] ?? null;
    }

    /**
     * fonction principale qui est appele dans l'index
     * @return void
     */
    public function run(): void
    {
        $act = explode("&",$this->action)[0];
        if (!isset($_SESSION['user']) && $act != 'sign-in' && $act != 'add-user' && $act != 'mdpoub') {    //si l'utilisateur n'est pas connecté et si il n'esssaye pas de s'inscrire ou de se connecter
            $html = "<div class='background'>
                        <img src='image/backgroundPageAccueil.png' alt='Background Image'>
                     </div>
                     <div class='content'>
                        <p id='wel'> Bienvenue sur le site <br><br> Court-Circuit Nancy<br>le local à vivre(s) !<br>
                     </div>";
        } else {

            switch ($act) {
                case 'add-user':                // inscription
                    $act = new AddUserAction();
                   $html = $act->execute();
                    break;
                case 'sign-in':                 //connexion
                    $act = new SigninAction();
                    $html = $act->execute();
                    break;
                case 'display-catalogue':       // affichage catalogue
                   $act = new DisplayCatalogueAction();
                   $html = $act->execute();
                    break;
                case "add-comment" :
                    $act = new AddCommentAction();
                    $html = $act->execute();
                    break;
                case 'allUser':           // affichage series
                  $act = new DisplayMembreAction(0);
                  $html = $act->execute();
                  break;
                case 'display-episode':         // affichage episodes
                  //  $act = new DisplayEpisodeAction();
                  //  $html = $act->execute();
                case 'display-article':         // affichage episodes
                  $act = new DisplayProduitAction();
                  $html = $act->execute();
                    break;
                case 'profil':                  // gestion du profil
                    $act = new ProfilAction();
                    $html = $act->execute();
                    break;
                case 'prefere':                 // gestion de la liste des preferes
                 $act = new PrefereAction();
                 $html = $act->execute();
                    break;
                case 'ajout':                 // gestion de la liste des preferes
                    $act = new AjoutAction();
                    $html = $act->execute();
                    break;
                case 'panier':     // affichage du panier
                    $act = new DisplayPanierAction();
                    $html = $act->execute();
                    break;
                case 'mdpoub':                  // gestion mot de passe oublie lors de l'inscription
                    $act = new MotDePasseOubAction();
                    $html = $act->execute();
                    break;
                case 'deconnexion':             // gestion de la deconnexion
                    $act = new DeconnexionAction();
                    $html = $act->execute();
                    break;
                case 'userAvancer':
                    $act = new DisplayMembreAction(1);
                    $html = $act->execute();
                    break;
                case 'droit':
                    echo 'ne marche pas';
                    //$act = new AdminAction();
                    //$html = $act->execute();
                    //break;
                default:                        // accueil
                    $act = new DisplayPrincipaleAction();
                    $html = $act->execute();
                    break;
            }
       }
        print($this->renderPage($html));
    }

    private function renderPage(string $res): string
    {
        if (isset($_SESSION['user'])) {         // si l'utilisateur est connecte
            $search = "";
            $this->action = explode("&",$this->action)[0];
            if ($this->action == 'display-catalogue') {         //si affichage du catalogue
                $search = "<div id='catalogue'><form method='post' action='?action=display-catalogue&page=1'><li id='searchbar'><input size='30%' type ='search' 
                            name='search' placeholder='Rechercher une série'></li></form>";     // barre de recherche

                $search .= "<form method='post' action='?action=display-catalogue&page=1'><li id='trie'>       
                            <select name='trie'>                                                   
                            <option value='---'>---</option>
                            <option value='titre'>categorie</option>
                            <option value='genre'>prix</option>
                            </select>
                           
                            <input type='submit' value='Trier'>Trier</input>
                            </li></form>";                                      // Choix du tri a effectuer dans l'affichage du catalogue

                // choix du type de public

                $search .= "<form method='post' action='?action=display-catalogue&page=1'><li id='filtre'>

                            <select name='filtre1'>
                            <option value='public viseF'>Type de publique</option>
                            <option value='adulte'>Adulte</option>
                            <option value='famille'>Famille</option>
                            <option value='adolescent'>Adolescent</option>
                            </select>                                       
                            
                            <select name='filtre2'>
                            <option value='genreF'>Genre</option>
                            <option value='horreur'>Horreur</option>
                            <option value='action'>Action</option>
                            <option value='aventure'>Aventure</option>
                            <option value='sport'>Sport</option>
                            <option value='nostalgie'>Nostalgie</option>
                            </select>
                            <input type='submit' name='bnt1'>Filtré</input>
                            </li>
                            </form></div>";                                 // choix du genre de series


            }
            // affichage de l'accueil
            // peut afficher le catalogue
            // modifier son profil
            // se deconnecter
            $rese = "<!DOCTYPE html>                     
                    <html lang='fr'>    
                    <head>
                        <title>Court Circuit</title>
                        <meta charset='UTF-8' />
                        <link rel='stylesheet' href='css/style.css'>
                        <link rel='icon' type='image/png' sizes='16x16' href=''>
                    </head>
                    <header>
                    <ul>
                        <div id='logodiv'><li><a href='./' id='logo'><img src='image/court-circuit-logo-allonge-jaune-vert.jpg' id='logo'></a></li></div>                 
                        <li><a href='?action=' id='navbar'>Accueil</a></li> 
                        <li><a href='?action=display-catalogue&page=1' id='navbar'>Afficher Catalogue</a></li>             
                        <li><a href='?action=profil' id='navbar'>Profil </a></li>                                   
                        
                        <li><a href='?action=panier' id='navbar'>Panier</a></li>                            
                        ";
            $bd = ConnectionFactory::makeConnection();


            $requete = <<<END
                    select * from user 
                    where user.login = ?;
                    END;
            $requete = $bd->prepare($requete);
            $user = unserialize($_SESSION['user']);
            $log = $user->login;
            $requete->bindParam(1, $log);
            $requete->execute();
            $data = $requete->fetch();
            if ($data['privilege'] == 1){
                $rese.="<li><a href='?action=allUser' id='navbar'>Liste des membres</a></li>";
            }
            $rese .= "$search      
                        <li><a href='?action=deconnexion' id='navbar'>Déconnexion</a></li>
                    </ul>
                    </header>
                        <body>
                        $res
                        </body>
                    </html>";
            return $rese;
        } else {                    // si l'user n'est pas connecte

            // peut se connecter
            // ou s'inscrire
            return "<!DOCTYPE html>
                    <html lang='fr'>
                    <head>
                        <title>CourtCircuit</title>
                        <meta charset='UTF-8' />
                        <link rel='stylesheet' href='css/style.css'>
                        <link rel='icon' type='image/png' sizes='16x16' href='Image/logoWeb.png'>
                        <script src='PageAccueil.js' defer></script>
                    </head>
                    <header>
                    <ul>
                        <li><a href='./' id='logo'><img src='image/court-circuit-logo-allonge-jaune-vert.jpg' id='logo'></a></li>     
                        <li><a href='?action=sign-in' id='navbar'>Connexion</a></li>                
                        <li><a href='?action=add-user' id='navbar'>Inscription</a></li>              
                    </ul>
                    </header>
                    <body>
                    $res
                    </body>
                    </html>";
        }
    }
}