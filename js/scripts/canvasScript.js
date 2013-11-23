
var c=document.getElementById("myCanvas");
var ctx=c.getContext("2d");

ctx.fillStyle="#FFCC00";
ctx.fillRect(0,0,500,500);

ctx.fillStyle= "#FFFFFF";
$a = 50;
$b = 100;
for ($y=0; $y<=5; $y++){
	for ($x=0; $x<=6; $x++)
	{
		ctx.beginPath();
		ctx.arc($a, $b, 27, 0, Math.PI*2, true); 
		ctx.closePath();
		ctx.fill();
		$a = $a + 65;
	}
	$b = $b + 65;
	$a = 50;
}
