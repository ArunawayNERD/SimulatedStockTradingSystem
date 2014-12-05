
<!-- Modal for creating a portfolio -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
	      <span aria-hidden="true">&times;</span>
	      <span class="sr-only">Close</span>
	    </button>
        <h4 class="modal-title" id="myModalLabel">Create a Portfolio</h4>
      </div>
      <div class="modal-body">
        <form method="post" action="index.php?portfolios">
          <input type="text" name="newName" value="Portfolio Name" />
          <input type="submit" value="Make New" />
        </form>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal">
	       Cancel
	     </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for setting active portfolio -->
<div class="modal fade" id="activeModal" tabindex="-1" 
  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
	  <span aria-hidden="true">&times;</span>
	  <span class="sr-only">Close</span>
	</button>
        <h4 class="modal-title" id="myModalLabel">Pick an Active Portfolio</h4>
      </div>
      <div class="modal-body">
        <form method="post" action="index.php?portfolios">
          <table>
          <?php
            // display the inactive portfolios as options for making active
            for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
            echo "<tr>" 
            . "<td><input type=\"radio\" name=\"active\" " 
	        . "value=\"" . $inactivePortfolios[$i][0] . "\" /></td>" 
            . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	        . "<td>" . $inactivePortfolios[$i][1] . "</td>"
	        . "</tr>\n";
            }
          ?>
          </table>
          <input type="submit" value="Make Active"/>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
	      Cancel
	    </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for deleting a portfolio -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
	      <span aria-hidden="true">&times;</span>
	      <span class="sr-only">Close</span>
	    </button>
        <h4 class="modal-title" id="myModalLabel">Delete a Portfolio</h4>
      </div>
      <div class="modal-body">
        <form method="post" action="index.php?portfolios">
        <table>
        <?php
          for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
          echo "<tr>" 
          . "<td><input type=\"radio\" name=\"delete\" " 
	      . "value=\"" . $inactivePortfolios[$i][0] . "\" /></td>" 
          . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	      . "<td>" . $inactivePortfolios[$i][1] . "</td>"
	      . "</tr>\n";
          }
        ?>
        </table>
        <button type="submit" class="btn btn-default">
	  Delete 
	</button>
        </form>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal">
	     Cancel
	 </button>
      </div>
    </div>
  </div>
</div>
	  
<!-- Modal for selecting a portfolio to rename -->
<div class="modal fade" id="renameSelection" tabindex="-1" 
  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
	      <span aria-hidden="true">&times;</span>
	      <span class="sr-only">Close</span>
	    </button>
        <h4 class="modal-title" id="myModalLabel">Rename the Portfolio</h4>
      </div>
      <div class="modal-body">
        <form method="post" action="index.php?portfolios">
        <table>
        <?php
        // display the inactive portfolios as options for renaming 
        for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
        echo "<tr>" 
        . "<td><input type=\"radio\" name=\"rename\" " 
        . "value=\"" . $inactivePortfolios[$i][0] . "\" /></td>" 
        . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	    . "<td>" . $inactivePortfolios[$i][1] . "</td>"
	    . "</tr>\n";
       }
       ?>
       </table>
       <button type="submit" class="btn btn-default">
         Select
       </button>
       <input type="hidden" name="isRename" value="true" />
       </form>
     </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal">
	     Cancel
	 </button>
      </div>
    </div>
  </div>
</div>


<!-- Modal for renaming a portfolio -->
<div class="modal fade"
  id="renameModal" tabindex="-1" 
  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
	      <span aria-hidden="true">&times;</span>
	      <span class="sr-only">Close</span>
	    </button>
        <h4 class="modal-title" id="myModalLabel">Rename the Portfolio</h4>
      </div>
      <div class="modal-body">
<!-- form to change the name -->
<?php
    echo "<form method=\"post\" action=\"index.php?portfolios\">\n";
    echo "<p>Rename " . $_POST['rename'] . " as </p>\n";
    echo "<input type=\"text\" name=\"renamedName\" />\n";
    echo "<input type=\"submit\" value=\"Rename\" />";
    echo "<input type=\"hidden\" name=\"changeName\" "
      . "value=\"" . $_POST['rename'] . "\" />";
    echo "</form>";
?>
     </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal">
	     Cancel
	 </button>
      </div>
    </div>
  </div>
</div>
