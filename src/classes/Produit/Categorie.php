<?php

namespace customBox\classes\Produit;

use customBox\classes\db\ConnectionFactory;

/**
 * Classe Categorie
 */
class Categorie
{

    /**
     * @var array listEpisode ce qui represente une liste d'episode
     * @var string titre, genre, publicVise, descriptif, sortie, dateAjout, img cela represente les caractéristique d'une serie
     * @var int nmbEpisode, id ce qui represente le nombre d episode et l id de la serie
     */
    protected array $listeproduits;
    protected string $nom;
    protected int $id;

    public function __construct(string $nom,int $id, array $listeproduits = [])
    {
        $this->listeproduits = $listeproduits;
        $this->id = $id;
        $this->nom = $nom;
    }



    /**
     * Get magique de la classe
     * @param string $at cela represente represente un nom d attribut de la classe
     * @return mixed cela renvoie n importe quel type
     * @throws \Exception
     */
    public function __get(string $at): mixed
    {
        if (property_exists($this, $at)) {
            return $this->$at;
        } else {
            throw new \Exception("$at: invalid property");
        }
    }

}