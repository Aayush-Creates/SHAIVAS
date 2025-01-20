<?php
$servername = "localhost";
$username = "root";
$password = "Aayush@967";
$dbname = "farm_inventory";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // Database created successfully or already exists
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    // Table created successfully or already exists
} else {
    die("Error creating table: " . $conn->error);
}

// Handle adding and deleting inventory items
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];

        $stmt = $conn->prepare("INSERT INTO inventory (name, quantity) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $quantity);

        if ($stmt->execute()) {
            echo "New item added successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Item deleted successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    if (isset($_POST['increase']) || isset($_POST['decrease'])) {
        $id = $_POST['id'];
        $quantityChange = isset($_POST['increase']) ? 1 : -1;

        // Get the current quantity
        $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($currentQuantity);
        $stmt->fetch();
        $stmt->close();

        // Update the quantity
        $newQuantity = $currentQuantity + $quantityChange;
        if ($newQuantity >= 0) {
            $stmt = $conn->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newQuantity, $id);

            if ($stmt->execute()) {
                echo "Quantity updated successfully";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

// Fetch inventory data
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);

$inventoryData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inventoryData[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Inventory Management</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f2; /* Light Beige */
            color: #4e342e; /* Earthy Brown */
        }
        .header {
            background: linear-gradient(90deg, #8bc34a, #558b2f); /* Fresh Green to Rich Green */
            color: white;
            padding: 1.5em;
            text-align: center;
            font-size: 1.8em;
        }
        .container {
            margin: 2em auto;
            max-width: 900px;
            background: #ffffff; /* White */
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-group {
            margin-bottom: 1em;
        }
        .form-group input {
            padding: 1em;
            width: calc(100% - 2.5em);
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            background-color: #f1f8e9; /* Very Light Green */
        }
        .form-actions {
            text-align: center;
        }
        .form-actions button {
            padding: 0.8em 2.5em;
            background-color: #8bc34a; /* Fresh Green */
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 0.5em;
        }
        .form-actions button:hover {
            background-color: #558b2f; /* Rich Green */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2em;
        }
        th {
            background-color: #aed581; /* Medium Green */
            color: white;
            padding: 1em;
        }
        td {
            padding: 1em;
            text-align: center;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f1f8e9; /* Very Light Green */
        }
        tr:nth-child(odd) {
            background-color: #ffffff; /* White */
        }
        .alert {
            color: red;
            font-weight: bold;
        }
        .delete-btn, .quantity-btn {
            color: white;
            border: none;
            padding: 0.5em;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .delete-btn {
            background-color: #f44336;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        .quantity-btn {
            background-color: #ffa500;
        }
        .quantity-btn:hover {
            background-color: #e65100;
        }
        
    </style>
</head>
<body>
    <div class="header">
        Farm Inventory Management
    </div>

    <div class="container">
        <div class="form-group">
            <label for="item">Item Name:</label>
            <input type="text" id="item" placeholder="Enter item name">
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" placeholder="Enter quantity">
        </div>

        <div class="form-actions">
            <button onclick="addItem()">Add Item</button>
            <button onclick="searchItem()">Search Item</button>
        </div>

        <h2>Inventory</h2>
        <table id="inventory">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="inventory-body">
                <!-- Items will be dynamically added here -->
                <?php
                foreach ($inventoryData as $item) {
                    echo "<tr><td>" . htmlspecialchars($item['name']) . "</td><td>" . htmlspecialchars($item['quantity']) . "</td><td>
                            <button onclick='changeQuantity(" . $item['id'] . ", \"increase\")' class='quantity-btn'>+</button>
                            <button onclick='changeQuantity(" . $item['id'] . ", \"decrease\")' class='quantity-btn'>-</button>
                            <button onclick='deleteItem(" . $item['id'] . ")' class='delete-btn'>Delete</button>
                          </td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function changeQuantity(id, operation) {
            fetch('inventory.php', {
                method: 'POST',
                body: new URLSearchParams({
                    'id': id,
                    [operation]: true
                })
            }).then(response => response.text()).then(result => {
                alert("Quantity updated successfully");
                location.reload();
            });
        }

        function deleteItem(id) {
            
                fetch('inventory.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        'id': id,
                        'delete': true
                    })
                }).then(response => response.text()).then(result => {
                    alert("Item deleted successfully");
                    location.reload();
                });
            
        }

        function searchItem() {
            const itemName = document.getElementById('item').value.toLowerCase();
            const rows = document.querySelectorAll("#inventory-body tr");
            rows.forEach(row => {
                const item = row.cells[0].textContent.toLowerCase();
                row.style.display = item.includes(itemName) ? '' : 'none';
            });
        }

        function addItem() {
            const itemName = document.getElementById('item').value;
            const itemQuantity = document.getElementById('quantity').value;

            if (!itemName || !itemQuantity) {
                alert("Please fill out both fields.");
                return;
            }

            fetch('inventory.php', {
                method: 'POST',
                body: new URLSearchParams({
                    'add': true,
                    'name': itemName,
                    'quantity': itemQuantity
                })
            }).then(response => response.text()).then(result => {
                alert("Item added successfully");
                location.reload();
            });
        }
    </script>
</body>
</html>
