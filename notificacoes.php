<!DOCTYPE html>
<html lang="en">
<head>
  <title>Notify</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div aria-live="polite" aria-atomic="true" class="position-relative">
<div class="position-relative" id="toast" style="top:0px; rigth:20px;">
  <div class="toast" data-autohide="false">
    <div class="toast-body">
     30 Pessoas estão comprando endcaps nesse momento.
    </div>
  </div>
</div>
</div>

<script>
// exibir toast
$(document).ready(function(){
  $('.toast').toast('show');

console.log('show');

//adicionar o toast no local definido
$('#toast-place').append(`
<div class="toast" role=alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
<div class="toast-header">
<div class="30 Pessoas estão comprando endcaps nesse momento"
</div>`
);

$('.toast').toast('hidden.bs.toast')
});
</script>

</body>
</html>

