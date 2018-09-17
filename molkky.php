<?php
$timestart = microtime(true);

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
//ini_set('log_errors', 'On');

define("WIDTH",  350);
define("HEIGHT", 530);
define("PLAYER",  50);
define("BLANK_WIDTH", 8);
define("BLANK_MAX", 3);
define("GROUND", HEIGHT - ((PLAYER + 1) * 3));
define("PLAYING", PLAYER - 10);
define("BITS", min(78, floor((GROUND - PLAYING) / 4)));
define("TARGET", BITS * 4);
define("TARGET_MARGIN", (GROUND - PLAYING - TARGET) / 2);
define("BUTTON_PLAY", floor(WIDTH / 5) - 4);

define("MAX_PLAYERS", 6);
define("BITS_NUMBER", 12);

if (stripos($_SERVER['HTTP_USER_AGENT'], "google") !== false) {
?>
    <html>
    <head>
	<meta name="robots" content="noindex,nofollow">
	<meta name="googlebot" content="noindex,nofollow,noarchive,nosnippet">
	<title>EMPTY</title>
	</head>
	<body>EMPTY</body>
	</html>
<?php
    exit(0);
}

function bit($nb) {
    printf("<span id=\"bit_%d\" onclick=\"bit(%d);\">%d</span>", $nb, $nb, $nb);
}
?>
<!doctype html>
<html> 
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MÖLKKY</title>
<script src="jquery.js"></script>
<script>
var bits = [];
var players = [];
var scores = [];
var blank = [];
var currentplayer = -1;
var winner = -1;
var nbplayer = 0;
var mode = 0; //0: adding player. 1: gaming. 2: done.

function firstload() {
    for (var i = 0; i < <?php print(MAX_PLAYERS); ?>; i++) {
        players[i] = "";
        scores[i] = 0;
        blank[i] = 0;
    }
    currentplayer = -1;
    winner = -1;
    nbplayer = 0;
    mode = 0;
    clearDiv();
    initExit();
    $("#play").hide();
    refresh();
}

function sameGame() {
    for (var i = 0; i < nbplayer; i++) {
        scores[i] = 0;
        blank[i] = 0;
    }
    winner = -1;
    clearDiv();
    readyToPlay();
}

function winGame() {
    players.sort(function(a, b) {
        var n, m = 0;
        if (a == "") return 1;
        if (b == "") return -1;
        for (var i = 0; i < nbplayer; i++) {
            if (players[i] == a) n = i;
            if (players[i] == b) m = i;
        }
        if (n == winner) return -1;
        if (m == winner) return 1;
        var v = scores[m] - scores[n];
        if (v != 0)
            return v;
        return blank[n] - blank[m];
    });
    sameGame();
}

function loseGame() {
    players.sort(function(a, b) {
        var n, m = 0;
        if (a == "") return 1;
        if (b == "") return -1;
        for (var i = 0; i < nbplayer; i++) {
            if (players[i] == a) n = i;
            if (players[i] == b) m = i;
        }
        if (n == winner) return 1;
        if (m == winner) return -1;
        var v = scores[n] - scores[m];
        if (v != 0)
            return v;
        return blank[m] - blank[n];
    });
    sameGame();
}

function rndGame() {
    players.sort(function(a, b) {
        if (a == "") return 1;
        if (b == "") return -1;
        return 0.5 - Math.random();
    });
    sameGame();
}

function exit1() {
    $("#exit1").hide();
    $("#exit2").show();
}

function initExit() {
    $("#exit1").show();
    $("#exit2").hide();
}

function bit(nb) {
    if (mode != 1)
        return;
    initExit();
    bits[nb - 1] = ! bits[nb - 1];
    turnBits();
}

function clearDiv() {
    document.getElementById("current").innerHTML = "";
    document.getElementById("winner").innerHTML = "";
    for (var i = 0; i < <?php print(MAX_PLAYERS); ?>; i++) {
        $("#player_" + i).css("background-color", "transparent");
        $("#player_" + i).css("color", "#EEEEEE");
        $("#player_" + i).css("opacity", "1");
        if (nbplayer == 0)
            $("#player_" + i).css("width", "100%");
        $("#player_" + i).hide();
        document.getElementById("name_" + i).innerHTML = players[i];
        document.getElementById("score_" + i).innerHTML = 0;
        for (var b = 0; b < <?php print(BLANK_MAX); ?>; b++)
            $("#blank_" + i + "_" + b).hide();
    }
}

function turnBits() {
    var bg = "";
    var v = false;
    for(var i = 0; i < bits.length; i++) {
        if (bits[i]) {
            bg = "859900";
            v = true;
        } else
            bg = "BBBBBB";
        $("#bit_" + (i+1)).css("background-color", "#" + bg);
    }
    if (v) {
        $("#go").show();
        $("#ko").hide();
    } else {
        $("#go").hide();
        $("#ko").show();
    }
    var n = 50 - scores[currentplayer];
    if (n <= 12)
        if (! bits[n - 1])
            $("#bit_" + n).css("background-color", "#B58900");
}

function updatePlayer() {
    initExit();
    var s = getScore();
    if (s < 0) {
        blank[currentplayer]++;
    } else {
        blank[currentplayer] = 0;
        scores[currentplayer] += s;
        if (scores[currentplayer] > 50) {
            scores[currentplayer] = 25;
        } else {
            if (scores[currentplayer] == 50) {
                winner = currentplayer;
                mode = 2;
            }
        }
    }
    nextPlayer();
    refresh();
}

function validPlayer(n) {
    if (n >= nbplayer)
        return false;
    if (blank[n] >= <?php print(BLANK_MAX); ?>)
        return false;
    return true;
}

function next(nb) {
    var p = nb;
    while (true) {
        p = (p + 1) % nbplayer;
        if (validPlayer(p))
            break;
        if (p == nb)
            break;
    }
    return p;
}
function nextPlayer() {
    if (mode != 1)
        return;
    currentplayer = next(currentplayer);
}

function lastPlayer() {
    return currentplayer == next(currentplayer);
}

function refresh() {
    if (mode == 0) {
        $("#go").hide();
        $("#ko").hide();
        $("#newplayer").show();
        $("#target").hide();
        $("#vicotory").hide();
    }
    if ((mode == 1) && (lastPlayer())) {
        winner = currentplayer;
        mode = 2;
    }
    if (mode == 1) {
        $("#newplayer").hide();
        $("#target").show();
        $("#vicotory").hide();
        clearBits();
        turnBits();
        document.getElementById("current").innerHTML = players[currentplayer];
    }
    if (mode == 2) {
        $("#go").hide();
        $("#ko").hide();
        $("#newplayer").hide();
        $("#target").hide();
        $("#vicotory").show();
        document.getElementById("winner").innerHTML = players[currentplayer];
        $("#player_" + winner).css("background-color", "#859900");
        $("#player_" + winner).css("color", "#268BD2");
    }
    for (var i = 0; i < nbplayer; i++) {
        $("#player_" + i).show();
        document.getElementById("name_" + i).innerHTML = players[i];
        document.getElementById("score_" + i).innerHTML = scores[i];
        var l = "";
        for (var b = 0; b < <?php print(BLANK_MAX); ?>; b++) {
            l = "#blank_" + i + "_" + b;
            if ((blank[i] > 0) && (b <= (blank[i] - 1)))
                $(l).show();
            else
                $(l).hide();
        }
        if (blank[i] >= <?php print(BLANK_MAX); ?>)
            $("#player_" + i).css("opacity", "0.5");
    }
}

function getScore() {
    var bit = 0;
    var nb = 0;
    for(var i = 0; i < bits.length; i++)
        if (bits[i]) {
            nb++;
            bit = i+1;
        }
    if (nb == 0)
        return -1;
    if (nb > 1)
        return nb;
    return bit;
}

function clearBits() {
    for (var i = 0; i < <?php print(BITS_NUMBER); ?>; i++)
        bits[i] = false;
}

function readyToPlay() {
    mode = 1;
    currentplayer = 0;
    refresh();
}

function addPlayer() {
    var n = document.getElementById("fname").value.trim();
    document.getElementById("fname").value = "";
    if (n.length <= 0)
        return;
    players[nbplayer] = n;
    nbplayer++;
    if (nbplayer == ((<?php print(MAX_PLAYERS); ?> / 2) + 1))
        for (var i = 0; i < nbplayer; i++)
            $("#player_" + i).css("width", "50%");
    if (nbplayer > ((<?php print(MAX_PLAYERS); ?> / 2) + 1))
        $("#player_" + (nbplayer - 1)).css("width", "50%");
    if (nbplayer > 1)
        $("#play").show();
    if (nbplayer >= <?php print(MAX_PLAYERS); ?>)
        readyToPlay();
    refresh();
}

</script>
<style>
@font-face {
    font-family: "mymenlo";
    src: url(menlo.ttf) format("truetype");
}
body {
    font-family: mymenlo;
    background-color: #002b36;
    color: #839496;
}
p {
    text-align: center;
    margin-bottom: 0px;
    margin-top: 3px;
}
a {
    text-decoration: none;
    color: #ABBCBE;
}
.centered {
  display: table;
  position: relative;
  left: 50%;
  /* bring your own prefixes */
  transform: translate(-50%, 0%);
}

#square {
    width: <?php print(WIDTH); ?>px;
    height: <?php print(HEIGHT); ?>px;
    margin: 0px;
    padding: 0px;
    border: 0px;
    position: relative;
}

#target, #vicotory, #newplayer {
    width: 100%;
    height: <?php print(GROUND); ?>px;
    margin: inherit;
    padding: inherit;
    border: inherit;
    position: absolute;
    left: 0px;
    bottom: 0px;
    display: none;
}
#target #playing, #vicotory #txt, #vicotory #winner {
    width: 100%;
    height: <?php print(PLAYING); ?>px;
    line-height: <?php print(PLAYING); ?>px;
    max-height: <?php print(PLAYING); ?>px;
    vertical-align: middle;
    text-align: center;
    font-size: 35px;
    text-transform: uppercase;
    padding: inherit;
    margin: inherit;
    border: inherit;
    position: relative;
    float: left;
}
#target #playing span {
    color: #EEEEEE;
}
#target #bits {
    width: 100%;
    height: <?php print(TARGET); ?>px;
    margin: <?php print(TARGET_MARGIN); ?>px 0px 0px 0px;
    padding: inherit;
    border: inherit;
    position: relative;
    float: left;
}
#target #bits div {
    width: 100%;
    height: <?php print(BITS); ?>px;
    margin: 0px;
    padding: inherit;
    border: inherit;
    position: relative;
    float: left;
    text-align: center;
}
#target #bits div span {
    width: <?php print(BITS); ?>px;
    height: <?php print(BITS); ?>px;
    line-height: <?php print(BITS); ?>px;
    max-height: <?php print(BITS); ?>px;
    vertical-align: middle;
    text-align: center;
    font-size: 30px;
    background-color: #BBBBBB;
    color: #555555;
    border-radius: 50%;
    margin: 0px 4px 0px 4px;
    padding: inherit;
    display: inline-block;
    cursor: pointer;
}
#target #go, #target #ko, #target #exit1, #target #exit2 {
    cursor: pointer;
    display: inline-block;
    margin: inherit;
    padding: inherit;
    position: absolute;
    bottom: 0px;
    display: none;
}
#target #go, #target #ko {
    width: 50px;
    height: 50px;
    border-radius: 100% 5px 5px 5px;
    right: 0px;
}
#target #go {
    background-color: #859900;
}
#target #ko, #target #exit2 {
    background-color: #DC322F;
}
#target #exit1, #target #exit2 {
    left: 0px;
    border-radius: 3px 100% 3px 3px;
}
#target #exit1 {
    width: 20px;
    height: 20px;
    background-color: #CB4B16;
}
#target #exit2 {
    width: 50px;
    height: 50px;
    line-height: 50px;
    max-height: 50px;
    vertical-align: middle;
    text-align: left;
    font-size: 30px;
}
#target #exit2 span {
    color: #EEEEEE;
    text-transform: uppercase;
    font-weight: bold;
    margin-left: 10px;
}

#vicotory #txt {
    margin-top: 60px;
    height: 60px;
    line-height: 60px;
    max-height: 60px;
    font-size: 50px;
    font-weight: bold;
    color: #859900;
}

#vicotory #winner {
    margin-top: 20px;
    font-size: 40px;
    color: #EEEEEE;
}

#vicotory #buttons {
    float: left;
    width: <?php print(BUTTON_PLAY * 3 + 20); ?>px;
    padding: inherit;
    border: inherit;
}

.button, .buttons, .buttonBs {
    vertical-align: middle;
    text-align: center;
    border-radius: 50%;
    cursor: pointer;
    display: inline-block;
    padding: inherit;
    background-color: #859900;
    color: #EEEEEE;
    text-transform: uppercase;
}

.button {
    width: 110px;
    height: 110px;
    line-height: 110px;
    max-height: 110px;
    margin-bottom: 30px;
    position: absolute;
    bottom: 0px;
    font-size: 35px;
}

.buttons, .buttonBs {
    margin: 2px;
    float: left;
}

.buttons {
    width: <?php print(BUTTON_PLAY); ?>px;
    height: <?php print(BUTTON_PLAY); ?>px;
    line-height: <?php print(BUTTON_PLAY); ?>px;
    max-height: <?php print(BUTTON_PLAY); ?>px;
    font-size: 25px;
}

.buttonBs {
    width: <?php print(BUTTON_PLAY * 1.5); ?>px;
    height: <?php print(BUTTON_PLAY * 1.5); ?>px;
    line-height: <?php print(BUTTON_PLAY * 1.5); ?>px;
    max-height: <?php print(BUTTON_PLAY * 1.5); ?>px;
    font-size: 50px;
}

#newplayer #takeit {
    width: 100%;
    height: <?php print(PLAYER); ?>px;
    line-height: <?php print(PLAYER); ?>px;
    max-height: <?php print(PLAYER); ?>px;
    margin: inherit;
    padding: inherit;
    border: inherit;
    vertical-align: middle;
    font-size: 25px;
}
input {
    width: 95%;
    height: <?php print(PLAYER); ?>px;
    line-height: <?php print(PLAYER); ?>px;
    max-height: <?php print(PLAYER); ?>px;
    font-size: 25px;
}
#newplayer #add {
    width: 100%;
    height: <?php print(PLAYER); ?>px;
    line-height: <?php print(PLAYER); ?>px;
    max-height: <?php print(PLAYER); ?>px;
    margin-top: 10px;
    padding: inherit;
    border: inherit;
    vertical-align: middle;
}
#newplayer #add div {
    width: <?php print(PLAYER); ?>px;
    height: <?php print(PLAYER); ?>px;
    line-height: <?php print(PLAYER); ?>px;
    max-height: <?php print(PLAYER); ?>px;
    vertical-align: middle;
    text-align: center;
    border-radius: 50%;
    cursor: pointer;
    display: inline-block;
    margin: 0px;
    padding: inherit;
    position: absolute;
    background-color: #859900;
    color: #EEEEEE;
    text-transform: uppercase;
    font-size: <?php print(PLAYER); ?>px;
    font-weight: bold;
}
.player {
    width: 100%;
    height: <?php print(PLAYER); ?>px;
    line-height: <?php print(PLAYER); ?>px;
    max-height: <?php print(PLAYER); ?>px;
    margin: inherit;
    padding: inherit;
    border-bottom: 1px solid #BABABA;
    position: relative;
    text-align: left;
    color: #EEEEEE;
    display: none;
    float: left;
}
.player span {
    margin-left: 10px;
    font-size: 25px;
    font-variant: small-caps;
    text-overflow: ellipsis;
}
.player div.score {
    width: <?php print(PLAYER); ?>px;
    height: inherit;
    line-height: inherit;
    max-height: inherit;
    vertical-align: middle;
    text-align: center;
    font-size: 30px;
    margin: inherit;
    padding: inherit;
    border: 0px;
    position: absolute;
    right: 0px;
    bottom: 0px;
}
.player div.blank {
    width: <?php print((BLANK_WIDTH + 4) * BLANK_MAX); ?>px;
    height: inherit;
    margin: inherit;
    padding: inherit;
    border: 0px;
    position: absolute;
    right: <?php print(PLAYER); ?>px;
    bottom: 0px;
}
.player div.blank div {
    width: <?php print(BLANK_WIDTH); ?>px;
    height: <?php print(PLAYER - 12); ?>px;
    margin: 6px 2px;
    padding: inherit;
    border: inherit;
    background-color: #DC322F;
    float: left;
    display: none;
}
</style>
</head>
<body onload="firstload()">
<div id="square" class="centered">
<?php
     for ($i = 0; $i < MAX_PLAYERS; $i++) {
         printf("<div class=\"player\" id=\"player_%d\"><span id=\"name_%d\"></span><div class=\"blank\">", $i, $i);
         for ($b = 0; $b < BLANK_MAX; $b++)
             printf("<div id=\"blank_%d_%d\"></div>", $i, $b);
         printf("</div><div class=\"score\" id=\"score_%d\"></div></div>", $i);
     }
?>
    <div id="newplayer">
        <div id="takeit"><input class="centered" type="text" id="fname" name="fname" placeholder="Player..."></div>
        <div id="add"><div class="centered" onclick="addPlayer();">+</div></div>
        <div id="play" class="button centered" onclick="readyToPlay();">Play</div>
    </div>
    <div id="target">
        <div id="playing">&gt;&gt;&gt; <span id="current"></span> &lt;&lt;&lt;</div>
        <div id="bits">
            <div><?php bit(7); bit(9); bit(8); ?></div>
            <div><?php bit(5); bit(11); bit(12); bit(6); ?></div>
            <div><?php bit(3); bit(10); bit(4); ?></div>
            <div><?php bit(1); bit(2); ?></div>
        </div>
        <div id="go" onclick="updatePlayer();"></div>
        <div id="ko" onclick="updatePlayer();"></div>
        <div id="exit1" onclick="exit1();"></div>
        <div id="exit2" onclick="firstload();"><span>X</span></div>
    </div>
    <div id="vicotory">
        <div id="txt">VICOTORY !</div>
        <div id="winner"></div>
        <div class="centered" style="position:absolute;bottom:0px;">
            <div id="buttons" class="centered">
                <div class="buttonBs" onclick="winGame();">W</div>
                <div class="buttonBs" onclick="loseGame();">L</div>
            </div>
            <div id="buttons" class="centered">
                <div class="buttons" onclick="rndGame();">R</div>
                <div class="buttons" onclick="sameGame();">S</div>
                <div class="buttons" onclick="firstload();">N</div>
            </div>
        </div>
    </div>
</div>
<p style="font-size:45%;">Generated in <?php print(microtime(true) - $timestart); ?> seconds::::<a href="https://github.com/er-1/molkky">GitHub</a></p>
</body>
</html>
