  <script type="text/javascript">
    ('#myModal').modal({show: false})
  </script>

<?php 
  // get session user's id
  $uid=$_SESSION['id'];

  // include portfolio engine functions
  include
    '/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php';
?>


<h1>Edit Portfolios</h1>
<p><a href="index.php?portfolios">Return to portfolios</a></p>
<ul>
  <li>You can not edit an active portfolio</li>
  <li>You can not delete a portfolio in a competition
    (not yet implemented)
  </li>
</ul>
      <?php 
  	//get inactive portfolios
	$inactivePortfolios = getInactiveUserPortfolios($uid);
        
	// table to select inactive portfolios
        echo "<table>";
        for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
          echo "<tr>" 
            . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	    . "<td>" . $inactivePortfolios[$i][1] . "</td>"
	    . "<td>"
	    .  "<button type=\"button\" class=\"btn btn-primary " 
	    .  "btn-small\" " 
	    .    " data-toggle=\"modal\" data-target=\"#myModal\"> "
	    .  "Rename" 
	    .  "</button>"
	    .  "</td>"
	    . "</tr>";
        }
        echo "</table>";
      
      
      ?> 
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" 
  aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
			          </div>
				        <div class="modal-body">
	
					
					
					</div>
						            <div class="modal-footer">
							            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								            <button type="button" class="btn btn-primary">Save changes</button>
									          </div>
										      </div>
										        </div>
											</div>
