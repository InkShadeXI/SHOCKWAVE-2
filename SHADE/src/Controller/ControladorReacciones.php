<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// ===== MUCHO CUIDADO, si no incluyes la entidad y el componente Doctrine, no funcionará =====
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ControladorReacciones extends AbstractController
{
    #[Route('/procesar_reaccion', name:'procesar_reaccion')]
    public function reaccion (Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils): Response {
        $id_post = $request->request->get("id_post");
        return $this->render('home.html.twig');
    }

}