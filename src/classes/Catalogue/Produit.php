<?php

namespace custumBox\Catalogue;
use custumbox\db\ConnectionFactory;

class Produit {

    protected int $id;
    protected String $nom;
    protected float $prix;
    protected String $poids;
    protected String $description;
    protected String $detail;
    protected String $lieu;
    protected int $distance;
    protected String $image;
    protected float $latitude;
    protected float $longitude;
    protected int $stock;
    protected int $categorie;

    public function __construct(int $id, String $nom, float $prix, String $poids, String $description, String $detail, String $lieu, int $distance, String $image, float $latitude, float $longitude, int $stock, int $categorie) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prix = $prix;
        $this->poids = $poids;
        $this->description = $description;
        $this->detail = $detail;
        $this->lieu = $lieu;
        $this->distance = $distance;
        $this->image = $image;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->stock = $stock;
        $this->categorie = $categorie;
    }

    public function __get(string $at):mixed {
        if (property_exists($this, $at)) {
            return $this->$at;
        }else {
            throw new \Exception("$at: invalid property");
        }
    }

    public function __set(string $at, mixed $value):void {
        if (property_exists($this, $at)) {
            $this->$at = $value;
        }else {
            throw new \Exception("$at: invalid property");
        }
    }

    public static function creerProduit(int $id){
        $bdd = ConnectionFactory::makeConnection();
        $req1 = $bdd->prepare("Select * from produit where id=:id"); //requete sql afin de récupérer toutes les valeurs d'une serie en fonction de l'id dans la base de données
        $req1->bindParam(":id", $id);
        $req1->execute();
        $d = $req1->fetch();
        //on enregistre les données de la base de données dans la serie
        return new Produit($d['id'],$d['nom'], $d['prix'], $d['poids'], $d['description'], $d['detail'],$d['lieu'], $d['distance'], "img/".$d['id'].".jpg", $d['latitude'], $d['longitude'], 0, $d['categorie']);
    }

}