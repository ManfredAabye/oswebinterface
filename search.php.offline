<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Search</title>

<!-- 
Datei search.php

Ich möchte das so umbauen das in unterschiedlichen schriftarten folgendes angezeigt wird:
A search function is already integrated; select People, Groups, Places, Land Sales, Events or Classifieds from the options above.
Eine Suchfunktion ist bereits integriert; wählen Sie oben Leute, Gruppen, Orte, Land-Verkauf, Events oder Anzeigen aus.
Une fonction de recherche est déjà intégrée ; sélectionnez Personnes, Groupes, Lieux, Ventes de terrains, Événements ou Petites annonces parmi les options ci-dessus.
Ya viene integrada una función de búsqueda; seleccione Personas, Grupos, Lugares, Ventas de terrenos, Eventos o Clasificados de las opciones anteriores.

Der Optionaler Button soll die Server IP ermitteln und verwenden.
-->

  <!-- W3.CSS -->
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

  <!-- Font Awesome 4 (oder 5/6, je nach Bedarf) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <style>
    body, html {
      height: 100%;
      margin: 0;
      background: #050b1a;
      color: #fff;
      font-family: "Segoe UI", Arial, sans-serif;
    }
    .bg-grid {
      background-image: linear-gradient(#1b2740 1px, transparent 1px),
                        linear-gradient(90deg, #1b2740 1px, transparent 1px);
      background-size: 40px 40px;
    }
    .holo-text {
      text-shadow: 0 0 10px #6cf, 0 0 20px #6cf;
      letter-spacing: 4px;
    }
    .port-text {
      font-size: 14px;
      color: #aaa;
    }
  </style>
</head>
<body>

<div class="w3-display-container w3-center bg-grid" style="height:100%;">
  <div class="w3-display-middle">

    <!-- Logo / Titelzeile -->
    <div class="w3-margin-bottom">
      <i class="fa fa-cube w3-text-green" style="font-size:48px;"></i>
      <span class="w3-xlarge w3-margin-left">OpenSimulator</span>
    </div>

    <!-- Search Code -->
    <div class="holo-text" style="font-size:80px; font-weight:bold;">
      Search
    </div>

    <!-- Text darunter -->

      <!-- Mehrsprachiger Hinweistext in unterschiedlichen Farben -->
      <div class="w3-large w3-margin-top">
        <div style="color: #e0f7fa; border-radius: 8px; margin-bottom: 8px; padding: 8px 12px;">
          A search function is already integrated - select People, Groups, Places, Land Sales, Events or Classifieds from the options above.
        </div>
        <div style="color: #fce4ec; border-radius: 8px; margin-bottom: 8px; padding: 8px 12px;">
          Eine Suchfunktion ist bereits integriert - wählen Sie oben Leute, Gruppen, Orte, Land-Verkauf, Events oder Anzeigen aus.
        </div>
        <div style="color: #fff3e0; border-radius: 8px; margin-bottom: 8px; padding: 8px 12px;">
          Une fonction de recherche est déjà intégrée - sélectionnez Personnes, Groupes, Lieux, Ventes de terrains, Événements ou Petites annonces parmi les options ci-dessus.
        </div>
        <div style="color: #e8f5e9; border-radius: 8px; padding: 8px 12px;">
          Ya viene integrada una función de búsqueda - seleccione Personas, Grupos, Lugares, Ventas de terrenos, Eventos o Clasificados de las opciones anteriores.
        </div>
      </div>

    <!-- Icon-Leiste (Teleport / Server etc.) -->
    <div class="w3-margin-top">
      <i class="fa fa-server w3-xlarge w3-margin-right"></i>
      <i class="fa fa-television w3-xlarge w3-margin-right"></i>
      <i class="fa fa-location-arrow w3-xlarge"></i>
    </div>

    <!-- Optionaler Button: Server-IP ermitteln und verwenden -->
        <!-- Optionaler Button -->
    <div class="w3-margin-top">
      <button onclick="redirectToIndexWithIP()" class="w3-button w3-border w3-round w3-hover-blue">
        <i class="fa fa-home w3-margin-right"></i>
        Go to homepage
      </button>
    </div>

    <script>
      function redirectToIndexWithIP() {
        fetch('https://api.ipify.org?format=json')
          .then(response => response.json())
          .then(data => {
            // IP als Query-Parameter an index.php anhängen
            window.location.href = 'index.php?serverip=' + encodeURIComponent(data.ip);
          })
          .catch(() => {
            document.getElementById('server-ip').textContent = 'IP konnte nicht ermittelt werden.';
          });
      }
    </script>

  </div>
</div>

</body>
</html>
