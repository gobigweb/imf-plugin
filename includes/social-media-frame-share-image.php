<?php
if(!isset($_GET['share-image']) && !isset($_SESSTION['loc'])){
    header("Redirect: index.php");
}
?>

<div id="imf-wrapper">
    <div id="imf-content">
    <?php
        $url = $_SESSION['loc'];        
        echo "<a href='". $url ."' download='imf-profile'><img src='". $url ."' /></a>";      
    ?>
    <br><br>
    <p>
        <a href="index.php"><button id="download">Create New</button></a>
    </p>
    </div>
</div>