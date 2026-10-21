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
            $mail->Subject = "[SecurePoll] Your Verification Code: {$otpCode}";
            
            $purposeSubject = "Verification Code";
            $purposeText = "To proceed with your request, please use the 6-digit security code below.";
            $actionStep = "Enter this code on the verification screen to continue.";
            
            switch ($purpose) {
                case 'registration':
                    $purposeSubject = "Complete Account Registration";
                    $purposeText = "Thank you for creating an account with SecurePoll. To verify your email address and activate your account, please enter the security code below.";
                    $actionStep = "Enter this code on the registration verification screen to complete sign-up.";
                    break;
                case 'new_device':
                    $purposeSubject = "New Device Authorization";
                    $purposeText = "We detected a login attempt from an unfamiliar browser or device. To confirm that you authorized this session, please use the code below.";
                    $actionStep = "Enter this code on the sign-in screen to authorize this trusted device.";
                    break;
                case 'password_reset':
                    $purposeSubject = "Password Reset Request";
                    $purposeText = "We received a request to reset the password for your SecurePoll account. To proceed with setting a new password, please enter the security code below.";
                    $actionStep = "Enter this code on the password reset screen to verify your identity.";
                    break;
            }
            
            $appUrl = Env::get('APP_URL', 'http://localhost/SecurePoll/public');
            $year = date('Y');
            
            $message = "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <title>SecurePoll Security Code</title>
</head>
<body style='margin: 0; padding: 0; background-color: #f5f2eb; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; color: #192326; -webkit-font-smoothing: antialiased;'>
    <table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color: #f5f2eb; padding: 40px 16px;'>
        <tr>
            <td align='center'>
                <table width='100%' cellpadding='0' cellspacing='0' border='0' style='max-width: 540px; background-color: #ffffff; border: 1px solid #e6dfd3; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(25, 35, 38, 0.05);'>
                    
                    <!-- Top Brand Bar -->
                    <tr>
                        <td style='padding: 28px 36px 20px 36px; border-bottom: 1px solid #f0eae1;'>
                            <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                <tr>
                                    <td>
                                        <span style='font-size: 20px; font-weight: 800; letter-spacing: -0.5px; color: #142529;'>
                                            Secure<span style='color: #c2593f;'>Poll</span>
                                        </span>
                                        <span style='font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #6e7d82; margin-left: 8px; font-weight: 600;'>Institutional Voting</span>
                                    </td>
                                    <td align='right'>
                                        <span style='display: inline-block; padding: 3px 10px; font-size: 11px; font-weight: 600; color: #164e49; background-color: #e6f0ef; border-radius: 6px;'>
                                            Security Notice
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Content Body -->
                    <tr>
                        <td style='padding: 32px 36px;'>
                            <h1 style='margin: 0 0 14px 0; font-size: 21px; font-weight: 700; color: #142529; line-height: 1.3;'>
                                {$purposeSubject}
                            </h1>
                            <p style='margin: 0 0 24px 0; font-size: 15px; line-height: 1.6; color: #445459;'>
                                {$purposeText}
                            </p>

                            <!-- Dedicated OTP Box -->
                            <table width='100%' cellpadding='0' cellspacing='0' border='0' style='margin: 24px 0 24px 0;'>
                                <tr>
                                    <td align='center' style='background-color: #faf8f5; border: 2px dashed #ded6c5; border-radius: 10px; padding: 24px 20px;'>
                                        <div style='font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; color: #78878c; margin-bottom: 8px;'>
                                            Your 6-Digit One-Time Code
                                        </div>
                                        <div style='font-family: \"Courier New\", Courier, Consolas, Monaco, monospace; font-size: 38px; font-weight: 800; letter-spacing: 10px; color: #142529; padding-left: 10px; user-select: all;'>
                                            {$otpCode}
                                        </div>
                                        <div style='font-size: 12px; color: #8a989e; margin-top: 8px;'>
                                            Valid for 10 minutes &bull; Single-use only
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style='margin: 0 0 20px 0; font-size: 14px; line-height: 1.5; color: #5a6b70;'>
                                {$actionStep} If this code expires, you can request a new one from the verification screen.
                            </p>

                            <!-- Security Advisory Callout -->
                            <table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color: #faf9f6; border-left: 3px solid #c2593f; border-radius: 4px; padding: 12px 16px; margin-top: 24px;'>
                                <tr>
                                    <td style='font-size: 12px; line-height: 1.5; color: #627277;'>
                                        <strong style='color: #142529;'>Security Reminder:</strong> Never share this code with anyone. SecurePoll administrators will never ask for your verification passcode. If you did not make this request, you can safely disregard this email.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style='padding: 20px 36px 28px 36px; border-top: 1px solid #f0eae1; background-color: #faf8f5; text-align: center;'>
                            <p style='margin: 0 0 6px 0; font-size: 12px; color: #8a989e;'>
                                SecurePoll &bull; Cryptographically Audited Voting Platform
                            </p>
                            <p style='margin: 0; font-size: 11px; color: #a4b0b5;'>
                                &copy; {$year} SecurePoll. For support: contact@pgdev.in
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
";

            $mail->Body = $message;
            $mail->AltBody = "SecurePoll — {$purposeSubject}\n\nYour 6-Digit One-Time Code: {$otpCode}\n\n{$purposeText}\n\nThis code will expire in 10 minutes.\n\nSecurity Notice: If you did not request this verification code, no action is needed.\n\nFor support, contact: contact@pgdev.in";

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
