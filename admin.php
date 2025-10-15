<?php
include "config.php";
$query = "SELECT * FROM students";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Import CSV</title>
        <style>
        *{margin:0px; padding:0px;}
        html{background:rgb(114, 215, 240);}
        body{height: auto; min-height: 1500px; width: 1500px; margin: 0 auto; background-color: rgb(255, 255, 255); padding: 5px; color: rgb(18, 34, 175);font-size: 125%;}
        table{border-collapse: collapse; border:1px solid gray; width: 100%; margin-top: 10px;}
        table td,table th{padding: 10px; border:1px solid gray; text-align: left;}
        h2{font-size:40px;text-align: center;padding: 20px;color:rgb(77, 87, 90);}
        h4{padding: 20px;color:rgb(0, 0, 0);}
        button{padding: 5px 25px 5px 25px;font-size:20px;background: rgb(114, 215, 240);color: black; border-radius: 5px; border-color: rgba(84, 136, 189, 0.539);justify-content: center;}
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8fb;
        }
        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th {
            background-color: #87ceeb; /* Sky Blue */
            color: white;
            padding: 10px;
            border: 1px solid #ccc;
        }
        td {
            background-color: #f0f0f0; /* Light Grey */
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        tr:nth-child(even) td {
            background-color: #e6e6e6; /* Slightly darker grey for alternate rows */
        }
        .no-data {
            text-align: center;
            margin-top: 50px;
            font-size: 20px;
            color: #666;
        }
    </style>
    </head> 
<body>
    <h2>WELCOME GPCW ADMIN!</h2><br>
    <p style="float: left";><button onclick="window.location.href='dashboard.php';"><b>Display dashboard</b></button></p>
    <p style="float: right";><button onclick="window.location.href='login.html';"><b>Logout</b></button></p><br><br>
    <form action="importdata.php" method="post" enctype="multipart/form-data"><br>
    <h4>Import CSV File here!<h4><input style="font-size:20px;color: red;" type="file" name="file"/>
        <button type="submit" name="submit"><b>IMPORT</b></button>
</form>
<?php if (mysqli_num_rows($result) > 0): ?>
<h2>Students Admission Data</h2>
    <table>
        <thead>
            <tr>
                <th>App_ID</th>
                <th>Name</th>
                <th>DOB</th>
                <th>Marks</th>
                <th>Community</th>
                <th>Rank</th>
                <th>Dept</th>
                <th>Status</th>
                <th>Allotted_Category</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['App_ID']; ?></td>
                <td><?= $row['Name']; ?></td>
                <td><?= $row['DOB']; ?></td>
                <td><?= $row['Marks']; ?></td>
                <td><?= $row['Community']; ?></td>
                <td><?= $row['Rank']; ?></td>
                <td><?= $row['Dept'] ?? 'NULL'; ?></td>
                <td><?= $row['Status'] ?? 'NULL'; ?></td>
                <td><?= $row['Allotted_Category'] ?? 'NULL'; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="no-data">THERE IS NO ADMISSION DATA!</div>
    <?php endif; ?>
</body>
</html>