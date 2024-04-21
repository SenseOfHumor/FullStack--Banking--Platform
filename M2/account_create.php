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

// Fetching the account numbers for the logged-in user
$stmt = $db->prepare("SELECT account_number FROM Accounts WHERE user_id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Account creation logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $balance = floatval($_POST["balance"]); // Convert balance to float
    if ($balance < 5) {
        echo "<p style='color: white;'>Minimum deposit required is $5.</p>";
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

    .brand-title {
        margin-bottom: 20px;
        font-weight: 900;
        font-size: 1.8rem;
        color: #363636;
        letter-spacing: 1px;
    }

    input[type="number"] {
        background: #ecf0f3;
        padding: 10px;
        padding-left: 20px;
        height: 50px;
        font-size: 14px;
        border-radius: 50px;
        box-shadow: inset 6px 6px 6px #cbced1, inset -6px -6px 6px white;
        width: calc(100% - 40px);
        margin-bottom: 20px;
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
    }

    ul {
        margin-top: 20px;
    }

    ul li {
        list-style: none;
        margin-bottom: 10px;
    }
</style>

<div class="container">
    <div class="brand-title">Create Account</div>

    <p>You are creating your number: <?php echo htmlspecialchars($num_accounts + 1); ?> account</p>

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

    <?php if ($accounts): ?>
        <h2>Existing Accounts</h2>
        <p>Number of existing accounts: <?php echo htmlspecialchars($num_accounts); ?></p>
        <ul>
            <?php foreach ($accounts as $account): ?>
                <li><?php echo htmlspecialchars($account["account_number"]); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

