<?php

class Media {

  public $imageInfo;
  public $fileName;
  public $fileType;
  public $fileTempPath;

  //Set destination for upload
  /* === changed by Yoel, at 2019.03.11 === */
  //public $userPath = SITE_ROOT . DS . '..' . DS . 'uploads/users';
  public $userPath;
  //public $productPath = SITE_ROOT . DS . '..' . DS . 'uploads/products';
  public $productPath;

  public $errors = array();
 	/* 
 	// English.-
  public $upload_errors = array(
    0 => 'There was no error, the file was uploaded with success',
    1 => 'The file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The file was only partially uploaded',
    4 => 'No file was uploaded',
    5 => 'The path to contain the file is undefined',
    6 => 'Missing a temporary folder',
    7 => 'Failed when writing file to disk.',
    8 => 'A PHP extension stopped the file upload.'
  );
	*/
	# Spanish.-
  public $upload_errors = array(
    0 => 'No hubo error, el archivo se subió con éxito',
    1 => 'El tamaño de archivo excede la directiva upload_max_filesize en php.ini',
    2 => 'El tamaño de archivo excede la directiva MAX_FILE_SIZE directive especificada en el formulario HTML',
    3 => 'El archivo fue subido parcialmente',
    4 => 'Ningun archivo fue subido',
    5 => 'No se ha definido la ruta para guardar el archivo',
    6 => 'Se perdió una carpeta temporal',
    7 => 'Falló la escritura del archivo en disco',
    8 => 'Una extensión PHP detuvo la carga del archivo'
  );
  public $upload_extensions = array(
   'gif',
   'jpg',
   'jpeg',
   'png',
  );
  
	/* === added by Yoel.- 2019.03.11 === 
	 * Constructor
	 */
	public function __construct() {
		$this->set_paths( );
	}
 
 	/* === added by Yoel.- 2019.03.11 === */
	public function set_paths() {
		//$this->userPath = SITE_ROOT . DS . 'uploads/users';
		//$this->productPath = SITE_ROOT . DS . '..' . DS . 'uploads/products';

    /* Yoel changed the value for the constant SITE_ROOT */
    $this->userPath = SITE_ROOT . DS . 'uploads/users';
    $this->productPath = SITE_ROOT . DS . 'uploads/products';    
	}
	
  public function file_ext($filename) {
     $ext = strtolower(substr( $filename, strrpos( $filename, '.' ) + 1 ) );
     if(in_array($ext, $this->upload_extensions)) {
       return true;
     }
   }

  public function upload($file)
  {
    if( !$file || empty($file) || !is_array($file) ) {
      $this->errors[] = "Ningún archivo subido.";
      return false;
     }
    elseif( $file['error'] != 0 ) {
      $this->errors[] = $this->upload_errors[$file['error']];
      return false;
    }
    elseif( !$this->file_ext($file['name']) ) {
      $this->errors[] = 'Formato de archivo incorrecto ';
      return false;
    }
    else {
      $this->imageInfo = getimagesize($file['tmp_name']);
      $this->fileName  = basename($file['name']);
      $this->fileType  = $this->imageInfo['mime'];
      $this->fileTempPath = $file['tmp_name'];
     	return true;
   	}
  }
	/*--------------------------------------------------------------
   * Generic function to process 
	 *--------------------------------------------------------------*/
	public function process() {
    if( !empty($this->errors) ) {
      return false;
    }
    elseif( empty($this->fileName) || empty($this->fileTempPath) || empty($this->productPath) ) {
      $this->errors[] = "La ubicación del archivo no esta disponible.";
      return false;
    }
    elseif( !is_writable($this->productPath) ){
      $this->errors[] = "Debe tener permisos de escritura";
      return false;
    }
    elseif(file_exists($this->productPath."/".$this->fileName)) {
      $this->errors[] = "El archivo {$this->fileName} ya existe.";
      return false;
    }
    else {
    	return true;
    }
 	}
	/*--------------------------------------------------------------
	 * Function to process media file
	 *--------------------------------------------------------------*/
  public function process_media() 
  {
    if( !empty($this->errors) ) {
      return false;
    }
    if( empty($this->fileName) || empty($this->fileTempPath) || empty($this->productPath) ) {
      $this->errors[] = "La ubicación del archivo no se encuenta disponible.";
      return false;
    }
    if( !is_writable($this->productPath) ) {
      $this->errors[] = "Debe tener permisos de escritura. ";
      return false;
    }
    if( file_exists($this->productPath.DS.$this->fileName) ) {
      $this->errors[] = "El archivo {$this->fileName} ya existe.";
      return false;
    }
    if( move_uploaded_file($this->fileTempPath,$this->productPath.'/'.$this->fileName) ) {
	    if( $this->insert_media() ) {
	      unset($this->fileTempPath);
	      return true;
	    }
    } 
    else {
      $this->errors[] = "Error en la carga del archivo, posiblemente debido a permisos incorrectos en la carpeta de carga.";
      return false;
    }
  }
  /*--------------------------------------------------------------*/
  /* Function to process user image
  /*--------------------------------------------------------------*/
 	public function process_user($id) 
 	{
    if(!empty($this->errors)){
        return false;
      }
    if(empty($this->fileName) || empty($this->fileTempPath)){
        $this->errors[] = "La ubicación del archivo no estaba disponible.";
        return false;
      }
    if(!is_writable($this->userPath)){
        $this->errors[] ="Debe tener permisos de escritura.";
        return false;
      }
    if(!$id){
      $this->errors[] = "ID de usuario ausente.";
      return false;
    }
    //$ext = explode(".",$this->fileName);
    //$new_name = randString(8).$id.'.' . end($ext);

    $ext = file_ext( $this->fileName );
    $new_name = randString(8).$id.'.' . $ext;

    $this->fileName = $new_name;
    if($this->user_image_destroy($id)) {
    if( move_uploaded_file($this->fileTempPath,$this->userPath.'/'.$this->fileName) ) {
			if($this->update_userImg($id)) {
			 unset($this->fileTempPath);
			 return true;
			}
       } else {
         $this->errors[] = "Error en la carga del archivo, posiblemente debido a permisos incorrectos en la carpeta de carga.";
         return false;
       }
  	}
 	}
	/*--------------------------------------------------------------*/
	/* Function to update user image
	/*--------------------------------------------------------------*/
  private function update_userImg($id){
    global $db;
    $sql = "UPDATE users SET";
    $sql .=" image='{$db->escape($this->fileName)}'";
    $sql .=" WHERE id='{$db->escape($id)}'";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
   }
	/*--------------------------------------------------------------*/
	/* Function to delete the old image from the server
	/*--------------------------------------------------------------*/
  public function user_image_destroy($id) 
  {
		$image = find_by_id('users',$id);
		if($image['image'] === 'no_image.jpg')
		{
		 return true;
		} else {
		 unlink($this->userPath.'/'.$image['image']);
		 return true;
		}
   }
	/*--------------------------------------------------------------*/
	/* Function to insert media image
	/*--------------------------------------------------------------*/
  private function insert_media()
  {
		global $db;
		$sql  = "INSERT INTO media ( file_name,file_type )";
		$sql .=" VALUES ";
		$sql .="(
			'{$db->escape($this->fileName)}',
			'{$db->escape($this->fileType)}'
    )";
    return ($db->query($sql) ? true : false);
  }
	/*--------------------------------------------------------------*/
	/* Function to delete media by id
	/*--------------------------------------------------------------*/
  public function media_destroy($id,$file_name){
		$this->fileName = $file_name;
		if(empty($this->fileName)) {
			$this->errors[] = "Falta el archivo de foto.";
			return false;
 		}
		if(!$id){
		 	$this->errors[] = "ID de foto ausente.";
		 	return false;
		}
		if(delete_by_id('media',$id)){
			unlink($this->productPath.'/'.$this->fileName);
			return true;
		} else {
			$this->error[] = "Se ha producido un error en la eliminación de fotografías.";
			return false;
		}
  }
}

?>