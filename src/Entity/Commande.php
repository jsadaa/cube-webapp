<?php

namespace App\Entity;

use DateTime;

class Commande {

    private int $id;
    private DateTime $dateCommande;
    private ?DateTime $dateLivraison = null;
    private StatutCommande $statut;
    /**
     * @var LigneCommandeClient[]
     */
    private array $ligneCommandeClients;

    public function __construct($id, DateTime $dateCommande, string $statut, array $ligneCommandeClients, ?DateTime $dateLivraison = null) {
        $this->id = $id;
        $this->dateCommande = $dateCommande;
        $this->dateLivraison = $dateLivraison;
        $this->statut = StatutCommande::from($statut);
        $this->ligneCommandeClients = $ligneCommandeClients;
    }

    public function getId() {
        return $this->id;
    }

    public function getDateCommande(): DateTime {
        return $this->dateCommande;
    }

    public function getDateLivraison(): ?DateTime {
        return $this->dateLivraison;
    }

    public function getStatut(): string {
        return preg_replace('/(?<!^)[A-Z]/', ' $0', $this->statut->value);
    }

    public function getLigneCommandeClients(): array {
        return $this->ligneCommandeClients;
    }

    public function getTotal(): float {
        $total = 0;
        foreach ($this->ligneCommandeClients as $ligneCommandeClient) {
            $total += $ligneCommandeClient->getTotal();
        }
        return round($total, 2);
    }

    public function getQuantite(): int {
        $quantite = 0;
        foreach ($this->ligneCommandeClients as $ligneCommandeClient) {
            $quantite += $ligneCommandeClient->getQuantite();
        }
        return $quantite;
    }
}
