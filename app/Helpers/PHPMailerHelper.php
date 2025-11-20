<?php

namespace App\Helpers;

use App\Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerHelper
{
    private PHPMailer $mail;

    public function __construct(\PDO $pdo)
    {
        $this->mail = new PHPMailer(true);

        // Buscar dados de configuração no banco usando a conexão $pdo passada
        $stmt = $pdo->prepare("SELECT servidor, email, pass, port FROM config LIMIT 1");
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            throw new \Exception('Configuração SMTP não encontrada no banco.');
        }

        // Configurações SMTP — personalize conforme sua hospedagem
        $this->mail->isSMTP();
        $this->mail->Host = $data['servidor'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $data['email'];

        if (defined('EMAIL') && filter_var(EMAIL, FILTER_VALIDATE_EMAIL)) {
            $this->mail->addBCC(EMAIL, 'Mensagem do site');
        }
        
        $this->mail->Password = $data['pass'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port = $data['port'];

        $this->mail->CharSet = 'UTF-8';

        // Evita erro em alguns ambientes
        $this->mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        // Remetente padrão
        $this->mail->setFrom($data['email'], SITE_NAME);
    }

    public function send(string $to, string $toName, string $subject, string $body, ?string $altBody = null): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to, $toName);

            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody ?? strip_tags($body);

            return $this->mail->send();
        } catch (Exception $e) {
            error_log('Erro ao enviar e-mail: ' . $this->mail->ErrorInfo);
            return false;
        }
    }
}
