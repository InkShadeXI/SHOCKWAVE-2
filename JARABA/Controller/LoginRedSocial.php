<?php 
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Usuario;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Mime\Email;

class LoginRedSocial extends AbstractController
{ 

    #[Route('/login', name:'log')]
    public function home(){
        return $this->render('login.html.twig');
    }

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
            return $this->redirectToRoute('home');
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

    #[Route('/usuario', name:'usuario')]
    public function mostrarUsuario(EntityManagerInterface $entityManager) {
        $usuario = $entityManager->getRepository(Usuario::class)->findAll();
        return $this->render("home.html.twig", ['usuario'=>$usuario]);
    }


    #[Route('/registro', name: 'registro')]
    public function registro(MailerInterface $mailer, Request $request)
    {
        if ($request->isMethod('POST')) {
            $nombre = $request->request->get('nombre');
            $correo = $request->request->get('correo');
            $passwd = $request->request->get('passwd');
            $localidad = $request->request->get('ciudad');
            $datos = $request->request->get('datos');
            if($datos == null){
                $datos = "Sin informacion";
            }
    
            $fechaN = $request->request->get('fechaN'); 
            // Si la fecha de nacimiento es nula, establecerla en la fecha actual
            if($fechaN == null){
                $fechaN = new \DateTime(); // Fecha de hoy
                $fechaN->modify('-1 day'); // Restar un día
            } else {
                $fechaNacimiento = \DateTime::createFromFormat('d-m-Y', $fechaN);
                if ($fechaNacimiento instanceof \DateTime) {
                    $fechaN = $fechaNacimiento;
                } 
            
            }
    
            $email = (new Email())
                ->from('shockwave@hotmail.com')
                ->to('destinatario@email.com')
                ->subject('Correo de confirmación')
                ->html("<p>Para confirmar el registro, haz click en este <a href='http://localhost:8000/confirmar_correo/{$nombre}/{$correo}/{$passwd}/{$localidad}/{$fechaN->format('Y-m-d')}/{$datos}'>enlace</a>.</p>");
    
            $mailer->send($email);
        } else {
            return $this->render('registro.html.twig');
        }
    
        return $this->render('login.html.twig');
    }

#[Route('/confirmar_correo/{n}/{c}/{p}/{l}/{f}/{d}', name: 'confirmar_correo')]
public function confirmar_correo(Request $request, EntityManagerInterface $entityManager, $n, $c, $p, $l, string $f, $d)
{

    $Correosexistentes = $entityManager->getRepository(Usuario::class)->findOneBy(['correo_usuario' => $c]);


    if(!$Correosexistentes){
        $admin = 0;

        $nuevo = new Usuario();
    
        $nuevo->setNombreUsuario($n);
        $nuevo->setUsuarioAdmin($admin);
        $nuevo->setCorreoUsuario($c);
        $nuevo->setContraseñaUsuario(password_hash($p, PASSWORD_BCRYPT)); 
        if ($d == null) {
            $nuevo->setDescripcion(" ");
        } else {
            $nuevo->setDescripcion($d);
        }
        $nuevo->setLocalidad($l);
        
        try {
            $fechaNacimiento = \DateTime::createFromFormat('Y-m-d', $f);
    
            $fechaNacimientoStr = $fechaNacimiento->format('Y-m-d');
    
            $nuevo->setFechaNacimiento($fechaNacimientoStr);
        } catch (\Exception $e) {
            echo "Error al procesar la fecha de nacimiento: " . $e->getMessage();
        }
    
        $entityManager->persist($nuevo);
        $entityManager->flush();
    
        return $this->redirectToRoute('log');
    }else{
        return $this->redirectToRoute('log');
    }
 }


    #[Route('/correo_contraseña', name: 'correo_contraseña')]
public function correoContraseña(MailerInterface $mailer, Request $request): Response 
{
    if ($request->isMethod('POST')) {
        $correo = $request->request->get('correo');
        
        if ($correo) {
            $email = (new Email())
                ->from('shockwave@hotmail.com')
                ->to($correo)
                ->subject('Correo de recuperación de contraseña')
                ->html("<p>Para cambiar la contraseña, haz click en este <a href='http://localhost:8000/recuperar_contraseña/{$correo}'>enlace</a>.</p>");
    
            $mailer->send($email);
        }
    } else {
        return $this->render('correo_contrasena.html.twig');
    }
    
    return $this->render('login.html.twig');
}

#[Route('/recuperar_contraseña/{correo}', name: 'recuperar_contraseña')]
public function recuperarContraseña(EntityManagerInterface $entityManager, Request $request, $correo): Response 
{   
    if ($request->isMethod('POST')) {
        $passw = $request->request->get('passw');
        
        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['correo_usuario' => $correo]);
        
        if ($usuario) {
            $usuario->setContraseñaUsuario(password_hash($passw, PASSWORD_BCRYPT));
            $entityManager->flush();
            
            return $this->redirectToRoute('log');
        }
    }
    
    return $this->render('recuperar_contraseña.html.twig', ["correo" => $correo]);
}
}
