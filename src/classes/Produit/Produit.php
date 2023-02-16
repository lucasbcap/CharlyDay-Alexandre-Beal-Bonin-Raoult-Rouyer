<?php

namespace customBox\Produit;
class Produit {

    protected int $id;
    protected String $nom;
    protected double $prix;
    protected String $poids;
    protected String $description;
    protected String $detail;
    protected String $lieu;
    protected int $distance;
    protected String $image;
    protected float $latitude;
    protected float $longitude;
    protected int $stock;
    protected Categorie $categorie;

    public function __construct(int $id, String $nom, double $prix, String $poids, String $description, String $detail, String $lieu, int $distance, String $image, float $latitude, float $longitude, int $stock, Categorie $categorie) {
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

}