<?php
  
function notify($contacts, $subject, $body, $conn){
    $query = "INSERT INTO Emails (Subj, Body, Contacts) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    if($stmt->execute(array($subject, $body, $contacts))){
        echo "Message sent.";
    }else{
        echo "Message send failed.";
    }
}
?>