<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity] 
#[ORM\Table(name: 'comentario')]
class Comentario 
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'IdComentario')]
    #[ORM\GeneratedValue]
    private $id_comentario;

    #[ORM\Column(type: 'integer', name: 'IdComentarioPost')]
    private $id_comentario_post;

    #[ORM\Column(type: 'integer', name: 'IdComentarioUsuario')]
    private $id_comentario_usuario;

    #[ORM\Column(type: 'string', name: 'TextoComentario')]
    private $texto_comentario;

    #[ORM\ManyToOne(targetEntity: "Usuario", inversedBy: 'comentarios')] // Corregido el alias
    #[ORM\JoinColumn(name: "IdComentarioUsuario", referencedColumnName: "IdUsuario")] // Corregido el nombre de la columna
    private $usuario_comentario;

    #[ORM\OneToOne(targetEntity: "PostUsuario", inversedBy: 'comentario')]
    #[ORM\JoinColumn(name: "IdComentarioPost", referencedColumnName: "IdPost")] // Corregido el nombre de la columna
    private $post_comentario;

    

    public function getIdComentario(){
       return $this->id_comentario;
    }

    public function setIdComentario($id_comentario){
        $this->id_comentario = $id_comentario;
    }

    public function getIdComentarioPost(){
        return $this->id_comentario_post;
     }
 
     public function setIdComentarioPost($id_comentario_post){
         $this->id_comentario_post = $id_comentario_post;
     }
    
     public function getIdComentarioUsuario(){
        return $this->id_comentario_usuario;
     }

 
     public function setIdComentarioUsuario($id_comentario_usuario){
         $this->id_comentario_usuario = $id_comentario_usuario;
     }
    
     public function getTextoComentario(){
        return $this->texto_comentario;
     }
 
     public function setTextoComentario($texto_comentario){
         $this->texto_comentario = $texto_comentario;
     }

     public function getPostUsuario(){
        return $this->post_comentario;
     }

     public function getUsuario(){
        return $this->usuario_comentario;
     }
 
}
