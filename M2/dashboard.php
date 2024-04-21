<?php
require(__DIR__ . "/partials/nav.php");




// Initialize database connection
$db = getDB();

// Fetch user's ID from the session
$user_id = $_SESSION["user"]["id"];

// Fetch user's ID from the database and set it to $user_id
$stmt = $db->prepare("SELECT id FROM Users WHERE id = :id");
$stmt->execute([":id" => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // User ID found in the database, assign it to $user_id
    $user_id = $user['id'];
}

function generateAccountNumber() {
    // Generate a random 12 character account number
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $account_number = '';
    for ($i = 0; $i < 12; $i++) {
        $account_number .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $account_number;
}

function isAccountNumberUnique($db, $account_number) {
    // Check if the generated account number is unique
    $stmt = $db->prepare("SELECT COUNT(*) AS count FROM Accounts WHERE account_number = :account_number");
    $stmt->execute([":account_number" => $account_number]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
}

function createCheckingAccount($db, $user_id, $balance) {
    // Create a checking account
    $account_number = generateAccountNumber();
    $account_type = "checking";
    $stmt = $db->prepare("INSERT INTO Accounts (account_number, user_id, balance, account_type) VALUES (:account_number, :user_id, :balance, :account_type)");
    $stmt->execute([":account_number" => $account_number, ":user_id" => $user_id, ":balance" => $balance, ":account_type" => $account_type]);
    return $account_number;
}

function recordTransaction($db, $account_src, $account_dest, $balance_change, $transaction_type, $memo, $expected_total) {
    // Record a transaction in the Transactions table
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = getDB();

    // Validate input
    $user_id = $_POST["user_id"];
    $balance = floatval($_POST["balance"]); // Convert balance to float
    if ($balance < 5) {
        echo "Minimum deposit required is $5.";
    } else {
        // Create checking account
        $account_number = createCheckingAccount($db, $user_id, $balance);

        // Record transaction with appropriate source account ID
        $source_account_id = 1; // Assuming '1' is the ID of the default source account
        recordTransaction($db, $source_account_id, $account_number, $balance, 'deposit', 'Initial deposit', $balance);

        // Redirect user to their Accounts page
        header("Location: accounts.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Account Information</title>
</head>
<body>
    <h2>View Account Information</h2>
    <form method="GET">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" required>
        <input type="submit" value="View Account">
    </form>
</body>
</html>