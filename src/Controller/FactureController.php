<?php

namespace App\Controller;

use App\Security\User;
use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Konekt\PdfInvoice\InvoicePrinter;

class FactureController extends AbstractController
{
    #[Route('/commande/{idCommande}/facture/{idFacture}', name: 'facture_pdf')]
    public function telecharger(ApiClientService $apiClientService, int $idCommande, int $idFacture) : Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $client = $apiClientService->getClient($user->getId());
        $facture = $apiClientService->getFacture($idFacture);
        $commande = $apiClientService->getCommande($idCommande);

        $invoice = new InvoicePrinter("A4", "€", "fr");
        $invoice->setLogo(__DIR__ . '/../../public/assets/img/logo.jpg');
        $invoice->setColor("#007fff");
        $invoice->setType("Facture");
        $invoice->setReference("NGS00" . $facture->getId());
        $invoice->setDate($facture->getDateFacture()->format('d/m/Y'));
        $invoice->setDue($facture->getDateFacture()->add(new \DateInterval('P2M'))->format('d/m/Y'));
        $invoice->setFrom([
            "Société Negosud",
            "123 Rue de la Paix",
            "75000",
            "Paris",
            "France"
        ]);

        $invoice->setTo([
            $client->getPrenom() . ' ' . $client->getNom(),
            $client->getAdresse(),
            $client->getCodePostal(),
            $client->getVille(),
            $client->getPays()
        ]);

        foreach ($commande->getLigneCommandeClients() as $ligneCommandeClient) {
            $invoice->addItem(
                $ligneCommandeClient->getProduit()->getNom(),
                $ligneCommandeClient->getProduit()->getDescription(),
                $ligneCommandeClient->getQuantite(),
                $ligneCommandeClient->getTotal() * 0.2,
                $ligneCommandeClient->getPrixUnitaire(),
                0,
                $ligneCommandeClient->getTotal() * 1.2
            );
        }

        $invoice->addTotal("Total", $facture->getPrixHt());
        $invoice->addTotal("TVA 20%", $facture->getTva());
        $invoice->addTotal("Total TTC", $facture->getPrixTtc());

        $invoice->addBadge("A payer", $facture->getPrixTtc());

        $invoice->addTitle("Informations");
        $invoice->addParagraph("Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sed lacus ut enim ultrices posuere. Nullam nec tincidunt arcu. Nulla facilisi. Nullam nec tincidunt arcu. Nulla facilisi.");

        $invoice->setFooternote("Merci de votre confiance.");

        $invoice->render('Facture-' . $facture->getId() . '.pdf', 'D');

        return new Response();
    }
}
