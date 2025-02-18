This is a project made on the topic of agriculture.

we're aiming to make a efficient website that is useful for the farmers.

In this branch i've added two sections of the main website 
that are namely livestock management and inventory management.

# Livestock Management System

This project is a web-based **Livestock Management System** that allows users to manage livestock data. Users can add, delete, search, and update livestock records with an intuitive dashboard interface. 

## Features

### Backend
- Built with PHP and MySQL for handling server-side functionality.
- Features include:
  - Creation of database and table if they do not exist.
  - Addition, deletion, and update of livestock records.
  - Validation of input data.
  - Alert messages for errors and success events.

### Frontend
- Built using HTML, CSS, and JavaScript.
- Features include:
  - A clean and responsive dashboard interface.
  - Ability to search for livestock by name.
  - Interactive buttons to increase, decrease, or delete records.
  - Popup notifications for successful operations.

## Prerequisites
1. **Server Requirements**
   - PHP (version 7.0 or higher recommended)
   - MySQL Server
2. **Web Browser**
   - A modern web browser (e.g., Chrome, Firefox).

## Installation

1. Clone or download the repository.
2. Place the project files in your server's root directory (e.g., `htdocs` for XAMPP).
3. Start your web server and MySQL server.
4. Access the project in your browser using:
   ```
   http://localhost/<project-folder-name>/
   ```

## Usage

### Adding Livestock
1. Navigate to the **Add Livestock** section.
2. Fill in the details for livestock (Name, Quantity, and optional Comments).
3. Click the **Add** button to save the record.

### Searching Livestock
1. Use the search bar under the **Search Livestock** section.
2. Enter the name of the livestock to filter the inventory table.

### Updating Livestock Quantity
1. Locate the livestock record in the inventory table.
2. Click the **Increase** or **Decrease** button to adjust the quantity.

### Deleting Livestock
1. Locate the livestock record in the inventory table.
2. Click the **Delete** button to remove the record.

## File Structure

```plaintext
.
├── index.php          # Main application file
├── style.css          # Embedded CSS for styling
└── README.md          # Documentation file (this file)
```

## Technical Notes

### Database Details
- Database: `livestock_management`
- Table: `livestock`
  - Columns:
    - `id` (INT, Primary Key, Auto Increment)
    - `name` (VARCHAR, NOT NULL)
    - `quantity` (INT, NOT NULL)
    - `comments` (TEXT)

### JavaScript Functionality
- Dynamic rendering of the livestock table.
- Popup notifications for user actions.
- Input validation for adding livestock.

### CSS Styling
- Responsive design with a clean and modern look.
- Styles include:
  - Linear gradient for the header.
  - Shadow effects for containers and buttons.
  - Table layout for displaying inventory.

## Possible Enhancements
1. Implement user authentication for secure access.
2. Add the ability to export data to CSV or Excel.
3. Include analytics or reports for livestock trends.

# Farm Inventory Management System

This project is a **Farm Inventory Management System**, a web application for managing and tracking inventory items. The system allows users to add, delete, and update inventory items, as well as search for specific items. It uses PHP and MySQL for the backend, and HTML, CSS, and JavaScript for the frontend.

## Features

- **Add Inventory Items:** Users can add new items with a name and quantity.
- **Delete Items:** Users can delete items from the inventory.
- **Update Quantity:** Users can increase or decrease the quantity of existing items.
- **Search Functionality:** Users can search for items by name.
- **Dynamic Table:** The inventory is displayed dynamically with real-time updates.

## Requirements

- **Web Server:** Apache, Nginx, or any other PHP-compatible server
- **PHP:** Version 7.0 or higher
- **MySQL:** Version 5.6 or higher
- **Browser:** Modern browser like Chrome, Firefox, Edge, or Safari

## Installation

1. Clone the repository or download the source code:
   ```bash
   git clone <repository-url>
   ```

2. Place the files in the web server directory (e.g., `htdocs` for XAMPP or `www` for WAMP).

3. Create a MySQL database named `farm_inventory` or use the included code to create it automatically.

4. Update the database credentials in the PHP file if needed:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "your_password";
   $dbname = "farm_inventory";
   ```

5. Start the web server and MySQL server.

6. Open the application in your browser:
   ```
   http://localhost/<project-directory>/
   ```

## File Structure

```
project-directory/
│
├── index.php          # Main application file (HTML, CSS, and JavaScript included)
├── inventory.php      # PHP file for handling backend operations (database interaction)
├── style.css          # CSS file for styling (optional if extracted)
└── README.md          # Documentation file
```

## Usage

1. **Add Items:**
   - Enter the item name and quantity in the input fields.
   - Click the `Add Item` button to add the item to the inventory.

2. **Delete Items:**
   - Click the `Delete` button next to an item to remove it from the inventory.

3. **Update Quantity:**
   - Use the `+` and `-` buttons to increase or decrease the quantity of an item.

4. **Search Items:**
   - Enter the name of an item in the search field to filter the inventory table.

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Styling Frameworks:** Custom CSS with a focus on usability and simplicity

## Example Screenshots

![Inventory Dashboard]((!(![Screenshot 2025-02-18 204106](https://github.com/user-attachments/assets/fe74e8f2-64e3-4046-b5ae-65bc9e93f95a)
)
)
)
![Livestock Dashboard](!(!(![Screenshot 2025-02-18 204312](https://github.com/user-attachments/assets/eee66cdf-c53a-49c5-b1b5-101c25a42143)
)
)
)

---

### Notes

- Ensure the database connection details match your environment.
- Run the application in a local or live server environment to use the PHP and MySQL features.
- You can expand the functionality by adding features like user authentication or detailed item categorization.

The entire guide to use the parent website is available on main branch.
Do check that out!
