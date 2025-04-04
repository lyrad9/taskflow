<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once "../../../vendor/autoload.php";
/* require __DIR__ . '/../vendor/autoload.php'; */


class EmailService {
    private $mailer;
    private $defaultFromEmail;
    private $defaultFromName;

    /**
     * Initialise le service d'email avec les paramètres SMTP
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);     
        $this->defaultFromEmail = "mbakopngako@gmail.com";
        $this->defaultFromName = "CK-project";

        try {
            // Configuration du serveur SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->defaultFromEmail;
            $this->mailer->Password = "fcsa jnit ilmc iioa";
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = "587";            
            
            // Paramètres par défaut
            $this->mailer->setFrom($this->defaultFromEmail, $this->defaultFromName);
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
        } catch (Exception $e) {
            die("Erreur SMTP : " . $e->getMessage());
        }
    }

    /**
     * Envoie un email standard
     * 
     * @param string $email Adresse email du destinataire
     * @param string $sujet Sujet de l'email
     * @param string $contenu Contenu de l'email
     * @param bool $isHtml Définit si le contenu est au format HTML
     * @return string Message de succès ou erreur
     */
    public function sendMail($email, $sujet, $contenu, $isHtml = true) {
        try {
            $this->mailer->clearAddresses(); // Nettoyer les destinataires précédents
            $this->mailer->addAddress($email);
            $this->mailer->Subject = $sujet;

            // Contenu
            $this->mailer->isHTML($isHtml);
            $this->mailer->Body = $contenu;
            if ($isHtml) {
                $this->mailer->AltBody = strip_tags($contenu);
            }

            $this->mailer->send();
            return "Email envoyé avec succès à $email";
        } catch (Exception $e) {
            return "Erreur lors de l'envoi de l'email : " . $this->mailer->ErrorInfo;
        }
    }

    /**
     * Envoie un email avec les identifiants de connexion à un nouveau membre
     * 
     * @param string $email Adresse email du destinataire
     * @param string $firstName Prénom du membre
     * @param string $lastName Nom du membre
     * @param string $username Nom d'utilisateur généré
     * @param string $password Mot de passe généré
     * @return string Message de succès ou erreur
     */
    public function sendCredentialsMail($email, $firstName, $lastName, $username, $password) {
        $subject = "Vos identifiants de connexion à CK-project";
        
        // Contenu HTML de l'email
        $content = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap');
                
                body { 
                    font-family: 'Geist', Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333;
                    background-color: #f5f6fa;
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    padding: 0;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    background-color: #ffffff;
                }
                .header { 
                    background-color: #4361ee; 
                    background-image: linear-gradient(135deg, #4361ee, #3730a3);
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-weight: 600;
                    font-size: 28px;
                    letter-spacing: -0.5px;
                }
                .content { 
                    padding: 30px; 
                }
                .welcome-text {
                    font-size: 16px;
                    margin-bottom: 25px;
                    color: #4b5563;
                }
                .name {
                    font-weight: 600;
                    color: #111827;
                }
                .credentials {
                    background-color: #f8fafc;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 25px 0;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                }
                .credentials p {
                    margin: 10px 0;
                    font-size: 15px;
                }
                .credentials .label {
                    font-weight: 500;
                    color: #4b5563;
                }
                .credentials .value {
                    font-family: 'Courier New', monospace;
                    background-color: #f1f5f9;
                    padding: 6px 10px;
                    border-radius: 4px;
                    margin-left: 8px;
                    color: #1f2937;
                    border: 1px solid #e5e7eb;
                    font-weight: 500;
                }
                .info {
                    margin: 25px 0;
                    font-size: 15px;
                    color: #4b5563;
                }
                .btn-container {
                    text-align: center;
                    margin: 30px 0;
                }
                .btn {
                    display: inline-block;
                    background-color: #4361ee;
                    background-image: linear-gradient(135deg, #4361ee, #3730a3);
                    color: white;
                    padding: 12px 30px;
                    text-decoration: none;
                    border-radius: 6px;
                    font-weight: 500;
                    font-size: 16px;
                    box-shadow: 0 3px 8px rgba(67, 97, 238, 0.3);
                    transition: transform 0.2s;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 12px rgba(67, 97, 238, 0.4);
                }
                .footer { 
                    text-align: center; 
                    padding: 20px;
                    background-color: #f8fafc;
                    border-top: 1px solid #e5e7eb;
                    font-size: 14px;
                    color: #6b7280;
                }
                .footer p {
                    margin: 5px 0;
                }
                .logo {
                    font-size: 24px;
                    font-weight: 700;
                    margin-bottom: 10px;
                    color: white;
                }
                .divider {
                    height: 1px;
                    background-color: #e5e7eb;
                    margin: 25px 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>CK-project</div>
                    <h1>Bienvenue dans notre équipe !</h1>
                </div>
                <div class='content'>
                    <p class='welcome-text'>Bonjour <span class='name'>$firstName $lastName</span>,</p>
                    
                    <p>Nous sommes ravis de vous accueillir sur notre plateforme de gestion de projets. 
                    Votre compte a été créé avec succès et vous pouvez désormais vous connecter avec les identifiants suivants :</p>
                    
                    <div class='credentials'>
                        <p><span class='label'>Nom d'utilisateur :</span> <span class='value'>$username</span></p>
                        <p><span class='label'>Mot de passe :</span> <span class='value'>$password</span></p>
                    </div>
                    
                    <p class='info'>Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe lors de votre première connexion.</p>
                    
                    <div class='divider'></div>
                    
                    <p>Vous pourrez accéder à votre espace personnel et consulter les projets qui vous seront assignés.</p>
                    
                    <div class='btn-container'>
                        <a href='http://localhost:3000/auth/login' class='btn'>Se connecter maintenant</a>
                    </div>
                </div>
                <div class='footer'>
                    <p>Si vous avez des questions, n'hésitez pas à contacter l'administrateur.</p>
                    <p>&copy; " . date('Y') . " CK-project. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendMail($email, $subject, $content, true);
    }
}
