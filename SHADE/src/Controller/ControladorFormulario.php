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

class ControladorFormulario extends AbstractController
{
    #[Route('/procesar_login', name:'procesar_login')]
    public function process(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils): Response
    {
        // obtener el último nombre de usuario introducido (si hay alguno)
        $lastUsername = $authenticationUtils->getLastUsername();

        // obtener el error de inicio de sesión (si lo hay)
        $error = $authenticationUtils->getLastAuthenticationError();

        if (!$lastUsername) {
            // Usuario no encontrado
            return $this->render('error_login.html.twig', [
                'error' => "Este usuario no existe :("
            ]);
        }

        // Comparar la contraseña
        if (!$error) {
            // Contraseña correcta
            return $this->render('home.html.twig');
        } else {
            // Contraseña incorrecta
            return $this->render('error_login.html.twig', [
                'error' => "Contraseña incorrecta, por favor, inténtalo de nuevo :("
            ]);
        }
    }

    #[Route('/logout', name:'app_logout')]
    public function logout()
    {
        return null;
    } 
}