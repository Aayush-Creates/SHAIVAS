<?php
$conn = new mysqli("localhost", "root", "Aayush@967", "farm_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$crop_name = $_POST['crop_name'];
$sql = "INSERT INTO crops (name) VALUES ('$crop_name')";

if ($conn->query($sql) === TRUE) {
    echo "Crop added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
