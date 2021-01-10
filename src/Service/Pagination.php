<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;


class Pagination{

    // 1- Utiliser la pagination à partir de n'importe quelle entité / on devra préciser l'entité concernée

    private $entityClass;
    private $limit=6;
    private $currentPage = 1;

    private $manager;

    private $twig;
    private $route;

    private $templatePath;

    public function __construct(ObjectManager $manager,Environment $twig,RequestStack $request,$templatePath){
        
        $this->route = $request->getCurrentRequest()->attributes->get('_route');     
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    public function display(){
        // appel le moteur twig et on précise quel template on veut utiliser

        $this->twig->display($this->templatePath,[
            // options nécessaire à l'affichage des données
            // variables : route / page / pages
            'page'=>$this->currentPage,
            'pages'=>$this->getPages(),
            'route'=>$this->route
        ]);
    }

    public function setEntityClass($entityClass){

        // ma donnée entityClass = donnée qui va m'être envoyée

        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass(){

        return $this->entityClass;
    }

    // 2- Quelle est la limite ?

    public function setLimit($limit){
        
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    // 3- Sur quelle page je me trouve actuellement

    public function setPage($page){
        
        $this->currentPage = $page;

        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }

    public function getData(){

        if(empty($this->entityClass)){
            throw new \Exception("setEntityClass n'a pas été renseigné dans le controlleur correspondant");
        }

        // calculer l'offset

        $offset = $this->currentPage * $this->limit - $this->limit;
    

        // demande au repository de trouver les éléments
        // on va chercher le bon repository

     
        $repo = $this->manager->getRepository($this->entityClass);

        // on construit notre requete

        $data = $repo->findBy([],[],$this->limit,$offset);

        return $data;
    }

    public function getPages(){


        $repo = $this->manager->getRepository($this->entityClass);

        $total = count($repo->findAll());   
        
        $pages = ceil($total/$this->limit);

        return $pages;
    }

    public function setRoute($route){
        
        $this->route = $route;

        return $this;
    }

    public function getRoute(){
        return $this->route;
    }

    public function setTemplatePath($templatePath){
        
        $this->templatePath = $templatePath;

        return $this;
    }

    public function getTemplatePath(){
        return $this->templatePath;
    }
}