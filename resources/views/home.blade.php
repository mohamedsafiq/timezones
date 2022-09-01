@include('layouts')
@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">{{ __('Times') }}</div>

				<div class="card-body">
					@if (session('status'))
						<div class="alert alert-success" role="alert">
							{{ session('status') }}
						</div>
					@endif
					<?php 
					$user = \App\Models\User::findOrFail(Auth::user()->id);
					?>
					<div class="form-group">
						<div class="col-md-12">
							<select class="form-control" name="timezone" id="timezone" onchange="getCurrentTime(this.value,<?=Auth::user()->id?>)">
								<?php
								$timezone = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
								foreach($timezone as $key => $timezone)
								{
									?>
									<option value="<?=$timezone?>" <?php echo ($timezone == $user->timezone)? "selected" : "" ?>><?=$timezone?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-md-12 text-center">
							<canvas id="canvas" width="250" height="250"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function getCurrentTime(city,user)
{
	$.ajax({
		url : '/changeTime',
		method : 'get',
		data: {
			user : user,
			city : city
		},
		success:function(result)
		{
			if(result.trim() == '1')
			{
				alert('Timezone Changed Successfully');
			}
		}
	});
}
var canvas = document.getElementById("canvas");
var ctx = canvas.getContext("2d");
var radius = canvas.height / 2;
ctx.translate(radius, radius);
radius = radius * 0.90;
// getCurrentTime('dsdds')
setInterval(drawClock, 1000);
function drawClock() {
  drawFace(ctx, radius);
  drawNumbers(ctx, radius);
  drawTime(ctx, radius,document.getElementById('timezone').value);
}

function drawFace(ctx, radius) {
  var grad;
  ctx.beginPath();
  ctx.arc(0, 0, radius, 0, 2*Math.PI);
  ctx.fillStyle = 'white';
  ctx.fill();
  grad = ctx.createRadialGradient(0,0,radius*0.95, 0,0,radius*1.05);
  grad.addColorStop(0, '#333');
  grad.addColorStop(0.5, 'white');
  grad.addColorStop(1, '#333');
  ctx.strokeStyle = grad;
  ctx.lineWidth = radius*0.1;
  ctx.stroke();
  ctx.beginPath();
  ctx.arc(0, 0, radius*0.1, 0, 2*Math.PI);
  ctx.fillStyle = '#333';
  ctx.fill();
}

function drawNumbers(ctx, radius) {
  var ang;
  var num;
  ctx.font = radius*0.15 + "px arial";
  ctx.textBaseline="middle";
  ctx.textAlign="center";
  for(num = 1; num < 13; num++){
    ang = num * Math.PI / 6;
    ctx.rotate(ang);
    ctx.translate(0, -radius*0.85);
    ctx.rotate(-ang);
    ctx.fillText(num.toString(), 0, 0);
    ctx.rotate(ang);
    ctx.translate(0, radius*0.85);
    ctx.rotate(-ang);
  }
}

function drawTime(ctx, radius,city){

   var current_time = new Date().toLocaleString("en-US", {timeZone: city});
	var hour = current_time.split(' ')[1].split(':')[0];
	var minute = current_time.split(' ')[1].split(':')[1];
	var second = current_time.split(' ')[1].split(':')[2];
    //hour
    hour=hour%12;
    hour=(hour*Math.PI/6)+
    (minute*Math.PI/(6*60))+
    (second*Math.PI/(360*60));
    drawHand(ctx, hour, radius*0.5, radius*0.07);
    //minute
    minute=(minute*Math.PI/30)+(second*Math.PI/(30*60));
    drawHand(ctx, minute, radius*0.8, radius*0.07);
    // second
    second=(second*Math.PI/30);
    drawHand(ctx, second, radius*0.9, radius*0.02);
}

function drawHand(ctx, pos, length, width) {
    ctx.beginPath();
    ctx.lineWidth = width;
    ctx.lineCap = "round";
    ctx.moveTo(0,0);
    ctx.rotate(pos);
    ctx.lineTo(0, -length);
    ctx.stroke();
    ctx.rotate(-pos);
}
</script>
@endsection
