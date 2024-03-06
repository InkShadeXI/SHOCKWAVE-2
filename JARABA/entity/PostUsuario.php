<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity] 
#[ORM\Table(name: 'postusuario')]
class PostUsuario 
{
	#[ORM\Id]
    #[ORM\Column(type:'integer', name:'IdPost')]
    #[ORM\GeneratedValue]
    private $id;

	#[ORM\Column(type:'integer', name:'IdUsuarioPost')]
    private $id_usuario_post;

	#[ORM\Column(type:'string', name:'TextoPost')]
    private $texto_post;

	#[ORM\Column(type:'string', name:'TituloFoto')]
    private $titulo_foto;

	#[ORM\Column(type:'integer', name:'NumLikes')]
    private $num_likes;

	#[ORM\Column(type:'date', name:'FechaCreacion')]
    private $fecha_creacion;

	#[ORM\Column(type:'integer', name:'NumDislikes')]
    private $num_dislikes;

    #[ORM\ManyToOne(targetEntity: 'Usuario', inversedBy: 'postusuario')]
    #[ORM\JoinColumn(name: 'IdUsuarioPost', referencedColumnName: 'IdUsuario')]
    private $usuario;
    
    #[ORM\OneToMany(targetEntity:'Comentario', mappedBy:'post_usuario')]
    private $comentarios;

    public function getIdPost(){
       return $this->id;
    }

    public function setIdPost($id){
        $this->id = $id;
    }

    public function getIdUsuarioPost(){
        return $this->id_usuario_post;
     }
 
     public function setIdUsuarioPost($id_usuario_post){
         $this->id_usuario_post = $id_usuario_post;
     }

     public function getTextoPost(){
        return $this->texto_post;
     }
 
     public function setTextoPost($texto_post){
         $this->texto_post = $texto_post;
     }

     public function getTituloFoto(){
        return $this->titulo_foto;
     }
 
     public function setTituloFoto($titulo_foto){
         $this->titulo_foto = $titulo_foto;
     }

     public function getNumLikes(){
        return $this->num_likes;
     }
 
     public function setNumLikes($num_likes){
         $this->num_likes = $num_likes;
     }
    
     public function getFechaCreacion(){
        return $this->fecha_creacion;
     }
 
     public function setFechaCreacion($fecha_creacion){
         $this->fecha_creacion = $fecha_creacion;
     }

     public function getNumDislikes(){
        return $this->num_dislikes;
     }
 
     public function setNumDislikes($num_dislikes){
         $this->num_dislikes = $num_dislikes;
     }

     public function getUsuario(){
        return $this->usuario;
     }

     public function setUsuario($usuario){
        $this->usuario = $usuario;
    }

     public function getComentario(){
        return $this->comentarios;
     }
 
     public function setComentario($comentarios){
         $this->comentarios = $comentarios;
     }

     public function addComentario(Comentario $comentario): self
     {
         if (!$this->comentarios->contains($comentario)) {
             $this->comentarios[] = $comentario;
             $comentario->setPostComentario($this);
         }
 
         return $this;
     }
 
     public function removeComentario(Comentario $comentario): self
     {
         if ($this->comentarios->removeElement($comentario)) {
             // set the owning side to null (unless already changed)
             if ($comentario->getPostComentario() === $this) {
                 $comentario->setPostComentario(null);
             }
         }
 
         return $this;
     }
 
    
}
