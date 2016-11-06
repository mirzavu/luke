<?php
phpinfo();
ini_set('display_errors', 1);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$servername = "localhost";
// $username = "root";
// $password = "root";
// $dbname = "luke";

$username = "mirza";
$password = "Upwork2016";
$dbname = "mirza_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 



// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  
  $target_dir = "uploads/";
  $target_file = $target_dir . $_REQUEST['fname'].'.pdf';
  $filename = $_REQUEST['fname'].'.jpg';
  $pathToImage = $target_dir . basename($_FILES["file"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);

    $imagick = new Imagick($target_file);
$imagick->setImageFormat('jpg');
file_put_contents($target_dir .$filename, $imagick);

    // $strOutFile = 'uploads/'.$filename;
    // $strURL = 'http://online.verypdf.com/api/?apikey=XXXX-XXXX-XXXX-XXXX&app=pdftools&infile=http://103.9.171.165/~mirza/uploads/'.$_REQUEST['fname'].'.pdf&outfile=' . $filename;
    // ExecuteCloudAPI($strURL, $strOutFile);
    makeThumbnails($target_dir, $filename);
    $sql = "UPDATE uploads SET last_upload=now() WHERE name='".$_REQUEST['fname']."'";
    
    $conn->query($sql);
    // if ($conn->query($sql) === TRUE) {
    //     echo "Record updated successfully";
    // } else {
    //     echo "Error updating record: " . $conn->error;
    // }

  
}
$sql = "SELECT * FROM uploads";
$result = $conn->query($sql);

$list = [];
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $list[$row['id']] = array('name' => $row['name'],'file_name' => $row['file_name'], 'upload_date' => $row['last_upload']);
    }
} else {
    echo "0 results";
}
$conn->close();
function makeThumbnails($updir, $img)
{
    $thumbnail_width = 100;
    $thumbnail_height = 100;
    $thumb_beforeword = "thumb";
    $arr_image_details = getimagesize("$updir" . "$img"); // pass id to thumb name
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];
    if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = intval($original_height * $new_width / $original_width);
    } else {
        $new_height = $thumbnail_height;
        $new_width = intval($original_width * $new_height / $original_height);
    }
    $dest_x = intval(($thumbnail_width - $new_width) / 2);
    $dest_y = intval(($thumbnail_height - $new_height) / 2);
    if ($arr_image_details[2] == IMAGETYPE_GIF) {
        $imgt = "ImageGIF";
        $imgcreatefrom = "ImageCreateFromGIF";
    }
    if ($arr_image_details[2] == IMAGETYPE_JPEG) {
        $imgt = "ImageJPEG";
        $imgcreatefrom = "ImageCreateFromJPEG";
    }
    if ($arr_image_details[2] == IMAGETYPE_PNG) {
        $imgt = "ImagePNG";
        $imgcreatefrom = "ImageCreateFromPNG";
    }
    if ($imgt) {
        $old_image = $imgcreatefrom("$updir" . "$img");
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, "$updir" . "$thumb_beforeword" . "$img");
    }
}




// gets the data from a URL
function get_data($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

//The Usage

function ExecuteCloudAPI($strURL, $strOutFile)
{
    // echo $strURL."\n";
    $strReturn = get_data($strURL);
    $strReturn = str_replace('[Output]', '', $strReturn);
    $strReturn = str_replace('<br>', '', $strReturn);
    $strReturn = str_replace(' ', '', $strReturn);
    $strLocalFile = dirname(__FILE__) . '/' . $strOutFile;
    DownloadFile($strReturn, $strLocalFile);
    // echo $strLocalFile."\n";
}

function DownloadFile($url, $path)
{
    $newfname = $path;
    $file = fopen ($url, "rb");
    if (!$file) 
        return false;
    $newf = fopen ($newfname, "wb");
    if (!$newf)
        return false;
    while(!feof($file)) 
    {
        fwrite($newf, fread($file, 1024 * 8 ));
    }
    fclose($newf);
    fclose($file);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" src="style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Logo</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Projects</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
      </ul>
    </div>
  </div>
</nav>
  
<div class="container-fluid text-center">
  <div class="row content">
    <div class="col-sm-2 sidenav">
      
    </div>
    <div class="col-sm-8 text-left">
      <h1>Welcome</h1>
      <hr>
      <div class="dropdown">
        <button id="file-btn" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">File list
        <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <?php
          foreach ($list as $item) {
            echo '<li><a update="'.$item['upload_date'].'" href="#">'.$item['name'].'</a></li>';
          }
          ?>
        </ul>
      </div>
      <br>
      <form action="" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="exampleInputFile">Upload your file</label>
        <input type="file" name="file">
        <input id="fnameid" type="hidden" name="fname">
        <!-- <p class="help-block">Example block-level help text here.</p> -->
      </div>
      <input class="btn btn-default" type="submit" name="submit" value="Submit">
      </form>
      <br>

      <div id="toggle-div" class="form-group" style="display:none">
        <a target=_blank href="#"><img id="img-load" src="" width="100" height = 100></a>
        <p id="upload-date"></p>
      </div>
    </div>
    <div class="col-sm-2 sidenav">
      <div class="well">
        <p>ADS</p>
      </div>
      <div class="well">
        <p>ADS</p>
      </div>
    </div>
  </div>
</div>

<footer class="container-fluid text-center">
  <p></p>
</footer>
<script type="text/javascript">
  $(function() {
    $('li').on('click',function(e){
      e.preventDefault();

      var file_val = $(this).children('a').html()
      $('#file-btn').html(file_val+'<span class="caret"></span>')
      $('#fnameid').val(file_val)
      $('#toggle-div').css({display: ''});
      d = new Date();
      $('#img-load').attr('src','http://103.9.171.165/~mirza/uploads/thumb'+file_val+'.jpg?'+d.getTime());
      $('#upload-date').html('Last Uploaded at '+$(this).children('a').attr('update'))
      $('#toggle-div a').attr('href','http://103.9.171.165/~mirza/uploads/'+file_val+'.jpg?'+d.getTime())
    })
  })

</script>
</body>
</html>