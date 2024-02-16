<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// ===== MUCHO CUIDADO, si no incluyes la entidad y el componente Doctrine, no funcionará =====
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\Amistad;
use App\Entity\PostUsuario;
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

        // Obtener los ids de los amigos (FUNCIONA)
        $idsAmigos = $entityManager->createQueryBuilder()
        ->select('CASE WHEN a.usuario1 = :idUsuario THEN IDENTITY(a.usuario2) ELSE IDENTITY(a.usuario1) END AS idAmigo')
        ->from(Amistad::class, 'a')
        ->andWhere('a.estado = :estado') // Aquí usa el nombre correcto de la columna 'Estado'
        ->andWhere('a.usuario1 = :idUsuario OR a.usuario2 = :idUsuario')
        ->setParameter('estado', 'Aceptado') // Aquí también usa el nombre correcto de la columna 'Estado'
        ->setParameter('idUsuario', $idUsuario)
        ->getQuery()
        ->getResult();

        // Transforma los resultados en un array plano de IDs
        $idsAmigos = array_column($idsAmigos, 'idAmigo');

        // Añade el ID del usuario actual para incluir también su propio ID
        $idsAmigos[] = $idUsuario;

        // Obtén los posts de los usuarios amigos
        $postsAmigos = $entityManager->createQueryBuilder()
        ->select('p.id, u.nombre_usuario, p.texto_post')
        ->from(PostUsuario::class, 'p')
        ->join('p.usuario', 'u')  // Une con la entidad Usuario
        ->andWhere('p.usuario IN (:idsAmigos)')
        ->setParameter('idsAmigos', $idsAmigos)
        ->getQuery()
        ->getResult();

        return $this->render('home.html.twig', [
            'idUsuario' => $idUsuario,
            'nombreUsuario' => $nombreUsuario,
            'postsAmigos' => $postsAmigos
        ]);
    } 
    else {
        throw $this->createAccessDeniedException('No estás autenticado.');
    }
    }
}
