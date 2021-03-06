<html>
<head>
    <title>Project 1B</title>
</head>
<h1> Information about this Movie: </h1>

<FORM METHOD = "POST" ACTION = "show_movie.php">
<br/>
<h4> Search for Movie:</h4>
<input type="text" name="key" value="" placeholder="" size="60">
<input type="submit" value="Submit" name="submit"><br>
<br><input type="submit" name="Homepage" value="Homepage">
<br/>
</FORM>

<?php
    # establish a connection
    $db_connection = mysql_connect("localhost", "cs143", "");
    # error handling
    if(!$db_connection) {
        $errmsg = mysql_error($db_connection);
        print "Connection failed: $errmsg <br />";
        exit(1);
    }

    # select a database
    mysql_select_db("CS143", $db_connection);

    if (isset($_POST["submit"])){
        $needle = mysql_escape_string($_POST["key"]);
        $query_movie = "SELECT id, title,year FROM Movie WHERE title LIKE '%{$needle}%'";
        $rs_movie = mysql_query($query_movie, $db_connection);
        $row = mysql_fetch_array($rs_movie);
        $no_rows = FALSE;

        if(!$row){
            echo 'Movie not found';
        }
        else{
?>
<h3> Results from Movie Search: </h3>
<table border="1" cellspace="1" cellpadding="2">
    <tr align="center">
        <?php
            echo '<th>Title</th>';
        ?>
    </tr>
<?php
            while (!$no_rows) {
                echo '<tr align="center">';
                if (isset($row[1])){
                    echo '<td>';
                    echo '<a href="show_movie.php?id='.$row[0].'">';
                    echo $row[1].' ('.$row[2].') ';
                    echo '</a>';
                    echo '</td>';
                }
                echo '</tr>';
                $no_rows = !($row = mysql_fetch_array($rs_movie));
            }
        }

    }
?>
</table>
<?php
    if ($mid = $_GET["id"]){

    	$query_1 = "SELECT title, company, rating, year FROM Movie WHERE id=$mid";
    	$rs_1 = mysql_query($query_1, $db_connection);
    	if(!$rs_1){
    		echo 'Basic Information: N/A <br/>';
    	}
    	else{
    		$row_1 = mysql_fetch_array($rs_1);
    		echo 'Title: '.$row_1[0].' ('.$row_1[3].') <br>';
    		echo 'Producer: '.$row_1[1]. '<br>';
    		echo 'MPAA Rating: '.$row_1[2]. '<br>';
    	}

    	// Director
    	$query_director = "SELECT Director.first, Director.last, Director.dob FROM Director, MovieDirector WHERE MovieDirector.mid=$mid AND MovieDirector.did=Director.id";
        $rs_director = mysql_query($query_director, $db_connection);
        if(!$rs_director){
            echo 'Director: <br>';
        }
        else{
            echo 'Director: ';
            while ($row_director = mysql_fetch_array($rs_director)){
                echo $row_director[0].' '.$row_director[1].' ('.$row_director[2].') ';
            }
            echo '<br>';
        }

        // Genre
        $query_genre = "SELECT genre FROM MovieGenre WHERE mid=$mid";
        $rs_genre = mysql_query($query_genre, $db_connection);
        if(!$rs_genre){
            echo 'Genre: <br>';
        }
        else{
        	echo 'Genre:';
            while ($row_genre = mysql_fetch_array($rs_genre)){
                echo ' '.$row_genre[0];
            }
            echo '<br>';
        }

        // Actors
    	$query_actor = "SELECT Actor.id, Actor.first, Actor.last, Actor.dob, MovieActor.role FROM Actor, MovieActor WHERE MovieActor.mid=$mid AND MovieActor.aid=Actor.id";
        $rs_actor = mysql_query($query_actor, $db_connection);
?>
<br>
<h3> Cast: </h3>
<table border="1" cellspace="1" cellpadding="2">
    <tr align="center">
		<th>Actor/Actress</th>
        <th>Role</th>
    </tr>
<?php
        if($rs_actor){
            while ($row_actor = mysql_fetch_array($rs_actor)){
                echo '<tr align="center">';
                if (isset($row_actor)){
                    echo '<td>';
                    echo '<a href="show_actor.php?id='.$row_actor[0].'">';
                    echo $row_actor[1]." ".$row_actor[2]." (".$row_actor[3].") ";
                    echo '</a>';
                    echo '</td>';

                    echo '<td>'.$row_actor[4].'</td>';
                }
                echo '</tr>';
            }
        }
?>
</table>


<?php
    	// Score
        $query_score = "SELECT AVG(rating) FROM Review WHERE mid=$mid";
        $rs_score = mysql_query($query_score, $db_connection);
        $row_score = mysql_fetch_array($rs_score);
        if($row_score[0]){
?>

<h3> Reviews: </h3>
<h4> Average score is <?php echo $row_score[0]?>/5.</h4>


<?php
	    }
	    else{
?>
<h3> Reviews: No one has reviewed this movie yet</h3>
<h4> Average score is N/A</h4>

<?php
	    }
    	// Reviews
        $query_review = "SELECT name, time, rating, comment FROM Review WHERE mid=$mid";
        $rs_review = mysql_query($query_review, $db_connection);
        if(!$rs_review){
            echo 'review: <br>';
        }
        else{
        	while($row_review = mysql_fetch_array($rs_review)){
        		echo $row_review[0]."rated this movie with a score ".$row_review[2]."/5 and commented at ".$row_review[1]."<br>";
        		echo $row_review[3]."<br>";
        	}
    	}

    
?>
<FORM METHOD = "POST" ACTION = "newComment.php">
<h4> Leave your comment 
    <input type="submit" value="here" name="comment"/> <br>
</h4>
</FORM>

<?php
        if (isset($_POST["comment"])){
            header("Location:newComment.php");
            exit();
        }
    }
    if (isset($_POST["Homepage"])){
        header("Location:homepage.php");
        exit();
    }
    mysql_close($db_connection);
?>

</html>