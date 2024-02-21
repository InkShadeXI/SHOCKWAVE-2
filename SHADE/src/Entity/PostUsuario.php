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
    private $id_post_usuario;

	#[ORM\Column(type:'string', name:'TextoPost')]
    private $texto_post;

	#[ORM\Column(type:'integer', name:'ExisteFoto')]
    private $existe_foto;

	#[ORM\Column(type:'string', name:'TituloFoto')]
    private $titulo_foto;

	#[ORM\Column(type:'integer', name:'NumLikes')]
    private $num_likes;

	#[ORM\Column(type:'date', name:'FechaCreacion')]
    private $fecha_creacion;

	#[ORM\Column(type:'integer', name:'NumDislikes')]
    private $num_dislikes;

    #[ORM\ManyToOne(targetEntity: 'Usuario', inversedBy: 'postUsuario')]
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
        return $this->id_post_usuario;
     }
 
     public function setIdUsuarioPost($id_post_usuario){
         $this->id_post_usuario = $id_post_usuario;
     }

     public function getTextoPost(){
        return $this->texto_post;
     }
 
     public function setTextoPost($texto_post){
         $this->texto_post = $texto_post;
     }

     public function getExisteFoto(){
        return $this->existe_foto;
     }
 
     public function setExisteFoto($existe_foto){
         $this->existe_foto = $existe_foto;
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

     public function getComentario(){
        return $this->comentarios;
     }
 
     public function setComentario($comentarios){
         $this->comentarios = $comentarios;
     }
    
}
