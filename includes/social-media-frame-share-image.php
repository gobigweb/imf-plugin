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
 
    </div>
</div>