<?
// Copyright 2011-~ Muammer TURKMEN
class sqlinj{
    private $gerideger;
    private $islet;
    public $liste=array("=","\'","\"","*","\-","declare","char","set","cast","convert","drop","exec","meta","script","select","truncate","insert","delete","union","update","create","where","join","information_schema","table_schema","into");
    public function basla($veri,$tur="normal"){
        if($tur=="normal"){
            return self::normal($veri);
        }elseif($tur=="all"){
            return self::tumsorgular($veri);
        }else{
            return self::req($tur,$veri);
        }
    }
    private function normal($deger){
        foreach($this->liste as $bul){
            $deger=str_replace($bul,'\\'.$bul.'\\',$deger);
            
        }
        return $deger;
    }
    private function tumsorgular($yapilacak){
            switch ($yapilacak){
            case "post":
            $this->islet=array("POST");
            break;
            case "get":
            $this->islet=array("GET");
            break;
            case "request":
            $this->islet=array("REQUEST");
            break;
            case "aio":
            $this->islet=array("POST","GET","REQUEST");
            break;
        }    
        foreach($this->islet as $islem){
        eval('foreach($_'.$islem.' as $ad=>$deger){
            $_'.$islem.'[$ad]=$deger;
            foreach($this->liste as $bul){
            $_'.$islem.'[$ad]=str_replace($bul,"\\\".$bul."\\\",$_'.$islem.'[$ad]);
            }
        }
        
            
return $_'.$islem.';
');
        }
    }
    private function req($deger,$method){
        switch ($method){
            case "post":
            $this->islet=$_POST[$deger];
            break;
            case "get":
            $this->islet=$_GET[$deger];
            break;
            case "request":
            $this->islet=$_REQUEST[$deger];
            break;
        }    
        foreach($this->liste as $bul){
            $this->islet=str_replace($bul,'\\'.$bul.'\\',$this->islet);
            
        }
        return $this->islet;    
    }
    public function ekleme($eklenecek){
        $this->liste[]=$eklenecek;
    }    
}
