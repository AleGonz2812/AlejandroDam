<?php
$archivo_xml = "tareas.xml";

// Crear archivo XML si no existe
if (!file_exists($archivo_xml)) {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tareas></tareas>');
    $xml->asXML($archivo_xml);
}

$xml = simplexml_load_file($archivo_xml);

// Procesar nueva tarea
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["nueva_tarea"])) {
    $nuevaTarea = trim($_POST["nueva_tarea"]);
    if ($nuevaTarea !== "") {
        $xml->addChild("tarea", htmlspecialchars($nuevaTarea));
        $xml->asXML($archivo_xml);
        header("Location: index.php"); // redirige al index
        exit;
    }
}

// Mostrar lista
echo "<ul>";
foreach ($xml->tarea as $tarea) {
    echo "<li>" . htmlspecialchars($tarea) . "</li>";
}
echo "</ul>";
