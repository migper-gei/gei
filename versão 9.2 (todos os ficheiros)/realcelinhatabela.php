
        <script>
highlight_row();
function highlight_row() {
    var table = document.getElementById('js-sort-table');
    var cells = table.getElementsByTagName('td');

    for (var i = 0; i < cells.length; i++) {
        // Take each cell
        var cell = cells[i];
        // do something on onclick event for cell
        cell.onmouseover = function () {
            // Get the row id where the cell exists
            var rowId = this.parentNode.rowIndex;

            var rowsNotSelected = table.getElementsByTagName('tr');
            for (var row = 0; row < rowsNotSelected.length; row++) {
                rowsNotSelected[row].style.backgroundColor = "";
                rowsNotSelected[row].classList.remove('selected');
            }
            var rowSelected = table.getElementsByTagName('tr')[rowId];
            rowSelected.style.backgroundColor = "#FAEEDC";
            rowSelected.className += " selected";

           // msg = 'The ID of the company is: ' + rowSelected.cells[0].innerHTML;
            //msg += '\nThe cell value is: ' + this.innerHTML;
            //alert(msg);
        }
    }

}
</script>