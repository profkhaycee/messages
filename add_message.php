<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include 'header.php';


if(isset($_POST['submitbtn_audio'])){

    if ($_FILES['message']["error"] > 0)  {
        echo "Return Code: " . $_FILES['message']["error"] . "<br />";
        echo '<script> alert("error 1") </script>';

        $result = false;
        $path = '';

    }else{
        echo "Upload: " . $_FILES['message']["name"] . "<br />";
        echo "Type: " . $_FILES['message']["type"] . "<br />";
        echo "Size: " . ($_FILES['message']["size"] / 1024) . " Kb<br />";
        echo "Temp file: " . $_FILES['message']["tmp_name"] . "<br />";

        if (file_exists("uploads/audio/" . $_FILES['message']["name"])){
            echo '<script> alert("'. $_FILES['message']["name"] . ' already exists.") </script>';
            $result = false;
            $path = '';
        } else {
           $result = move_uploaded_file($_FILES['message']["tmp_name"], "uploads/audio/" . $_FILES['message']["name"]);
           $path = "uploads/audio/" . $_FILES['message']["name"];
            // echo "Stored in: " . "upload/" . $_FILES['message']["name"];
        }
    }


    $title = $action->validate($_POST['title']);
    $series = $action->validate($_POST['series']);
    $preacher = $action->validate($_POST['preacher']);
    $date_preached = $action->validate($_POST['date_preached']);
    // $series = $action->validate($_POST['series']);

    // $action->createWard($name,$lga);
    // if($action->addAudio() !=""){
        // $path = $action->addAudio();
    if($path != ''){
        $action->addMessage($title, $series, $preacher,$date_preached, $path);
    }else{
        echo "<h1>error</h1>";
    }

}




?>


<section class="gap">

    <div class="container">
      
    </div>

    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3 mt-3">
                <label for="text">Name:</label>
                <input type="text" required class="form-control" id="name" placeholder="Enter name of ward" name="title">
            </div>
            <div class="mb-3 mt-3">
                <label for="text">series:</label>
                <input type="number" required class="form-control" name="series" placeholder="Enter name of ward" >
            </div>

            <div class="mb-3 mt-3">
                <label for="text">preacher:</label>
                <input type="number" required class="form-control" name="preacher" placeholder="Enter name of ward">
            </div>
            <div class="mb-3 mt-3">
                <label for="text">date preached:</label>
                <input type="date" required class="form-control" name="date_preached" placeholder="Enter name of ward" >
            </div>
            <div class="mb-3 mt-3">
                <label for="text">Upload Audio:</label>
                <input type="file" accept="audio/*" required class="form-control" name="message" placeholder="Enter name of ward" >
            </div>
            <button type="submit"  id="submitbtn-ward" name="submitbtn_audio" class="btn btn-primary">Submit</button>

        </form>
    </div>
</section>