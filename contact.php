<?php
require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv($line);
    }
}

$mongoUri = getenv('MONGO_URI') ?: 'mongodb://localhost:27017';
$dbName = getenv('MONGO_DB') ?: 'marketsite';
$collectionName = getenv('MONGO_COLLECTION') ?: 'contact_submissions';
$allowedOrigin = getenv('ALLOWED_ORIGIN') ?: '*';

header("Access-Control-Allow-Origin: $allowedOrigin");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['name']) || !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid payload']);
    exit;
}

try {
    $client = new MongoDB\Client($mongoUri);
    $collection = $client->{$dbName}->{$collectionName};

    $doc = [
        'name' => trim($data['name']),
        'email' => trim($data['email']),
        'message' => isset($data['message']) ? trim($data['message']) : '',
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $result = $collection->insertOne($doc);

    $insertedId = (string)$result->getInsertedId();

    $smtpHost = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $smtpUser = getenv('SMTP_USER') ?: '';
    $smtpPass = getenv('SMTP_PASS') ?: '';
    $smtpPort = getenv('SMTP_PORT') ?: 587;
    $emailFrom = getenv('EMAIL_FROM') ?: $smtpUser;
    $emailTo = getenv('EMAIL_TO') ?: $smtpUser;

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int)$smtpPort;

        $mail->setFrom($emailFrom, 'MarketSite');
        $mail->addAddress($emailTo);

        $mail->isHTML(true);
        $mail->Subject = 'New contact submission from MarketSite';
        $mail->Body = '<p><strong>Name:</strong> ' . htmlspecialchars($doc['name']) . '</p>' .
                      '<p><strong>Email:</strong> ' . htmlspecialchars($doc['email']) . '</p>' .
                      '<p><strong>Message:</strong> ' . nl2br(htmlspecialchars($doc['message'])) . '</p>' .
                      '<p><strong>ID:</strong> ' . $insertedId . '</p>';

        $mail->send();
    } catch (Exception $e) {
        error_log('Mail error: ' . $e->getMessage());
    }

    echo json_encode(['success' => true, 'id' => $insertedId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>