<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\PostUsuario;
use App\Entity\Comentario;
use App\Entity\Amistad;
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

    return $this->redirectToRoute('amistad');
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


    #[Route('/perfil', name: 'perfil')]
    public function mostrarPerfil(EntityManagerInterface $entityManager,$id): Response
    {
        $usuario = $entityManager->getRepository(Usuario::class)->find($id);

        if (!$usuario) {
            throw $this->createNotFoundException('No se encontró el usuario.');
        }
        return $this->render('perfil.html.twig', ['usuario' => $usuario,]);
    }


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
}
