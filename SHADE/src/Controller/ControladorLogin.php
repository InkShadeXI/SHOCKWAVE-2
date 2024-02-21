<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ControladorLogin extends AbstractController{
    
	// Enrutado básico
	#[Route('/login', name:'log')]
    public function home(){
        return $this->render('login.html.twig');
    }

}