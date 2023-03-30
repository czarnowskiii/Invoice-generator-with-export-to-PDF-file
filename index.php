<?php 
require "connect.php"; 
?>


<?php
 $query = "SELECT * FROM `invoice`";
 $ilosc_faktur = $con->query($query) or die($con->error.__LINE__);
 $sum_inv = $ilosc_faktur->num_rows;
 $next=$sum_inv+1;

?>
<!DOCTYPE html>
<html lang="pl">

  <head>
    <title>Faktury</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Generator Faktur ONLINE">
     <meta name="keywords" content="faktury, invoice, generator, dla, firm, for, companies">
     <meta name="author" content="Konrad Czarnowski">
    
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="script.js"></script>

    <link rel='stylesheet' href='https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css'>
    <script src="https://code.jquery.com/ui/1.13.0-rc.3/jquery-ui.min.js" integrity="sha256-R6eRO29lbCyPGfninb/kjIXeRjMOqY3VWPVk6gMhREk=" crossorigin="anonymous"></script>
    
  </head>
<body>


<div class='container pt-5'>
  <?php
    

    if(isset($_POST["submit"])){
      $invoice_nr=$_POST["invoice_nr"];
      $invoice_date=date("Y-m-d",strtotime($_POST["invoice_date"]));
      $cname=mysqli_real_escape_string($con,$_POST["cname"]);
      $caddress=mysqli_real_escape_string($con,$_POST["caddress"]);
      $ccity=mysqli_real_escape_string($con,$_POST["ccity"]);
      $cnip=mysqli_real_escape_string($con,$_POST["cnip"]);
      $grand_total=mysqli_real_escape_string($con,$_POST["grand_total"]);

      $sql="insert into invoice (INVOICE_NR,INVOICE_DATE,CNAME,CADDRESS,NIP,CCITY,GRAND_TOTAL) values ('{$invoice_nr}','{$invoice_date}','{$cname}','{$caddress}','{$cnip}','{$ccity}','{$grand_total}') ";
      if($con->query($sql)){
        $sid=$con->insert_id;
        
        $sql2="insert into invoice_products (SID,PNAME,PRICE,QTY,TOTAL,VAT) values ";
        $rows=[];

        for($i=0;$i<count($_POST["pname"]);$i++)
        {
          $pname=mysqli_real_escape_string($con,$_POST["pname"][$i]);
          $price=mysqli_real_escape_string($con,$_POST["price"][$i]);
          $qty=mysqli_real_escape_string($con,$_POST["qty"][$i]);
          $total=mysqli_real_escape_string($con,$_POST["total"][$i]);
          $vat=mysqli_real_escape_string($con,$_POST["vat"][$i]);
          $rows[]="('{$sid}','{$pname}','{$price}','{$qty}','{$total}','{$vat}')";
        }
        $sql2.=implode(",",$rows);
        if($con->query($sql2)){
          echo "<div class='alert alert-success' id='hide'>Invoice Added Successfully. <a href='print.php?id={$sid}' target='_BLANK'>Click </a> here to Print Invoice </div> ";
        }else{
          echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
        }
      }else{
        echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
      }
      header("Refresh:10; url=index.php");
    }
 
 
 ?>
   <form method="post" action="index.php" autocomplete="off">
     <div class='row'>
      <div class='col-md-4'>
        <h5 class="text-success">Szczegóły faktury</h5>
          <div class='form-group'>
            <label>Numer faktury</label>
            <input type="number" value="<?php echo $next ?>" name='invoice_nr' required class='form-control'>
          </div>

          <div class='form-group'>
            <label>Data faktury</label>
            <input type='text' name='invoice_date' id='date' required class='form-control'>
          </div>
          

      </div>

      <div class='col-md-8'>
        <h5 class="text-success">Dane kupującego</h5>
        <div class='form-group'>
          <label>Imię i nazwisko</label>
          <input type='text' name='cname'  required class='form-control'>
        </div>

        <div class='form-group'>
          <label>Adres</label>
          <input type='text' name='caddress'  required class='form-control'>
        </div>

        <div class='form-group'>
          <label>Miasto</label>
          <input type='text' name='ccity'  required class='form-control'>
        </div>
        <div class='form-group'>
          <label>NIP</label>
          <input type='number' name='cnip'  required class='form-control'>
        </div>

      </div>
    </div>
    <div class='row'>
      <div class='col-md-12'>
        <h5 class="text-success">Dane produktu</h5>
        
        <table class="table table-bordered">
            <thead>
              <tr>
                <th>Produkt</th>
                <th>Cena netto</th>
                <th>Ilość</th>
                <th>Stawka VAT</th>
                <th>Wartość VAT</th>
                <th>Razem brutto</th>
                <th>Usuń</th>
              </tr>
            </thead>
            <tbody id="product_tbody">
                <tr>
                  <td><input type="text" required name="pname[]" class="form-control"></td>
                  <td><input type="text" required name="price[]" class="form-control price"></td>
                  <td><input type="text" required name="qty[]" class="form-control qty"></td>
                  <td>
                      <select class="selectVal" class="form-select" required name="vat[]" >
                      <option value="1">-</option>
                        <option value="0.23">23%</option>
                        <option value="0.08">8%</option>
                  </td>
                  <td><input type="text" required name="tara[]" class="form-control tara" readonly></td>
                  <td><input type="text" required name="total[]" class="form-control total"  readonly></td>
                  <td><input type="button" value="x" class="btn btn-danger btn-sm btn-row-remove"></td>
                </tr>
            </tbody>
            <tfoot>
              <td><input type="button" value="+ Add row" class="btn btn-primary btn-sm" id="btn-add-row"></td>
              <td colspan="4" class="text-right">Total</td>
              <td><input type="text" name="grand_total" id="grand_total" class="form-control" required readonly></td>
            </tfoot>
        </table>
        <input type="submit" name="submit" value="Zapisz fakturę" class="btn btn-success float-right">
      </div>
    </div>
  </form>
</div>


<script>

</script>

</body>

</html>