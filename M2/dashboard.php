<?php
require(__DIR__ . "/partials/nav.php");

// Initialize database connection
$db = getDB();

// Fetch the logged-in user's ID from the session
$user_id = $_SESSION["user"]["id"];

// Fetching the user's ID from the database
$stmt = $db->prepare("SELECT id FROM Users WHERE id = :id");
$stmt->execute([":id" => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // User ID found in the database, assign it to $user_id
    $user_id = $user['id'];
}

// Fetching the number of existing accounts for the logged-in user
$stmt = $db->prepare("SELECT COUNT(*) AS num_accounts FROM Accounts WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$num_accounts = $stmt->fetch(PDO::FETCH_ASSOC)["num_accounts"];

// Account creation logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $balance = floatval($_POST["balance"]); // Convert balance to float
    if ($balance < 5) {
        echo "Minimum deposit required is $5.";
    } else {
        // Create checking account
        $account_number = generateAccountNumber();
        $account_type = "checking";
        $stmt = $db->prepare("INSERT INTO Accounts (account_number, user_id, balance, account_type) VALUES (:account_number, :user_id, :balance, :account_type)");
        $stmt->execute([
            ":account_number" => $account_number,
            ":user_id" => $user_id,
            ":balance" => $balance,
            ":account_type" => $account_type
        ]);

        // Record transaction with appropriate source account ID
        $source_account_id = 1; // Assuming '1' is the ID of the default source account
        recordTransaction($db, $source_account_id, $account_number, $balance, 'deposit', 'Initial deposit', $balance);

        // Redirect user to their Accounts page
        header("Location: accounts.php");
        exit();
    }
}

// Function to generate a random account number
function generateAccountNumber() {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $account_number = '';
    for ($i = 0; $i < 12; $i++) {
        $account_number .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $account_number;
}

// Function to record a transaction
function recordTransaction($db, $account_src, $account_dest, $balance_change, $transaction_type, $memo, $expected_total) {
    $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total, created) VALUES (:account_src, :account_dest, :balance_change, :transaction_type, :memo, :expected_total, NOW())");
    $stmt->execute([
        ":account_src" => $account_src,
        ":account_dest" => $account_dest,
        ":balance_change" => $balance_change,
        ":transaction_type" => $transaction_type,
        ":memo" => $memo,
        ":expected_total" => $expected_total
    ]);
}
?>

<h2>Create Account</h2>

<p>You are creating your #<?php echo htmlspecialchars($num_accounts + 1); ?> account</p>

<form method="POST">
    <div>
        <label for="user_id">User ID</label>
        <input type="number" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" placeholder="User ID" disabled>
    </div>
    <div>
        <label for="balance">Balance</label>
        <input type="number" step="0.01" id="balance" name="balance" placeholder="Balance" required>
    </div>
    <input type="submit" value="Create Account">
</form>

<?php
// Fetching the account numbers for the logged-in user
$stmt = $db->prepare("SELECT account_number FROM Accounts WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($accounts): ?>
    <h2>Existing Accounts</h2>
    <p>Number of existing accounts: <?php echo htmlspecialchars($num_accounts); ?></p>
    <ul>
        <?php foreach ($accounts as $account): ?>
            <li><?php echo htmlspecialchars($account["account_number"]); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
