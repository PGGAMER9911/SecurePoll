<?php
namespace app\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use app\Helpers\Env;

class EmailService {
    private $lastError = null;

    public function getLastError() {
        return $this->lastError;
    }

    public function sendOtp($to, $otpCode, $purpose) {
        $this->lastError = null;
        $mail = new PHPMailer(true);

        try {
            // Server settings from .env
            $mail->isSMTP();
            $mail->Host       = Env::get('SMTP_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = Env::get('SMTP_USERNAME');
            $mail->Password   = Env::get('SMTP_PASSWORD');
            
            $encryption = Env::get('SMTP_ENCRYPTION', 'tls');
            if ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            $mail->Port = Env::get('SMTP_PORT', 587);

            // Recipients
            $mail->setFrom(Env::get('SMTP_FROM_EMAIL', 'noreply@securepoll.local'), Env::get('SMTP_FROM_NAME', 'SecurePoll'));
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your SecurePoll Verification Code';
            
            $purposeText = "";
            $headerText = "Verify your action";
            
            switch ($purpose) {
                case 'registration':
                    $headerText = "Verify your email address";
                    $purposeText = "Thank you for registering with SecurePoll. To complete your sign-up, please use the verification code below.";
                    break;
                case 'new_device':
                    $headerText = "New device detected";
                    $purposeText = "We detected a login attempt from a new device. To authorize this login, please enter the verification code below.";
                    break;
                case 'password_reset':
                    $headerText = "Reset your password";
                    $purposeText = "We received a request to reset your password. Use the verification code below to proceed.";
                    break;
            }
            
            $appUrl = Env::get('APP_URL', 'http://localhost/SecurePoll/public');
            
            $message = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
                    .wrapper { background-color: #f8fafc; padding: 40px 20px; width: 100%; box-sizing: border-box; }
                    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
                    .logo { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 32px; text-align: center; letter-spacing: -0.5px; }
                    .logo span { color: #6366f1; }
                    .header { font-size: 20px; font-weight: 600; color: #0f172a; margin-bottom: 16px; text-align: center; }
                    .text { font-size: 15px; color: #475569; line-height: 1.6; text-align: center; margin-bottom: 32px; }
                    .otp-container { background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1px solid #c7d2fe; border-radius: 12px; padding: 32px 24px; text-align: center; margin-bottom: 32px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); }
                    .otp-label { display: block; font-size: 12px; text-transform: uppercase; font-weight: 700; color: #6366f1; margin-bottom: 12px; letter-spacing: 1px; }
                    .otp-code { font-size: 42px; font-weight: 800; color: #4f46e5; letter-spacing: 12px; margin: 0; display: inline-block; user-select: all; padding-left: 12px; }
                    .button { display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 8px; font-size: 14px; text-align: center; transition: background-color 0.3s; }
                    .footer { text-align: center; margin-top: 40px; font-size: 13px; color: #94a3b8; line-height: 1.5; }
                </style>
            </head>
            <body>
                <div class='wrapper'>
                    <div class='container'>
                        <div class='logo'>Secure<span>Poll</span></div>
                        
                        <div class='header'>{$headerText}</div>
                        
                        <div class='text'>
                            {$purposeText}
                        </div>
                        
                        <div class='otp-container'>
                            <span class='otp-label'>Your Verification Code</span>
                            <h1 class='otp-code'>{$otpCode}</h1>
                        </div>
                        
                        <div style='text-align: center;'>
                            <p style='font-size: 14px; color: #64748b; margin-bottom: 24px;'>Double-tap or highlight the code above to copy it.</p>
                            <a href='{$appUrl}' class='button'>Return to SecurePoll</a>
                        </div>
                        
                        <div class='footer'>
                            <p>This code will expire in 10 minutes.</p>
                            <p>If you didn't request this code, you can safely ignore this email.</p>
                            <p style='margin-top: 24px;'>&copy; " . date('Y') . " SecurePoll. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ";

            $mail->Body    = $message;
            $mail->AltBody = "Your SecurePoll Verification Code is: {$otpCode}\n\n{$purposeText}\n\nThis code will expire in 10 minutes.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->lastError = $mail->ErrorInfo ?: $e->getMessage();
            error_log("Message could not be sent. Mailer Error: {$this->lastError}");
            return false;
        } catch (\Throwable $t) {
            $this->lastError = $t->getMessage();
            error_log("Message could not be sent. Error: {$this->lastError}");
            return false;
        }
    }

    public function sendEmail($to, $subject, $htmlBody, $altBody = '') {
        $this->lastError = null;
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = Env::get('SMTP_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = Env::get('SMTP_USERNAME');
            $mail->Password   = Env::get('SMTP_PASSWORD');
            
            $encryption = Env::get('SMTP_ENCRYPTION', 'tls');
            if ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            $mail->Port = Env::get('SMTP_PORT', 587);

            $mail->setFrom(Env::get('SMTP_FROM_EMAIL', 'noreply@securepoll.local'), Env::get('SMTP_FROM_NAME', 'SecurePoll'));
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $altBody ?: strip_tags(str_replace('<br>', "\n", $htmlBody));

            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->lastError = $mail->ErrorInfo ?: $e->getMessage();
            error_log("Email could not be sent. Mailer Error: {$this->lastError}");
            return false;
        } catch (\Throwable $t) {
            $this->lastError = $t->getMessage();
            error_log("Email could not be sent. Error: {$this->lastError}");
            return false;
        }
    }

    public function sendPasswordResetLink($to, $resetLink) {
        $subject = 'Reset Your SecurePoll Password';
        $appUrl = Env::get('APP_URL', 'http://localhost/SecurePoll/public');

        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
                .wrapper { background-color: #f8fafc; padding: 40px 20px; width: 100%; box-sizing: border-box; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
                .logo { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 32px; text-align: center; letter-spacing: -0.5px; }
                .logo span { color: #6366f1; }
                .header { font-size: 20px; font-weight: 600; color: #0f172a; margin-bottom: 16px; text-align: center; }
                .text { font-size: 15px; color: #475569; line-height: 1.6; text-align: center; margin-bottom: 32px; }
                .button-container { text-align: center; margin-bottom: 32px; }
                .button { display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 8px; font-size: 14px; text-align: center; }
                .url-container { background-color: #f1f5f9; padding: 16px; border-radius: 8px; font-size: 13px; color: #64748b; word-break: break-all; margin-bottom: 32px; text-align: center; }
                .footer { text-align: center; margin-top: 40px; font-size: 13px; color: #94a3b8; line-height: 1.5; }
            </style>
        </head>
        <body>
            <div class='wrapper'>
                <div class='container'>
                    <div class='logo'>Secure<span>Poll</span></div>
                    <div class='header'>Reset your password</div>
                    <div class='text'>
                        We received a request to reset the password for your SecurePoll account. Click the button below to choose a new password.
                    </div>
                    <div class='button-container'>
                        <a href='{$resetLink}' class='button' style='color: #ffffff;'>Reset Password</a>
                    </div>
                    <div class='url-container'>
                        If the button above doesn't work, copy and paste this link into your browser:<br>
                        <a href='{$resetLink}' style='color: #4f46e5;'>{$resetLink}</a>
                    </div>
                    <div class='footer'>
                        <p>This password reset link will expire in 30 minutes.</p>
                        <p>If you didn't request a password reset, you can safely ignore this email.</p>
                        <p style='margin-top: 24px;'>&copy; " . date('Y') . " SecurePoll. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        $altBody = "Reset your SecurePoll password by visiting the following link:\n\n{$resetLink}\n\nThis link will expire in 30 minutes. If you did not request this reset, you can safely ignore this email.";

        return $this->sendEmail($to, $subject, $htmlBody, $altBody);
    }
}
