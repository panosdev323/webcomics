<?php
session_start();

function getComic(int $id = null): array {
  if ($id) {
    $apiUrl = "https://xkcd.com/" . $id . "/info.0.json";
  } else {
    $apiUrl = "https://xkcd.com/info.0.json";
  }
  // Initialize cURL session
  $ch = curl_init();
  // Set the URL
  curl_setopt($ch, CURLOPT_URL, $apiUrl);
  // Set the method to GET
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // Execute the request
  $response = curl_exec($ch);
  // Check for errors
  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  } else {
    // Decode the JSON response
    $data = json_decode($response, true);
    // return the data  
    return [
      'num' => $data['num'] ?? 0,
      'comicnum' => $data['num'] ?? 0,
      'title' => $data['safe_title'] ?? '-',
      'image' => $data['img'] ?? null,
      'alt' =>  $data['alt'] ?? '-',
      'year' => $data['year'] ?? '-'
    ];
  }
  // Close the cURL session
  curl_close($ch);
}

// get latest comic and total comics
$result = getComic();
$last = $result['num'];
// set current commic number
if (!isset($_SESSION['current'])) {
  $_SESSION['current'] = 1;
}

function btnActions()
{
  global $result, $last;
  if (isset($_POST['submit'])) {
    switch ($_POST['submit']) {
      case "first":
        $_SESSION['current'] = 1;
        break;
      case "last":
        $_SESSION['current'] = $last;
        break;
      case "next":
        $_SESSION['current'] < $last ? $_SESSION['current'] += 1 : $_SESSION['current'] = $last;
        break;
      case "previus":
        $_SESSION['current'] > 1 ? $_SESSION['current'] -= 1 : $_SESSION['current'] = 1;
        break;
      case "random":
        $_SESSION['current'] = rand(1, $last);
        break;
      default:
        $_SESSION['current'] = 1;
        break;
    }
    $result = getComic($_SESSION['current']);
  }
}
// actions on button submits
btnActions();
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Webcomics xkcd</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <main>
      <?php echo
      "<h4 class='totalc'>Total Comics: " .  $last . "</h4>
              <article>
                <div class='d-flex'>
                  <h5 class='comicn'>N." .  $result['comicnum'] . "</h5>
                  <h5>Title: " .  $result['title'] . "</h5>
                </div>
                <img src=" . $result['image'] . " alt=" . $result['alt'] . ">
                <figcaption class='figc'>" . $result['alt'] . "</figcaption>
              <article>";
      ?>
      <form method="POST">
        <section class="btnSection">
          <div>
            <button id="firstb" type="submit" name="submit" value="first">First</button>
          </div>
          <div>
            <button type="submit" name="submit" value="previus">Previus</button>
            <button type="submit" name="submit" value="next">Next</button>
            <button type="submit" name="submit" value="random">Random</button>
          </div>
          <div>
            <button type="submit" name="submit" value="last">Last</button>
          </div>
        </section>
      </form>
    </main>
  </body>
</html>