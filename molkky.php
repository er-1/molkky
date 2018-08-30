<?php
$timestart = microtime(true);

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
//ini_set('log_errors', 'On');

define("WIDHT",  350);
define("HEIGHT", 530);
define("PLAYER",  50);
define("GROUND", HEIGHT - ((PLAYER + 1) * 3));
define("PLAYING", PLAYER - 10);
define("BITS", min(78, floor((GROUND - PLAYING) / 4)));
define("TARGET", BITS * 4);
define("TARGET_MARGIN", (GROUND - PLAYING - TARGET) / 2);

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
?>
<!doctype html>
<html> 
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MÖLKKY</title>
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

.square {
  width: <?php print(WIDTH); ?>px;
  height: <?php print(HEIGHT); ?>px;
  margin: 0px;
  padding: 0px;
  border: 0px;
}

.target {
  width: 100%;
  height: <?php print(GROUND); ?>px;
  margin: inherit;
  padding: inherit;
  border: inherit;
  position: relative;
}
.target div.player {
    width: 100%;
    height: <?php print(PLAYING); ?>px;
    line-height: <?php print(PLAYING); ?>px;
    max-height: <?php print(PLAYING); ?>px;
    vertical-align: middle;
    text-align: center;
    font-size: 35px;
    text-transform:uppercase;
    padding: inherit;
    margin: inherit;
    border: inherit;
    position: relative;
    float: left;
}
.target div.bits {
    width: 100%;
    height: <?php print(TARGET); ?>px;
    margin: <?php print(TARGET_MARGIN); ?>px 0px 0px 0px;
    padding: inherit;
    border: inherit;
    position: relative;
    float: left;
}
.target div.bits div {
    width: 100%;
    height: <?php print(BITS); ?>px;
    margin: 0px;
    padding: inherit;
    border: inherit;
    position: relative;
    float: left;
    text-align: center;
}
.target div.bits div span {
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
}

.player {
    width: 100%;
    height: <?php print(PLAYER); ?>px;
    line-height: <?php print(PLAYER); ?>px;
    max-height: <?php print(PLAYER); ?>px;
    margin: inherit;
    padding: inherit;
    border-bottom: 1px solid #AAAAAA;
    position: relative;
    text-align: left;
    font-size: 20px;
    font-variant: small-caps;
}
.player div {
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
    color: #FF0000;
}
</style>
</head>
<body>
<div class="square centered">
    <div class="player">Thomas<div>23</div></div>
    <div class="player">Damien<div>10</div></div>
    <div class="player">Louis<div>42</div></div>
    <div class="target">
        <div class="player">&gt; Damien &lt;</div>
        <div class="bits">
            <div><span>7</span><span>9</span><span>8</span></div>
            <div><span>5</span><span>11</span><span>12</span><span>6</span></div>
            <div><span>3</span><span>10</span><span>4</span></div>
            <div><span>1</span><span>2</span></div>
        </div>
    </div>
</div>
<p style="font-size:45%;">Generated in <?php print(microtime(true) - $timestart); ?> seconds::::<a href="https://github.com/er-1/molkky">GitHub</a></p>
</body>
</html>
