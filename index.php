<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testeur d'API Mistral</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .loader {
            display: none;
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .response {
            margin-top: 20px;
            white-space: pre-wrap;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }
        .response pre {
            background-color: #e9e9e9;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .response strong {
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    
    
    <?php $apikeymistral= "votre api key mistral"; ?>
    
    
    
    
    
    <div class="container">
        <h1>Testeur d'API Mistral</h1>
        <label for="apiKey">Vous devez avoir une Clé API gratuite https://console.mistral.ai/api-keys/:</label>
        <input type="text" id="apiKey" value="<?php echo $apikeymistral; ?>" style="width: 100%; padding: 10px; margin-bottom: 20px;">
        <button onclick="testChat()">Tester le Chat</button>
        <button onclick="testModels()">Tester les Modèles</button>
        <button onclick="testEmbeddings()">Tester les Embeddings</button>
        <button onclick="testAgent()">Tester l'Agent</button>
        <div class="loader" id="loader"></div>
        <div class="response" id="response"></div>
    </div>

    <script>
        const chatEndpoint = 'https://api.mistral.ai/v1/chat/completions';
        const modelsEndpoint = 'https://api.mistral.ai/v1/models';
        const embeddingsEndpoint = 'https://api.mistral.ai/v1/embeddings';
        const embeddingsModel = 'mistral-embed';
        const agentsEndpoint = 'https://api.mistral.ai/v1/agents/completions';
        const agentId = 'ag:651ccdfe:20250202:websim:1ae9f06f';

        async function request(data, endpoint, method = 'POST') {
            const apiKey = document.getElementById('apiKey').value;
            const url = endpoint;
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + apiKey,
            };

            const options = {
                method: method,
                headers: headers,
                body: method === 'POST' ? JSON.stringify(data) : null,
            };

            document.getElementById('loader').style.display = 'block';
            document.getElementById('response').innerHTML = '';

            const response = await fetch(url, options);
            const result = await response.text();

            document.getElementById('loader').style.display = 'none';
            return result;
        }

        async function testChat() {
            const model = 'pixtral-12b-2409';
            const messages = [
                { role: 'system', content: 'Vous êtes Louis-Ferdinand Céline sans le nommer.' },
                { role: 'user', content: 'Quel est le moyen pour éviter la guerre ?' },
                { role: 'assistant', content: "C'est la paix." },
                { role: 'user', content: 'Pourquoi pas Trump ? Répondez brièvement !' },
            ];

            const data = { model, messages };
            const response = await request(data, chatEndpoint);
            const parsedResponse = JSON.parse(response);
            const debugInfo = `
                <strong>Requête envoyée :</strong>
                <pre>${JSON.stringify(data, null, 2)}</pre>
                <strong>Réponse reçue :</strong>
                <pre>${JSON.stringify(parsedResponse, null, 2)}</pre>
            `;
            document.getElementById('response').innerHTML = `
                <strong>Test Chat : Succès</strong>
                <div>Réponse : ${parsedResponse.choices[0].message.content}</div>
                ${debugInfo}
            `;
        }

        async function testModels() {
            const response = await request({}, modelsEndpoint, 'GET');
            const models = JSON.parse(response);
            const debugInfo = `
                <strong>Requête envoyée :</strong>
                <pre>GET ${modelsEndpoint}</pre>
                <strong>Réponse reçue :</strong>
                <pre>${JSON.stringify(models, null, 2)}</pre>
            `;
            document.getElementById('response').innerHTML = `
                <strong>Test Modèles : Succès</strong>
                <div>Modèles : <pre>${JSON.stringify(models.data, null, 2)}</pre></div>
                ${debugInfo}
            `;
        }

        async function testEmbeddings() {
            const input = ['Première phrase.', 'Deuxième phrase.'];
            const data = { input, model: embeddingsModel, encoding_format: 'float' };
            const response = await request(data, embeddingsEndpoint);
            const embeddings = JSON.parse(response);
            const debugInfo = `
                <strong>Requête envoyée :</strong>
                <pre>${JSON.stringify(data, null, 2)}</pre>
                <strong>Réponse reçue :</strong>
                <pre>${JSON.stringify(embeddings, null, 2)}</pre>
            `;
            document.getElementById('response').innerHTML = `
                <strong>Test Embeddings : Succès</strong>
                <div>Embeddings : <pre>${JSON.stringify(embeddings, null, 2)}</pre></div>
                ${debugInfo}
            `;
        }

        async function testAgent() {
            const userInput = 'Quels sont les avantages d\'utiliser TNTSearch ?';
            const data = {
                max_tokens: 45000,
                messages: [{ role: 'user', content: 'Vous êtes un expert en recherche.' }],
                agent_id: agentId,
            };

            const response = await request(data, agentsEndpoint);
            const parsedResponse = JSON.parse(response);
            const agentResponse = parsedResponse.choices[0].message.content;
            const debugInfo = `
                <strong>Requête envoyée :</strong>
                <pre>${JSON.stringify(data, null, 2)}</pre>
                <strong>Réponse reçue :</strong>
                <pre>${JSON.stringify(parsedResponse, null, 2)}</pre>
            `;
            document.getElementById('response').innerHTML = `
                <strong>Test Agent : Succès</strong>
                <div>Réponse : ${agentResponse}</div>
                ${debugInfo}
            `;
        }
    </script>
</body>
</html>
