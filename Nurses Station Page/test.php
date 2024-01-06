<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>HTML5 Starter Template</title>
  <meta name="description" content="Starter Template">
  <meta name="author" content="Gregry Pike">
  <!-- For the toast messages -->
  <link href="css/toast.css" rel="stylesheet">
</head>
<body>
  <script src="js/scripts.js"></script>

  <!-- Toast messages -->
  <div class = "buttons">
    <button onclick="showToast(successMsg)">Success</button>
    <button onclick="showToast(errorMsg)">Error</button>
    <button onclick="showToast(invalidMsg)">Invalid</button>
</div>

<div id  ="toastBox"></div>

<script>

let toastBox = document.getElementById('toastBox');
let successMsg = '<i class="fa-solid fa-circle-check"></i> Successfully submitted';
let errorMsg = '<i class="fa-solid fa-circle-xmark"></i> Please fix the error!';
let invalidMsg = '<i class="fa-solid fa-circle-exclamation"></i> Invalid input, check again';

function showToast(msg){
    let toast = document.createElement('div');
    toast.classList.add('toast');
    toast.innerHTML = msg;
    toastBox.appendChild(toast);

    if(msg.includes('error')){
        toast.classList.add('error');
    }
    if(msg.includes('Invalid')){
        toast.classList.add('invalid');
    }

    setTimeout(()=>{
        toast.remove();
    }, 6000);
}

</script>
</body>
</html>