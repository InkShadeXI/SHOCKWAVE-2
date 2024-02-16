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


class ControladorHome extends AbstractController {
    #[Route('/home', name:'home')]
    public function process(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils): Response {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    // Obtén el usuario actualmente autenticado
    $usuario = $this->getUser();

    // Dentro de tu controlador
    dump($usuario);

    // Verifica si el usuario está autenticado
    if ($usuario) {
        // Obtén el ID del usuario actual
        $idUsuario = $usuario->getIdUsuario();
        $nombreUsuario = $usuario->getNombreUsuario();

         // Obtén los IDs de los amigos del usuario
         $idsAmigos = $entityManager->createQueryBuilder()
         ->select('a.usuario1', 'a.usuario2')
         ->from(Amistad::class, 'a')
         ->andWhere('a.estado = :estadoAceptado')
         ->andWhere('a.usuario1 = :idUsuario OR a.usuario2 = :idUsuario')
         ->setParameter('estadoAceptado', 'Aceptado')
         ->setParameter('idUsuario', $idUsuario)
         ->getQuery()
         ->getArrayResult();

     $idsAmigos = array_merge(...$idsAmigos); // Fusiona los resultados en un solo array

        // Ahora, $idUsuario contiene el ID del usuario actual autenticado

        return $this->render('home.html.twig', [
            'idUsuario' => $idUsuario,
            'nombreUsuario' => $nombreUsuario,
            'idsAmigos' => $idsAmigos
            // Otros datos necesarios para la plantilla
        ]);
    } 
    else {
        throw $this->createAccessDeniedException('No estás autenticado.');
    }
    }
}
