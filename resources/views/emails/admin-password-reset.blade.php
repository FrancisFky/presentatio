<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background-color: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background-color: #4F46E5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔑 Réinitialisation du mot de passe</h1>
    </div>

    <div class="content">
        <h2>Bonjour {{ $admin->name }},</h2>

        <p>Une demande de réinitialisation du mot de passe de votre compte administrateur du site de l'Ambassade a été reçue.</p>

        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Choisir un nouveau mot de passe</a>
        </div>

        <p>Ce lien est valable {{ $expiresInMinutes }} minutes et ne peut servir qu'une fois.</p>
        <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail : votre mot de passe reste inchangé.</p>

        <p style="word-break: break-all; font-size: 12px; color: #666;">Si le bouton ne fonctionne pas, copiez ce lien : {{ $resetUrl }}</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Ambassade de la République du Congo au Kenya</p>
    </div>
</body>
</html>
