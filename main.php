<?php
include "config.php";?>
<?php
// Initialize all fields
$application_id = "";$department ="";$name="";$dob="";$marks="";$rank="";$community="";$quota="";$msg1 = "";$msg2 = "";$result1 = null;$status="";$data="";
//fetch
if (isset($_POST['searchdata'])) {
    $application_id = $_POST['search'];

    $application_id_safe = mysqli_real_escape_string($conn, $application_id);
    $query = "SELECT * FROM students WHERE App_ID = '$application_id_safe'";
    $data = mysqli_query($conn, $query);
    if ($data && mysqli_num_rows($data) > 0) {
        $msg1 = "✅ Application ID found!";
        $result1 = mysqli_fetch_assoc($data);
        // Set values from database
        $name = $result1['Name'];
        $marks = $result1['Marks'];
        $dob = $result1['DOB'];
        $rank = $result1['Rank'];
        $community = $result1['Community'];
        $quota= $result1['Allotted_Category'];
        $status=$result1['Status'];
        if ($status == 'Confirmed') {
            $m1="$application_id is already confirmed.";//can only drop
        }
        if($status=='Waiting'){
            $m2="$application_id is already in waiting list.";//can allocate, drop
        }
        if($status=='Non-appear'){
            $m4="$application_id was absent.";//can allocate, wait
        }
        if($status=='Dropped'){
            $m3="$application_id is already dropped.";//can't do any changes
        }
    } else {
        $msg2 = "❌ Invalid! Enter a valid Application ID.";//can't perform actions
    }
}
?>
<?php 
//confirm
if(isset($_POST['update']))
{
    // Get student info from form
    $application_id = $_POST['search'];$department = $_POST['Dept'];$name=$_POST['name'];$dob=$_POST['DOB'];$marks=$_POST['Marks'];$rank=$_POST['Rank'];$community=$_POST['Community'];$quota=$_POST['quota'];
    // Get student data
    $query = "SELECT * FROM students WHERE App_ID = '$application_id'";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);
    // Get seat info from selected department
    $seat_query = "SELECT `$quota`, `{$quota}_WL` FROM `$department`";
    $seat_result = mysqli_query($conn, $seat_query);
    $seats = mysqli_fetch_assoc($seat_result); 
    //get status 
    $q = "SELECT Status, Allotted_Category, Dept FROM students WHERE App_ID = '$application_id'";
    $r = mysqli_query($conn, $q);
    if ($r && mysqli_num_rows($r) > 0) {
        $row = mysqli_fetch_assoc($r);
        $sta = $row['Status'];$wseat = $row['Allotted_Category'];$wdept = $row['Dept'];
        if ($sta == "Waiting" && $seats[$quota] > 0) {
            // Fetch current waiting list count
            $s_query = "SELECT `{$wseat}_WL` FROM `$wdept`";
            $s_result = mysqli_query($conn, $s_query);
                if ($s_result && mysqli_num_rows($s_result) > 0) {
                $s = mysqli_fetch_assoc($s_result);
                $current_count = (int)$s["{$wseat}_WL"];
                    // Decrease waiting list count only if it's more than 0
                    if ($current_count > 0) {
                    $w_update = $current_count - 1;
                    $wupdate_query = "UPDATE `$wdept` SET `{$wseat}_WL` = $w_update";
                    mysqli_query($conn, $wupdate_query);
                    }   
            // Seat available - allocate
            $updated_count = $seats[$quota] - 1;
            $update_seat_query = "UPDATE `$department` SET `$quota` = $updated_count";
            mysqli_query($conn, $update_seat_query);
            $confirm_query = "UPDATE students SET  Dept = '$department', Community ='$community', Status = 'Confirmed', Allotted_Category = '$quota', `Name`='$name', DOB='$dob', Marks='$marks', `Rank`='$rank' WHERE App_ID = '$application_id'";
            mysqli_query($conn, $confirm_query);
            $message1 = "✅ Seat allocated in $department for $application_id under $quota category.";
            $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";}
            else{
                $message2 = "⚠️ No seat available in $department for $application_id under $quota category.";
                $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";
            }
        }
    elseif($sta=="Non-appear"&& $seats[$quota] > 0 ){
        $updated_count = $seats[$quota] - 1;
        $update_seat_query = "UPDATE `$department` SET `$quota` = $updated_count";
        mysqli_query($conn, $update_seat_query);
        $confirm_query = "UPDATE students SET  Dept = '$department', Community ='$community', Status = 'Confirmed', Allotted_Category = '$quota', `Name`='$name', DOB='$dob', Marks='$marks', `Rank`='$rank' WHERE App_ID = '$application_id'";
        mysqli_query($conn, $confirm_query);
        $message1 = "✅ Seat allocated in $department for $application_id under $quota category.";
        $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";}
    elseif($seats[$quota] > 0) {
        // Seat available - allocate
        $updated_count = $seats[$quota] - 1;
        $update_seat_query = "UPDATE `$department` SET `$quota` = $updated_count";
        mysqli_query($conn, $update_seat_query);
        // Update student status and department
        $confirm_query = "UPDATE students SET  Dept = '$department', Community ='$community', Status = 'Confirmed', Allotted_Category = '$quota', `Name`='$name', DOB='$dob', Marks='$marks', `Rank`='$rank' WHERE App_ID = '$application_id'";
        mysqli_query($conn, $confirm_query);
        $message1 = "✅ Seat allocated in $department for $application_id under $quota category.";
        $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";
    } 
    else{
    $message2 = "⚠️ No seat available in $department for $application_id under $quota category.";
    $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";
}}}
//waiting
if(isset($_POST['waiting']))
{
// Get student info from form
$application_id = $_POST['search'];
$department = $_POST['Dept'];
$name=$_POST['name'];
$dob=$_POST['DOB'];
$marks=$_POST['Marks'];
$rank=$_POST['Rank'];
$community=$_POST['Community'];
$quota=$_POST['quota'];
    // Get student data
    $query = "SELECT * FROM students WHERE App_ID = '$application_id'";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);

    // Get seat info from selected department
    $seat_query = "SELECT `$quota`, `{$quota}_WL` FROM `$department`";
    $seat_result = mysqli_query($conn, $seat_query);
    $seats = mysqli_fetch_assoc($seat_result);  
    //get status info
    //get status 
    $q = "SELECT Status, Allotted_Category, Dept FROM students WHERE App_ID = '$application_id'";
    $r = mysqli_query($conn, $q);
    if ($r && mysqli_num_rows($r) > 0) {
        $row = mysqli_fetch_assoc($r);
        $sta = $row['Status'];}
    if($sta=="Non-appear"&& $seats[$quota] > 0 ){
        // No seat - add to waiting list
        $wl_updated = $seats["{$quota}_WL"] + 1;
        $update_wl_query = "UPDATE `$department` SET `{$quota}_WL` = $wl_updated";
        mysqli_query($conn, $update_wl_query);
        // Optional: mark student status as waiting
        $wait_query = "UPDATE students SET Status = 'Waiting', Dept = '$department',Allotted_Category = '$quota' WHERE App_ID = '$application_id'";
        mysqli_query($conn, $wait_query);
        $message4 = "$application_id is added to waiting list in $department under $quota category.";
        $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";
    }
    elseif($seats[$quota] > 0){
        $message3= "Seat is already available.";
        $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";
    }
    else{
         // No seat - add to waiting list
         $wl_updated = $seats["{$quota}_WL"] + 1;
         $update_wl_query = "UPDATE `$department` SET `{$quota}_WL` = $wl_updated";
         mysqli_query($conn, $update_wl_query);
         // Optional: mark student status as waiting
         $wait_query = "UPDATE students SET Status = 'Waiting', Dept = '$department',Allotted_Category = '$quota' WHERE App_ID = '$application_id'";
         mysqli_query($conn, $wait_query);
         $message4 = "$application_id is added to waiting list in $department under $quota category.";
         $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";
    } 
}
// drop
if(isset($_POST['drop']))
{
        $application_id = $_POST['search'];$department = $_POST['Dept'];$name=$_POST['name'];$dob=$_POST['DOB'];$marks=$_POST['Marks'];$rank=$_POST['Rank'];$community=$_POST['Community'];$quota=$_POST['quota'];
        $q = "SELECT Status FROM students WHERE App_ID = '$application_id'";
        $r= mysqli_query($conn, $q); $row = mysqli_fetch_assoc($r);$sta = $row['Status'];
        //confirmed already
            if($sta=="Confirmed"){
                $drop_query = "UPDATE students SET Status = 'Dropped', Dept ='', Allotted_Category=''  WHERE App_ID = '$application_id'";
                mysqli_query($conn, $drop_query);
                //increase seat count
                $seat_query = "SELECT `$quota` FROM `$department`";
                $seat_result = mysqli_query($conn, $seat_query);$seats = mysqli_fetch_assoc($seat_result);  
                $com_updated = $seats["{$quota}"] + 1;
                $update_com_query = "UPDATE `$department` SET `{$quota}` = $com_updated";
                mysqli_query($conn, $update_com_query);
                $message5 = "✅ The applicant $application_id is dropped from $department.";
            }
            elseif($sta=="Waiting"){
                $drop_query = "UPDATE students SET Status = 'Dropped', Dept ='', Allotted_Category=''  WHERE App_ID = '$application_id'";
                mysqli_query($conn, $drop_query);
                //decrease seat count in WL
                $seat_query = "SELECT  `{$quota}_WL` FROM `$department`";
                $seat_result = mysqli_query($conn, $seat_query);$seats = mysqli_fetch_assoc($seat_result);  
                $wl_updated = $seats["{$quota}_WL"] - 1;
                $update_wl_query = "UPDATE `$department` SET `{$quota}_WL` = $wl_updated";
                mysqli_query($conn, $update_wl_query);
                $message5 = "✅ The applicant $application_id is dropped from $department waiting list.";
            }
            else{
            $drop_query = "UPDATE students SET Status = 'Dropped' WHERE App_ID = '$application_id'";
            mysqli_query($conn, $drop_query);
            $message5 = "✅ The applicant $application_id is dropped.";
        }
    $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";
} 
// absent
if(isset($_POST['absent']))
{
        // Get student id from form
        $application_id = $_POST['search'];
        // Update student status
        $drop_query = "UPDATE students SET Status = 'Non-appear' WHERE App_ID = '$application_id'";
        mysqli_query($conn, $drop_query);
        $message6 = "✅ The applicant $application_id is marked as Non-appear.";
        $application_id="";$name = "";$dob = "";$rank = "";$community="";$marks="";$department="";$quota="";
} 
?>
<?php
$query1 = "SELECT CE FROM computer_engineering";
$resultce = mysqli_query($conn, $query1);
if ($row = mysqli_fetch_assoc($resultce)) {
    $CE = $row['CE'];
}
$query2 = "SELECT OC, OC_WL FROM computer_engineering";
$resultoc = mysqli_query($conn, $query2);
if ($row = mysqli_fetch_assoc($resultoc)) {
    $oc= $row['OC'];
    $oc_waiting_list = $row['OC_WL']; // Fetching waiting list
    if($oc==0 && $oc_waiting_list>0){
        $OC="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($oc_waiting_list) . "</span>";
    }
    else{
        $OC=$oc;
    }
}
$query3 = "SELECT BC, BC_WL FROM computer_engineering";
$resultbc = mysqli_query($conn, $query3);
if ($row = mysqli_fetch_assoc($resultbc)) {
    $bc = $row['BC'];
    $bc_waiting_list = $row['BC_WL']; // Fetching waiting list
    if($bc==0 && $bc_waiting_list>0){
        $BC="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bc_waiting_list) . "</span>";
    }
    else{
        $BC=$bc;
    }
}
$query4 = "SELECT BCM, BCM_WL FROM computer_engineering";
$resultbcm = mysqli_query($conn, $query4);
if ($row = mysqli_fetch_assoc($resultbcm)) {
    $bcm = $row['BCM'];
    $bcm_waiting_list = $row['BCM_WL']; // Fetching waiting list
    if($bcm==0 && $bcm_waiting_list>0){
        $BCM="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bcm_waiting_list) . "</span>";
    }
    else{
        $BCM=$bcm;
    }
}
$query5 = "SELECT MBC, MBC_WL FROM computer_engineering";
$resultmbc = mysqli_query($conn, $query5);
if ($row = mysqli_fetch_assoc($resultmbc)) {
    $mbc = $row['MBC'];
    $mbc_waiting_list = $row['MBC_WL']; // Fetching waiting list
    if($mbc==0 && $mbc_waiting_list>0){
        $MBC="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($mbc_waiting_list) . "</span>";
    }
    else{
        $MBC=$mbc;
    }
} 
$query6 = "SELECT SC, SC_WL FROM computer_engineering";
$resultsc = mysqli_query($conn, $query6);
if ($row = mysqli_fetch_assoc($resultsc)) {
    $sc = $row['SC'];
    $sc_waiting_list = $row['SC_WL']; // Fetching waiting list
    if($sc==0 && $sc_waiting_list>0){
        $SC="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sc_waiting_list) . "</span>";
    }
    else{
        $SC=$sc;
    }
} 
$query7 = "SELECT SCA, SCA_WL FROM computer_engineering";
$resultsca = mysqli_query($conn, $query7);
if ($row = mysqli_fetch_assoc($resultsca)) {
    $sca = $row['SCA'];
    $sca_waiting_list = $row['SCA_WL']; // Fetching waiting list
    if($sca==0 && $sca_waiting_list>0){
        $SCA="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sca_waiting_list) . "</span>";
    }
    else{
        $SCA=$sca;
    }
} 
$query8 = "SELECT ST, ST_WL FROM computer_engineering";
$resultst = mysqli_query($conn, $query8);
if ($row = mysqli_fetch_assoc($resultst)) {
    $st = $row['ST'];
    $st_waiting_list = $row['ST_WL']; // Fetching waiting list
    if($st==0 && $st_waiting_list>0){
        $ST="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($st_waiting_list) . "</span>";
    }
    else{
        $ST=$st;
    }
} 

$query9 = "SELECT CT FROM computer_technology";
$result = mysqli_query($conn, $query9);
if ($row = mysqli_fetch_assoc($result)) {
    $CT = $row['CT'];
} 
$query10 = "SELECT OC, OC_WL FROM computer_technology";
$result = mysqli_query($conn, $query10);
if ($row = mysqli_fetch_assoc($result)) {
    $oc_ct = $row['OC'];
    $oc_waiting_list_ct = $row['OC_WL']; // Fetching waiting list
    if($oc_ct==0 && $oc_waiting_list_ct>0){
        $OC_CT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($oc_waiting_list_ct) . "</span>";
    }
    else{
        $OC_CT=$oc_ct;
    }
}
$query11 = "SELECT BC, BC_WL FROM computer_technology";
$result = mysqli_query($conn, $query11);
if ($row = mysqli_fetch_assoc($result)) {
    $bc_ct = $row['BC'];
    $bc_waiting_list_ct = $row['BC_WL']; // Fetching waiting list
    if($bc_ct==0 && $bc_waiting_list_ct>0){
        $BC_CT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bc_waiting_list_ct) . "</span>";
    }
    else{
        $BC_CT=$bc_ct;
    }
}
$query12 = "SELECT BCM, BCM_WL FROM computer_technology";
$result = mysqli_query($conn, $query12);
if ($row = mysqli_fetch_assoc($result)) {
    $bcm_ct = $row['BCM'];
    $bcm_waiting_list_ct = $row['BCM_WL']; // Fetching waiting list
    if($bcm_ct==0 && $bcm_waiting_list_ct>0){
        $BCM_CT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bcm_waiting_list_ct) . "</span>";
    }
    else{
        $BCM_CT=$bcm_ct;
    }
}
$query13 = "SELECT MBC, MBC_WL FROM computer_technology";
$result = mysqli_query($conn, $query13);
if ($row = mysqli_fetch_assoc($result)) {
    $mbc_ct = $row['MBC'];
    $mbc_waiting_list_ct = $row['MBC_WL']; // Fetching waiting list
    if($mbc_ct==0 && $mbc_waiting_list_ct>0){
        $MBC_CT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($mbc_waiting_list_ct) . "</span>";
    }
    else{
        $MBC_CT=$mbc_ct;
    }
} 
$query14 = "SELECT SC, SC_WL FROM computer_technology";
$result = mysqli_query($conn, $query14);
if ($row = mysqli_fetch_assoc($result)) {
    $sc_ct = $row['SC'];
    $sc_waiting_list_ct = $row['SC_WL']; // Fetching waiting list
    if($sc_ct==0 && $sc_waiting_list_ct>0){
        $SC_CT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sc_waiting_list_ct) . "</span>";
    }
    else{
        $SC_CT=$sc_ct;
    }
} 
$query15 = "SELECT SCA, SCA_WL FROM computer_technology";
$result = mysqli_query($conn, $query15);
if ($row = mysqli_fetch_assoc($result)) {
    $sca_ct = $row['SCA'];
    $sca_waiting_list_ct = $row['SCA_WL']; // Fetching waiting list
    if($sca_ct==0 && $sca_waiting_list_ct>0){
        $SCA_CT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sca_waiting_list_ct) . "</span>";
    }
    else{
        $SCA_CT=$sca_ct;
    }
} 
$query16 = "SELECT ST, ST_WL FROM computer_technology";
$result = mysqli_query($conn, $query16);
if ($row = mysqli_fetch_assoc($result)) {
    $st_ct = $row['ST'];
    $st_waiting_list_ct = $row['ST_WL']; // Fetching waiting list
    if($st_ct==0 && $st_waiting_list_ct>0){
        $ST_CT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($st_waiting_list_ct) . "</span>";
    }
    else{
        $ST_CT=$st_ct;
    }
} 

$query9 = "SELECT MECH FROM mechanical_engineering";
$result = mysqli_query($conn, $query9);
if ($row = mysqli_fetch_assoc($result)) {
    $MECH = $row['MECH'];
} 
$query10 = "SELECT OC, OC_WL FROM mechanical_engineering";
$result = mysqli_query($conn, $query10);
if ($row = mysqli_fetch_assoc($result)) {
    $oc_mech = $row['OC'];
    $oc_waiting_list_mech = $row['OC_WL']; // Fetching waiting list
    if($oc_mech==0 && $oc_waiting_list_mech>0){
        $OC_MECH="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($oc_waiting_list_mech) . "</span>";
    }
    else{
        $OC_MECH=$oc_mech;
    }
}
$query11 = "SELECT BC, BC_WL FROM mechanical_engineering";
$result = mysqli_query($conn, $query11);
if ($row = mysqli_fetch_assoc($result)) {
    $bc_mech = $row['BC'];
    $bc_waiting_list_mech = $row['BC_WL']; // Fetching waiting list
    if($bc_mech==0 && $bc_waiting_list_mech>0){
        $BC_MECH="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bc_waiting_list_mech) . "</span>";
    }
    else{
        $BC_MECH=$bc_mech;
    }
}
$query12 = "SELECT BCM, BCM_WL FROM mechanical_engineering";
$result = mysqli_query($conn, $query12);
if ($row = mysqli_fetch_assoc($result)) {
    $bcm_mech = $row['BCM'];
    $bcm_waiting_list_mech = $row['BCM_WL']; // Fetching waiting list
    if($bcm_mech==0 && $bcm_waiting_list_mech>0){
        $BCM_MECH="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bcm_waiting_list_mech) . "</span>";
    }
    else{
        $BCM_MECH=$bcm_mech;
    }
}
$query13 = "SELECT MBC, MBC_WL FROM mechanical_engineering";
$result = mysqli_query($conn, $query13);
if ($row = mysqli_fetch_assoc($result)) {
    $mbc_mech = $row['MBC'];
    $mbc_waiting_list_mech = $row['MBC_WL']; // Fetching waiting list
    if($mbc_mech==0 && $mbc_waiting_list_mech>0){
        $MBC_MECH="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($mbc_waiting_list_mech) . "</span>";
    }
    else{
        $MBC_MECH=$mbc_mech;
    }
} 
$query14 = "SELECT SC, SC_WL FROM mechanical_engineering";
$result = mysqli_query($conn, $query14);
if ($row = mysqli_fetch_assoc($result)) {
    $sc_mech = $row['SC'];
    $sc_waiting_list_mech = $row['SC_WL']; // Fetching waiting list
    if($sc_mech==0 && $sc_waiting_list_mech>0){
        $SC_MECH="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sc_waiting_list_mech) . "</span>";
    }
    else{
        $SC_MECH=$sc_mech;
    }
} 
$query15 = "SELECT SCA, SCA_WL FROM mechanical_engineering";
$result = mysqli_query($conn, $query15);
if ($row = mysqli_fetch_assoc($result)) {
    $sca_mech = $row['SCA'];
    $sca_waiting_list_mech = $row['SCA_WL']; // Fetching waiting list
    if($sca_mech==0 && $sca_waiting_list_mech>0){
        $SCA_MECH="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sca_waiting_list_mech) . "</span>";
    }
    else{
        $SCA_MECH=$sca_mech;
    }
} 
$query16 = "SELECT ST, ST_WL FROM mechanical_engineering";
$result = mysqli_query($conn, $query16);
if ($row = mysqli_fetch_assoc($result)) {
    $st_mech = $row['ST'];
    $st_waiting_list_mech = $row['ST_WL']; // Fetching waiting list
    if($st_mech==0 && $st_waiting_list_mech>0){
        $ST_MECH="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($st_waiting_list_mech) . "</span>";
    }
    else{
        $ST_MECH=$st_mech;
    }
} 

$query9 = "SELECT CIV FROM civil_engineering";
$result = mysqli_query($conn, $query9);
if ($row = mysqli_fetch_assoc($result)) {
    $CIV = $row['CIV'];
} 
$query10 = "SELECT OC, OC_WL FROM civil_engineering";
$result = mysqli_query($conn, $query10);
if ($row = mysqli_fetch_assoc($result)) {
    $oc_civ = $row['OC'];
    $oc_waiting_list_civ = $row['OC_WL']; // Fetching waiting list
    if($oc_civ==0 && $oc_waiting_list_civ>0){
        $OC_CIV="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($oc_waiting_list_civ) . "</span>";
    }
    else{
        $OC_CIV=$oc_civ;
    }
}
$query11 = "SELECT BC, BC_WL FROM civil_engineering";
$result = mysqli_query($conn, $query11);
if ($row = mysqli_fetch_assoc($result)) {
    $bc_civ = $row['BC'];
    $bc_waiting_list_civ = $row['BC_WL']; // Fetching waiting list
    if($bc_civ==0 && $bc_waiting_list_civ>0){
        $BC_CIV="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bc_waiting_list_civ) . "</span>";
    }
    else{
        $BC_CIV=$bc_civ;
    }
}
$query12 = "SELECT BCM, BCM_WL FROM civil_engineering";
$result = mysqli_query($conn, $query12);
if ($row = mysqli_fetch_assoc($result)) {
    $bcm_civ = $row['BCM'];
    $bcm_waiting_list_civ = $row['BCM_WL']; // Fetching waiting list
    if($bcm_civ==0 && $bcm_waiting_list_civ>0){
        $BCM_CIV="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bcm_waiting_list_civ) . "</span>";
    }
    else{
        $BCM_CIV=$bcm_civ;
    }
}
$query13 = "SELECT MBC, MBC_WL FROM civil_engineering";
$result = mysqli_query($conn, $query13);
if ($row = mysqli_fetch_assoc($result)) {
    $mbc_civ = $row['MBC'];
    $mbc_waiting_list_civ = $row['MBC_WL']; // Fetching waiting list
    if($mbc_civ==0 && $mbc_waiting_list_civ>0){
        $MBC_CIV="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($mbc_waiting_list_civ) . "</span>";
    }
    else{
        $MBC_CIV=$mbc_civ;
    }
} 
$query14 = "SELECT SC, SC_WL FROM civil_engineering";
$result = mysqli_query($conn, $query14);
if ($row = mysqli_fetch_assoc($result)) {
    $sc_civ = $row['SC'];
    $sc_waiting_list_civ = $row['SC_WL']; // Fetching waiting list
    if($sc_civ==0 && $sc_waiting_list_civ>0){
        $SC_CIV="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sc_waiting_list_civ) . "</span>";
    }
    else{
        $SC_CIV=$sc_civ;
    }
} 
$query15 = "SELECT SCA, SCA_WL FROM civil_engineering";
$result = mysqli_query($conn, $query15);
if ($row = mysqli_fetch_assoc($result)) {
    $sca_civ = $row['SCA'];
    $sca_waiting_list_civ = $row['SCA_WL']; // Fetching waiting list
    if($sca_civ==0 && $sca_waiting_list_civ>0){
        $SCA_CIV="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sca_waiting_list_civ) . "</span>";
    }
    else{
        $SCA_CIV=$sca_civ;
    }
} 
$query16 = "SELECT ST,ST_WL FROM civil_engineering";
$result = mysqli_query($conn, $query16);
if ($row = mysqli_fetch_assoc($result)) {
    $st_civ = $row['ST'];
    $st_waiting_list_civ = $row['ST_WL']; // Fetching waiting list
    if($st_civ==0 && $st_waiting_list_civ>0){
        $ST_CIV="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($st_waiting_list_civ) . "</span>";
    }
    else{
        $ST_CIV=$st_civ;
    }
} 

$query9 = "SELECT ICE FROM instrumental_and_communication_engineering";
$result = mysqli_query($conn, $query9);
if ($row = mysqli_fetch_assoc($result)) {
    $ICE = $row['ICE'];
} 
$query10 = "SELECT OC, OC_WL FROM instrumental_and_communication_engineering";
$result = mysqli_query($conn, $query10);
if ($row = mysqli_fetch_assoc($result)) {
    $oc_ice = $row['OC'];
    $oc_waiting_list_ice = $row['OC_WL']; // Fetching waiting list
    if($oc_ice==0 && $oc_waiting_list_ice>0){
        $OC_ICE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($oc_waiting_list_ice) . "</span>";
    }
    else{
        $OC_ICE=$oc_ice;
    }
}
$query11 = "SELECT BC, BC_WL FROM instrumental_and_communication_engineering";
$result = mysqli_query($conn, $query11);
if ($row = mysqli_fetch_assoc($result)) {
    $bc_ice = $row['BC'];
    $bc_waiting_list_ice = $row['BC_WL']; // Fetching waiting list
    if($bc_ice==0 && $bc_waiting_list_ice>0){
        $BC_ICE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bc_waiting_list_ice) . "</span>";
    }
    else{
        $BC_ICE=$bc_ice;
    }
}
$query12 = "SELECT BCM, BCM_WL FROM instrumental_and_communication_engineering";
$result = mysqli_query($conn, $query12);
if ($row = mysqli_fetch_assoc($result)) {
    $bcm_ice = $row['BCM'];
    $bcm_waiting_list_ice = $row['BCM_WL']; // Fetching waiting list
    if($bcm_ice==0 && $bcm_waiting_list_ice>0){
        $BCM_ICE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bcm_waiting_list_ice) . "</span>";
    }
    else{
        $BCM_ICE=$bcm_ice;
    }
}
$query13 = "SELECT MBC, MBC_WL FROM instrumental_and_communication_engineering";
$result = mysqli_query($conn, $query13);
if ($row = mysqli_fetch_assoc($result)) {
    $mbc_ice = $row['MBC'];
    $mbc_waiting_list_ice = $row['MBC_WL']; // Fetching waiting list
    if($mbc_ice==0 && $mbc_waiting_list_ice>0){
        $MBC_ICE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($mbc_waiting_list_ice) . "</span>";
    }
    else{
        $MBC_ICE=$mbc_ice;
    }
} 
$query14 = "SELECT SC, SC_WL FROM instrumental_and_communication_engineering";
$result = mysqli_query($conn, $query14);
if ($row = mysqli_fetch_assoc($result)) {
    $sc_ice = $row['SC'];
    $sc_waiting_list_ice = $row['SC_WL']; // Fetching waiting list
    if($sc_ice==0 && $sc_waiting_list_ice>0){
        $SC_ICE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sc_waiting_list_ice) . "</span>";
    }
    else{
        $SC_ICE=$sc_ice;
    }
} 
$query15 = "SELECT SCA, SCA_WL FROM instrumental_and_communication_engineering";
$result = mysqli_query($conn, $query15);
if ($row = mysqli_fetch_assoc($result)) {
    $sca_ice = $row['SCA'];
    $sca_waiting_list_ice = $row['SCA_WL']; // Fetching waiting list
    if($sca_ice==0 && $sca_waiting_list_ice>0){
        $SCA_ICE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sca_waiting_list_ice) . "</span>";
    }
    else{
        $SCA_ICE=$sca_ice;
    }
} 
$query16 = "SELECT ST, ST_WL FROM instrumental_and_communication_engineering";
$result = mysqli_query($conn, $query16);
if ($row = mysqli_fetch_assoc($result)) {
    $st_ice = $row['ST'];
    $st_waiting_list_ice = $row['ST_WL']; // Fetching waiting list
    if($st_ice==0 && $st_waiting_list_ice>0){
        $ST_ICE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($st_waiting_list_ice) . "</span>";
    }
    else{
        $ST_ICE=$st_ice;
    }
} 

$query9 = "SELECT ECE FROM electrical_and_electronics_engineering";
$result = mysqli_query($conn, $query9);
if ($row = mysqli_fetch_assoc($result)) {
    $ECE = $row['ECE'];
} 
$query10 = "SELECT OC, OC_WL FROM electrical_and_electronics_engineering";
$result = mysqli_query($conn, $query10);
if ($row = mysqli_fetch_assoc($result)) {
    $oc_ece = $row['OC'];
    $oc_waiting_list_ece = $row['OC_WL']; // Fetching waiting list
    if($oc_ece==0 && $oc_waiting_list_ece>0){
        $OC_ECE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($oc_waiting_list_ece) . "</span>";
    }
    else{
        $OC_ECE=$oc_ece;
    }
}
$query11 = "SELECT BC, BC_WL FROM electrical_and_electronics_engineering";
$result = mysqli_query($conn, $query11);
if ($row = mysqli_fetch_assoc($result)) {
    $bc_ece = $row['BC'];
    $bc_waiting_list_ece = $row['BC_WL']; // Fetching waiting list
    if($bc_ece==0 && $bc_waiting_list_ece>0){
        $BC_ECE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bc_waiting_list_ece) . "</span>";
    }
    else{
        $BC_ECE=$bc_ece;
    }
}
$query12 = "SELECT BCM, BCM_WL FROM electrical_and_electronics_engineering";
$result = mysqli_query($conn, $query12);
if ($row = mysqli_fetch_assoc($result)) {
    $bcm_ece = $row['BCM'];
    $bcm_waiting_list_ece = $row['BCM_WL']; // Fetching waiting list
    if($bcm_ece==0 && $bcm_waiting_list_ece>0){
        $BCM_ECE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bcm_waiting_list_ece) . "</span>";
    }
    else{
        $BCM_ECE=$bcm_ece;
    }
}
$query13 = "SELECT MBC, MBC_WL FROM electrical_and_electronics_engineering";
$result = mysqli_query($conn, $query13);
if ($row = mysqli_fetch_assoc($result)) {
    $mbc_ece = $row['MBC'];
    $mbc_waiting_list_ece = $row['MBC_WL']; // Fetching waiting list
    if($mbc_ece==0 && $mbc_waiting_list_ece>0){
        $MBC_ECE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($mbc_waiting_list_ece) . "</span>";
    }
    else{
        $MBC_ECE=$mbc_ece;
    }}
$query14 = "SELECT SC, SC_WL FROM electrical_and_electronics_engineering";
$result = mysqli_query($conn, $query14);
if ($row = mysqli_fetch_assoc($result)) {
    $sc_ece = $row['SC'];
    $sc_waiting_list_ece = $row['SC_WL']; // Fetching waiting list
    if($sc_ece==0 && $sc_waiting_list_ece>0){
        $SC_ECE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sc_waiting_list_ece) . "</span>";
    }
    else{
        $SC_ECE=$sc_ece;
    }
} 
$query15 = "SELECT SCA, SCA_WL FROM electrical_and_electronics_engineering";
$result = mysqli_query($conn, $query15);
if ($row = mysqli_fetch_assoc($result)) {
    $sca_ece = $row['SCA'];
    $sca_waiting_list_ece = $row['SCA_WL']; // Fetching waiting list
    if($sca_ece==0 && $sca_waiting_list_ece>0){
        $SCA_ECE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sca_waiting_list_ece) . "</span>";
    }
    else{
        $SCA_ECE=$sca_ece;
    }
} 
$query16 = "SELECT ST, ST_WL FROM electrical_and_electronics_engineering";
$result = mysqli_query($conn, $query16);
if ($row = mysqli_fetch_assoc($result)) {
    $st_ece = $row['ST'];
    $st_waiting_list_ece = $row['ST_WL']; // Fetching waiting list
    if($st_ece==0 && $st_waiting_list_ece>0){
        $ST_ECE="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($st_waiting_list_ece) . "</span>";
    }
    else{
        $ST_ECE=$st_ece;
    }
} 

$query9 = "SELECT MOP FROM modern_office_practice";
$result = mysqli_query($conn, $query9);
if ($row = mysqli_fetch_assoc($result)) {
    $MOP= $row['MOP'];
} 
$query10 = "SELECT OC, OC_WL FROM modern_office_practice";
$result = mysqli_query($conn, $query10);
if ($row = mysqli_fetch_assoc($result)) {
    $oc_mop = $row['OC'];
    $oc_waiting_list_mop = $row['OC_WL']; // Fetching waiting list
    if($oc_mop==0 && $oc_waiting_list_mop>0){
        $OC_MOP="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($oc_waiting_list_mop) . "</span>";
    }
    else{
        $OC_MOP=$oc_mop;
    }
}

$query11 = "SELECT BC, BC_WL FROM modern_office_practice";
$result = mysqli_query($conn, $query11);
if ($row = mysqli_fetch_assoc($result)) {
    $bc_mop = $row['BC'];
    $bc_waiting_list_mop = $row['BC_WL']; // Fetching waiting list
    if($bc_mop==0 && $bc_waiting_list_mop>0){
        $BC_MOP="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bc_waiting_list_mop) . "</span>";
    }
    else{
        $BC_MOP=$bc_mop;
    }
}
$query12 = "SELECT BCM, BCM_WL FROM modern_office_practice";
$result = mysqli_query($conn, $query12);
if ($row = mysqli_fetch_assoc($result)) {
    $bcm_mop = $row['BCM'];
    $bcm_waiting_list_mop = $row['BCM_WL']; // Fetching waiting list
    if($bcm_mop==0 && $bcm_waiting_list_mop>0){
        $BCM_MOP="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bcm_waiting_list_mop) . "</span>";
    }
    else{
        $BCM_MOP=$bcm_mop;
    }
}
$query13 = "SELECT MBC, MBC_WL FROM modern_office_practice";
$result = mysqli_query($conn, $query13);
if ($row = mysqli_fetch_assoc($result)) {
    $mbc_mop = $row['MBC'];
    $mbc_waiting_list_mop = $row['MBC_WL']; // Fetching waiting list
    if($mbc_mop==0 && $mbc_waiting_list_mop>0){
        $MBC_MOP="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($mbc_waiting_list_mop) . "</span>";
    }
    else{
        $MBC_MOP=$mbc_mop;
    }
}
$query14 = "SELECT SC, SC_WL FROM modern_office_practice";
$result = mysqli_query($conn, $query14);
if ($row = mysqli_fetch_assoc($result)) {
    $sc_mop = $row['SC'];
    $sc_waiting_list_mop = $row['SC_WL']; // Fetching waiting list
    if($sc_mop==0 && $sc_waiting_list_mop>0){
        $SC_MOP="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sc_waiting_list_mop) . "</span>";
    }
    else{
        $SC_MOP=$sc_mop;
    }
} 
$query15 = "SELECT SCA, SCA_WL FROM modern_office_practice";
$result = mysqli_query($conn, $query15);
if ($row = mysqli_fetch_assoc($result)) {
    $sca_mop = $row['SCA'];
    $sca_waiting_list_mop = $row['SCA_WL']; // Fetching waiting list
    if($sca_mop==0 && $sca_waiting_list_mop>0){
        $SCA_MOP="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sca_waiting_list_mop) . "</span>";
    }
    else{
        $SCA_MOP=$sca_mop;
    }
} 
$query16 = "SELECT ST, ST_WL FROM modern_office_practice";
$result = mysqli_query($conn, $query16);
if ($row = mysqli_fetch_assoc($result)) {
    $st_mop = $row['ST'];
    $st_waiting_list_mop = $row['ST_WL']; // Fetching waiting list
    if($st_mop==0 && $st_waiting_list_mop>0){
        $ST_MOP="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($st_waiting_list_mop) . "</span>";
    }
    else{
        $ST_MOP=$st_mop;
    }
} 

$query9 = "SELECT GT FROM garment_technology";
$result = mysqli_query($conn, $query9);
if ($row = mysqli_fetch_assoc($result)) {
    $GT = $row['GT'];
} 
$query10 = "SELECT OC, OC_WL FROM garment_technology";
$result = mysqli_query($conn, $query10);
if ($row = mysqli_fetch_assoc($result)) {
    $oc_gt = $row['OC'];
    $oc_waiting_list_gt = $row['OC_WL']; // Fetching waiting list
    if($oc_gt==0 && $oc_waiting_list_gt>0){
        $OC_GT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($oc_waiting_list_gt) . "</span>";
    }
    else{
        $OC_GT=$oc_gt;
    }
}
$query11 = "SELECT BC, BC_WL FROM garment_technology";
$result = mysqli_query($conn, $query11);
if ($row = mysqli_fetch_assoc($result)) {
    $bc_gt = $row['BC'];
    $bc_waiting_list_gt = $row['BC_WL']; // Fetching waiting list
    if($bc_gt==0 && $bc_waiting_list_gt>0){
        $BC_GT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bc_waiting_list_gt) . "</span>";
    }
    else{
        $BC_GT=$bc_gt;
    }
}
$query12 = "SELECT BCM, BCM_WL FROM garment_technology";
$result = mysqli_query($conn, $query12);
if ($row = mysqli_fetch_assoc($result)) {
    $bcm_gt = $row['BCM'];
    $bcm_waiting_list_gt = $row['BCM_WL']; // Fetching waiting list
    if($bcm_gt==0 && $bcm_waiting_list_gt>0){
        $BCM_GT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($bcm_waiting_list_gt) . "</span>";
    }
    else{
        $BCM_GT=$bcm_gt;
    }
}
$query13 = "SELECT MBC,MBC_WL FROM garment_technology";
$result = mysqli_query($conn, $query13);
if ($row = mysqli_fetch_assoc($result)) {
    $mbc_gt = $row['MBC'];
    $mbc_waiting_list_gt = $row['MBC_WL']; // Fetching waiting list
    if($mbc_gt==0 && $mbc_waiting_list_gt>0){
        $MBC_GT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($mbc_waiting_list_gt) . "</span>";
    }
    else{
        $MBC_GT=$mbc_gt;
    }
} 
$query14 = "SELECT SC, SC_WL FROM garment_technology";
$result = mysqli_query($conn, $query14);
if ($row = mysqli_fetch_assoc($result)) {
    $sc_gt = $row['SC'];
    $sc_waiting_list_gt = $row['SC_WL']; // Fetching waiting list
    if($sc_gt==0 && $sc_waiting_list_gt>0){
        $SC_GT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sc_waiting_list_gt) . "</span>";
    }
    else{
        $SC_GT=$sc_gt;
    }
} 
$query15 = "SELECT SCA, SCA_WL FROM garment_technology";
$result = mysqli_query($conn, $query15);
if ($row = mysqli_fetch_assoc($result)) {
    $sca_gt = $row['SCA'];
    $sca_waiting_list_gt = $row['SCA_WL']; // Fetching waiting list
    if($sca_gt==0 && $sca_waiting_list_gt>0){
        $SCA_GT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($sca_waiting_list_gt) . "</span>";
    }
    else{
        $SCA_GT=$sca_gt;
    }
} 
$query16 = "SELECT ST, ST_WL FROM garment_technology";
$result = mysqli_query($conn, $query16);
if ($row = mysqli_fetch_assoc($result)) {
    $st_gt = $row['ST'];
    $st_waiting_list_gt = $row['ST_WL']; // Fetching waiting list
    if($st_gt==0 && $st_waiting_list_gt>0){
        $ST_GT="<span style='color: red;font-size: 21px;'>WL: " . htmlspecialchars($st_waiting_list_gt) . "</span>";
    }
    else{
        $ST_GT=$st_gt;
    }
} 
?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>GPCW SEAT ALLOCATION</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body class="body" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;">
<div class="container">
    <div class="top-section">
        <p align="center" style="font-weight: bold;font-size:50px;color:  rgb(75, 206, 239);">DASHBOARD STATUS</p>
        <p align="right">
        <a href="login.html" class="btn btn-info btn-lg">
        <span class="glyphicon glyphicon-log-out"></span>Log out</a></p>
        <div class="background">
        <div class="division" style="font-size: x-large;color:rgb(0, 0, 0);font-weight: 600;">💻
                <div class="oval">CE<br><?php echo htmlspecialchars($CE); ?></div>
                OC<div class="content-box"><?php echo $OC; ?></div>
                BC<div class="content-box"><?php echo $BC; ?></div>
                BCM<div class="content-box"><?php echo $BCM; ?></div>
                MBC<div class="content-box"><?php echo $MBC; ?></div>            
                SC<div class="content-box"><?php echo $SC; ?></div>
                SCA<div class="content-box"><?php echo $SCA; ?></div>            
                ST<div class="content-box"><?php echo $ST; ?></div>
            </div>
            <div class="division" style="font-size: x-large;color:rgb(0, 0, 0);font-weight: 600;">🌐
                <div class="oval" id="ct">CT<br><?php echo htmlspecialchars($CT); ?></div>
                OC<div class="content-box"><?php echo $OC_CT; ?></div>
                BC<div class="content-box"><?php echo $BC_CT; ?></div>
                BCM<div class="content-box"><?php echo $BCM_CT; ?></div>
                MBC<div class="content-box"><?php echo $MBC_CT; ?></div>            
                SC<div class="content-box"><?php echo $SC_CT; ?></div>
                SCA<div class="content-box"><?php echo $SCA_CT; ?></div>            
                ST<div class="content-box"><?php echo $ST_CT; ?></div>
            </div>
            <div class="division" style="font-size: x-large;color:rgb(0, 0, 0);font-weight: 600;">🔧
                <div class="oval">MECH<br><?php echo htmlspecialchars($MECH); ?></div>
                OC<div class="content-box"><?php echo $OC_MECH; ?></div>
                BC<div class="content-box"><?php echo $BC_MECH; ?></div>
                BCM<div class="content-box"><?php echo $BCM_MECH; ?></div>
                MBC<div class="content-box"><?php echo $MBC_MECH; ?></div>            
                SC<div class="content-box"><?php echo $SC_MECH; ?></div>
                SCA<div class="content-box"><?php echo $SCA_MECH; ?></div>            
                ST<div class="content-box"><?php echo $ST_MECH; ?></div>
            </div>
            <div class="division" style="font-size: x-large;color:rgb(0, 0, 0);font-weight: 600;">🏗️
                <div class="oval">CIV<br><?php echo htmlspecialchars($CIV); ?></div>
                OC<div class="content-box"><?php echo $OC_CIV; ?></div>
                BC<div class="content-box"><?php echo $BC_CIV; ?></div>
                BCM<div class="content-box"><?php echo $BCM_CIV; ?></div>
                MBC<div class="content-box"><?php echo $MBC_CIV; ?></div>            
                SC<div class="content-box"><?php echo $SC_CIV; ?></div>
                SCA<div class="content-box"><?php echo $SCA_CIV; ?></div>            
                ST<div class="content-box"><?php echo $ST_CIV; ?></div>          
            </div>
            <div class="division" style="font-size: x-large;color:rgb(0, 0, 0);font-weight: 600;">⚙️
                <div class="oval">ICE<br><?php echo htmlspecialchars($ICE); ?></div>
                OC<div class="content-box"><?php echo $OC_ICE; ?></div>
                BC<div class="content-box"><?php echo $BC_ICE; ?></div>
                BCM<div class="content-box"><?php echo $BCM_ICE; ?></div>
                MBC<div class="content-box"><?php echo $MBC_ICE; ?></div>            
                SC<div class="content-box"><?php echo $SC_ICE; ?></div>
                SCA<div class="content-box"><?php echo $SCA_ICE; ?></div>            
                ST<div class="content-box"><?php echo $ST_ICE; ?></div>
            </div>
            <div class="division"style="font-size: x-large;color:rgb(0, 0, 0);font-weight: 600;">📡
                <div class="oval">ECE<br><?php echo htmlspecialchars($ECE); ?></div>
                OC<div class="content-box"><?php echo $OC_ECE; ?></div>
                BC<div class="content-box"><?php echo $BC_ECE; ?></div>
                BCM<div class="content-box"><?php echo $BCM_ECE; ?></div>
                MBC<div class="content-box"><?php echo $MBC_ECE; ?></div>            
                SC<div class="content-box"><?php echo $SC_ECE; ?></div>
                SCA<div class="content-box"><?php echo $SCA_ECE; ?></div>            
                ST<div class="content-box"><?php echo $ST_ECE; ?></div>        
            </div>
            <div class="division"style="font-size: x-large;color:rgb(0, 0, 0);font-weight: 600;">🗂️
                <div class="oval">MOP<br><?php echo htmlspecialchars($MOP); ?></div>
                OC<div class="content-box"><?php echo $OC_MOP; ?></div>
                BC<div class="content-box"><?php echo $BC_MOP; ?></div>
                BCM<div class="content-box"><?php echo $BCM_MOP; ?></div>
                MBC<div class="content-box"><?php echo $MBC_MOP; ?></div>            
                SC<div class="content-box"><?php echo $SC_MOP; ?></div>
                SCA<div class="content-box"><?php echo $SCA_MOP; ?></div>            
                ST<div class="content-box"><?php echo $ST_MOP; ?></div>       
            </div>
            <div class="division" style="font-size: x-large;color:rgb(0, 0, 0);font-weight: 600;">🧵
                <div class="oval">GT<br><?php echo htmlspecialchars($GT); ?></div>
                OC<div class="content-box"><?php echo $OC_GT; ?></div>
                BC<div class="content-box"><?php echo $BC_GT; ?></div>
                BCM<div class="content-box"><?php echo $BCM_GT; ?></div>
                MBC<div class="content-box"><?php echo $MBC_GT; ?></div>            
                SC<div class="content-box"><?php echo $SC_GT; ?></div>
                SCA<div class="content-box"><?php echo $SCA_GT; ?></div>            
                ST<div class="content-box"><?php echo $ST_GT; ?></div>
            </div>
        </div><br><br>
    <div class="bottom-section">
    <div class="column">
    <form action="#" method="POST">
        <label for="search">Application ID
        <input class="buttonclass" name="search" type="text" style="font-size: 20px;" placeholder="Appln. ID" value="<?php echo htmlspecialchars($application_id); ?>"><br>
        <p align="center">
        <button type="submit" class="submit" name="searchdata" style="background-color:rgb(114, 215, 240)"><font style="font-size:20px;">Fetch Details</font></button></p> 
        <!-- Display message -->
        <?php
    if (!empty($msg1)) {
        echo "<p style='color: green;'>$msg1</p>";
    } elseif (!empty($msg2)) {
        echo "<p style='color: red;'>$msg2</p>";
    }
    ?>
            <label for="name">Name
            <input class="buttonclass" name="name" type="text" value="<?php echo htmlspecialchars($name); ?>">
            <label for="DOB">Date Of Birth
            <input class="buttonclass" name='DOB' type="date" value="<?php echo htmlspecialchars($dob); ?>">
            <label for="Marks">Mark Scored
            <input class="buttonclass" type="text" name='Marks'   value="<?php echo htmlspecialchars($marks); ?>">
    </div>
    <div class=" column ">
    <label for="Rank"><br><br>Rank
    <input class="buttonclass" type="number"  name='Rank' value="<?php echo htmlspecialchars($rank); ?>">
    <label for="Community">Community
    <input class="buttonclass" type="text" name='Community'  value="<?php echo htmlspecialchars($community); ?>">
    <label for="Dept">Department
    <select class="buttonclass" name="Dept">
            <option value="Select Department">Select Department</option>
            <option value="computer_engineering" <?php if($result1['Dept']=='computer_engineering'){echo "selected";}?>>computer_engineering</option>
            <option value="computer_technology" <?php if($result1['Dept']=='computer_technology'){echo "selected";}?>>computer_technology</option>
            <option value="mechanical_engineering" <?php if($result1['Dept']=='mechanical_engineering'){echo "selected";}?>>mechanical_engineering</option>
            <option value="civil_engineering" <?php if($result1['Dept']=='civil_engineering'){echo "selected";}?>>civil_engineering</option>
            <option value="garment_technology" <?php if($result1['Dept']=='garment_technology'){echo "selected";}?>>garment_technology</option>
            <option value="electrical_and_electronics_engineering" <?php if($result1['Dept']=='electrical_and_electronics_engineering'){echo "selected";}?>>electrical_and_electronics_engineering</option>
            <option value="instrumental_and_communication_engineering" <?php if($result1['Dept']=='instrumental_and_communication_engineering'){echo "selected";}?>>instrumental_and_communication_engineering</option>
            <option value="modern_office_practice" <?php if($result1['Dept']=='modern_office_practice'){echo "selected";}?>>modern_office_practice</option>
    </select>
    <label for="quota">Quota
    <select class="buttonclass" name="quota">
            <option value="Select Quota">Select Quota</option>
            <option value="OC" <?php if($result1['Allotted_Category']=='OC'){echo "selected";}?>>OC</option>
            <option value="BC" <?php if($result1['Allotted_Category']=='BC'){echo "selected";}?>>BC</option>
            <option value="BCM" <?php if($result1['Allotted_Category']=='BCM'){echo "selected";}?>>BCM</option>
            <option value="MBC" <?php if($result1['Allotted_Category']=='MBC'){echo "selected";}?>>MBC</option>
            <option value="SC" <?php if($result1['Allotted_Category']=='SC'){echo "selected";}?>>SC</option>
            <option value="SCA" <?php if($result1['Allotted_Category']=='SCA'){echo "selected";}?>>SCA</option>
            <option value="ST" <?php if($result1['Allotted_Category']=='ST'){echo "selected";}?>>ST</option>
    </select><br>
    <div style="display: flex; justify-content: space-between; gap: 50px;">
        <button type="submit"name="update" class="submit" style="background-color:rgb(114, 215, 240)"<?php if ($data && mysqli_num_rows($data) <= 0){echo'disabled';} if ($status == 'Confirmed'){echo 'disabled'; }if ($status == 'Dropped'){echo 'disabled'; }?>><font style="font-size:20px;">Confirm</font></button>
        <button type="submit" name="waiting" class="submit"  style="background-color:rgb(114, 215, 240)" <?php if ($data && mysqli_num_rows($data) <= 0){echo'disabled';}if ($status == 'Confirmed'){ echo 'disabled'; }if ($status == 'Dropped'){echo 'disabled';if ($status == 'Waiting'){echo 'disabled'; } }?>><font style="font-size:20px;">Waiting list</font></button>
    </div><br>
    <div style="display: flex; justify-content: space-between; gap: 50px;">
        <button type="submit" name="drop" class="submit"  style="background-color:rgb(114, 215, 240)" <?php if ($data && mysqli_num_rows($data) <= 0){echo'disabled';}if ($status == 'Dropped'){echo 'disabled'; }?>><font style="font-size:20px;">Drop</font></button>
        <button type="submit" name="absent" class="submit"  style="background-color:rgb(114, 215, 240)"<?php if ($data && mysqli_num_rows($data) <= 0){echo'disabled';} if ($status == 'Confirmed') echo 'disabled';if ($status == 'Dropped'){echo 'disabled'; }if ($status == 'Waiting'){echo 'disabled'; }if ($status == 'Non-appear'){echo 'disabled'; }?>><font style="font-size:20px;">Absent</font></button></div><br>
        <?php if (!empty($message1)) : ?><p style="color: green;"><?php echo $message1; ?></p><?php endif; ?>
        <?php if (!empty($message2)) : ?><p style="color: red;"><?php echo $message2; ?></p><?php endif; ?>
        <?php if (!empty($message3)) : ?><p style="color: orange;"><?php echo $message3; ?></p><?php endif; ?>
        <?php if (!empty($message4)) : ?><p style="color: orange;"><?php echo $message4; ?></p><?php endif; ?>
        <?php if (!empty($message5)) : ?><p style="color: blue;"><?php echo $message5; ?></p><?php endif; ?> 
        <?php if (!empty($message6)) : ?><p style="color: black;"><?php echo $message6; ?></p><?php endif; ?>
        <?php if (!empty($m1)) : ?><p style="color: red;"><?php echo $m1; ?></p><?php endif; ?>
        <?php if (!empty($m2)) : ?><p style="color: red;"><?php echo $m2; ?></p><?php endif; ?>
        <?php if (!empty($m3)) : ?><p style="color: red;"><?php echo $m3; ?></p><?php endif; ?>
        <?php if (!empty($m4)) : ?><p style="color: red;"><?php echo $m4; ?></p><?php endif; ?>
</div></div></div></form>
</div>
</body></html>