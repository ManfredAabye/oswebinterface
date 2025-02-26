<?php
$title = "GridStatus";
include_once 'include/header.php';
/*
   ; Simulator Stats URI
   ; Aktivieren Sie JSON-Simulatordaten, indem Sie einen URI-Namen festlegen (Grosschreibung-/Kleinschreibung beachten)
   Stats_URI = "jsonSimStats"
*/
?>

<main>
    
    <style>
        h2 {font-family: <?php echo FONT_FAMILY; ?>;font-size: <?php echo TITLE_FONT_SIZE; ?>;}
        /* #statitstics {font-family: <?php echo FONT_FAMILY; ?>;font-size: <?php echo TITLE_FONT_SIZE; ?>;} */
        .pacifico-regular {font-family: "Pacifico", serif; font-weight: 400; font-style: normal;}
        .material-symbols-outlined {font-variation-settings:'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 } 
    </style>

    <h2><?php echo SITE_NAME; ?> GridStatus Overview</h2>
    <p>All information related to the GridStatus can be found here.</p>

</main>

<?php
    $con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    $result1 = mysqli_query($con, "SELECT COUNT(*) FROM Presence");
    list($totalUsers) = mysqli_fetch_row($result1);

    $result2 = mysqli_query($con, "SELECT COUNT(*) FROM regions");
    list($totalRegions) = mysqli_fetch_row($result2);

    $result3 = mysqli_query($con, "SELECT COUNT(*) FROM UserAccounts");
    list($totalAccounts) = mysqli_fetch_row($result3);

    $result4 = mysqli_query($con, "SELECT COUNT(*) FROM GridUser WHERE Login > (UNIX_TIMESTAMP() - (30*86400))");
    list($activeUsers) = mysqli_fetch_row($result4);

    $result5 = mysqli_query($con, "SELECT COUNT(*) FROM GridUser");
    list($totalGridAccounts) = mysqli_fetch_row($result5);
    
    echo "<div id='statitstics'>";
    echo "<b>Nutzer im Grid</font>: " . $totalUsers . "<br>";
    echo "Regionen</font>: " . $totalRegions . "<br>";
    echo "Aktiv in den letzten 30 Tagen</font>: " . $activeUsers . "<br>";
    echo "Inworld Nutzer</font>: " . $totalAccounts . "<br>";
    echo "HG Grid Nutzer</font>: " . $totalGridAccounts . "<br>";
?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="http://www.google.com/jsapi"></script>
<script>
SimFPS = 0;
PhyFPS = 0;
Memory = 0;
RootAg = 0;
ChldAg = 0;
Uptime = "";
Version = "";
// var url = "http://openmanniland.de:9010/jsonSimStats/?callback=?";
var url = "http://openmanniland.de:9010/jsonSimStats?region=OML-Welcome";
 
setInterval(function() {$.getJSON(url, function(data) {
    SimFPS = Math.round(data.SimFPS);
    PhyFPS = Math.round(data.PhyFPS);
    Memory = Math.round(data.Memory);
    ChldAg = data.ChldAg;
    RootAg = data.RootAg;
    Uptime = data.Uptime;
    Version = data.Version;
    drawChart();
    setTags();
})}, 3000);
 
google.load("visualization", "1.0", {packages:["gauge"]});
google.setOnLoadCallback(drawChart);
 
function drawChart() {
    var cdata = new google.visualization.DataTable();
    cdata.addColumn('string', 'Label');
    cdata.addColumn('number', 'Value');
    cdata.addRows(3);
    cdata.setValue(0, 0, 'SimFPS');
    cdata.setValue(0, 1, SimFPS);
    cdata.setValue(1, 0, 'PhyFPS');
    cdata.setValue(1, 1, PhyFPS);
    cdata.setValue(2, 0, 'Memory');
    cdata.setValue(2, 1, Memory);
    var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
    var options = {width: 400, height: 120, redFrom: 90, redTo: 100, yellowFrom:75, yellowTo: 90, minorTicks: 5};
    chart.draw(cdata, options);
}
 
function setTags() {
    $("#par-uptime").text("Uptime: "  + Uptime);
    $("#par-ragent").text("Root Agent: " + RootAg);
    $("#par-version").text("Version: " + Version);
    $("#par-cagent").text("Child Agent: " + ChldAg);
}
</script>

<body>
<br>
    = <?php echo SITE_NAME; ?> Development Region =
    <table>
        <tr>
            <td><div id="par-version">Version:</div></td>
            <td><div id="par-ragent">Root Agent:</div></td>
        </tr>
        <tr>
            <td><div id="par-uptime">Uptime:</div></td>
            <td><div id="par-cagent">Child Agent:</div></td>
        </tr>
    </table>
    <div id="chart_div"></div>
</body>

