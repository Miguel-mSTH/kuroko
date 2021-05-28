<?php  
 //export.php  
 require_once('includes/load.php');
 require_once('PHPExcel.php');

 if(!empty($_FILES["excel_file"]))  
 {  
      $con = mysqli_connect("localhost", "root", "", "almacen");  
      $file_array = explode(".", $_FILES["excel_file"]["name"]);  
      if($file_array[1] == "xls")  
      {  
           include("PHPExcel/IOFactory.php");  
           $output = '';  
           $output .= "  
           <label class='text-success'>Data Inserted</label>  
                <table class='table table-bordered'>  
                     <tr bgcolor='blue'>
                         <th>Id</th>    
                          <th>Customer Name</th>  
                          <th>Address</th>  
                          <th>City</th>  
                          <th>Postal Code</th>  
                          <th>Country</th>  
                     </tr>  
                     ";  
           $object = PHPExcel_IOFactory::load($_FILES["excel_file"]["tmp_name"]);
           foreach($object->getWorksheetIterator() as $worksheet)  
           {  
                $highestRow = $worksheet->getHighestRow();  
                for($row=2; $row<=$highestRow; $row++)  
                {
                     
                    $id=mysqli_real_escape_string($con, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
                    $name = mysqli_real_escape_string($con, $worksheet->getCellByColumnAndRow(1, $row)->getValue()); 
                    $address = mysqli_real_escape_string($con, $worksheet->getCellByColumnAndRow(2, $row)->getValue());  
                    $city = mysqli_real_escape_string($con, $worksheet->getCellByColumnAndRow(3, $row)->getValue());   
                    $postal_code = mysqli_real_escape_string($con, $worksheet->getCellByColumnAndRow(4, $row)->getValue());   
                    $country = mysqli_real_escape_string($con, $worksheet->getCellByColumnAndRow(5, $row)->getValue());

                     $query="select count(*) as contador from tbl_customer where CustomerID='".$id."'";
                     $resultado=$con->query($query)or die($con->error);
                     $repuesta=$resultado->fetch_assoc();
                     if ($id==""){

                         
                         
                     }elseif($repuesta['contador']=='0'){
                         $consulta = "  
                         INSERT INTO tbl_customer  
                         (CustomerID, CustomerName, Address, City, PostalCode, Country)   
                         VALUES ('".$id."','".$name."', '".$address."', '".$city."', '".$postal_code."', '".$country."')  
                         ";  
                         mysqli_query($con, $consulta);  
                         $output .= '  
                         <tr>  
                              <td>'.$id.'</td>
                              <td>'.$name.'</td>  
                              <td>'.$address.'</td>  
                              <td>'.$city.'</td>  
                              <td>'.$postal_code.'</td>  
                              <td>'.$country.'</td>  
                         </tr>  
                         '; 

                     }
                      
                }  
           }  
           $output .= '</table>';  
           echo $output;  
      }  
      else  
      {  
          echo '<label class="text-danger">Archivo no compatible</label>';
      }  
 }  
 ?>  