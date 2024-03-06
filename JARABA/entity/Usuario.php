<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity] 
#[ORM\Table(name: 'usuario')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer', name: 'IdUsuario')]
    private $id_usuario;

    #[ORM\Column(type:'string', name:'NombreUsuario')]
    private $nombre_usuario;

    #[ORM\Column(type:'string', name:'CorreoUsuario')]
    private $correo_usuario;

    #[ORM\Column(type: 'string', name: 'ContrasenaUsuario')]
    private $contraseña_usuario;

    #[ORM\Column(type:'string', name:'Descripcion')]
    private $descripcion;

    #[ORM\Column(type:'string', name:'Localidad')]
    private $localidad;

    #[ORM\Column(type: 'string', name: 'FechaNacimiento')]
    private $fecha_nacimiento;

    #[ORM\Column(type:'boolean', name:'UsuarioAdmin')]
    private $admin;

    #[ORM\OneToMany(targetEntity:'Comentario', mappedBy:'usuario')]
    private $comentarios;

    #[ORM\OneToMany(targetEntity: 'PostUsuario', mappedBy: 'usuario')]
private $postsUsuario;

    #[ORM\OneToMany(targetEntity: "Amistad", mappedBy: "usuario")]
    private $amistades;

    public function __construct()
    {
        $this->postsUsuario = new ArrayCollection();
        $this->comentarios = new ArrayCollection();
       $this->amistades = new ArrayCollection();
    }

    public function getUsuarioAdmin()
    {
       return $this->admin;
    }

    public function setUsuarioAdmin( $admin)
    {
        $this->admin = $admin;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function setIdUsuario( $id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    public function getNombreUsuario()
    {
        return $this->nombre_usuario;
    }

    public function setNombreUsuario( $nombre_usuario)
    {
        $this->nombre_usuario = $nombre_usuario;
    }

    public function getCorreoUsuario()
    {
        return $this->correo_usuario;
    }

    public function setCorreoUsuario( $correo_usuario)
    {
        $this->correo_usuario = $correo_usuario;
    }

    public function getContraseñaUsuario()
    {
        return $this->contraseña_usuario;
    }

    public function setContraseñaUsuario( $contraseña_usuario)
    {
        $this->contraseña_usuario = $contraseña_usuario;

        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion( $descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function getLocalidad()
    {
        return $this->localidad;
    }

    public function setLocalidad( $localidad)
    {
        $this->localidad = $localidad;
    }

    public function getFechaNacimiento()
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento( $fecha_nacimiento)
    {
        $this->fecha_nacimiento = $fecha_nacimiento;
    }

    public function getComentarios(): ArrayCollection
    {
        return $this->comentarios;
    }

    public function setComentarios(ArrayCollection $comentarios)
    {
        $this->comentarios = $comentarios;
    }

    public function getPostsUsuario(): ArrayCollection
    {
        return $this->postsUsuario;
    }

    public function setPostsUsuario(ArrayCollection $postsUsuario)
    {
        $this->postsUsuario = $postsUsuario;
    }

    public function getAmistades(): ArrayCollection
    {
        return $this->amistades;
    }

    public function setAmistades(ArrayCollection $amistades)
    {
        $this->amistades = $amistades;
    }

    public function getRoles(): array
    {
        if ($this->admin == 1) {
            return ['ROLE_USER', 'ROLE_ADMIN'];
        } else {
            return ['ROLE_USER'];
        }
    }

    public function getPassword(): string
    {
        return $this->contraseña_usuario;
    }

    public function getUserIdentifier(): string
    {
        return $this->nombre_usuario;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // No es necesario hacer nada aquí, pero el método debe ser implementado
    }

    public function addPost(PostUsuario $post): self
    {
        if (!$this->postsUsuario->contains($post)) {
            $this->postsUsuario[] = $post;
            $post->setUsuario($this);
        }

        return $this;
    }

    public function removePost(PostUsuario $post): self
    {
        if ($this->postsUsuario->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUsuario() === $this) {
                $post->setUsuario(null);
            }
        }

        return $this;
    }

    public function addComentario(Comentario $comentario): self
    {
        if (!$this->comentarios->contains($comentario)) {
            $this->comentarios[] = $comentario;
            $comentario->setUsuario($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): self
    {
        if ($this->comentarios->removeElement($comentario)) {
            // set the owning side to null (unless already changed)
            if ($comentario->getUsuario() === $this) {
                $comentario->setUsuario(null);
            }
        }

        return $this;
    }
}
