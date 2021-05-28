<?php
  $page_title = 'Lista de productos';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
  $products = join_product_table();
?>
<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
      
        <div class="panel-heading clearfix">
        
         <div class="pull-right">
           <a href="add_product.php" class="btn btn-primary">Agregar producto</a>
         </div>
         </div>
        <div class="panel-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center" style="width: 2em;"></th>
                <th class="text-center" style="width: 5%;"> Imagen </th>
                <th style="text-align: left; width: 20%">Producto</th>
                <th class="text-center" style="width: 10%;"> COD/PartNo </th>
                <th class="text-center" style="width: 10%;"> Categor&iacute;a </th>
                <th class="text-center" style="width: 10%;"> Stock </th>
                <th class="text-center" style="width: 10%;"> Ubicaci&oacute;n </th>

                

                <th class="text-center" style="width: 10%;"> Agregado </th>
                <th class="text-center" style=""> Acciones </th>
                <th class="text-center" style="width: 100px;"> Alertas </th>
              </tr>
            </thead>
            <tbody>

              <?php foreach ($products as $product):?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td>
                  <?php if($product['media_id'] === '0'): ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                  <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['image']; ?>" alt="">
                  <?php endif; ?>
                </td>
                <td> <?php echo remove_junk($product['name']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['partNo']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['categorie']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['quantity']); ?></td>

                <td class="text-center"> <?php echo remove_junk($product['location']); ?></td>
                <td class="text-center"> <?php echo read_date($product['date']); ?></td>

                <td class="text-center">
                  <div class="btn-group">
                    <a style="margin-right: 3pt" href="edit_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                      <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
                
                <?php 
                  $categorias=array("Tecnologia","computadora","Support");
                  $cantidad = array(100, 50, 300);
                  $arrlength=count($categorias);
                  
                  
                  for($i=0;$i<$arrlength;$i++)
                    {
                      if (remove_junk($product['categorie']) == $categorias[$i])
                      {
                        if (remove_junk($product['quantity']) < $cantidad[$i])
                        {
                          echo "<td class=\"text-center\" style=\"background-color: yellow; color: red;\">Nivel Bajo</td>";
                        }else{
                          echo "<td class=\"text-center\" style=\"background-color: green; color: white;\">Bueno</td>";
                        }
                      }
                      
                    
                    }
                  //endif; 
                ?>
              </tr>
             <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php include_once('layouts/footer.php'); ?>

  <script>  
 $(document).ready(function(){  
      $('#excel_file').change(function(){  
           $('#export_excel').submit();  
      });  
      $('#export_excel').on('submit', function(event){  
           event.preventDefault();  
           $.ajax({  
                url:"export.php",  
                method:"POST",  
                data:new FormData(this),  
                contentType:false,  
                processData:false,  
                success:function(data){  
                     $('#result').html(data);  
                     $('#excel_file').val('');  
                }  
           });  
      });  
 });  
 </script> 
