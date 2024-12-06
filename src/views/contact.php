<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulaire de Contact</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center" >
  <div class="container max-w-3xl mx-auto p-8" style="margin-top: 100px;">
    <div class="bg-white rounded-lg shadow-lg p-8">
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Nous Contacter</h2>
      <p class="text-gray-600 text-center mb-8">
        Avez-vous des questions ou besoin d'aide ? Envoyez-nous un message et nous vous répondrons rapidement.
      </p>
      <form action="submit_contact.php" method="POST" class="space-y-6">
        <!-- Nom -->
        <div>
          <label for="nom" class="block text-lg font-medium text-gray-700">Nom</label>
          <input type="text" id="nom" name="nom" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <!-- Email -->
        <div>
          <label for="email" class="block text-lg font-medium text-gray-700">Email</label>
          <input type="email" id="email" name="email" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <!-- Sujet -->
        <div>
          <label for="sujet" class="block text-lg font-medium text-gray-700">Sujet</label>
          <input type="text" id="sujet" name="sujet" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <!-- Message -->
        <div>
          <label for="message" class="block text-lg font-medium text-gray-700">Message</label>
          <textarea id="message" name="message" rows="5" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <!-- Bouton Envoyer -->
        <div class="text-center">
          <button type="submit" class="w-full bg-blue-500 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Envoyer le Message
          </button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
