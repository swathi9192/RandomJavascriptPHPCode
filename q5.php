<!-- 
### Create database model with below commands in mysql

create table bxp_ab_testing (design_id int not null auto_increment, design_name varchar(50), split_percent int, visits int,promotion_id int, primary key (design_id));

create table bxp_promotions (id int not null auto_increment, name varchar(50), primary key (id));

create table counter (promotion_id int, counter int);

alter table bxp_ab_testing add constraint fk_ab_testing_1 foreign key (promotion_id) references bxp_promotions (id);

alter table counter add constraint fk_counter_1 foreign key (promotion_id) references bxp_promotions (id);

### Insert promotions into bxp_promotions 

insert into bxp_promotions(name) values("promotion 1");
insert into bxp_promotions(name) values("promotion 2");

### Insert counter values for each promotions in counter table

insert into counter values(0,1);
insert into counter values(0,2);

### Insert designs into bxp_ab_testing table for each promotion

insert into bxp_ab_testing (design_name, split_percent, visits, promotion_id) values ("Design 1",50,0,1);
insert into bxp_ab_testing (design_name, split_percent, visits, promotion_id) values ("Design 2",25,0,1);
insert into bxp_ab_testing (design_name, split_percent, visits, promotion_id) values ("Design 3",25,0,1);
insert into bxp_ab_testing (design_name, split_percent, visits, promotion_id) values ("Design 4",50,0,2);
insert into bxp_ab_testing (design_name, split_percent, visits, promotion_id) values ("Design 5",30,0,2);
insert into bxp_ab_testing (design_name, split_percent, visits, promotion_id) values ("Design 6",20,0,2);
-->
<!--
Note:
For promotion 1, out of every 4 hits, design 1 is shown 2 times , design 2 is shown 1 time and design 3 is shown 1 time.
For promotion 2, out of every 10 hits, design 4 is shown 5 times, design 5 is shown 3 times and design 6 is shown 2 times.
-->
<html>

<head>

    <style>
        body {
            text-align: center;
            background-color: lightgray;
            margin: 50px auto;
        }

        button {

            background-color: #4CAF50;
            border: none;
            color: black;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 50px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php 
        // Change promotion id for a different promotions
        $promotion_id=1;
        //Returns connection object of mysql db
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
            die("connection failed");
        }
        // updates counter for each visit to the page. counter gives the total number of visits for the selected promotion.
        $sql = "update bxp_at_home.counter set counter = counter+1 where promotion_id=".$promotion_id;
        $count=0;
        if ($conn->query($sql) === TRUE) {
            // Gets counter and assigns it to $count varaible
            $sql = "select counter from bxp_at_home.counter where promotion_id=".$promotion_id;
            $result = $conn->query($sql);
            if($row = $result->fetch_row())
            {
                $count = $row[0];
            }
            else
            {
                echo "Error selecting counter record: ". $conn->error;
            }
            $result -> free_result();
        } else {
            echo "Error updating counter record: " . $conn->error;
        }
        // Gets the designs for the selected promotion
        $sql = "select design_id,design_name,split_percent,visits from bxp_at_home.bxp_ab_testing where promotion_id=".$promotion_id;
        $resultset= array();
        if($result = $conn->query($sql))
        {       
            while($row = $result->fetch_assoc())
            {
                $resultset[] = $row;
            
            }        
        }
        else {
            echo "Error selecting design records: ".$conn->error;
        }
        $result -> free_result();       
        
        if(count($resultset)<3)
        {
            echo "Please add atleast 3 designs for the promotion in bxp_ab_testing table";
        }
        else{
            $design_id = 0;
            echo "<div id ='root'><h3>total num of visits to site ".$count." for promotion id ".$promotion_id."</h3>";
            /* checks if no of visits to each design is less than its percentage of total visits, 
                if yes returns the design and increments its visits by 1
                supposing each promotion has atleast 3 designs */
            if($resultset[0]['visits'] < ($count*$resultset[0]['split_percent'])/100)
            {
                echo "<button style='background-color:red'>".$resultset[0]['design_name']." visits ".($resultset[0]['visits']+1)."</button>";
                $design_id = $resultset[0]['design_id'];
            }
            else if($resultset[1]['visits']< ($count*$resultset[1]['split_percent'])/100)
            {
                echo "<button style='background-color:yellow'>".$resultset[1]['design_name']." visits ".($resultset[1]['visits']+1)."</button>";
                $design_id = $resultset[1]['design_id'];
            }
            else if($resultset[2]['visits']< ($count*$resultset[2]['split_percent'])/100)
            {
                echo "<button style='background-color:blue'>".$resultset[2]['design_name']." visits ".($resultset[2]['visits']+1)."</button>";
                $design_id = $resultset[2]['design_id'];
            }
            
            echo "</div>";
            // It updates the no of visits to the design rendered
            $stmt = $conn->prepare("update bxp_at_home.bxp_ab_testing set visits=visits+1 where design_id = ? ");
            $stmt->bind_param("i", $design_id);

            // set parameters and execute
            $design_id = $design_id;
            $stmt->execute();
        }
        $conn->close();

    ?>

</body>

</html>