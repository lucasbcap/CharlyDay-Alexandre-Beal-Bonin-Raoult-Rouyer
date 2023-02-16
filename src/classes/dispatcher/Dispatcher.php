<?php


namespace custumbox\dispatcher;
use custumbox\action\AddUserAction;
use custumbox\action\DisplayPrincipaleAction;
use custumbox\action\SigninAction;
use custumbox\action\DeconnexionAction;
use custumbox\action\MotDePasseOubAction;
use custumbox\action\ProfilAction;

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
        $act = $this->action;
        if (!isset($_SESSION['user']) && $act != 'sign-in' && $act != 'add-user' && $act != 'mdpoub') {    //si l'utilisateur n'est pas connecté et si il n'esssaye pas de s'inscrire ou de se connecter
            $html = "<div class='grayscale'><div id='st'></dib></div></div><h1 id='wel'>Bienvenue</h1>          ";     // affichage de bienvenue
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
                   // $act = new DisplayCatalogueAction();
                   // $html = $act->execute();
                    break;
                case 'display-serie':           // affichage series
                  //  $act = new DisplaySerieAction();
                  //  $html = $act->execute();
                    break;
                case 'display-episode':         // affichage episodes
                  //  $act = new DisplayEpisodeAction();
                  //  $html = $act->execute();
                    break;
                case 'profil':                  // gestion du profil
                    $act = new ProfilAction();
                    $html = $act->execute();
                    break;
                case 'prefere':                 // gestion de la liste des preferes
                 //   $act = new PrefereAction();
                 //   $html = $act->execute();
                    break;
                case 'display-commentaire':     // affichage des commentaires et gestion
                 //   $act = new DisplayCommentaireAction();
                 //   $html = $act->execute();
                    break;
                case 'mdpoub':                  // gestion mot de passe oublie lors de l'inscription
                    $act = new MotDePasseOubAction();
                    $html = $act->execute();
                    break;
                case 'deconnexion':             // gestion de la deconnexion
                    $act = new DeconnexionAction();
                    $html = $act->execute();
                    break;
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
            if ($this->action == 'display-catalogue') {         //si affichage du catalogue
                $search = "<div id='catalogue'><form method='post' action='?action=display-catalogue'><li id='searchbar'><input size='30%' type ='search' 
                            name='search' placeholder='Rechercher une série'></li></form>";     // barre de recherche

                $search .= "<form method='post' action='?action=display-catalogue'><li id='trie'>       
                            <select name='trie'>                                                   
                            <option value='---'>---</option>
                            <option value='titre'>Titre</option>
                            <option value='genre'>Genre</option>
                            <option value='public vise'>Public Visé</option>
                            <option value='annee'>Année</option>
                            <option value='date ajout'>Date Ajout</option>
                            <option value='moyenne'>Moyenne</option>
                            </select>
                            <button name='bnt1'>Trier</button>
                            </li></form>";                                      // Choix du tri a effectuer dans l'affichage du catalogue

                // choix du type de public

                $search .= "<form method='post' action='?action=display-catalogue'><li id='filtre'>

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
                            <button name='bnt1'>Filtré</button>
                            </li>
                            </form></div>";                                 // choix du genre de series


            }
            // affichage de l'accueil
            // peut afficher le catalogue
            // modifier son profil
            // se deconnecter
            return "<!DOCTYPE html>                     
                    <html lang='fr'>    
                    <head>
                        <title>NetVOD</title>
                        <meta charset='UTF-8' />
                        <link rel='stylesheet' href='css/style.css'>
                        <link rel='icon' type='image/png' sizes='16x16' href='Image/logoWeb.png'>
                    </head>
                    <header>
                    <ul>
                        <div id='logodiv'><li><a href='./' id='logo'><img src='Image/logo.png' id='logo'></a></li></div>                 
                        <li><a href='?action=display-catalogue' id='navbar'>Afficher Catalogue</a></li>             
                        <li><a href='?action=profil' id='navbar'>Profil </a></li>                                   
                        <li><a href='?action=deconnexion' id='navbar'>Déconnexion</a></li>                         
                        $search
                             
                    </ul>
                    </header>
                    <body>
                    $res
                    </body>
                    </html>";
        } else {                    // si l'user n'est pas connecte

            // peut se connecter
            // ou s'inscrire
            return "<!DOCTYPE html>
                    <html lang='fr'>
                    <head>
                        <title>NetVOD</title>
                        <meta charset='UTF-8' />
                        <link rel='stylesheet' href='css/style.css'>
                        <link rel='icon' type='image/png' sizes='16x16' href='Image/logoWeb.png'>
                    </head>
                    <header>
                    <ul>
                        <div id='logodiv'><li><a href='./' id='logo'><img src='Image/logo.png' id='logo'></a></li></div>        
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