<?php

/*sending mail*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer\src\Exception.php';
require 'PHPMailer\src\PHPMailer.php';
require 'PHPMailer\src\SMTP.php';


function reply($replytoaddress, $issue_id)
{
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPAuth = true;
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->Username = 'rana.randheer249@gmail.com';
	$mail->Password = 'password';
	$mail->setFrom('rana.randheer249@gmail.com');

	$mail->addAddress($replytoaddress);

	$mail->Subject = 'reply issue management system';
	$mail->Body = 'thank you for using IRM. 
	your complaint is registered.
	your issue id is '.$issue_id.'
	for further query include #issue_id# issue id in body 
	example: #issue_id# 249';
//send the message, check for errors
	/*if (!$mail->send())
	{
		echo "ERROR: " . $mail->ErrorInfo;
	} 
	else
	{
    echo "SUCCESS";
	}*/
}

function errormsg($replytoaddress, $issue_id)
{
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPAuth = true;
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->Username = 'rana.randheer249@gmail.com';
	$mail->Password = 'password';
	$mail->setFrom('rana.randheer249@gmail.com');

	$mail->addAddress($replytoaddress);

	$mail->Subject = 'reply issue management system';
	$mail->Body = 'thank you for using IRM. 
	issue id is '.$issue_id.' is invalid
	please recheck your issue id
	for further query include #issue_id# issue id in body 
	example: #issue_id# 249';
//send the message, check for errors
	/*if (!$mail->send())
	{
		echo "ERROR: " . $mail->ErrorInfo;
	} 
	else
	{
    echo "SUCCESS";
	}*/
}




/*database functions*/
$user="root";
$pwd="";
$db="database";
$conn=new mysqli("localhost", $user, $pwd, $db) or die(); 

function update($issue_id,$description)
{
	global $conn;/*
	if ($conn->query("UPDATE parsingtable SET description='$description' WHERE issue_id=$issue_id") === TRUE)
	{
		echo "Record updated successfully";
	}
	else
	{
		echo "Error updating record: " . $conn->error;
	}*/
}


function verify_issue_id($issue_id,$description)
{
	global $user,$pwd,$db,$conn;
	
	$checkid=mysqli_query($conn, "SELECT issue_id FROM parsingtable WHERE issue_id=$issue_id");
	$test=mysqli_fetch_array($checkid);
	if($test)
	{
		echo "$issue_id is in database<br>";
		$result=$conn->query("SELECT * FROM parsingtable WHERE issue_id='$issue_id'");
		$row = $result->fetch_assoc();
		$description = $row["description"]." + ".$description;
		//echo $description,PHP_EOL;
		update($issue_id,$description);
		return true;
	}
	else
	{
		echo "$issue_id is not in database<br>";
		return false;
		
	}
}


function push_data($user_id,$category_id,$description)
{
	global $conn;
	mysqli_query($conn, "INSERT INTO parsingtable (issue_id,user_id,category_id,description,status) VALUES ('', '$user_id', '$category_id', '$description','')");
	$result=$conn->query("SELECT * FROM parsingtable ORDER BY issue_id DESC LIMIT 1");
	$row = $result->fetch_assoc();
	$issue_id=$row["issue_id"];
	return($issue_id);
}





session_start();
//initilizing parameters
$l_utime = 1530866309 ;
if (isset($_SESSION["l_utime"]))
	$l_utime = $_SESSION["l_utime"];
else
	$_SESSION["l_utime"] = $l_utime;

echo "l_utime = ";
echo $l_utime,PHP_EOL;
echo "<br><br>";


//access inbox
$imap = imap_open('{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX', 'rana.randheer249@gmail.com', 'password');

if( $imap )
{  
	$num = imap_num_msg($imap);
	if( $num > 0 )
	{
		$n=$num;
		$header = imap_header($imap, $n);
		$r_utime=$header->udate;
		
		if($r_utime<=$l_utime)
			echo "No new mail", PHP_EOL;
		
		while($r_utime>$l_utime)
		{
			$header = imap_header($imap, $n);
			echo "<br>unix time = ";
			echo $header->udate;
			echo "<br>sender address = ";
			echo $header->senderaddress;
			echo "<br>subject = ";
			$h=$header->subject;
			echo $h,PHP_EOL;
			echo "<br>body<br>";
			$body = imap_fetchbody($imap, $num,1);
			if (preg_match('/^([a-zA-Z0-9]{76} )+[a-zA-Z0-9]{76}$/', $body))
			{
				$body = base64_decode($body);
			}
			echo $body."<br>";
			//echo iconv("UTF-8", "ASCII", $body),PHP_EOL;
			//echo imap_qprint(imap_body($imap, $n));
			//echo "<br><br>";
			
			$i=0;
			//does issue id exist
			while($i<strlen($body)-10)
			{
				if($body[$i].$body[$i+1].$body[$i+2].$body[$i+3].$body[$i+4].$body[$i+5].$body[$i+6].$body[$i+7].$body[$i+8].$body[$i+9]=="#issue_id#")
				{
					echo "<br>issue id found<br>";
					//extracting issue_id
					$i=$i+10;
					while($i<strlen($body) && $body[$i]==' ')
					{
						$i++;
					}
					$string="0";
					while($i<strlen($body) && $body[$i]!=' ')
					{
						$string=$string.$body[$i];
						$i++;
					}
					$issue_id = (int)$string;
					//echo $issue_id,PHP_EOL;
					//verify is issue_id and push body in description field
					$bool=true;
					$bool=verify_issue_id($issue_id,$body);
					if(!$bool)
					{
						$replyto=$header->reply_to;
						$replytoaddress = $replyto[0]->mailbox . "@" . $replyto[0]->host;
						errormsg($replytoaddress, $issue_id);
					}
					break;
				}
				$i++;
			}
			
			//push data int database and reply back if issue id not found
			if($i==strlen($body)-10 || $i<10)
			{
				$replyto=$header->reply_to;
				$replytoaddress = $replyto[0]->mailbox . "@" . $replyto[0]->host;
				
				/*checking for issue id in subject*/
				$i=0;
				while($i<strlen($h)-10)
				{
				if($h[$i].$h[$i+1].$h[$i+2].$h[$i+3].$h[$i+4].$h[$i+5].$h[$i+6].$h[$i+7].$h[$i+8].$h[$i+9]=="#issue_id#")
				{
					echo "<br>issue id found<br>";
					//extracting issue_id
					$i=$i+10;
					while($i<strlen($h) && $h[$i]==' ')
					{
						$i++;
					}
					$string="0";
					while($i<strlen($h) && $h[$i]!=' ')
					{
						$string=$string.$h[$i];
						$i++;
					}
					$issue_id = (int)$string;
					//echo $issue_id,PHP_EOL;
					//verify is issue_id and push body in description field
					$bool=true;
					$bool=verify_issue_id($issue_id,$body);
					if(!$bool)
					{
						$replyto=$header->reply_to;
						$replytoaddress = $replyto[0]->mailbox . "@" . $replyto[0]->host;
						errormsg($replytoaddress, $issue_id);
					}
					break;
				}
				$i++;
				}
				/**/
				
				if(($i==strlen($h)-10 || $i<10)&&$h[0].$h[1]!="Re")
				{
					$user_id=0;
					$category_id=0;
					
					$checkid=mysqli_query($conn, "SELECT * FROM member WHERE email='".$replytoaddress."'");
					$test=mysqli_fetch_array($checkid);
					if($checkid)
					{
						//echo "$email is in database<br>";
						$result=$conn->query("SELECT * FROM member WHERE email='".$replytoaddress."'");
						$row = $result->fetch_assoc();
						$user_id=$row["id"];
						$category_id=$row["department"];
					}
					
					$issue_id=push_data($user_id,$category_id,$body);
					
					reply($replytoaddress, $issue_id);
				}
			}
			
			echo "<br><br><br><br>";
			$n--;
			$header = imap_header($imap, $n);
			$r_utime=$header->udate;
		}
    }
	
	$header = imap_header($imap, $num);
	$r_utime=$header->udate;
}

imap_close($imap); 

if($r_utime>$l_utime)
$_SESSION["l_utime"] = $r_utime;

?>

