<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Use POST.']);
  exit;
}

function field(string $key): string {
  $value = $_POST[$key] ?? '';
  if (!is_string($value)) {
    return '';
  }
  return trim($value);
}

$name = field('name');
$email = field('email');
$phone = field('phone');
$topic = field('topic');
$message = field('message');
$formName = field('form-name');

if ($name === '' || $email === '' || $message === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Name, email, and a short note are required.']);
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Please enter a valid email.']);
  exit;
}

$to = 'LKBCounseling@outlook.com';
$subject = 'Consultation request from LKB Counseling preview';
$safeForm = $formName !== '' ? $formName : 'inquiry';

$body = "A consultation request was sent from the LKB Counseling preview.\n\n";
$body .= "Form: {$safeForm}\n";
$body .= "Name: {$name}\n";
$body .= "Email: {$email}\n";
$body .= "Phone: {$phone}\n";
$body .= "Topic: {$topic}\n\n";
$body .= "Message:\n{$message}\n";

$headers = [
  'From: LKB Counseling Preview <noreply@' . ($_SERVER['HTTP_HOST'] ?? 'coryc26.sg-host.com') . '>',
  'Reply-To: ' . $email,
  'Content-Type: text/plain; charset=UTF-8',
  'X-Mailer: PHP/' . phpversion(),
];

$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

if (!$sent) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'The server could not send mail.']);
  exit;
}

echo json_encode(['ok' => true]);
