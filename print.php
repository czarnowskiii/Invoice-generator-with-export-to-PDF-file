<?php 
  require ("fpdf/fpdf.php");
  require ("word.php");
  require "connect.php"; 

  //customer and invoice details
  $info=[
    "customer"=>"",
    "address"=>",",
    "city"=>"",
    "nip"=>"",
    "invoice_nr"=>"",
    "invoice_date"=>"",
    "total_amt"=>"",
    "words"=>"",
  ];
  
  //Select Invoice Details From Database
  $sql="select * from invoice where SID='{$_GET["id"]}'";
  $res=$con->query($sql);
  if($res->num_rows>0){
	  $row=$res->fetch_assoc();
	  
	  $obj=new IndianCurrency($row["GRAND_TOTAL"]);
	 

	  $info=[
		"customer"=>$row["CNAME"],
		"address"=>$row["CADDRESS"],
		"city"=>$row["CCITY"],
    "nip"=>$row["NIP"],
		"invoice_nr"=>$row["INVOICE_NR"],
		"invoice_date"=>date("d-m-Y",strtotime($row["INVOICE_DATE"])),
		"total_amt"=>$row["GRAND_TOTAL"],
		"words"=> $obj->get_words(),
	  ];
  }
  
  //invoice Products
  $products_info=[];
  
  //Select Invoice Product Details From Database
  $sql="select * from invoice_products where SID='{$_GET["id"]}'";
  $res=$con->query($sql);
  if($res->num_rows>0){
	  while($row=$res->fetch_assoc()){
		   $products_info[]=[
			"name"=>$row["PNAME"],
			"price"=>$row["PRICE"],
			"qty"=>$row["QTY"],
      "vat"=>$row["VAT"]*100,
      "val_vat"=>$row["PRICE"]*$row["QTY"]*$row["VAT"],
			"total"=>$row["TOTAL"],
		   ];
	  }
  }
  
  class PDF extends FPDF
  {
    function Header(){
      
      //Display Company Info
      $this->SetFont('Arial','B',14);
      $this->Cell(50,10,"Firma Sp z.o.o.",0,1);
      $this->SetFont('Arial','',14);
      $this->Cell(50,7,"Ulica nr",0,1);
      $this->Cell(50,7,"XX-XX Miejscowosc",0,1);
      $this->Cell(50,7,"NIP: XXXXXXXX",0,1);
      
      //Display INVOICE text
      $this->SetY(15);
      $this->SetX(-40);
      $this->SetFont('Arial','B',18);
      //$this->Cell(50,10,"FAKTURA",0,0);
      $this->Cell(0,10,"FAKTURA",0,0,"R");
      //$this->Ln(50);
      $this->Image('logo512.png',173,25,20);
      
      //Display Horizontal line
      $this->Line(0,48,210,48);
    }
    
    function body($info,$products_info){
      
      //Billing Details
      $this->SetY(50);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(50,10,"Nabywca: ",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(50,7,$info["customer"],0,1);
      $this->Cell(50,7,$info["address"],0,1);
      $this->Cell(50,7,$info["city"],0,1);
      $this->Cell(50,7,"NIP:".$info["nip"],0,1);
      
      //Display Invoice nr
      $this->SetY(55);
      $this->SetX(-60);
      $this->Cell(50,7,"Numer faktury: ".$info["invoice_nr"]);
      
      //Display Invoice date
      $this->SetY(63);
      $this->SetX(-60);
      $this->Cell(50,7,"Data wystawienia: ".$info["invoice_date"]);
      
      //Display Table headings
      $this->SetY(95);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(50,9,"Nazwa towaru",1,0);
      $this->Cell(28,9,"Cena netto",1,0,"C");
      $this->Cell(24,9,"Ilosc",1,0,"C");
      $this->Cell(24,9,"VAT",1,0,"C");
      $this->Cell(32,9,"Wartosc VAT",1,0,"C");      
      $this->Cell(32,9,"Wartosc brutto",1,1,"C");
      $this->SetFont('Arial','',12);
      
      //Display table product rows
      foreach($products_info as $row){
        $this->Cell(50,9,$row["name"],"LR",0);
        $this->Cell(28,9,$row["price"],"R",0,"R");
        $this->Cell(24,9,$row["qty"],"R",0,"C");
        $this->Cell(24,9,$row["vat"]."%","R",0,"C");
        $this->Cell(32,9,$row["val_vat"],"R",0,"C");
        $this->Cell(32,9,$row["total"],"R",1,"R");
      }
      //Display table empty rows
      for($i=0;$i<12-count($products_info);$i++)
      {
        $this->Cell(50,9,"","LR",0);
        $this->Cell(28,9,"","R",0,"R");
        $this->Cell(24,9,"","R",0,"C");
        $this->Cell(24,9,"","R",0,"C");
        $this->Cell(32,9,"","R",0,"C");
        $this->Cell(32,9,"","R",1,"R");
      }
      //Display table total row
      $this->SetFont('Arial','B',12);
      $this->Cell(158,9,"Razem",1,0,"R");
      $this->Cell(32,9,$info["total_amt"],1,1,"R");
      
      //Display amount in words
      $this->SetY(225);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,9,"RAZEM SLOWNIE ",0,1);
      $this->SetFont('Arial','',12);
      $this->Cell(0,9,$info["words"],0,1);
      
    }
 
    function Footer(){
      
      //set footer position
      $this->SetY(-40);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,10,"................................................",0,0,"L");
      $this->Cell(0,10,"................................................",0,0,"R");
      $this->Ln(5);
      $this->SetFont('Arial','',10);
      $this->Cell(0,10,"Osoba uprawniona do odbioru",0,0,"L");
      $this->Cell(0,10,"Osoba uprawniona do wystawienia",0,0,"R");
     
      $this->SetFont('Arial','',10);
      
      //Display Footer Text
      $this->Ln(25);
     // $this->Cell(0,10,"Faktura wygenerowana automatycznie &#169",0,1,"C");
      $this->Cell(0,10,iconv("UTF-8", "ISO-8859-1", "©").'Konrad Czarnowski',0,1,'C',0);
      
    }
    
  }
  
  //Create A4 Page with Portrait 
  $pdf=new PDF("P","mm","A4");
  $pdf->AddPage();
  $pdf->body($info,$products_info);
  $pdf->Output();
  //$pdf->Output('F', 'C:\xampp\htdocs\invoice\faktura.pdf'.$info["invoice_nr"]);

  $fullname = "faktura-".$info["invoice_nr"]."-".$info["invoice_date"];

  $pdf_file_name = $fullname.".pdf";

$pdf->Output($pdf_file_name,'F');

  ?>