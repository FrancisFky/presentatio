<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #667eea;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .otp-container {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 8px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .expires-info {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #92400e;
        }
        .warning {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #991b1b;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #5a67d8;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            .content {
                padding: 20px 15px;
            }
            .otp-code {
                font-size: 24px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Code de vérification</h1>
            <p>Vérification de votre compte administrateur</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Bonjour {{ $admin->name ?? 'Administrateur' }},</h2>
            
            <p>Vous avez demandé un code de vérification pour accéder à votre compte administrateur du site de l'Ambassade.</p>

            <!-- OTP Code -->
            <div class="otp-container">
                <h3>Votre code de vérification :</h3>
                <div class="otp-code">{{ $otpCode }}</div>
                <p><strong>Entrez ce code dans l'application pour continuer</strong></p>
            </div>

            <!-- Expiration Info -->
            <div class="expires-info">
                <strong>⚠️ Attention :</strong> Ce code expire le {{ $expiresAt->format('d/m/Y à H:i') }} (dans {{ $expiresAt->diffForHumans() }})
            </div>

            <!-- Security Warning -->
            <div class="warning">
                <strong>🔒 Sécurité :</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Ne partagez jamais ce code avec qui que ce soit</li>
                    <li>L'Ambassade ne vous demandera jamais ce code par email ou téléphone</li>
                    <li>Si vous n'avez pas demandé ce code, ignorez cet email</li>
                </ul>
            </div>

            <p>Si vous rencontrez des problèmes, contactez notre équipe de support.</p>

            <p>Cordialement,<br>
            <strong>Ambassade de la République du Congo au Kenya</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Cet email a été envoyé à {{ $admin->email }}</p>
            <p>&copy; {{ date('Y') }} Ambassade de la République du Congo au Kenya</p>
            <p style="font-size: 12px; margin-top: 10px;">
                Cet email est confidentiel et destiné uniquement au destinataire spécifié.
            </p>
        </div>
    </div>
</body>
</html> 