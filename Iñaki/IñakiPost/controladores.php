  ##Copia el controladores en tu BaseRedSocial, esta es la ruta que pilla el botón "Crear Post"

#[Route('/crear_post', name: 'crearPost')]
    public function CrearPost(){
        return $this->render('post.html.twig');
    }
