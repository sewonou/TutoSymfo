<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();
        $form = $this->createForm(ContactType::class,  $data);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $mail = (new TemplatedEmail())
                    ->to($data->service)
                    ->from($data->email)
                    ->subject('New Contact from Recette du jour')
                    ->context(['data' => $data])
                    ->htmlTemplate('emails/contact.html.twig');
                $mailer->send($mail);
                $this->addFlash('success', 'Votre message a été envoyée avec succès');

                return $this->redirectToRoute('contact');
            }catch (\Exception $e){
                $this->addFlash('danger', 'Votre message n\'a pas été envoyée, une erreur c\'est produite.');
                return $this->redirectToRoute('contact');
            }

        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
