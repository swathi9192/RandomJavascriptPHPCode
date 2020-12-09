
<html>

<head>
    <style>
        body {
            text-align: center;
            background-color: lightgray;
            margin: 50px auto;
        }

        table.results {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 60%;
            margin: 0px auto;
        }

        table.results td,
        table.results th {
            border: 1px solid #dddddd;
            text-align: center;
            padding: 8px;
        }

        table.results tr:nth-child(even) {
            background-color: #dddddd;
        }

        #insertForm {
            margin-top: 20px;
        }

        #insertForm table {
            margin: 0px auto;
        }

        #insertForm table td {
            text-align: left;
        }

        input[type="submit"] {

            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            margin: 20px;
            cursor: pointer;
        }

        .error {
            color: #FF0000;
        }
    </style>

</head>

<body>
    <?php 
        $nameErr = $ageErr = $jobErr = "";
        $name = $age = $job_title = "";
        //Returns database connection to mysql
        function sqlConnect()
        {
            $host = "127.0.0.1";
            $username="BXP_At_Home_Name";
            $password="BXP_At_Home_Key";
            return new mysqli($host, $username, $password);
        }
        $conn = sqlConnect();
        if($conn->connect_error)
        {
            die("Connection failed");
        }
        //Query to select data from bxp_test table
        $sql = "select name,age,job_title from bxp_at_home.bxp_test";
        $result = $conn->query($sql);
        //checks if the request method is post. True when user submits valid form data
        if (!empty($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST' ){
      
            $name = sanitizeInput($_POST["name"]);      
            $age = sanitizeInput($_POST["age"]);       
            $job_title = sanitizeInput($_POST["job_title"]);      
            insertRecord($name,$age,$job_title);  
        }
        //Returns form field data after removing spaces, lashes and encoding html characters
        function sanitizeInput($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data,ENT_QUOTES);        
            return $data;
        }
        // Inserts record into bxp_test table
        function insertRecord($name,$age,$job_title)
        {         
            global $conn;          
            $sql = "INSERT INTO bxp_at_home.bxp_test VALUES ('".$name."','".$age."','".$job_title."')";           
            if ($conn->query($sql) === TRUE) {   
                $conn->close();    
                header("Location:".$_SERVER['PHP_SELF']);
            } else {
                echo $conn->error;
            }  
             
        }
    ?>
    <!-- Builds a html table with resultset -->
    <div id="root">
        <h3>PHP database connectivity to MySQL</h3>
        <?php    
        if($result->num_rows > 0)
        {   
            echo "<table class='results'><tr><th>Name</th><th>Age</th><th>Job Title</th></tr>";
           
            while($row = $result->fetch_assoc())
            {
                echo "<tr><td>".$row['name']."</td><td>".
                $row['age']."</td><td>".$row['job_title']."</td></tr>";
            }
            echo "</table>";
        }
        else {
            echo "<h4>No data found in bxp_test table. Add a record in the form below.</h4>";       
        }
        ?>
        <!-- HTML user form to post data to bxp_test -->
        <form id="insertForm" action="" method="POST">
            <table>
                <tr>
                    <td>
                        <label for="fname">Name: </label>
                    </td>
                    <td>
                        <input type="text" id="fname" name="name" value=<?php $name?>>                        
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="fage">Age: </label>
                    </td>
                    <td>
                        <input type="number" id="fage" name="age" min="1" max="150">                      
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="fjobtitle">Job Title: </label>
                    </td>
                    <td>
                        <input type="text" id="fjobtitle" name="job_title">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center">
                        <input type="submit" value="Insert Record" name="submit">
                    </td>
                </tr>
            </table>
        </form>
        <p id="err" class="error"></p>

    </div>
    <script type="text/javascript">
        
        window.addEventListener("DOMContentLoaded", function () {
            var frm = document.getElementById("insertForm");
            frm.addEventListener("submit", validateForm);
            //Validates the form data before posting it to mysql
            function validateForm(evt) {
                var err = "";
                var name = document.getElementById("fname").value;
                var age = document.getElementById("fage").value;
                var job_title = document.getElementById("fjobtitle").value;

                if (name == "") {
                    err = "Please enter name";

                } else if (age == "") {
                    err = "Please enter age";

                } else if (job_title == "") {
                    err = "Please enter job title";

                }
                if (err != "") {
                    document.getElementById("err").innerText = err;
                    evt.preventDefault();
                    evt.stopPropagation();
                    return false;
                } else {
                    return true;
                }
            }
        });
    </script>
</body>

</html>