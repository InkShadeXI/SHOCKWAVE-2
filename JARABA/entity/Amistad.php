<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity] 
#[ORM\Table(name: 'amistad')]
class Comentario 
{
	#[ORM\Id]
    #[ORM\Column(type:'integer', name:'IdUsusario1')]
    #[ORM\GeneratedValue]
    private $id_usuario_1;

	#[ORM\Column(type:'integer', name:'IdUsusario1')]
    private $id_usuario_2;

	#[ORM\Column(type:'integer', name:'Aceptado')]
    private $aceptado;

    #[ORM\ManyToOne(targetEntity:'Usuario', inversedBy:'amistad')]
	#[ORM\JoinColumn(name:'Usuario', referencedColumnName:'IdUsuario')]
    private $usuario; 

    public function getIdUsusario1(){
       return $this->id_usuario_1;
    }

    public function setIdUsusario1($id_usuario_1){
        $this->id_usuario_1 = $id_usuario_1;
    }

    public function getIdUsusario2(){
        return $this->id_usuario_2;
     }
 
     public function setIdUsusario2($id_usuario_2){
         $this->id_usuario_2 = $id_usuario_2;
     }
    
     public function getAceptado(){
        return $this->aceptado;
     }
 
     public function setAceptado($aceptado){
         $this->aceptado = $aceptado;
     }
    
     public function getUsuario(){
        return $this->usuario;
     }
}