<!DOCTYPE html>
<html>
<head>
    <title>Informacion Zaragoza</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>
    <script src="js/map.js"></script>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<?php
/**o
 * IP de la conexion y otras variables
 */
date_default_timezone_set('UTC');
$ipaddress = $_SERVER['REMOTE_ADDR'];
$fecha = date("y-m-d", time());
?>

<div class="contenedor">
    <div class="lateral">
        <fieldset>
            <legend>Llegar a una estacion de Bizi</legend>

            <!-- Cuadro de texto para introducir la direccion -->
            <form name="formulario" method="post">
                <div class="element">
                    <input id="start" type="text" style="width: 320px" placeholder="Direccion de comienzo">
                </div>
                <div class="element">
                    <select id="estacionesBizi" name="estacion">
                    </select>
                </div>
                <!-- Selector JSON/XML para bici-->
                <div class="element2">
                    <input type="radio" name="extension" id="extension1" value="JSON">
                    <label for="JSON">JSON</label>

                    <input type="radio" name="extension" id="extension2" value="XML">
                    <label for="XML">XML</label>
                </div>
                <div class="element">
                    <input type="button" onClick=
                    <?php
                    /**
                     * Hace la llamada a la funcion JS "calcRoute()" y genera un nuevo
                     * objeto parse para el control estadistico
                     */
                    include 'accionParse.php';
                    try
                    {
                        $extension = " ";

                        if(isset($_POST["extension"]))
                            $extension = $_POST["extension"];

                        $accion = "servicio_bici";
                        $estado = "correcto";

                        echo "calcRoute();";

                        guardarAccionUsuario($ipaddress, $accion, $extension, $estado, $fecha);
                    }
                    catch(Exception $e)
                    {
                        $extension = " ";
                        $accion = "servicio_bici";
                        $estado = "error";

                        echo "Error mostrando ruta";

                        guardarAccionUsuario($ipaddress, $accion, $extension, $estado, $fecha);
                    }

                    ?>
                    style="margin-left:40%;" value="Buscar ruta">
                </div>
            </form>

        </fieldset>

        </br>

        <!-- Boton de acceso al control estadistico -->
        <input type="button" onClick="location.href='controlEstadistico.php'" id="botonControl" value="Control estad&iacute;stico">
    </div>

    <form action="index.php" method="post">
        <div class="otrolado">
            <fieldset>
                <legend>Prevision meteorologica</legend>

                <!-- Gestion de la tabla del tiempo -->
                <div class="element">
                    <?php
                    /**
                     * Se encarga de mostrar la tabla del tiempo de la localidad elegida.
                     * Tambien crea un nuevo objeto parse para el control estadistico.
                     */
                    try{
                        include 'weather.php';

                        $extension = "";
                        $localidad = "";

                        if(isset($_POST["extensionMeteo"]))
                            $extension = $_POST["extensionMeteo"];
                        if(isset($_POST["localidad"]))
                            $localidad = $_POST["localidad"];

                        $accion = "prevision_metereologica";

                        if(($extension != "") && $localidad != "")
                        {
                            //LLAMADA A FUNCION EN WEATHER.PHP
                            echo obtenerTiempo($extension, $localidad, $ipaddress, $accion, $fecha);

                            //ESTADISTICAS
                            $estado = "correcto";
                            guardarAccionUsuario($ipaddress, $accion, $extension, $estado, $fecha);
                        }
                        else
                        {
                            //PLACEHOLDER
                            echo "<img src=\"img/prevision.png\" alt=\"Elije una poblacion\">";
                        }
                    }
                    catch(Exception $e)
                    {
                        echo "Error conectando con el servicio AEMET";

                        //ESTADISTICAS
                        $estado = "error";
                        guardarAccionUsuario($ipaddress, $accion, $extension, $estado, $fecha);
                    }
                    ?>
                </div>

                <!-- Leemos el fichero CSV -->
                <div class="element">
                    <?php
                    $handle = fopen(__DIR__ . "/resources/codigos.csv", "r");
                    echo "<select name=\"localidad\">";
                    if ($handle !== FALSE)
                    {
                        $row = 0;
                        while ( ($data = fgetcsv($handle, 133, ",")) !== FALSE )
                        {
                            $substring = $data[0].substr($data[1],0,3);
                            printf('<option value="%s">%s</option>', $substring, $data[2]);
                        }
                        fclose($handle);
                    }
                    echo "</select>";
                    ?>

                </div>

                <!-- Selector JSON/XML para meteo-->
                <div class="element2">
                    <input type="radio" name="extensionMeteo" id="extensionMeteo1" value="JSON">
                    <label for="JSON">JSON</label>

                    <input type="radio" name="extensionMeteo" id="extensionMeteo2" value="XML">
                    <label for="XML">XML</label>
                </div>

                <div class="element">
                    <input type="submit" value="Obtener informacion meteorologica">
                </div>

            </fieldset>
        </div>
    </form>

    <div class="principal">
        <fieldset>
            <legend>Google maps</legend>

            <!-- Mapa GMaps -->
            <div id="googleMap">
                <script src="js/map.js"></script>
            </div>

        </fieldset>
    </div>
</div>
<div class="pie">
    &#169; 2015 Alejandro Galvez y Sandra Campos
</div>
</body>
</html>