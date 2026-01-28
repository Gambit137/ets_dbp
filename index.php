<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koncar Lobby</title>
    <style>
        :root {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #0284c7;
            --success: #10b981;
            --warning: #f59e0b;
            --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', Consolas, monospace;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        nav {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .nav-logo {
            height: 50px;
            width: auto;
        }

        h1 { margin: 0; font-size: 1.5rem; font-weight: 800; color: #1e293b; letter-spacing: -0.025em; }
        
        .rules-section {
            padding: 2rem 2rem 0;
            display: flex;
            justify-content: center;
        }

        .rules-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            color: #92400e;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .code-snippet {
            background: rgba(255,255,255,0.6);
            padding: 2px 8px;
            border-radius: 4px;
            font-family: var(--font-mono);
            font-weight: bold;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .container {
            max-width: 1800px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }

        @media (max-width: 1200px) { .container { grid-template-columns: 1fr; } }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title { font-size: 1.1rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; }
        
        .badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 99px; font-weight: 600; text-transform: uppercase; }
        .badge-blue { background-color: #e0f2fe; color: #0369a1; }
        .badge-green { background-color: #dcfce7; color: #15803d; }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.2s;
        }
        
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

        .card-header {
            background-color: #f8fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-name { font-family: var(--font-mono); font-size: 0.95rem; font-weight: 700; color: #334155; }
        .record-count { font-size: 0.8rem; color: var(--text-muted); background: #f1f5f9; padding: 2px 8px; border-radius: 4px; }

        .table-responsive { overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        
        th {
            text-align: left;
            padding: 0.75rem 1.5rem;
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        td { padding: 0.75rem 1.5rem; border-bottom: 1px solid var(--border-color); color: var(--text-main); }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f1f5f9; }

        .empty-state { padding: 3rem; text-align: center; color: var(--text-muted); font-style: italic; }
        .error-state { padding: 1rem; color: #b91c1c; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; margin: 1rem; text-align: center; }
    </style>
</head>
<body>

<nav>
    <img src="logo.png" alt="Logo" class="nav-logo">
    <h1>Končar Lobby</h1>
</nav>

<div class="container">
    
    <div class="column">
        <div class="section-header">
            <span class="section-title">Zadane Baze</span>
            <span class="badge badge-blue">SELECT ONLY</span>
        </div>

        <?php
        $servername = "10.0.1.7";
        $username   = "koncar";
        $dbname     = "ETS_Baza";
        $password   = getenv('MOJA_TAJNA_SIFRA');

        if ($password === false) die("<div class='error-state'>Greška: Nije postavljena Environment Varijabla u Coolifyju!</div>");

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) die("<div class='error-state'>Greška spajanja na MySQL: " . $conn->connect_error . "</div>");

        function renderTable($conn, $tableName) {
            $check = $conn->query("SHOW TABLES LIKE '$tableName'");
            if($check->num_rows == 0) return;

            $sql = "SELECT * FROM $tableName";
            $result = $conn->query($sql);
            
            echo "<div class='card'>";
            echo "<div class='card-header'>";
            echo "<span class='table-name'>$tableName</span>";
            echo "<span class='record-count'>" . ($result ? $result->num_rows : 0) . " redova</span>";
            echo "</div>";
            echo "<div class='table-responsive'>";
            
            if ($result && $result->num_rows > 0) {
                echo "<table><thead><tr>";
                $fields = $result->fetch_fields();
                foreach ($fields as $field) echo "<th>" . $field->name . "</th>";
                echo "</tr></thead><tbody>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach($row as $val) echo "<td>" . htmlspecialchars($val) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='empty-state'>Tablica je prazna.</div>";
            }
            echo "</div></div>";
        }

        renderTable($conn, 'master_igre');
        renderTable($conn, 'master_izdavaci');
        renderTable($conn, 'master_prodaja');
        ?>
    </div>

    <div class="column">
        <div class="section-header">
            <span class="section-title">Vaše Baze</span>
            <span class="badge badge-green">OSTALE NAREDBE</span>
        </div>

        <?php
        $tables = $conn->query("SHOW TABLES WHERE Tables_in_$dbname NOT LIKE 'master_%'");
        
        $hasTables = false;

        if ($tables && $tables->num_rows > 0) {
            while ($row = $tables->fetch_array()) {
                $tblName = $row[0];
                $hasTables = true;
                
                try {
                    $res = $conn->query("SELECT * FROM $tblName LIMIT 10");
                    
                    echo "<div class='card'>";
                    echo "<div class='card-header'>";
                    echo "<span class='table-name' style='color:#0284c7'>$tblName</span>";
                    echo "<span class='badge badge-green' style='font-size:0.6rem'>SANDBOX</span>";
                    echo "</div>";
                    echo "<div class='table-responsive'>";
                    
                    if ($res && $res->num_rows > 0) {
                        echo "<table><thead><tr>";
                        $fields = $res->fetch_fields();
                        foreach ($fields as $field) echo "<th>" . $field->name . "</th>";
                        echo "</tr></thead><tbody>";
                        while($data = $res->fetch_assoc()) {
                            echo "<tr>";
                            foreach($data as $v) echo "<td>" . htmlspecialchars($v) . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<div class='empty-state'>Tablica kreirana. Čekam podatke (INSERT)...</div>";
                    }
                    echo "</div></div>";
                } catch (Exception $e) { }
            }
        } 
        
        if (!$hasTables) {
            echo "<div class='card' style='border-style:dashed'><div class='empty-state'>Nema učeničkih tablica.<br>Napravite <code>CREATE TABLE ime_prezime...</code></div></div>";
        }
        ?>
    </div>

</div>
</body>
</html>
