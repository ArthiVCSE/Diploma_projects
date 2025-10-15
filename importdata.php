<?php
// Include the database connection
include "config.php";  // Your MySQL database connection script

// Check if the form was submitted
if (isset($_POST['submit'])) {
    
    // MIME type for CSV files
    $csvMimes = array('application/vnd.ms-excel', 'text/csv', 'application/csv');
    
    // Check if a file was uploaded and if it's a CSV file
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
        
        // Open the CSV file
        if (($handle = fopen($_FILES['file']['tmp_name'], 'r')) !== FALSE) {
            // Skip the first row if it contains headers
            fgetcsv($handle);

            // Loop through each row of the CSV file
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Extract values from each row (adjust according to your CSV columns)
                $App_ID= $data[0];
                $Name= $data[1];
                $DOB= $data[2];
                $Marks= $data[3];
                $Community= $data[4];
                $Rank= $data[5];

                        // Insert data into the database using prepared statements
                        $stmt = $conn->prepare("INSERT INTO students (App_ID, Name, DOB, Marks, Community, `Rank`) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssss", $App_ID, $Name, $DOB, $Marks, $Community, $Rank);
                        $stmt->execute();
                    }
        
                    fclose($handle);  // Close the file after reading
                    echo "CSV data imported successfully!";
                } else {
                    echo "Error opening the file.";
                }
            } else {
                echo "Invalid.Please upload a CSV file.";
            }
        }
        ?>
        