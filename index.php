<?php

header("Access-Control-Allow-Origin: *");

require_once 'vendor/autoload.php';

$aplicacion = new \Slim\Slim();

/* Variable para hacer consulta de la base de datos */
$base_datos = new mysqli("localhost", "root", "", "angular_backend");


/* Rutas para cargar diferentes contextos en el navegador */
/* metodo get */
$aplicacion->get('/pruebas', function() use($aplicacion, $base_datos) {

    echo "Saludando Desde Slim";
    
    /* funciona para mostrar los datos del metodo */
   var_dump($base_datos);
});





/* Mostrar todos los Productos con el metodo get */
    $aplicacion->get('/productos', function() use($aplicacion, $base_datos) {
        $sql = "SELECT * FROM productos ORDER BY id DESC;";
        $query = $base_datos->query($sql);
        /* query = ejecuta una sentencia sql */

        /* fetch_assoc = devuelve un array asociativo */


        /* bucle while para imprimir todos los productos */
        $productos = array();
        while($producto = $query->fetch_assoc()) {
            $productos [] = $producto;
        }

        $result = array (
           'status' => 'success',
            'code' => 200,
            'data' => $productos 
        );

        echo json_encode($result);
    
    });





/* Mostrar un solo Producto */
    $aplicacion->get("/productos/:id", function($id) use($aplicacion, $base_datos) {
        $sql = 'SELECT * FROM productos WHERE id = ' . $id;
        $query = $base_datos->query($sql);

        $result = array(
            'status' => 'error',
            'code' => '404',
            'message' => 'Producto No Encontrado'
        );

        if($query->num_rows == 1) {
            /* obtengo el numero de filas */

            $producto = $query->fetch_assoc();

            $result = array(
            'status' => 'success',
            'code' => '202',
            'data' => $producto
        );
        } 

        echo json_encode($result);  
    });



/* Eliminar Productos */
    $aplicacion->get('/delete/:id', function($id) use($aplicacion, $base_datos) {
        $sql = 'DELETE FROM productos WHERE id = ' . $id;
        $query = $base_datos->query($sql);

        if($query) {
            $result = array(
             'status' => 'success',
             'code' => '200',
             'message' => 'El Producto Se Elimino Correctamente'
            );
        }else {
            $result = array(
             'status' => 'error',
             'code' => '404',
             'message' => 'El Producto No Se Elimino'
            );
        }

        echo json_encode($result);
    });

/* Actualizar Productos */
    $aplicacion->post('/update/:id', function($id) use($aplicacion, $base_datos) {
        $json = $aplicacion->request->post('json');
        $data = json_decode($json, true);

        $sql = "UPDATE productos SET " .
                "nombre = '{$data["nombre"]}',".
                "description = '{$data["description"]}',".
                "precio = '{$data["precio"]}' WHERE id = {$id}";

        $query = $base_datos->query($sql);



        if($query) {
            $result = array(
             'status' => 'success',
             'code' => '200',
             'message' => 'El Producto Se actualiso Correctamente'
            );
        }else {
            $result = array(
             'status' => 'error',
             'code' => '404',
             'message' => 'El Producto No Se actualiso'
            );
        }

        echo json_encode($result);



    });

/* Subir una Imagen */
$aplicacion->post('/upload-files', function() use($aplicacion, $base_datos) {
    $result = array(
             'status' => 'error',
             'code' => '404',
             'message' => 'El Archivo No Se Ha Subido'
            );

            /* $_FILES = variable global de los archivos */
    if(isset($_FILES['subidas'])) {
            /* Creo una instancia de la libreria */
        $piramide = new PiramideUploader();

        /* Llamada al metodo */
        $upload = $piramide->upload('image', "subidas", "subidas", array('image/jpeg', 'image/png', 'image/gif'));
        /* 1- parametro el nombre de los archivo */
        /* 2- parametro el nombre que recibe en la key $_FILES */
       /*  3- parametro de la carpeta donde se guardara */
       /* 4- parametro el tipo de archivo */

       $files = $piramide->getInfoFile();
       $file_names = $files['complete_name'];

       var_dump($files);

       
    }

    echo json_encode($result);

});


/* Metodo post */
    /* Guardado De Productos */
 $aplicacion->post('/productos', function() use($aplicacion, $base_datos) {

    /* Hace un request al metodo post */
    $json = $aplicacion->request->post('json');
    $data = json_decode($json, true);
   /*  Decodifica el json */

    /* si algun campo llega vacio puede rellenarse con null o enviarse vacio */
   if(!isset($data['imagen'])) {
       $data['imagen'] = null;
   }

   if(!isset($data['description'])) {
       $data['description'] = null;
   }

   if(!isset($data['nombre'])) {
       $data['nombre'] = null;
   }

   if(!isset($data['precio'])) {
       $data['precio'] = null;
   }


   /*  Query para hacer la consulta a la base de datos */
    $query = "INSERT INTO productos VALUES(
             NULL,".
             "'{$data['nombre']}',".
             "'{$data['description']}',".
             "'{$data['precio']}',".
             "'{$data['imagen']}'".
            ")";

    $insert = $base_datos->query($query);
    /* insert a la base de datos con el query */

    /* Si no se agrega */
    $result = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'Producto No Se Agrego'
        );

        /* si insert es true */
    if($insert) {
        $result = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Producto Agregado'
        );
    }

    echo json_encode($result);

        
             
 });

/* Para correr toda la aplicacion */
$aplicacion->run();
