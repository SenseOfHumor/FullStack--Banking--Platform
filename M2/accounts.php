<?php
require(__DIR__ . "/partials/nav.php");

// Initialize database connection
$db = getDB();

// Fetching the user's account numbers
$user_id = $_SESSION["user"]["id"];

// Fetch the user's account numbers from the Accounts table
$stmt = $db->prepare("SELECT account_number FROM Accounts WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetching account information for the selected account, if available
if(isset($_GET["selected_account"])) {
    $selected_account = $_GET["selected_account"];
    
    // Fetch account information for the selected account
    $stmt = $db->prepare("SELECT * FROM Accounts WHERE account_number = :account_number");
    $stmt->execute([":account_number" => $selected_account]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if($account) {
?>
        <style>
            body {
                background: url('bg1.jpg') no-repeat center center fixed;
                margin: 0;
                width: 100vw;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Poppins', sans-serif;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th, td {
                text-align: left;
                padding: 8px;
                border-bottom: 1px solid #ddd;
            }

            th {
                background-color: #f2f2f2;
            }

            tr:hover {
                background-color: #f2f2f2;
            }
        </style>

        <div class="container">
            <h2>Account Information</h2>
            <table>
                <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Account Type</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($account['account_number']); ?></td>
                        <td><?php echo htmlspecialchars($account['account_type']); ?></td>
                        <td><?php echo htmlspecialchars($account['balance']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
<?php
    } else {
        echo "<p>No account found for the selected account number.</p>";
    }
}
?>

<style>
    body {
        background: url('bg1.jpg') no-repeat center center fixed;
        margin: 0;
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
    }

    .container {
        width: 350px;
        padding: 40px;
        background: #ecf0f3;
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
        margin-bottom: 20px;
        font-weight: 900;
        font-size: 1.8rem;
        color: #363636;
        letter-spacing: 1px;
    }

    form {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        color: #363636;
    }

    select {
        background: #ecf0f3;
        padding: 10px;
        height: 50px;
        font-size: 14px;
        border-radius: 50px;
        box-shadow: inset 6px 6px 6px #cbced1, inset -6px -6px 6px white;
        width: calc(100% - 40px);
        margin-bottom: 20px;
        border: none;
    }

    input[type="submit"] {
        background-color: #1DA1F2;
        border: none;
        border-radius: 20px;
        color: #fff;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        padding: 10px;
        width: calc(100% - 40px);
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #0d8ae5;
    }

    p {
        margin-bottom: 20px;
        font-size: 1rem;
        color: #363636;
    }
</style>

<div class="container">
    <?php if (!$accounts): ?>
        <p>No accounts found.</p>
    <?php else: ?>
        <h2>Select Account</h2>
        <form method="GET">
            <label for="selected_account">Select Account:</label>
            <select id="selected_account" name="selected_account" required>
                <option value="" selected disabled>Select an account</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?php echo htmlspecialchars($account['account_number']); ?>"><?php echo htmlspecialchars($account['account_number']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="View Account">
        </form>
    <?php endif; ?>
</div>
