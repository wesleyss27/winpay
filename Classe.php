<script>
function fnOne(node){
  var leVariable = [];

  function leNode(node){
    if(node && node.childNodes && node.childNodes.length){
      for(var i = 0, len = node.childNodes.length; i<len; i++){
        leNode(node.childNodes[i]);
      }
    }
    else{
      leVariable.push(node.nodeValue);
    }
  }

  leNode(node);
  return leVariable.join('');
}

console.log(fnOne(2))
</script>