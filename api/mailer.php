<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$secretKey = $_ENV['SECRET_KEY']; 

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['captcha'])) {
    echo json_encode(["success" => false, "message" => "Captcha non fourni."]);
    exit;
}

$captchaResponse = $data['captcha'];

// Vérifier si la méthode de la requête est bien POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupère les données du formulaire
    $data = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $firstname = $data['firstname'];
        $lastname = $data['lastname'];
        $email = $data['email'];
    
    } else {
        echo json_encode(["success" => false, "message" => "Erreur de décodage JSON"]);
    }

    // Sécurisation des données saisies
    $lastname = htmlspecialchars(trim($data['lastname']));
    $firstname = htmlspecialchars(trim($data['firstname']));
    $company = htmlspecialchars(trim($data['company']));
    $email = htmlspecialchars(trim($data['email']));
    $number = htmlspecialchars(trim($data['number']));
    $message = htmlspecialchars(trim($data['message']));

    // Vérifier les champs obligatoires
    if (!empty($lastname) && !empty($firstname) && !empty($email) && !empty($message)) {
        // Valider l'adresse e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "message" => "Adresse e-mail invalide. ❌"]);
            exit;
        }

        // Configuration de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP avec les variables .env
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->CharSet = 'UTF-8';  
            $mail->SMTPDebug = 0;  
            $mail->Debugoutput = 'html';

            // Informations concernant l'expéditeur et le destinataire
            $mail->setFrom($_ENV['SMTP_USERNAME'], "$firstname $lastname");
            $mail->addAddress($_ENV['RECEIVER_EMAIL'], 'Destinataire');

            // Contenu de l'e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Nouveau message du formulaire de contact 📩';
            $mail->Body = "
                <h3>Nom : $lastname</h3>
                <h3>Prénom : $firstname</h3>
                <h4>Entreprise : $company</h4>
                <h4>Email : $email</h4>
                <h4>Numéro de téléphone : $number</h4>
                <p>Message : $message</p>
            ";
            $mail->AltBody = "Nom: $lastname\nPrénom: $firstname\nEntreprise: $company\nEmail: $email\nNuméro de téléphone: $number\nMessage: $message"; 

            // Envoi du mail
            $mail->send();
            echo json_encode(["success" => true, "message" => "Message envoyé avec succès ! 🎉"]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Erreur lors de l'envoi : {$mail->ErrorInfo} 😞"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Veuillez remplir tous les champs obligatoires. ⚠️"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Méthode non autorisée. 🚫"]);
}
?>