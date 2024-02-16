<-Actualizado 12/02: Adaptado para ver perfiles de más usuarios->

<?php

    namespace App\Controller;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\Usuario;
    use Doctrine\ORM\EntityManager;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


    class controladores extends AbstractController{

        #[Route('/login', name:'log')]
    public function home(){
        return $this->render('login.html.twig');
    }

    #[Route('/procesar_login', name:'procesar_login')]
    public function process(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils): Response
    {
        $usuario=$this->getUser();

        if (!$usuario) {
            // Usuario no encontrado
            return $this->render('error_login.html.twig', [
                'error' => "Este usuario no existe :("
            ]);
        }        
            return $this->render('home.html.twig');
    }

        #[Route('/registro', name: 'registro')]
    public function registro(): Response{
        return $this->render('registro.html.twig');
    }

        #[Route('/perfil/{id}', name: 'perfil')]
        public function mostrarPerfil(EntityManagerInterface $entityManager,$id): Response
        {
            $usuario = $entityManager->getRepository(Usuario::class)->find($id);
    
            if (!$usuario) {
                throw $this->createNotFoundException('No se encontró el usuario.');
            }
            return $this->render('perfil.html.twig', ['usuario' => $usuario,]);
    }

    
        #[Route('/buscar-usuarios', name: 'buscar_usuarios', methods: ('GET'))]
        
        public function buscarUsuarios(Request $request): Response
        {
            $query = $request->query->get('q');

            $usuarios = $this->getDoctrine()->getRepository(User::class)->buscarPorNombreOEmail($query);

            return $this->render('includes/resultado_busqueda.html.twig', ['usuarios' => $usuarios,]);
        }

        #[Route('/logout', name:'app_logout')]
        public function logout()
        {
            return null;
        } 
    }
?>
