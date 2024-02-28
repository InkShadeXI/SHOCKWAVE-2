<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Mime\Email;
use App\Entity\Usuario;
use App\Entity\PostUsuario;
use App\Entity\Comentario;
use App\Entity\Amistad;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class BaseRedSocial extends AbstractController
{

    #[Route('/amistad', name: 'amistad')]
    public function mostrarAmistades(EntityManagerInterface $entityManager): Response
    {
        $usuarioActual = $this->getUser();

        $solicitudes = $entityManager->getRepository(Amistad::class)->createQueryBuilder('a')
            ->where('a.usuario2 = :usuarioActual')
            ->andWhere('a.estado = :estado')
            ->setParameter('usuarioActual', $usuarioActual)
            ->setParameter('estado', 'pendiente')
            ->getQuery()
            ->getResult();

        return $this->render('amistad.html.twig', ['solicitudes' => $solicitudes]);
    }


    #[Route('/solicitudes_peneientes/{id_usuario_1}/{id_usuario_2}', name: 'solicitudes_peneientes')]
    public function checkPendingFriendRequest(EntityManagerInterface $entityManager,$id_usuario_1, $id_usuario_2): Response
    { 
        $solicitud = $entityManager->getRepository(Amistad::class)->findOneBy(['usuario1' => $id_usuario_1,'usuario2' => $id_usuario_2,'estado' => 'pendiente']);

        if ($solicitud) {
            return $this->json(['hay_solicitud_pendiente' => true]);
        } else {
            return $this->json(['hay_solicitud_pendiente' => false]);
        }
    }


    #[Route('/enviar_solicitud', name: 'enviar_solicitud_amistad', methods: ['POST'])]
    public function enviarSolicitud(EntityManagerInterface $entityManager, Request $request): Response
{
    $usuarioActual = $this->getUser();
    $usuarioDestinoNombre = $request->request->get('usuario_destino_nombre');

    // Buscar al usuario destinatario por su nombre
    $usuarioDestino = $entityManager->getRepository(Usuario::class)->findOneBy(['nombre_usuario' => $usuarioDestinoNombre]);

    if (!$usuarioDestino) {
        $this->addFlash('error', 'El usuario al que intentas enviar la solicitud no existe.');
        return $this->redirectToRoute('amistad');
    }

    $solicitudExistente = $entityManager->getRepository(Amistad::class)->findOneBy(['usuario1' => $usuarioActual,'usuario2' => $usuarioDestino,'estado' => 'pendiente']);

    if ($solicitudExistente) {
        $this->addFlash('error', 'Ya has enviado una solicitud de amistad a este usuario.');
        return $this->redirectToRoute('amistad');
    }

    $solicitud = new Amistad();
    $solicitud->setUsuario1($usuarioActual);
    $solicitud->setUsuario2($usuarioDestino);
    $solicitud->setEstado('pendiente');

    $entityManager->persist($solicitud);
    $entityManager->flush();

    return $this->render('home.html.twig');
}

    #[Route('/aceptar_solicitud/{id}', name: 'aceptar_solicitud_amistad')]
    public function aceptarSolicitud(EntityManagerInterface $entityManager, $id): Response
    {
        $solicitud = $entityManager->getRepository(Amistad::class)->find($id);

        if (!$solicitud) {
            throw $this->createNotFoundException('Solicitud de amistad no encontrada');
        }

        $solicitud->setEstado('aceptada');
        $entityManager->flush();

        return $this->redirectToRoute('amistad');
    }

    #[Route('/denegar_solicitud/{id}', name: 'denegar_solicitud_amistad')]
    public function denegarSolicitud(EntityManagerInterface $entityManager, $id): Response
    {
        $solicitud = $entityManager->getRepository(Amistad::class)->find($id);

        if (!$solicitud) {
            throw $this->createNotFoundException('Solicitud de amistad no encontrada');
        }

        $entityManager->remove($solicitud);
        $entityManager->flush();

        return $this->redirectToRoute('amistad');
    }


// Apartir de aqui es codigo de la zona admin

    #[Route('/zona_admin', name: 'zona_admin')]
    public function ZonaAdmin(EntityManagerInterface $entityManager): Response
    {
        $usuarioActual = $this->getUser();
    
        if ($usuarioActual) {
            if ($usuarioActual->getUsuarioAdmin() == 1) {
                return $this->render('zona_admin.html.twig', ['usuario' => $usuarioActual]);
            } else {
                return $this->render('home.html.twig');
            }
        } else {
            return $this->render('home.html.twig');
        }
    }

        #[Route('/borrar_usuario', name: 'borrar_usuario')]
        public function BorrarUsuario(EntityManagerInterface $entityManager,Request $request): Response
        {
            if ($request->isMethod('POST')) {
            $nombre = $request->request->get('usu_delete');
            $usuarioActual = $this->getUser();
            if ($usuarioActual->getUsuarioAdmin() == 1) {
                    $usu_delete = $entityManager->getRepository(Usuario::class)->findOneBy(['nombre_usuario' => $nombre]);
                        $entityManager->remove($usu_delete);
                        $entityManager->flush();

                        return $this->render('zona_admin.html.twig');
            }else{
                return $this->render('home.html.twig');
            }
        }else{
            return $this->render('home.html.twig');
        }
    
        }

        #[Route('/borrar_post', name: 'borrar_post')]
        public function BorrarPost(EntityManagerInterface $entityManager,Request $request): Response
        {
            if($request->isMethod('POST')) {
                $id = $request->request->get('post_delete');
                $usuarioActual = $this->getUser();
                if ($usuarioActual->getUsuarioAdmin() == 1) {
                    $post_delete = $entityManager->getRepository(PostUsuario::class)->findOneBy(['id' => $id]);
                 //   if ($post_delete !== null) {
                        $entityManager->remove($post_delete);
                        $entityManager->flush();
                  /*  } else {
                        return $this->render('error_lo.html.twig');
                    }*/
                        return $this->render('zona_admin.html.twig');
                }else{
                    return $this->render('home.html.twig');
                }
            }else{
                return $this->render('home.html.twig');
            }
        }

        #[Route('/ascender_admin', name: 'ascender_admin')]
        public function AscenderAdmin(EntityManagerInterface $entityManager,Request $request): Response
        {
            if($request->isMethod('POST')) {
                $nombre = $request->request->get('ascender');
                $usuarioActual = $this->getUser();
                if ($usuarioActual->getUsuarioAdmin() == 1) {
                    $ascender = $entityManager->getRepository(Usuario::class)->findOneBy(['nombre_usuario' => $nombre]);
                    $ascender -> setUsuarioAdmin(1);
                    $entityManager->persist($ascender);
                    $entityManager->flush();

                    return $this->render('zona_admin.html.twig');
                        
                }else{
                    return $this->render('home.html.twig');
                }
            }else{
                return $this->render('home.html.twig');
            }
        }

// codigo de perfil

#[Route('/perfil/{id}', name: 'perfil')]
public function mostrarPerfil(EntityManagerInterface $entityManager, $id): Response
{
    $usuarioActual = $this->getUser();
    $usuarioPerfil = $entityManager->getRepository(Usuario::class)->find($id);

    if (!$usuarioPerfil) {
        throw $this->createNotFoundException('No se encontró el usuario.');
    }

    // Verificar si ya son amigos
    $amistad = $entityManager->getRepository(Amistad::class)->findOneBy([
        'usuario1' => $usuarioActual,
        'usuario2' => $usuarioPerfil,
        'estado' => 'aceptada'
    ]);

    $puedeEnviarSolicitud = true; // Por defecto, permitir enviar solicitud de amistad

    if ($amistad) {
        $puedeEnviarSolicitud = false; // Si ya son amigos, no permitir enviar solicitud
    }

    return $this->render('perfil.html.twig', [
        'usuario' => $usuarioPerfil,
        'puedeEnviarSolicitud' => $puedeEnviarSolicitud
    ]);
}
        #[Route('/usuarios', name:'usuarios')]
        public function mostrarUsuarios(EntityManagerInterface $entityManager,Request $request): Response
        {
            if ($request->isMethod('GET')) {
                $query = $request->query->get('q');

              
                    $usuarios = $entityManager->createQueryBuilder()
                        ->select('u.nombre_usuario', 'u.correo_usuario', 'u.localidad,u.id_usuario')
                        ->from(Usuario::class, 'u')
                        ->andWhere('u.nombre_usuario LIKE :letra')
                        ->setParameter('letra', '%' . $query . '%')
                        ->getQuery()
                        ->getArrayResult();
            
                    return $this->render('usuarios.html.twig', ['usuario' => $usuarios]);
                
            }else{
            $usuarios= $entityManager->getRepository(Usuario::class)->findAll();
            return $this->render('usuarios.html.twig', ['usuario' => $usuarios,]);
            }
        }
        

      
        #[Route('/comentar_post', name:'comentar_post')]
        public function comentar(Request $request, EntityManagerInterface $entityManager): Response {
            $usuarioReceptor = $request->request->get("usuario");
            $idPost = $request->request->get("id_post");
            $comentarios = $entityManager->getRepository(Comentario::class)
                ->findBy(['id_comentario_post' => $idPost]);
            
            return $this->render('comentario.html.twig', [
                "usuario_receptor" => $usuarioReceptor,
                "id_post" => $idPost,
                "comentarios" => $comentarios
            ]);
        }

        #[Route('/procesar_reaccion', name:'procesar_reaccion')]
        public function reaccion (Request $request, EntityManagerInterface $entityManager): Response {
            $id_post = $request->request->get("id_post");
            return $this->render('home.html.twig');
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


// home

#[Route('/home', name: 'home')]
public function process(Request $request, EntityManagerInterface $entityManager): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    $usuario = $this->getUser();

    if ($usuario) {
        $idUsuario = $usuario->getIdUsuario();
        $nombreUsuario = $usuario->getNombreUsuario();

        // Obtener los IDs de amigos del usuario
        $idsAmigos = $entityManager->createQueryBuilder()
            ->select('CASE WHEN a.usuario1 = :idUsuario THEN IDENTITY(a.usuario2) ELSE IDENTITY(a.usuario1) END AS idAmigo')
            ->from(Amistad::class, 'a')
            ->andWhere('a.estado = :estado') 
            ->andWhere('a.usuario1 = :idUsuario OR a.usuario2 = :idUsuario')
            ->setParameter('estado', 'aceptada') // Cambiado de 'Aceptado' a 'aceptada'
            ->setParameter('idUsuario', $idUsuario)
            ->getQuery()
            ->getResult();

        // Extraer solo los IDs de amigos
        $idsAmigos = array_column($idsAmigos, 'idAmigo');

        // Agregar el ID del usuario actual para incluir sus propios posts
        $idsAmigos[] = $idUsuario;

        // Obtener los posts de los amigos
        $postsAmigos = $entityManager->createQueryBuilder()
            ->select('p.id, u.nombre_usuario, p.texto_post, p.num_likes, p.num_dislikes')
            ->from(PostUsuario::class, 'p')
            ->join('p.usuario', 'u')  
            ->andWhere('p.usuario IN (:idsAmigos)')
            ->setParameter('idsAmigos', $idsAmigos)
            ->getQuery()
            ->getResult();

                return $this->render('home.html.twig', [
                    'idUsuario' => $idUsuario,
                    'nombreUsuario' => $nombreUsuario,
                    'postsAmigos' => $postsAmigos
                ]);

    } else {
        throw $this->createAccessDeniedException('No estás autenticado.');
    }
}

// codigo de borrar el correo

    #[Route('/correo_borrado', name:'correo_borrado')]
    public function CorreoBorrado(MailerInterface $mailer, Request $request): Response {
        $confirmacion = $request->request->get('confirmar');
        if ($request->isMethod('POST')) {
            $usuarioActual = $this->getUser();
            $id_usuario = $usuarioActual->getIdUsuario();
            if(isset($confirmacion)){
                $confirmacion = true;
                $email = (new Email())
                ->from('shockwave@hotmail.com')
                ->to('destinatario@email.com')
                ->subject('Correo de confirmación')
                ->html("<p>Para confirmar el borrado de la cuenta, haz click en este <a href='http://localhost:8000/borrar_cuenta/{$confirmacion}/{$id_usuario}'>enlace</a>.</p><br><p>Recuerda que se borrara todo lo relacionado a ti</p>");
    
            $mailer->send($email);
            }
        } else {
            return $this->render('registro.html.twig');
        }
    
        return $this->render('login.html.twig');
    }


    #[Route('/borrar_cuenta/{c}/{id_usuario}', name: 'borrar_cuenta')]
    public function borrarCuenta(EntityManagerInterface $entityManager, bool $c, $id_usuario): Response 
    {   
        try {
            if ($c) {
                $usuario = $entityManager->getRepository(Usuario::class)->find($id_usuario);
                
                if ($usuario) {
                    $entityManager->remove($usuario);
                    $entityManager->flush();
                }
                
                return $this->redirectToRoute('ctrl_logout');
            } else {
                return $this->redirectToRoute('ctrl_logout');
            }
        } catch (\Exception $e) {
            return $this->redirectToRoute('ctrl_logout');
        }
    }

}
