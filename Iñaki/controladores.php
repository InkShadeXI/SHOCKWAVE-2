<?php

    namespace App\Controller;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Doctrine\ORM\EntityManagerInterface;

    class controladores extends AbstractController{

        #[Route('/registro', name: 'registro')]
    public function registro(): Response{
        return $this->render('registro.html.twig');
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

    }
?>
