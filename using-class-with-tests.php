<?php 

$rows = 10;
$cols = 10;
$mines = 1;


// Place mines randomly on the board
$random_mine_locations = [];
for ($k = 0; $k < $mines; $k++) {
  $i = rand(0, $rows - 1);
  $j = rand(0, $cols - 1);
  if (!in_array([$i, $j], $random_mine_locations)) {
    $random_mine_locations[] = [$i, $j];
  } else {
    $k--;
  }
}


class Game {
    
    private $board;
    private $count;
    private $mines;
    private $rows;
    private $cols;
    private $mine_locations;

    function __construct($rows, $cols, $mine_locations) {
        $this->rows = $rows;
        $this->cols = $cols;
        $this->mine_locations= $mine_locations;
        $this->mines = count($this->mine_locations);
        $this->initBoard();
        $this->initMineCount();
    }
    
    private function initBoard() {
        // Initialize the game board with all cells hidden
        $this->board = [];
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $this->board[$i][$j] = "hidden";
            }
        }
    }
    
    private function initMineCount() {
        // Calculate the number of mines surrounding each cell
        $this->count = [];
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $this->count[$i][$j] = 0;
                foreach ($this->mine_locations as $mine) {
                    if (abs($i - $mine[0]) <= 1 && abs($j - $mine[1]) <= 1) {
                        $this->count[$i][$j]++;
                    }
                }
            }
        }
    }
    
    // Renders the game board (with no fog)
    public function renderBoard($all = False) {
        echo "  ";
        for ($i = 0; $i < $this->cols; $i++) {
            echo " " . ($i + 1);
        }
        echo "\n";
        for ($i = 0; $i < $this->rows; $i++) {
            // echo ($i + 1) . " ";
            printf("%02d ", $i + 1);

            for ($j = 0; $j < $this->cols; $j++) {
    
                if ($this->board[$i][$j] === 'hidden' && !$all) {
                    echo "# ";
                    continue;
                }    
    
                if (in_array([$i, $j], $this->mine_locations)) {
                    echo "*";
                } else {
                    if ($this->count[$i][$j] == 0) {
                        echo " ";
                    } else {
                        echo $this->count[$i][$j];
                    }
                }
                echo " ";
            }
            echo "\n";
        }
    }


    private function checkWin() {
        $c = 0;
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                if ($this->board[$i][$j] === "hidden") {
                    $c++;
                }
                if ($c > $this->mines) {
                    return False;
                }
            }
        }
        return ($c === $this->mines);
    }
    
    private function revealOne($i, $j, &$next) {
        if ($i < 0 || $j < 0 || $i >= $this->rows || $j >= $this->cols) {
            return;
        }
    
        if ($this->count[$i][$j] !== 0 || $this->board[$i][$j] !== 'hidden') {
            return;
        }
    
        $this->board[$i][$j] = 'shown';
        $next[] = [$i, $j];
    }


    private function revealGroup($i, $j) {
        $next = [];
    
        if ($this->count[$i][$j] !== 0 || $this->board[$i][$j] !== 'hidden') {
            return;
        }
    
        $this->board[$i][$j] = 'shown';
        $next[] = [$i, $j];
    
        while (count($next) !== 0) {
            $n = array_pop($next);
            $this->revealOne($n[0]-1, $n[1]-1, $next);
            $this->revealOne($n[0]-1, $n[1], $next);
            $this->revealOne($n[0]-1, $n[1]+1, $next);
            $this->revealOne($n[0], $n[1]-1, $next);
            $this->revealOne($n[0], $n[1]+1, $next);
            $this->revealOne($n[0]+1, $n[1]-1, $next);
            $this->revealOne($n[0]+1, $n[1], $next);
            $this->revealOne($n[0]+1, $n[1]+1, $next);
        }
    }
    
    // Function to be written
    public function reveal($row, $col) {
        
        $i = $row-1;
        $j = $col-1;
        
        if ($i < 0 || $j < 0 || $i >= $this->rows || $j >= $this->cols) {
            return;
        }

        if (in_array([$i, $j], $this->mine_locations)) {
            $this->board[$i][$j] = 'shown';
            echo "\n(" . $row . ", " . $col . ") >> You lost \n";
            $this->renderBoard(True);
            return False;
        }
  
        $this->revealGroup($i, $j);
        $this->board[$i][$j] = 'shown';
  
  
        if ($this->checkWin()) {
            echo "\n(" . $row . ", " . $col . ") >> You win \n";
            $this->renderBoard(True);
            return False;
        }

        echo "\n(" . $row . ", " . $col . ") \n";
        $this->renderBoard();
        return True;
    }


}


function testWin01() {
    $game = new Game(10, 10, [[4,4]]);
    $game->renderBoard();

    if (
        $game->reveal(9,9)
        && $game->reveal(4,4)
        && $game->reveal(4,5)
        && $game->reveal(4,6)
        && $game->reveal(5,4)
        && $game->reveal(5,6)
        && $game->reveal(6,4)
        && $game->reveal(6,5)
        && !$game->reveal(6,6)
    ) {
        echo "Win01 Passed \n";
    } else {
        echo "Win01 Failed \n";
    }
}

function testWin02() {
    $game = new Game(10, 10, [[4,6],[3,7]]);
    $game->renderBoard();

    if (
        $game->reveal(3,7)
        && $game->reveal(3,8)
        && $game->reveal(3,9)
        && $game->reveal(4,6)
        && $game->reveal(4,7)
        && $game->reveal(4,9)
        && $game->reveal(5,6)
        && $game->reveal(5,8)
        && $game->reveal(5,9)
        && $game->reveal(6,6)
        && $game->reveal(6,7)
        && $game->reveal(6,8)
        && !$game->reveal(2,2)
    ) {
        echo "Win02 Passed \n";
    } else {
        echo "Win02 Failed \n";
    }
}

function testLost01() {
    $game = new Game(10, 10, [[4,4]]);
    $game->renderBoard();

    if (
        $game->reveal(9,9)
        && $game->reveal(4,4)
        && $game->reveal(4,5)
        && $game->reveal(4,6)
        && $game->reveal(5,4)
        && $game->reveal(5,6)
        && $game->reveal(6,4)
        && $game->reveal(6,5)
        && !$game->reveal(5,5)
    ) {
        echo "Lost01 Passed \n";
    } else {
        echo "Lost01 Failed \n";
    }
}

function testLost02() {
    $game = new Game(10, 10, [[4,6],[3,7]]);
    $game->renderBoard();

    if (
        $game->reveal(3,7)
        && $game->reveal(3,8)
        && $game->reveal(3,9)
        && $game->reveal(4,6)
        && $game->reveal(4,7)
        && $game->reveal(4,9)
        && $game->reveal(5,6)
        && $game->reveal(5,8)
        && $game->reveal(5,9)
        && $game->reveal(6,6)
        && $game->reveal(6,7)
        && $game->reveal(6,8)
        && !$game->reveal(4,8)
    ) {
        echo "Lost02 Passed \n";
    } else {
        echo "Lost02 Failed \n";
    }
}


testWin01();
testWin02();
testLost01();
testLost02();


?>
