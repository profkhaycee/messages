<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

class Action{
   
    public $connect;

    public function __construct(){
        $this->connect = new mysqli('localhost', 'root', '','kdc_messages') or die("error in connection: ".$this->connect->connect_error);
    }

    public function validate($textInput){
        $textInput = trim($textInput);
        $textInput = stripslashes($textInput);
        $textInput = strip_tags($textInput);
        $textInput = htmlspecialchars($textInput);
        $textInput = $this->connect->real_escape_string($textInput); 

    
        return $textInput;
    }

    public function uploadAudio()
    {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    public function exist($name, $table, $column){
        $sql = "select * from $table where $column = '$name'";
        $result = $this->connect->query($sql);
        if($result->num_rows > 0){
            return true;
        }else{
            return false;
        }
    }


    public function addMessage($title, $series, $preacher, $datePreached, $path)
    {
        if(!$this->exist($title, 'messages','title')){

            $sql = "insert into messages (title, series, preacher, path, date_preached) values ('$title', $series,$preacher, '$path', '$datePreached') "; 
            if($this->connect->query($sql)){
                echo "<script>alert('message successfully Added!!!'); </script>";
            }else{
                echo "<script>alert('SOMETHING WENT WRONG!!!'); </script>";

            }
        }else {
            echo "<script>alert('Message already exist'); </script>";
        }
    }


    public function messageCard($title, $series, $path, $preacher, $date_preached){

        return '<div class="sermoffn my-4 aos-init" data-aos="zoom-in-right" data-aos-duration="1000">

                <div class="card shadow-lg border-none">
                    <div class="card-header ">
                        <div class="row">
                            <p class="col-9"> Series: <a href="series_view?id='.$series.'">'. $this->getSeriesTitle($series) .'</a></p>
                            <p class="col-3"><a href="'.$path.'"> <i class="fa fa-play">  </i> </a> <a download href="'.$path.'"> <i class="fa fa-download"></i> </a></p> 
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">'.$title.'</h5>
                        
                    </div>
                    <div class="card-footer text-muted ">
                        <div class="row">
                            <p class="col-8">Preacher: '.$preacher.'</p>
                            <p class="col-4" style="font-size:12px">Date Preached: '.$date_preached.'</p>
                        </div>
                    </div>
                </div>
            </div>';

    }


    public function fetchAllMessages(){
        $sql = "select * from messages ";
        $str='';
        $result = $this->connect->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
               $str .= $this->messageCard($row['title'], $row['series'],$row['path'], $row['preacher'],$row['date_preached'] );
             
            }
        }
        return $str;
    }

    public function fetchMessagesBySeries($id){
        $sql = "select * from messages where series = ".$id ;
        $str='';
        $result = $this->connect->query($sql);
        // implode(' - ', $result);
        // echo json_encode($result->num_rows);
        $rst = array();
        // echo array_push($rst, $result);

        if($result->num_rows > 0){
            while($row = $result->fetch_array()){
                // array_push($rst, $row['title']);
                array_push($rst, array(
                   'name'=> $row['title'], 
                   'preacher' => $this->getPreacherName( $row['preacher']), 
                   'series' => $this->getSeriesTitle( $row['series']),
                   'file' => $row['path'],
                   'duration' => '08:46'
                ));
            }
        }
        // echo json_encode($rst);

        $listAudio = json_encode($rst);
        echo $rst[1]['preacher'];
        echo $rst[0]['preacher'];
        return $listAudio;

    }

    public function addSeries($title)
    {
        
        if(!$this->exist($title, 'series','title')){

            $sql = "insert into series (title) values ('$title')";
            if($this->connect->query($sql)){
                echo "<script>alert('Series SUCCESSFULLY addedd!!!'); </script>";
            }else{
                echo "<script>alert(''SOMETHING WENT WRONG!!!' ; </script>";

            }
        }else{
            echo "<script>alert('Series already exist'); </script>";

        }
        
    }


    public function fetchAllSeries(){
        $sql = "select * from series ";
        $str='';
        $result = $this->connect->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
              $str .=' <div class="col-lg-3 col-md-6 col-sm-12 " data-aos="fade-down" data-aos-delay="300" data-aos-duration="800">
                            <div class="product light-bg shadow-lg position-relative">
                            
                                <div class="product-description text-center">
                                
                                    <a class="stretched-link" href="view_series.php?id='.$row['id'].'">'.$row['title'].'</a>
                                
                                </div>
                            </div>
                        </div>';
            }
        }else{
           echo ' No series available';
        }
        return $str;
    }

    public function addPastor($name)
    {
        $sql = "insert into preacher (name) values ('$name')";
        if($this->connect->query($sql)){
            echo "<script>alert('ACCOUNT SUCCESSFULLY CREATED!!!'); </script>";
        }else{
            echo "<script>alert('ERROR IN ACCOUNT CREATION \n\n SOMETHING WENT WRONG!!!'); </script>";

        }
    }


    public function addAudio(){
        
        $allowedExts = array("mp3", "wma");
        $targetPath = "uploads/audio/";
        $extension = pathinfo($_FILES['message']['name'], PATHINFO_EXTENSION);

        if (( ($_FILES['message']["type"] == "audio/mp3") || ( $_FILES['message']["type"] == "audio/wma")) && in_array($extension, $allowedExts)){
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

                if (file_exists("uploads/audio" . $_FILES['message']["name"])){
                    echo '<script> alert("'. $_FILES['message']["name"] . ' already exists.") </script>';
                    $result = false;
                    $path = '';
                } else {
                   $result = move_uploaded_file($_FILES['message']["tmp_name"], $targetPath . $_FILES['message']["name"]);
                   $path = $targetPath . $_FILES['message']["name"];
                    // echo "Stored in: " . "upload/" . $_FILES['message']["name"];
                }
            }
        }else {
            // echo "Invalid file";
            $result = false;
            $path = '';


        }
        return $path;

    }

    public function getSeriesTitle($id){
      $sql = "select title from series where id = ".$id;

      $result = $this->connect->query($sql);
      if($result->num_rows > 0){
          while($row = $result->fetch_assoc()){
            $title =$row['title'] ;
          }
      }

      return $title;
    }

    public function getPreacherName($id){
        $sql = "select name from preacher where id = ".$id;

        $result = $this->connect->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
              $name =$row['name'] ;
            }
        }
  
        return $name;
      
    }

    public function searchMessages($searchInput ){
        $sql = "select * from messages where title like '%$searchInput%' "  ;
        // if(isset($startDate) && !empty($startDate)){
        //     $sql .= " and date_preached >= '$startDate'";
        // }
        // if(isset($endDate) && !empty($endDate)){
        //     $sql .= " and date_preached <= '$endDate'";
        // }
        $result = $this->connect->query($sql);
        if($result->num_rows > 0){
            $str =" ";
            while($row = $result->fetch_assoc()){
                $str .= $this->messageCard($row['title'], $row['series'],$row['path'], $row['preacher'],$row['date_preached'] );

            }
        }else{
            $str = "<h2 class='mt-4 pt-2 text-center'>  Your Search '$searchInput' did not return any result </h2>";
        }
        return $str;

    }

    public function searchSeries($searchInput){
        $sql = "select * from series where title like '%$searchInput%'";
        $str=' ';
        $result = $this->connect->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
              $str .=' <div class="col-lg-3 col-md-6 col-sm-12 " data-aos="fade-down" data-aos-delay="300" data-aos-duration="800">
                            <div class="product light-bg shadow-lg position-relative">
                            
                                <div class="product-description text-center">
                                
                                    <a class="stretched-link" href="view_series.php?id='.$row['id'].'">'.$row['title'].'</a>
                                
                                </div>
                            </div>
                        </div>';
            }
        }else{
            $str = "<h2 class='mt-4 pt-2 text-center'>  Your Search '$searchInput' did not return any result </h2>";
        }
        return $str;
    }

}
?>