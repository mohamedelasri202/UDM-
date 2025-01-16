<?php
   

abstract class cours {
    protected $id;
    protected $title ;
    protected $description ;
    protected $price ;

    public function __construct($id , $title ,$description ,$price )
    {
        $this->id=$id;
        $this->title=$title;
        $this->description=$description;
        $this->price = $price ;
        
    }
    abstract function addcours();




}

class textcours extends cours { 
    
    public function __construct($)

     function addcours(){

     }

}







