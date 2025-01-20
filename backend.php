<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $standCount = isset($_POST['standCount']) ? (float) $_POST['standCount'] : 0;
    $rowLength = isset($_POST['rowLength']) ? (float) $_POST['rowLength'] : 0;
    $rowSpacing = isset($_POST['rowSpacing']) ? (float) $_POST['rowSpacing'] : 0;

    if ($standCount > 0 && $rowLength > 0 && $rowSpacing > 0) {
        $rowSpacingFeet = $rowSpacing / 12; // Convert inches to feet
        $plantsPerAcre = ($standCount / $rowLength) * (43560 / $rowSpacingFeet);
        echo json_encode(["population" => round($plantsPerAcre)]);
    } else {
        echo json_encode(["error" => "Invalid input values"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
