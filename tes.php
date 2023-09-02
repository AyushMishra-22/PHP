<!DOCTYPE html>
<html>
<head>
    <title>Contact Form</title>
</head>
<body>
    <?php

    // try to provide you the simple Solution of your problem and i only thing need to inform you that i am just started php so if any thing which i forget right now i will understand with the help of internet.
    // due to the external web cam in my laptop i can not give you the commplete recording because i tried many times but whenever wire looses connection get lost show hope this recording will give you the exact idea what i want to convey to you.
    require 'vendor/autoload.php';

    $fullname = $phone = $email = $subject = $message = "";
    $fullnameErr = $phoneErr = $emailErr = $subjectErr = $messageErr = "";
    $formSubmitted = false; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        if (empty($_POST["fullname"])) {
            $fullnameErr = "Full Name is required";
        } else {
            $fullname = test_input($_POST["fullname"]);

            if (!preg_match("/^[a-zA-Z ]*$/", $fullname)) {// only characters are allowed 
                $fullnameErr = "Only letters and white space allowed";
            }
        }

        if (empty($_POST["phone"])) {
            $phoneErr = "Phone Number is required";
        } else {
            $phone = test_input($_POST["phone"]);
            if (!preg_match("/^\d{10}$/", $phone)) { // depending upon the country regex will change 
                $phoneErr = "Invalid phone number";
            }
        }


        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
        } else {
            $email = test_input($_POST["email"]);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {// email validation of @,.com can also explicitely performed here, depending upon which which type email you are accepting.
                $emailErr = "Invalid email format";
            }
        }

        // Here the subject description is not provided as required means what should be the strlen and all.thats why simple validation is applied here 
        if (empty($_POST["subject"])) {
            $subjectErr = "Subject is required";
        } else {
            $subject = test_input($_POST["subject"]);
        }

        // here also the same thing applies that message len is not provided
        if (empty($_POST["message"])) {
            $messageErr = "Message is required";
        } else {
            $message = test_input($_POST["message"]);
        }


        if (empty($fullnameErr) && empty($phoneErr) && empty($emailErr) && empty($subjectErr) && empty($messageErr)) {
            $formSubmitted = true;

            // write your own credentials to work with this code as Right now I dont remember Mysql credentials thats why, blank space is there.
            $servername = "";
            $username = "";
            $password = "";
            $dbname = "";

        
            $conn = new mysqli($servername, $username, $password, $dbname);

        
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }


            $stmt = $conn->prepare("INSERT INTO contact_form (fullname, phone, email, subject, message, ip_address, timestamp) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssss", $fullname, $phone, $email, $subject, $message, $_SERVER['REMOTE_ADDR']);

        
            if ($stmt->execute()) {
            
                $mail = new PHPMailer\PHPMailer\PHPMailer();

                $mail->isSMTP();
                $mail->Host = 'smtp.example.com';// Did not get the actual understanding what you acctually want to send to the user thats why implemented the Simple mail transfer protocol.
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@example.com';
                $mail->Password = 'your_email_password';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

               
                $mail->setFrom('your_email@example.com', 'Your Name');
                $mail->addAddress('site_owner@example.com', 'Site Owner');

              
                $mail->isHTML(true);
                $mail->Subject = 'New Form Submission';
                $mail->Body = "Full Name: $fullname<br>Phone: $phone<br>Email: $email<br>Subject: $subject<br>Message: $message<br>IP Address: " . $_SERVER['REMOTE_ADDR'];

               
                if ($mail->send()) {
                    echo '<h2>Thank you for your submission!</h2>';
                } else {
                    echo 'Email could not be sent.';
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                }

            
                $stmt->close();
                $conn->close();
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    ?>

    <?php
    if (!$formSubmitted) {
    ?>

    <h2>Contact Us</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Full Name: <input type="text" name="fullname" value="<?php echo $fullname; ?>">
        <span class="error"><?php echo $fullnameErr; ?></span>
        <br><br>
        
        Phone Number: <input type="text" name="phone" value="<?php echo $phone; ?>">
        <span class="error"><?php echo $phoneErr; ?></span>
        <br><br>
        
        Email: <input type="text" name="email" value="<?php echo $email; ?>">
        <span class="error"><?php echo $emailErr; ?></span>
        <br><br>
        
        Subject: <input type="text" name="subject" value="<?php echo $subject; ?>">
        <span class="error"><?php echo $subjectErr; ?></span>
        <br><br>
        
        Message: <textarea name="message" rows="5" cols="40"><?php echo $message; ?></textarea>
        <span class="error"><?php echo $messageErr; ?></span>
        <br><br>

        <input type="submit" name="submit" value="Submit">
    </form>

    <?php
    }
    ?>
</body>
</html>

