<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'amistad')]
class Amistad
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer', name: 'IdAmistad')]
    private $id_amistad;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Usuario')]
#[ORM\JoinColumn(name: 'IdUsuario1', referencedColumnName: 'IdUsuario')]
private $usuario1;

#[ORM\ManyToOne(targetEntity: 'App\Entity\Usuario')]
#[ORM\JoinColumn(name: 'IdUsuario2', referencedColumnName: 'IdUsuario')]
private $usuario2;

    #[ORM\Column(type: 'string', name: 'Estado')]
    private $estado;

    public function getId()
    {
        return $this->id_amistad;
    }
    

    public function setIdAmistad($id_amistad)
    {
        $this->id_amistad = $id_amistad;
    }

    public function getUsuario1()
    {
        return $this->usuario1;
    }

    public function setUsuario1($usuario1)
    {
        $this->usuario1 = $usuario1;
    }

    public function getUsuario2()
    {
        return $this->usuario2;
    }

    public function setUsuario2($usuario2)
    {
        $this->usuario2 = $usuario2;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
}
