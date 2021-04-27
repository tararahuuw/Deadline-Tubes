<?php
include '../function.php';
// connecting to database
$conn = mysqli_connect("localhost", "root", "", "bot") or die("Database Error");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// getting user message through ajax
$getMesg = mysqli_real_escape_string($conn, $_POST['text']);

//  ============================================== MULAI MAIN PROGRAM ==============================================

if (isdeletetask($getMesg)) {
  $nomorId = getinputtaskid($getMesg);
  if ($nomorId == 0) {
    echo "Nomor id ga bisa aku temuin, atau kamu cari nomor id 0 dimana itu tidak ada";
  } else if ($nomorId == -1) {
    echo 'Aku kurang ngerti perintah kamu';
  } else {
    $deletequery = 'DELETE FROM chatbot WHERE Id = ' . $nomorId;
    if ($conn->query($deletequery) === TRUE) {
      if ($conn->affected_rows > 0) {
        echo "Id $nomorId berhasil dihapus :D, keren semangat terus ya!";
      } else {
        echo "Maaff, id yang kamu cari gaada tuh :(";
      }
    } else {
      echo "Terjadi kesalahan pada database";
    }
  }
} else if (isShowTask($getMesg)) {
  $kata = getTimePeriodWord($getMesg);
  $query = getShowQuery($getMesg, $kata);
  if ($query == ';') {
    echo 'Pesan tidak dikenali';
  } else {
    $result = $conn->query($query);
    if (mysqli_num_rows($result) > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        echo "(ID: " . $row["Id"] . ") " . $row["Deadline"] . " - " . $row["Subjects"] . " - " . $row['Keyword'] . " - " . $row["Topic"] . "<br><br>";
      }
    } else {
      echo "Tidak ada";
    }
  }
} else if (isDelayTask($getMesg)){
  $query = getDelayQuery($getMesg);
  if($query == ''){
    echo 'Tambahkan id task pada pesan ya, contoh: (task X) X adalah id';
  } else if ($query == "Tanggal tidak valid"){
    echo "Tanggal yang kamu masukan tidak valid, tanggal baru harus lebih besar dari hari ini";
  } else {
    $result = $conn->query($query);
    if ($result && $conn->affected_rows > 0) {
      echo 'Berhasil memperbarui task';
    } else {
      echo "Gagal memperbarui task, id tidak ada atau deadline yang kamu masukan salah";
    }
  }
} else if (deadline($getMesg)) {
  preg_match("/[a-z A-Z]{2}[\d]{4}/", $getMesg, $matches2);
  preg_match("/kuis|tubes|tucil|tugas|ujian/i", $getMesg, $matches3);
  // echo $matches2[0];
  // echo $matches3[0];
  $query = "SELECT * FROM chatbot WHERE Matkul = '$matches2[0]' AND Keyword = '$matches3[0]'";
  if ($query == ';') {
    echo 'Pesan tidak dikenali';
  } 
  else {
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        echo $row["Deadline"];
      }
    } else {
      echo "Tidak ada";
    }
  }
else {
  // INI BUAT SEMENTARA AJA ELSE NYA NANTI DIRAPIHIN
  //checking user query to database query
  // $check_data = "INSERT INTO chatbot (queries, replies) VALUES ('$getMesg', 'Data sudah ada di database')";

  // $check_data = "INSERT INTO dummydate (tanggal) VALUES (
  //     preg_match(((\d{4})-(\d{1,2})-(\d{1,2})), $getMesg))";

  // Contoh pesan : tubes IF2230 2021-07-01 bab 10

  preg_match("/\d{4}-\d{1,2}-\d{1,2}/", $getMesg, $matches1);

  preg_match("/[a-z A-Z]{2}[\d]{4}/", $getMesg, $matches2);

  preg_match("/[K k]uis|[T t]ubes|[U u]jian|[T t]ucil/", $getMesg, $matches3);

  preg_match("/bab./", $getMesg, $matches4);

  preg_match("/[K k]apan|[D d]eadline/", $getMesg, $matches5);

  if ($matches1[0] != NULL && $matches2[0] != NULL && $matches3[0] != NULL && $matches4[0] != NULL && $matches5 == NULL) {
    $check_data = "INSERT INTO chatbot (Deadline,Subjects,Keyword,Topic) VALUES ('$matches1[0]','$matches2[0]','$matches3[0]','$matches4[0]')";
  } else if ($matches5 != NULL && $matches2[0] != NULL && $matches3[0] != NULL) {
    echo $matches5[0];
  }

  // if ($matches2[0] == "kuis") {
  //     for i in range database

  // }
  // $run_query = mysqli_query($conn, $check_data) or die("Error");

  // // if user query matched to database query we'll show the reply otherwise it go to else statement
  // if(mysqli_num_rows($run_query) > 0){
  //     //fetching replay from the database according to the user query
  //     $fetch_data = mysqli_fetch_assoc($run_query);
  //     //storing replay to a varible which we'll send to ajax
  //     $replay = $fetch_data['replies'];
  //     echo $replay;

  // }else{
  //     echo "Sorry can't be able to understand you!";
  // }
  // if ($conn->query($check_data) === TRUE) {
  //     echo "record inserted successfully";
  // } else {
  //     echo ($matches[0]);
  // }

  if (mysqli_query($conn, $check_data)) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $check_data . "<br>" . mysqli_error($conn);
  }
}
mysqli_close($conn);
