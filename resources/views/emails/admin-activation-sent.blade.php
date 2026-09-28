<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation de votre compte administrateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background-color: #4F46E5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-box {
            background-color: #EBF8FF;
            border-left: 4px solid #3182CE;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Activation de votre compte</h1>
        <p>Bienvenue dans l'équipe d'administration du site de l'Ambassade !</p>
    </div>

    <div class="content">
        <h2>Bonjour {{ $admin->name }},</h2>
        
        <p>Votre compte administrateur a été créé avec succès sur le site de l'Ambassade de la République du Congo au Kenya. Pour commencer à utiliser votre compte, vous devez l'activer en définissant un mot de passe.</p>

        <div class="info-box">
            <h3>📋 Informations de votre compte :</h3>
            <ul>
                <li><strong>Nom :</strong> {{ $admin->name }}</li>
                <li><strong>Email :</strong> {{ $admin->email }}</li>
                <li><strong>Nom d'utilisateur :</strong> {{ $admin->username }}</li>
                <li><strong>Rôle :</strong> {{ $admin->role_name }}</li>
            </ul>
        </div>

        <p>Cliquez sur le bouton ci-dessous pour activer votre compte :</p>

        <div style="text-align: center;">
            <a href="{{ $activationUrl }}" class="button">
                ✅ Activer mon compte
            </a>
        </div>

        <p><strong>Important :</strong></p>
        <ul>
            <li>Ce lien d'activation est valide et sécurisé</li>
            <li>Vous devrez créer un mot de passe fort lors de l'activation</li>
            <li>Une fois activé, vous pourrez vous connecter à l'administration</li>
            <li>Gardez vos identifiants de connexion en sécurité</li>
        </ul>

        <p>Si le bouton ne fonctionne pas, vous pouvez copier et coller ce lien dans votre navigateur :</p>
        <p style="word-break: break-all; color: #4F46E5;">{{ $activationUrl }}</p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">

        <p>Si vous n'avez pas demandé la création de ce compte ou si vous pensez que c'est une erreur, veuillez ignorer cet email ou contacter l'administrateur système.</p>

        <p>Cordialement,<br>
        <strong>Ambassade de la République du Congo au Kenya</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Ambassade de la République du Congo au Kenya</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html>