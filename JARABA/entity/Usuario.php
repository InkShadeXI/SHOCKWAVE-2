<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity] 
#[ORM\Table(name: 'usuario')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
    #[ORM\Column(type:'integer', name:'IdUsuario')]
    #[ORM\GeneratedValue]
    private $id;

	#[ORM\Column(type:'string', name:'NombreUsuario')]
    private $nombre_usuario;

	#[ORM\Column(type:'string', name:'CorreoUsuario')]
    private $correo_usuario;

	#[ORM\Column(type:'string', name:'ContraseñaUsuario')]
    private $contraseña_usuario;

	#[ORM\Column(type:'string', name:'Descripcion')]
    private $descripcion;

	#[ORM\Column(type:'string', name:'Localidad')]
    private $localidad;

	#[ORM\Column(type:'date', name:'FechaDeNacimiento')]
    private $fechanacimiento;

	#[ORM\Column(type:'boolean', name:'UsuarioAdmin')]
    private $admin;

    #[ORM\OneToMany(targetEntity:'Comentario', mappedBy:'usuario')]
    private $comentarios;

    #[ORM\OneToMany(targetEntity:'PostUsuario', mappedBy:'usuario')]
    private $posts_usuario;

    #[ORM\OneToMany(targetEntity:'Amistad', mappedBy:'usuario')]
    private $amistades;

    public function getUsuarioAdmin(){
       return $this->admin;
    }

    public function setUsuarioAdmin($admin){
        $this->admin = $admin;
    }

    public function getIdUsuario(){
        return $this->id;
    }
    public function setIdUsuario($id){
        return  $this->id = $id;
    }
    public function setNombreUsuario($nombre_usuario){
        $this->nombre_usuario = $nombre_usuario;
    }
    public function getNombreUsuario(){
        return $this->nombre_usuario;
    }
    public function setCorreoUsuario($correo_usuario){
        $this->correo_usuario = $correo_usuario;
    }
    public function getCorreoUsuario(){
        return $this->correo_usuario;
    }
    public function setContraseñaUsuario($contraseña_usuario){
        $this->contraseña_usuario = $contraseña_usuario;
    }
	public function getContraseñaUsuario(){
        return $this->contraseña_usuario;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function setLocalidad($localidad){
        $this->localidad = $localidad;
    }

    public function getLocalidad(){
        return $this->localidad;
    }

    public function setFechaDeNacimiento($fechanacimiento){
        $this->fechanacimiento = $fechanacimiento;
    }

    public function getFechaDeNacimiento(){
        return $this->fechanacimiento;
    }

    public function setComentario($comentarios){
        $this->comentarios = $comentarios;
    }

    public function getComentario(){
        return $this->comentarios;
    }

    public function setPostUsuario($posts_usuario){
        $this->posts_usuario = $posts_usuario;
    }

    public function getPostUsuario(){
        return $this->posts_usuario;
    }

    public function setAmistad($amistades){
        $this->amistades = $amistades;
    }

    public function getAmistad(){
        return $this->amistades;
    }



	public function getRoles(): array
    {
        if($this->admin == 1){
            return ['ROLE_USER','ROLE_ADMIN'];   
        }else{
            return ['ROLE_USER'];    
        }
		        
	}

    public function getPassword(): string
    {
        return $this->getContraseñaUsuario();
    }


    public function getUserIdentifier(): string
    {
        return $this->getCorreoUsuario();
    }

    public function getSalt(): ?string
    {
        return null;
    }
	
    public function eraseCredentials(): void
    {

    }
}
