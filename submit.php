<?php
/**
 * Three Small Prints - Form Handler
 * Sends replacement requests to nathan.ash@gmail.com
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Collect and sanitize input
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$prints = $_POST['prints'] ?? [];
$notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Validate required fields
if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Invalid submission');
}

// Build email content
$subject = "TPN Print Request from {$name}";
$body = "New replacement request:\n\n";
$body .= "Name: {$name}\n";
$body .= "Email: {$email}\n\n";
$body .= "Requested items:\n";

if (is_array($prints) && !empty($prints)) {
    foreach ($prints as $print) {
        $body .= "- " . filter_var($print, FILTER_SANITIZE_FULL_SPECIAL_CHARS) . "\n";
    }
} else {
    $body .= "- (not specified)\n";
}

if ($notes) {
    $body .= "\nNotes:\n{$notes}\n";
}

$body .= "\n--\nThree Small Prints\nhttps://ompster.github.io/three-small-prints";

// Send email
$headers = "From: Three Small Prints <noreply@yourdomain.com>\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail('nathan.ash@gmail.com', $subject, $body, $headers)) {
    // Success - redirect with thank you
    header('Location: /three-small-prints/?submitted=1');
    exit;
} else {
    http_response_code(500);
    exit('Failed to send. Please email nathan.ash@gmail.com directly.');
}
