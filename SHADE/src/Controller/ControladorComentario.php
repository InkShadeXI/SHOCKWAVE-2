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

class ControladorComentario extends AbstractController
{
    #[Route('/comentar_post', name:'comentar_post')]
    public function comentar (Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils): Response {
        $nombre_usuario = $request->request->get("usuario");
        return $this->render('comentario.html.twig', [
            "usuario_destino" => $nombre_usuario
        ]);
    }

    #[Route('/procesar_comentario', name:'procesar_comentario')]
    public function procesar_comentario (Request $request): Response {
        $usuario_receptor = $request->request->get("nombre_usuario");
        $mensaje = $request->request->get("comentario");
        return $this->render('error_login.html.twig', [
            "error" => $usuario_receptor . " ha comentado " . $mensaje
        ]);
    }
}