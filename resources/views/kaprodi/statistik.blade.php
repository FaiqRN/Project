<html>
<head>
    <title>Statistik Beban Kerja Dosen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #d3d3d3;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .container {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .controls select, .controls button {
            margin: 0 10px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .controls button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .controls button:hover {
            background-color: #218838;
        }
        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .chart {
            width: 100%;
            height: 300px;
        }
        .table-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #d3d3d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Statistik Beban Kerja Dosen</div>
        <div class="controls">
            <select>
                <option>2024</option>
            </select>
            <select>
                <option>Semester ganjil</option>
            </select>
            <button><i class="fas fa-download"></i> Unduh File</button>
        </div>
        <div class="chart-title">Grafik Visualisasi Beban Kerja</div>
        <div class="chart">
            <canvas id="workloadChart"></canvas>
        </div>
    </div>

    <div class="table-container">
        <h1>Hasil Statistik Beban Kerja Dosen</h1>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIDN</th>
                    <th>Rapat Kurikulum</th>
                    <th>Workshop</th>
                    <th>Seminar Akademik</th>
                    <th>Pengabdian</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Dr. Andi Wijaya</td>
                    <td>12345678</td>
                    <td>7,5</td>
                    <td>7,5</td>
                    <td>7,5</td>
                    <td>7,5</td>
                    <td>30</td>
                </tr>
                <tr>
                    <td>Dr. Citra Lestari</td>
                    <td>12345678</td>
                    <td>10</td>
                    <td>10</td>
                    <td>10</td>
                    <td>10</td>
                    <td>40</td>
                </tr>
                <tr>
                    <td>Dr. Wijaya</td>
                    <td>12345678</td>
                    <td>6,3</td>
                    <td>6,3</td>
                    <td>6,3</td>
                    <td>6,3</td>
                    <td>25</td>
                </tr>
                <tr>
                    <td>Dr. Kurnia</td>
                    <td>12345678</td>
                    <td>11</td>
                    <td>11</td>
                    <td>11</td>
                    <td>11</td>
                    <td>42</td>
                </tr>
                <tr>
                    <td>Dr. Diah</td>
                    <td>12345678</td>
                    <td>13</td>
                    <td>13</td>
                    <td>13</td>
                    <td>13</td>
                    <td>50</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('workloadChart').getContext('2d');
        var workloadChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Dr. Andi Wijaya', 'Dr. Citra Lestari', 'Dr. Wijaya', 'Dr. Kurnia', 'Dr. Diah'],
                datasets: [{
                    label: 'Total',
                    data: [30, 40, 20, 35, 45],
                    backgroundColor: '#d4a017'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>