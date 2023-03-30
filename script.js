$(document).ready(function(){
    $("#date").datepicker({
      dateFormat:"dd-mm-yy"
    });

    $("#btn-add-row").click(function(){
          var row="<tr> <td><input type='text' required name='pname[]' class='form-control'></td> <td><input type='text' required name='price[]' class='form-control price'></td> <td><input type='text' required name='qty[]' class='form-control qty'></td> <td><select class='selectVal' required name='vat[]' ><option value='0.23'>23%</option><option value='0.08'>8%</option></td><td><input type='text' required name='tara[]' class='form-control tara' readonly></td><td><input type='text' required name='total[]' class='form-control total' readonly></td> <td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'> </td> </tr>";
          $("#product_tbody").append(row);
    });
    $("body").on("click",".btn-row-remove",function(){
          if(confirm("Are You Sure?")){
            $(this).closest("tr").remove();
            grand_total();
          }
    });
    $("body").on("keyup",".price",function(){
          var price=Number($(this).val());
          var qty=Number($(this).closest("tr").find(".qty").val());
          $(this).closest("tr").find(".total").val((price*qty*(selectedItemNUM+1)).toFixed(2));
          $(this).closest("tr").find(".tara").val((price*qty*selectedItemNUM).toFixed(2));
          grand_total();
    });
        
    $("body").on("keyup",".qty",function(){
          var qty=Number($(this).val());
          var price=Number($(this).closest("tr").find(".price").val());
          $(this).closest("tr").find(".total").val((price*qty*(selectedItemNUM+1)).toFixed(2));
          $(this).closest("tr").find(".tara").val((price*qty*selectedItemNUM).toFixed(2));
          grand_total();
    });      

    
      $("body").on("click",".selectVal",function(){


              let selectedItem = $(this).children("option:selected").val();
              selectedItemNUM=parseFloat(selectedItem);
              console.log( selectedItemNUM+1);
              var qty=Number($(this).closest("tr").find(".qty").val());
              var price=Number($(this).closest("tr").find(".price").val());
              $(this).closest("tr").find(".total").val((price*qty*(selectedItemNUM+1)).toFixed(2));
              $(this).closest("tr").find(".tara").val((price*qty*selectedItemNUM).toFixed(2))
              grand_total();
      });

  
      
    function grand_total(){
          var tot=0;
          $(".total").each(function(){
            tot+=Number($(this).val());
          });
          tot2=tot.toFixed(2);
          $("#grand_total").val(tot2);

          
        }
    
  });

  

  $(document).ready(function() { 
    $("#hide").click(function() { 
        $(this).hide(); 
    }); 
  });
      

      

