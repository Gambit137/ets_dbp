<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Workshop Environment</title>
    <style>
        :root {
            --bg-body: #f3f4f6;
            --bg-card: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --primary: #2563eb;
            --success: #059669;
            --font-sans: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
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

        header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 { margin: 0; font-size: 1.25rem; font-weight: 600; }
        .status-dot { height: 8px; width: 8px; background-color: var(--success); border-radius: 50%; display: inline-block; margin-right: 8px; }
        .server-status { font-size: 0.875rem; color: var(--text-muted); display: flex; align-items: center; }

        .container {
            max-width: 1600px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 1024px) { .container { grid-template-columns: 1fr; } }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title { font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--text-muted); }
        .badge { font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; font-weight: 500; }
        .badge-primary { background-color: #dbeafe; color: #1e40af; }
        .badge-success { background-color: #d1fae5; color: #065f46; }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            background-color: #f9fafb;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-name { font-family: var(--font-mono); font-size: 0.875rem; font-weight: 600; color: var(--text-main); }
        .record-count { font-size: 0.75rem; color: var(--text-muted); }

        .table-responsive { overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        
        th {
            text-align: left;
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border-color);
        }

        td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); color: var(--text-main); }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f9fafb; }

        .empty-state { padding: 2rem; text-align: center; color: var(--text-muted); font-style: italic; font-size: 0.875rem; }
        .error-state { padding: 1rem; color: #b91c1c; background-color: #fef2f2; font-size: 0.875rem; border: 1px solid #fecaca; border-radius: 6px; margin-bottom: 1rem; }
    </style>
</head>
<body>

<header>
    <h1>SQL Workshop</h1>
    <div class="server-status">
        <span class="status-dot"></span> System Online
    </div>
</header>

<div class="container">
    
    <div class="column">
        <div class="section-header">
            <span class="section-title">Reference Datasets</span>
            <span class="badge badge-primary">READ ONLY</span>
        </div>

        <?php
        $servername = "mysql-database-v84g000ok0s40g4wwk4ccg48";
        $username   = "koncar";
        $dbname     = "ETS_Baza";
        $password   = getenv('MOJA_TAJNA_SIFRA');

        if ($password === false) {
            die("<div class='error-state'>Configuration Error: Environment variable missing.</div>");
        }

        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("<div class='error-state'>Database Connection Failed: " . $conn->connect_error . "</div>");
        }

        function renderTable($conn, $tableName, $displayName) {
            $check = $conn->query("SHOW TABLES LIKE '$tableName'");
            if($check->num_rows == 0) return;

            $sql = "SELECT * FROM $tableName";
            $result = $conn->query($sql);
            
            echo "<div class='card'>";
            echo "<div class='card-header'>";
            echo "<span class='table-name'>$displayName</span>";
            echo "<span class='record-count'>" . ($result ? $result->num_rows : 0) . " records</span>";
            echo "</div>";
            echo "<div class='table-responsive'>";
            
            if ($result && $result->num_rows > 0) {
                echo "<table><thead><tr>";
                $fields = $result->fetch_fields();
                foreach ($fields as $field) {
                    echo "<th>" . $field->name . "</th>";
                }
                echo "</tr></thead><tbody>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach($row as $val) {
                        echo "<td>" . htmlspecialchars($val) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='empty-state'>No data available.</div>";
            }
            echo "</div></div>";
        }

        renderTable($conn, 'master_igre', 'public.master_igre');
        renderTable($conn, 'master_izdavaci', 'public.master_izdavaci');
        renderTable($conn, 'master_prodaja', 'public.master_prodaja');
        ?>
    </div>

    <div class="column">
        <div class="section-header">
            <span class="section-title">Student Workspaces</span>
            <span class="badge badge-success">SANDBOX</span>
        </div>

        <?php
        $tables = $conn->query("SHOW TABLES LIKE 'ucenik_%'");
        
        if ($tables && $tables->num_rows > 0) {
            while ($row = $tables->fetch_array()) {
                $tblName = $row[0];
                $studentName = ucfirst(str_replace("ucenik_", "", $tblName));
                
                try {
                    $res = $conn->query("SELECT * FROM $tblName LIMIT 10");
                    
                    echo "<div class='card'>";
                    echo "<div class='card-header'>";
                    echo "<span class='table-name'>$tblName</span>";
                    echo "<span class='badge badge-success'>Active</span>";
                    echo "</div>";
                    echo "<div class='table-responsive'>";
                    
                    if ($res && $res->num_rows > 0) {
                        echo "<table><thead><tr>";
                        $fields = $res->fetch_fields();
                        foreach ($fields as $field) {
                            echo "<th>" . $field->name . "</th>";
                        }
                        echo "</tr></thead><tbody>";
                        while($data = $res->fetch_assoc()) {
                            echo "<tr>";
                            foreach($data as $v) {
                                echo "<td>" . htmlspecialchars($v) . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<div class='empty-state'>Table created. Waiting for data insertion.</div>";
                    }
                    echo "</div></div>";
                } catch (Exception $e) {
                    // Silent fail
                }
            }
        } else {
            echo "<div class='card'><div class='empty-state'>No active student tables found.<br>Execute CREATE TABLE statement to begin.</div></div>";
        }
        ?>
    </div>

</div>
</body>
</html>
