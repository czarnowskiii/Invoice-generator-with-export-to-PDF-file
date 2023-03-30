<?php 
//Convert Number to Indian Currency Format
class IndianCurrency{
	
  public function __construct($amount){
    $this->amount=$amount;
    $this->hasgrosz=false;
    $arr=explode(".",$this->amount);
    $this->PLN=$arr[0];
    if(isset($arr[1])&&((int)$arr[1])>0){
      if(strlen($arr[1])>2){
        $arr[1]=substr($arr[1],0,2);
      }
      $this->hasgrosz=true;
      $this->grosz=$arr[1];
    }
  }
  
  public function get_words(){
    $w="";
    $crore=(int)($this->PLN/10000000);
    $this->PLN=$this->PLN%10000000;
    $w.=$this->single_word($crore,"Cror ");
    $lakh=(int)($this->PLN/100000);
    $this->PLN=$this->PLN%100000;
    $w.=$this->single_word($lakh,"Tysiace ");
    $thousand=(int)($this->PLN/1000);
    $this->PLN=$this->PLN%1000;
    $w.=$this->single_word($thousand,"tysiac  ");
    $hundred=(int)($this->PLN/100);
    $w.=$this->single_word($hundred,"sto ");
    $ten=$this->PLN%100;
    $w.=$this->single_word($ten,"");
    $w.="PLN ";
    if($this->hasgrosz){
      if($this->grosz[0]=="0"){
        $this->grosz=(int)$this->grosz;
      }
      else if(strlen($this->grosz)==1){
        $this->grosz=$this->grosz*10;
      }
      $w.=" i ".$this->single_word($this->grosz," grosze");
    }
    return $w;
  }

  private function single_word($n,$txt){
    $t="";
    if($n<=19){
      $t=$this->words_array($n);
    }else{
      $a=$n-($n%10);
      $b=$n%10;
      $t=$this->words_array($a)." ".$this->words_array($b);
    }
    if($n==0){
      $txt="";
    }
    return $t." ".$txt;
  }

  private function words_array($num){
    $n=[0=>"", 1=>"Jeden", 2=>"Dwa", 3=>"Trzy", 4=>"Cztery", 5=>"Piec", 6=>"Szesc", 7=>"Siedem", 8=>"Osiem", 9=>"Dziewiec", 10=>"Dziesiec", 11=>"Jednascie", 12=>"Dwanascie", 13=>"Trzynascie", 14=>"Czternascie", 15=>"Pietnascie", 16=>"Szesnascie", 17=>"Siedemnascie", 18=>"Osiemnascie", 19=>"Dziewietnascie", 20=>"Dwadziescia", 30=>"Trzydziesci", 40=>"Czterdziesci", 50=>"Piecdziesiat", 60=>"Szesdziesiat", 70=>"Siedemdziesiat", 80=>"Osiemdziesiat", 90=>"Dziewiedziesiat", 100=>"Sto",];
    return $n[$num];
  }
}
?>