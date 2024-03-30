<?php

namespace App\Entity;

use DateTime;

class Client {

    private int $id;
    private string $username;
    private string $nom;
    private string $prenom;
    private string $adresse;
    private string $codePostal;
    private string $ville;
    private string $pays;
    private string $telephone;
    private string $email;
    private DateTime $dateNaissance;
    private DateTime $dateInscription;
    private ?string $password = null;

    public function __construct(int $id, string $username, string $nom, string $prenom, string $adresse, string $codePostal, string $ville, string $pays, string $telephone, string $email, DateTime $dateNaissance, DateTime $dateInscription, string $password = null) {
        $this->id = $id;
        $this->username = $username;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->codePostal = $codePostal;
        $this->ville = $ville;
        $this->pays = $pays;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->dateNaissance = $dateNaissance;
        $this->dateInscription = $dateInscription;
        $this->password = $password;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getAdresse(): string {
        return $this->adresse;
    }

    public function getCodePostal(): string {
        return $this->codePostal;
    }

    public function getVille(): string {
        return $this->ville;
    }

    public function getPays(): string {
        return $this->pays;
    }

    public function getTelephone(): string {
        return $this->telephone;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getDateNaissance(): DateTime {
        return $this->dateNaissance;
    }

    public function getDateInscription(): DateTime {
        return $this->dateInscription;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void {
        $this->prenom = $prenom;
    }

    public function setAdresse(string $adresse): void {
        $this->adresse = $adresse;
    }

    public function setCodePostal(string $codePostal): void {
        $this->codePostal = $codePostal;
    }

    public function setVille(string $ville): void {
        $this->ville = $ville;
    }

    public function setPays(string $pays): void {
        $this->pays = $pays;
    }

    public function setTelephone(string $telephone): void {
        $this->telephone = $telephone;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function toArray(): array {
        return [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'adresse' => $this->adresse,
            'codePostal' => $this->codePostal,
            'ville' => $this->ville,
            'pays' => $this->pays,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'dateNaissance' => $this->dateNaissance->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function toArrayAvecMotDePasse(): array {
        return [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'adresse' => $this->adresse,
            'codePostal' => $this->codePostal,
            'ville' => $this->ville,
            'pays' => $this->pays,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'dateNaissance' => $this->dateNaissance->format('Y-m-d\TH:i:s.u\Z'),
            'password' => $this->password,
        ];
    }
}