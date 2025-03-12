<?php
$title = "About";
include_once 'include/header.php';
// About Version 1.2

// Sprachauswahl (Standard: Deutsch)
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'de';
?>

<style>
   .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
      color: rgb(31, 31, 31);
      background-color: rgb(238, 241, 241);
      border: 1px solid #ddd;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
   }

   .language-switcher {
      text-align: right;
      margin-bottom: 20px;
   }

   .language-switcher a {
      text-decoration: none;
      color: #007BFF;
      margin: 0 5px;
   }

   .language-switcher a:hover {
      text-decoration: underline;
   }
</style>

<main class="container">
    <!-- Sprachauswahl -->
    <div class="language-switcher">
        <a href="?lang=de">Deutsch</a> | <a href="?lang=en">English</a>
    </div>

    <?php if ($lang == 'de'): ?>
        <!-- Deutsche Version -->
        <section>
            <h1>Über Uns</h1>
            <p>Willkommen auf unserer <?php echo SITE_NAME; ?> Webseite! Hier finden Sie Informationen über unser Unternehmen und unsere Dienstleistungen.</p>
        </section>

        <section>
            <h2>Haftungsausschluss</h2>
            <p>Die Nutzung dieser Webseite erfolgt auf eigene Gefahr. Wir übernehmen keine Gewähr für die Richtigkeit, Vollständigkeit oder Aktualität der bereitgestellten Informationen.</p>
        </section>

        <section>
            <h3>Internationaler Haftungsausschluss</h3>
            <p>
                Die Informationen auf dieser Webseite werden ohne jegliche Gewährleistung, ausdrücklich oder implizit, bereitgestellt. Wir schließen jegliche Haftung für Schäden aus, die direkt oder indirekt aus der Nutzung dieser Webseite entstehen.
            </p>
            <p>
                Dies umfasst, ohne Einschränkung, Schäden durch verlorene Daten, entgangenen Gewinn oder Betriebsunterbrechungen, unabhängig davon, ob wir auf die Möglichkeit solcher Schäden hingewiesen wurden.
            </p>
            <p>
                Diese Webseite kann Links zu externen Webseiten Dritter enthalten, auf deren Inhalte wir keinen Einfluss haben. Für die Inhalte und die Richtigkeit der Informationen auf diesen externen Webseiten übernehmen wir keine Haftung. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich.
            </p>
        </section>

        <section>
            <h3>Virtuelle Welten mit OpenSimulator</h3>
            <p>
                OpenSimulator ist eine Open-Source-Software, die es ermöglicht, virtuelle 3D-Welten zu erstellen und zu betreiben. Diese Welten können für verschiedene Zwecke genutzt werden, einschließlich Bildung, soziale Interaktion und Geschäftstätigkeiten.
            </p>
            <p>
                OpenSimulator ist kompatibel mit dem Second Life-Protokoll, was bedeutet, dass Benutzer mit Second Life-kompatiblen Clients auf diese Welten zugreifen können.
            </p>
            <p>
                OpenSimulator bietet eine flexible Plattform, die es Benutzern ermöglicht, ihre eigenen virtuellen Umgebungen zu gestalten. Dies umfasst die Erstellung von Landschaften, Gebäuden, Objekten und sogar komplexen Simulationen. Die Software unterstützt auch Skripting, sodass Benutzer interaktive Elemente und Funktionen in ihren Welten implementieren können.
            </p>
            <p>
                Die in OpenSimulator erstellten virtuellen Welten sind nutzergeneriert, und die Inhalte werden von den Benutzern selbst erstellt und verwaltet. Dies bedeutet, dass die Verantwortung für die Inhalte, die in diesen Welten geteilt werden, bei den jeweiligen Nutzern liegt. Wir übernehmen keine Verantwortung für die Inhalte, die in diesen virtuellen Welten erstellt oder geteilt werden.
            </p>
            <p>
                Die Nutzung von OpenSimulator und den damit verbundenen virtuellen Welten erfolgt auf eigene Gefahr. Wir empfehlen den Benutzern, die Nutzungsbedingungen und Datenschutzrichtlinien der jeweiligen virtuellen Welten sorgfältig zu lesen und zu befolgen. Zudem sollten Benutzer sich bewusst sein, dass virtuelle Welten möglicherweise nicht für alle Altersgruppen geeignet sind und dass sie ihre Privatsphäre und Sicherheit in solchen Umgebungen schützen sollten.
            </p>
            <p>
                Weitere Informationen zu OpenSimulator finden Sie auf der offiziellen Wiki-Seite: <a href="http://opensimulator.org/wiki/Main_Page/de" target="_blank">OpenSimulator Wiki</a>.
            </p>
        </section>

    <?php else: ?>
        <!-- Englische Version -->
        <section>
            <h1>About Us</h1>
            <p>Welcome to our <?php echo SITE_NAME; ?> website! Here you will find information about our company and our services.</p>
        </section>

        <section>
            <h2>Disclaimer</h2>
            <p>Use of this website is at your own risk. We do not guarantee the accuracy, completeness, or timeliness of the information provided.</p>
        </section>

        <section>
            <h3>International Disclaimer</h3>
            <p>
                The information on this website is provided without any warranties, express or implied. We disclaim all liability for damages arising directly or indirectly from the use of this website.
            </p>
            <p>
                This includes, without limitation, damages due to lost data, lost profits, or business interruption, regardless of whether we have been advised of the possibility of such damages.
            </p>
            <p>
                This website may contain links to external websites of third parties, over whose content we have no control. We assume no responsibility for the content and accuracy of the information on these external websites. The respective provider or operator of the linked pages is always responsible for their content.
            </p>
        </section>

        <section>
            <h3>Virtual Worlds with OpenSimulator</h3>
            <p>
                OpenSimulator is an open-source software that allows the creation and operation of virtual 3D worlds. These worlds can be used for various purposes, including education, social interaction, and business activities.
            </p>
            <p>
                OpenSimulator is compatible with the Second Life protocol, which means that users can access these worlds using Second Life-compatible clients.
            </p>
            <p>
                OpenSimulator provides a flexible platform that enables users to design their own virtual environments. This includes the creation of landscapes, buildings, objects, and even complex simulations. The software also supports scripting, allowing users to implement interactive elements and functions in their worlds.
            </p>
            <p>
                The virtual worlds created in OpenSimulator are user-generated, and the content is created and managed by the users themselves. This means that the responsibility for the content shared in these worlds lies with the respective users. We are not responsible for the content created or shared in these virtual worlds.
            </p>
            <p>
                The use of OpenSimulator and the associated virtual worlds is at your own risk. We recommend that users carefully read and follow the terms of use and privacy policies of the respective virtual worlds. Additionally, users should be aware that virtual worlds may not be suitable for all age groups and that they should protect their privacy and safety in such environments.
            </p>
            <p>
                For more information about OpenSimulator, please visit the official wiki page: <a href="http://opensimulator.org/wiki/Main_Page/de" target="_blank">OpenSimulator Wiki</a>.
            </p>
        </section>
    <?php endif; ?>
</main>

<?php include_once 'include/footer.php'; ?>