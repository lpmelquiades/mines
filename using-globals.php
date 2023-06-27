<?php 

$rows = 10;
$cols = 10;
$mines = 1;

// Initialize the game board with all cells hidden
$board = [];
for ($i = 0; $i < $rows; $i++) {
  for ($j = 0; $j < $cols; $j++) {
    $board[$i][$j] = "hidden";
  }
}

// Place mines randomly on the board
$mine_locations = [];
for ($k = 0; $k < $mines; $k++) {
  $i = rand(0, $rows - 1);
  $j = rand(0, $cols - 1);
  if (!in_array([$i, $j], $mine_locations)) {
    $mine_locations[] = [$i, $j];
  } else {
    $k--;
  }
}

// Calculate the number of mines surrounding each cell
$count = [];
for ($i = 0; $i < $rows; $i++) {
  for ($j = 0; $j < $cols; $j++) {
    $count[$i][$j] = 0;
    foreach ($mine_locations as $mine) {
      if (abs($i - $mine[0]) <= 1 && abs($j - $mine[1]) <= 1) {
        $count[$i][$j]++;
      }
    }
  }
}


// Renders the game board (with no fog)
function renderBoard($all = False) {
  global $board, $count, $rows, $cols, $mine_locations;
  echo "  ";
  for ($i = 0; $i < $cols; $i++) {
    echo " " . ($i + 1);
  }
  echo "\n";
  for ($i = 0; $i < $rows; $i++) {
    // echo ($i + 1) . " ";
    printf("%02d ", $i + 1);


    for ($j = 0; $j < $cols; $j++) {
    
      if ($board[$i][$j] === 'hidden' && !$all) {
          echo "# ";
          continue;
      }    
    
      if (in_array([$i, $j], $mine_locations)) {
        echo "*";
      } else {
        if ($count[$i][$j] == 0) {
          echo " ";
        } else {
          echo $count[$i][$j];
        }
      }
      echo " ";
    }
    echo "\n";
  }
}
   
function checkWin() {
  global $board, $mines, $rows, $cols;
  $c = 0;
  for ($i = 0; $i < $rows; $i++) {
    for ($j = 0; $j < $cols; $j++) {
      if ($board[$i][$j] === "hidden") {
          $c++;
      }
      if ($c > $mines) {
          return False;
      }
    }
  }
  return ($c === $mines);
}

function revealOne($i, $j, &$next) {
    global $board, $rows, $cols, $count;
    if ($i < 0 || $j < 0 || $i >= $rows || $j >= $cols) {
        return;
    }
    
    if ($count[$i][$j] !== 0 || $board[$i][$j] !== 'hidden') {
        return;
    }
    
    $board[$i][$j] = 'shown';
    $next[] = [$i, $j];

}

function revealGroup($i, $j) {
    global $board, $count;
    $next = [];
    
    if ($count[$i][$j] !== 0 || $board[$i][$j] !== 'hidden') {
        return;
    }
    
    $board[$i][$j] = 'shown';
    $next[] = [$i, $j];
    
    while (count($next) !== 0) {
        $n = array_pop($next);
        revealOne($n[0]-1, $n[1]-1, $next);
        revealOne($n[0]-1, $n[1], $next);
        revealOne($n[0]-1, $n[1]+1, $next);
        revealOne($n[0], $n[1]-1, $next);
        revealOne($n[0], $n[1]+1, $next);
        revealOne($n[0]+1, $n[1]-1, $next);
        revealOne($n[0]+1, $n[1], $next);
        revealOne($n[0]+1, $n[1]+1, $next);
    }
}



// Function to be written
function reveal($row, $col) {
  global $mine_locations, $board;
  $i = $row-1;
  $j = $col-1;

  if (in_array([$i, $j], $mine_locations)) {
    $board[$i][$j] = 'shown';
    echo "\n(" . $row . ", " . $col . ") >> You lost \n";
    renderBoard(True);
    return False;
  }
  
  revealGroup($i, $j);
  
  
  if (checkWin()) {
    echo "\n(" . $row . ", " . $col . ") >> You win \n";
    renderBoard(True);
    return False;
  }

  echo "\n(" . $row . ", " . $col . ") \n";
  renderBoard();
  return True;
}


renderBoard();

while(reveal(
        rand(1, $rows),
        rand(1, $cols)
)) {
}





?>
