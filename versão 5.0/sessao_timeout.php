<?php
// Session timeout duration in seconds
// Specify value lesser than the PHPs default timeout of 60 minutes
$timeout = 5400;
     

//$timeout = 10;



// Check existing timeout variable
if( isset( $_SESSION[ 'lastaccess' ] ) ) {


	//echo(isset( $_SESSION[ 'lastaccess' ]) );

	// Time difference since user sent last request
	$duration = time() - intval( $_SESSION[ 'lastaccess' ] );

//echo('<br>'.$duration);

	// Destroy if last request was sent before the current time minus last request
	if( $duration > $timeout ) {

		//include ("config.php");
		// Initialize the session
	  session_start();
     $id=$_SESSION['user_id'];
	 $sql = "update utilizadores set sessao_ativa=0 where id=".$id."";
	 $result = mysqli_query($db,$sql);



		// Clear the session
		session_unset();

		// Destroy the session
		session_destroy();

		// Restart the session
	  //session_start();
	  


	//echo("A sua sessão terminou.");
	 // header("location: index.php");
	  
	 // header("Refresh:0;url=index.php");

	
	 

?>
	<script> location.replace("i"); </script> 

<?php


	}




}
?>