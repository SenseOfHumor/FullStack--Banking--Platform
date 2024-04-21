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
        echo "<p>No account found for the selected account number.</p>";
    }
}
?>

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
