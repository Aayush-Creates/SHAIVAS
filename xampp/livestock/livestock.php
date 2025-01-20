<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "livestock_management";

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
$sql = "CREATE TABLE IF NOT EXISTS livestock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    comments TEXT
)";

if ($conn->query($sql) === TRUE) {
    // Table created successfully or already exists
} else {
    die("Error creating table: " . $conn->error);
}

// Handle adding and deleting livestock records
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $quantity = $_POST['quantity'];
        $comments = $_POST['comments'] ?? '';

        $stmt = $conn->prepare("INSERT INTO livestock (name, quantity, comments) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $quantity, $comments);

        if ($stmt->execute()) {
            echo "";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM livestock WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    if (isset($_POST['increase']) || isset($_POST['decrease'])) {
        $id = $_POST['id'];
        $quantityChange = isset($_POST['increase']) ? 1 : -1;

        // Get the current quantity
        $stmt = $conn->prepare("SELECT quantity FROM livestock WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($currentQuantity);
        $stmt->fetch();
        $stmt->close();

        // Update the quantity
        $newQuantity = $currentQuantity + $quantityChange;
        if ($newQuantity >= 0) {
            $stmt = $conn->prepare("UPDATE livestock SET quantity = ? WHERE id = ?");
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

// Fetch livestock data
$sql = "SELECT * FROM livestock";
$result = $conn->query($sql);

$livestockData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $livestockData[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livestock Management</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f7;
            color: #333;
        }
        .header {
            background: linear-gradient(90deg, #8bc34a, #558b2f);
            color: white;
            padding: 1.5em;
            text-align: center;
            font-size: 1.8em;
        }
        .container {
            margin: 2em auto;
            max-width: 900px;
            background: #ffffff;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form {
            margin-bottom: 2em;
        }
        .form input {
            margin: 0.5em 0;
            padding: 1em;
            width: calc(100% - 2.5em);
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
        }
        .form button {
            padding: 0.8em 2.5em;
            background-color: #8bc34a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 0.5em;
        }
        .form button:hover {
            background-color: #558b2f;
        }
        .livestock-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1em;
        }
        .livestock-table th, .livestock-table td {
            border: 1px solid #ddd;
            padding: 1em;
            text-align: left;
        }
        .livestock-table td {
            vertical-align: middle; /* Ensures text is vertically aligned */
        }
        .livestock-table th {
            background-color: #f9f9f9;
        }
        .livestock-name {
    margin: 0; /* Ensures no extra spacing */
}

        .popup {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            padding: 1em;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
        }
        .popup.show {
            display: block;
        }
        .popup-message {
            color: #333;
            font-size: 1em;
        }
        .alert {
            color: red;
            font-weight: bold;
        }
        .delete-btn {
            color: white;
            background-color: #f44336;
            border: none;
            padding: 0.5em;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            margin: 0.3em;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        .search {
            margin-bottom: 2em;
        }
        .search input {
            padding: 1em;
            width: calc(100% - 2.5em);
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
        }
        /* Remove gaps between records */
        .livestock-table tr {
            margin: 0; /* Remove any margin */
        }
    </style>
</head>
<body>
    <div class="header">
        Livestock Management Dashboard
    </div>
    <div class="container">
        <div class="form">
            <h2>Add Livestock</h2>
            <input type="text" id="name" placeholder="Enter Livestock Name">
            <input type="number" id="quantity" placeholder="Enter Quantity">
            <input type="text" id="comments" placeholder="Add Comments (Optional)">
            <button onclick="addLivestock()">Add</button>
        </div>
        <div class="search">
            <h2>Search Livestock</h2>
            <input type="text" id="search" placeholder="Search by Name" oninput="searchLivestock()">
        </div>
        <h2>Livestock Inventory</h2>
        <table class="livestock-table" id="livestockTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Comments</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Initially empty, will be populated by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Popup for Record Update (Creation, Deletion, and Quantity Update) -->
    <div class="popup" id="popup">
        <div class="popup-message" id="popupMessage">Record updated successfully.</div>
    </div>

    <script>
        let livestockData = []; // Start with an empty array

        function addLivestock() {
            const name = document.getElementById('name').value.trim();
            const quantity = parseInt(document.getElementById('quantity').value);
            const comments = document.getElementById('comments').value.trim();

            if (!name || isNaN(quantity) || quantity <= 0) {
                alert('Please enter valid livestock details.');
                return;
            }

            livestockData.push({ name, quantity, comments });
            document.getElementById('name').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('comments').value = '';
            showPopup(`${name} added successfully.`);
            renderTable();
        }

        function deleteLivestock(index) {
            const name = livestockData[index].name;
            livestockData.splice(index, 1);
            showPopup(`${name} deleted successfully.`);
            renderTable();
        }

        function increaseQuantity(index) {
            livestockData[index].quantity++;
            showPopup(`${livestockData[index].name} quantity increased by 1.`);
            renderTable();
        }

        function decreaseQuantity(index) {
            if (livestockData[index].quantity > 0) {
                livestockData[index].quantity--;
                showPopup(`${livestockData[index].name} quantity decreased by 1.`);
                renderTable();
            } else {
                alert('Quantity cannot be less than 0');
            }
        }

        function searchLivestock() {
            const query = document.getElementById('search').value.trim().toLowerCase();
            const filteredData = livestockData.filter(item => item.name.toLowerCase().includes(query));
            renderTable(filteredData);
        }

        function showPopup(message) {
            const popup = document.getElementById('popup');
            const popupMessage = document.getElementById('popupMessage');
            popupMessage.textContent = message;
            popup.classList.add('show');
            setTimeout(() => {
                popup.classList.remove('show');
            }, 2000); // Hide after 2 seconds
        }

        function renderTable(data = livestockData) {
            const tableBody = document.getElementById('livestockTable').querySelector('tbody');
            tableBody.innerHTML = '';

            data.forEach((item, index) => {
                const row = document.createElement('tr');

                const nameCell = document.createElement('td');
                nameCell.classList.add('livestock-name');
                nameCell.textContent = item.name;
                row.appendChild(nameCell);

                const quantityCell = document.createElement('td');
                quantityCell.textContent = item.quantity;
                row.appendChild(quantityCell);

                const commentsCell = document.createElement('td');
                commentsCell.textContent = item.comments || 'N/A';
                row.appendChild(commentsCell);

                const actionCell = document.createElement('td');
                const increaseButton = document.createElement('button');
                increaseButton.textContent = 'Increase';
                increaseButton.className = 'delete-btn';
                increaseButton.onclick = () => increaseQuantity(index);
                actionCell.appendChild(increaseButton);

                const decreaseButton = document.createElement('button');
                decreaseButton.textContent = 'Decrease';
                decreaseButton.className = 'delete-btn';
                decreaseButton.onclick = () => decreaseQuantity(index);
                actionCell.appendChild(decreaseButton);

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.className = 'delete-btn';
                deleteButton.onclick = () => deleteLivestock(index);
                actionCell.appendChild(deleteButton);

                row.appendChild(actionCell);

                tableBody.appendChild(row);
            });
        }

        // Initial render
        renderTable();
    </script>
</body>
</html>
