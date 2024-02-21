<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

// ===== MUCHO CUIDADO, si no incluyes la entidad y el componente Doctrine, no funcionará =====
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\Amistad;
use App\Entity\PostUsuario;
use App\Entity\Comentario;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ControladorComentario extends AbstractController
{
    #[Route('/comentar_post', name:'comentar_post')]
    public function comentar (Request $request, EntityManagerInterface $entityManager): Response {
        $usuario_receptor = $request->request->get("usuario");
        $id_post = $request->request->get("id_post");
        $query_comentarios = $entityManager->createQueryBuilder()
        ->select('c.id_comentario, u.nombre_usuario, c.texto_comentario')
        ->from(Comentario::class, 'c')
        ->join('c.usuario_comentario', 'u')  // Une con la entidad Usuario (usando el alias 'usuario_comentario')
        ->andWhere('c.id_comentario_post = :idPost')
        ->setParameter('idPost', $id_post)
        ->getQuery()
        ->getResult();
        return $this->render('comentario.html.twig', [
            "usuario_receptor" => $usuario_receptor,
            "id_post" => $id_post,
            "comentarios" => $query_comentarios
        ]);
    }

    #[Route('/procesar_comentario', name:'procesar_comentario')]
    public function procesar_comentario (Request $request, EntityManagerInterface $entityManager): Response {
        $id_post = $request->request->get("id_post");
        $id_usuario_comentario = $this->getUser()->getIdUsuario();
        $comentario_usuario = $request->request->get("comentario");

        // Crear una nueva instancia de Comentario (AQUI ESTA EL ERROR, MIRALO)
        $comentario = new Comentario();
        $comentario->setIdComentarioPost(2);
        $comentario->setIdComentarioUsuario($id_usuario_comentario);
        $comentario->setTextoComentario($comentario_usuario);

        // Persistir el comentario en la base de datos
        $entityManager->persist($comentario);
        $entityManager->flush();

        return $this->render('error_login.html.twig', [
            "error" => "Has comentado " . $comentario_usuario . " en el post con id: " . $id_post . " y tienes el id: " . $id_usuario_comentario
        ]);
    }
}