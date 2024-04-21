<?php
require(__DIR__ . "/partials/nav.php");

// Initialize database connection
$db = getDB();

// Check if the account number is provided in the URL query parameters
if(isset($_GET["account_number"])) {
    $account_number = $_GET["account_number"];
    
    // Fetch account information for the provided account number
    $stmt = $db->prepare("SELECT * FROM Accounts WHERE account_number = :account_number");
    $stmt->execute([":account_number" => $account_number]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if($account) {
?>
        <h2>Account Information</h2>
        <table>
            <thead>
                <tr>
                    <th style="padding: 8px; border-bottom: 1px solid #ddd;">Account Number</th>
                    <th style="padding: 8px; border-bottom: 1px solid #ddd;">Account Type</th>
                    <th style="padding: 8px; border-bottom: 1px solid #ddd;">Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($account['account_number']); ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($account['account_type']); ?></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($account['balance']); ?></td>
                </tr>
            </tbody>
        </table>
<?php
    } else {
        echo "<p>No account found for the provided account number.</p>";
    }
} else {
    // If account number is not provided, display a form to enter the account number
?>
    <h2>View Account Information</h2>
    <form method="GET">
        <label for="account_number">Enter Account Number:</label>
        <input type="text" id="account_number" name="account_number" required>
        <input type="submit" value="View Account">
    </form>
<?php
}
?>
