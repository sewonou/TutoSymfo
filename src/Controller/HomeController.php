<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em, UserPasswordHasherInterface $encoder): Response
    {
        /*$user = new User();
        $user->setUsername('djantadev')
            ->setPassword($encoder->hashPassword($user, '1234'))
            ->setEmail('djanta@dev.com')
            ->setRoles([])
            ;
        $em->persist($user);
        $em->flush();*/

        return $this->render('home/index.html.twig', [

        ]);
    }
}
