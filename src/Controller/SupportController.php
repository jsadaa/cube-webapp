<?php

namespace App\Controller;

use App\Security\User;
use App\Service\ApiClientService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class SupportController extends AbstractController
{
    #[Route('/support', name: 'support')]
    public function index(): Response
    {
        return $this->render('support/index.html.twig');
    }

    #[Route('/support/send', name: 'support_send')]
    public function send(MailerInterface $mailer, Request $request, ApiClientService $apiClientService): Response
    {
        try {

            $data = $request->request->all();
            /**
             * @var User $user
             */
            $user = $this->getUser();
            $client = $apiClientService->getClient($user->getId());

            $email = (new TemplatedEmail())
                ->from($user->getEmail())
                ->to('leo.paillard@gmail.com')
                ->subject($data['subject'])
                ->htmlTemplate('email/support.html.twig')
                ->locale('fr')
                ->context([
                    'name' => $client->getPrenom() . ' ' . $client->getNom(),
                    'mail' => $user->getEmail(),
                    'message' => $data['message']
                ]);

            $mailer->send($email);

            return $this->render('support/send.html.twig',
                [
                    'email' => $user->getEmail(),
                    'subject' => $data['subject'],
                    'message' => $data['message']
                ]
            );
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', ['error' => $e->getMessage()]);
        }
    }
}
