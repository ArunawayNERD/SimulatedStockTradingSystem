
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<p onclick="meh()">CLICK ME!</p>

<p>
<img id="graph" src="" />	
</p>

<script type="text/javascript">
  function meh() {
    document.getElementById("graph").src="CurrentGraph.php?ticker=IBM";
  }
</script>
