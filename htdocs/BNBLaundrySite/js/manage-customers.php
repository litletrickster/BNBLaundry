<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["staff_id"])) {
    header("Location: staff-login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Delete Customer
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $conn->query("DELETE FROM Customers WHERE Customer_ID = $delete_id");
    header("Location: manage-customers.php");
    exit();
}

// Fetch customers and their last transaction from Orders table
$query = "
    SELECT c.Customer_ID, c.Name, c.Phone_Number, c.Email, 
           (SELECT MAX(Date_Created) FROM Orders o WHERE o.Customer_ID = c.Customer_ID) AS Last_Transaction
    FROM Customers c
    ORDER BY c.Name ASC";
$customers = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers | BnB Laundry</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4fbff; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; }
        
        /* Top Bar */
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .top-bar button { background: #00a8e8; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .top-bar button:hover { background: #007bb5; }
        .search-box { padding: 8px; border: 1px solid #ccc; border-radius: 5px; width: 200px; }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #00a8e8;
            color: white;
            padding-top: 20px;
            position: fixed;
            left: 0;
            top: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 18px;
            transition: 0.3s;
        }
        .sidebar ul li a:hover {
            background: #007bb5;
            border-radius: 5px;
        }
        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #00a8e8; color: white; }
        .action-btn { padding: 5px 10px; border: none; cursor: pointer; border-radius: 5px; text-decoration: none; font-size: 14px; }
        .edit-btn { background: #007bb5; color: white; }
        .delete-btn { background: #e74c3c; color: white; }
        .delete-btn:hover { background: #c0392b; }
        .edit-btn:hover { background: #005f8d; }

        /* Footer */
        footer { text-align: center; margin-top: 20px; padding: 15px; background: white; border-radius: 10px; box-shadow: 0px -2px 4px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Customers</h2>

    <!-- Top Bar -->
    <div class="top-bar">
        <button onclick="window.location.href='add-customer.php'">Add Customer</button>
        <input type="text" class="search-box" placeholder="Search Customers...">
    </div>
<!-- Sidebar -->
<div class="sidebar">
    <h2>BnB Laundry</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="record-transactions.php">Record Orders</a></li>
        <li><a href="manage-customers.php">Manage Customers</a></li>
        <li><a href="manage-services.php">Manage Services</a></li>
        <li><a href="expense-tracking.php">Track Expenses</a></li>
        <li><a href="generate-reports.php">Generate Reports</a></li>
        <li><a href="create-announcement.php">Create Announcements</a></li>
    </ul>
</div>
    <!-- Customers Table -->
    <table>
        <tr>
            <th>Customer ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Last Transaction</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($customers)) { ?>
            <?php foreach ($customers as $customer) { ?>
                <tr>
                    <td><?= $customer["Customer_ID"] ?></td>
                    <td><?= $customer["Name"] ?></td>
                    <td><?= $customer["Email"] ?></td>
                    <td><?= $customer["Phone_Number"] ?></td>
                    <td>
                        <?= !empty($customer["Last_Transaction"]) ? date('Y-m-d', strtotime($customer["Last_Transaction"])) : "No Transactions" ?>
                    </td>
                    <td>
                        <a href="edit-customer.php?edit_id=<?= $customer['Customer_ID'] ?>" class="action-btn edit-btn">Edit</a>
                        <a href="manage-customers.php?delete_id=<?= $customer['Customer_ID'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="6" style="text-align:center;">No customers available</td>
            </tr>
        <?php } ?>
    </table>
</div>

<footer>
    &copy; 2025 BnB Laundry. All Rights Reserved.
</footer>

</body>
</html>
