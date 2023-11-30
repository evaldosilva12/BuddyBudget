<!-- Add Transaction Modal -->
<div id="addTransactionModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTransactionTitle">Add Transaction</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeAddModal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addTransactionForm">
          <input type="hidden" id="userid" name="userid" value="<?php echo $userId; ?>">
          <input type="hidden" id="addTransactionType" name="type">
          
          <div class="form-group">
            <label for="addAmount">Amount:</label>
            <input type="number" id="addAmount" name="amount" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="addDate">Date:</label>
            <input type="date" id="addDate" name="date" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="addDescription">Description:</label>
            <input type="text" id="addDescription" name="description" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="addCategory">Category:</label>
            <select id="addCategory" name="category" class="form-control" required>
              <!-- Options will be populated dynamically with JavaScript -->
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeAddModal()">Close</button>
        <button type="submit" class="btn btn-primary" form="addTransactionForm">Save Transaction</button>
      </div>
    </div>
  </div>
</div>
