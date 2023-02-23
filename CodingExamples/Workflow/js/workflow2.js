"use strict";

const wf = {

  // action buttons holder
  actionButtonsHldr : document.querySelector('#actionForm'),

  // reference to main element  
  contentArea : document.querySelector('#content'),
  
  // reference to data table of work items
  dataTable : document.querySelector('#projectsTbl'),

  // reference to tbody within data table
  dataRowHolder : document.querySelector('#projectsTbl tbody'),

  // reference to data rows within table; we want this to be a dynamic node list
  dataRows : document.getElementById('projectsTbl').getElementsByTagName('tbody')[0].getElementsByTagName('tr'),

  // reference to thead within data table
  headerRowHolder : document.querySelector('#projectsTbl thead'),

  tableHeaders : document.querySelectorAll('#projectsTbl thead tr th'),

  changeDiv : document.getElementById('recentChanges'),
  
  // number of columns; determined in init()
  totalCols : 0,
  
  // number of rows, including the header row; determined in init()
  totalRows : 0,
  
  // number of data rows; determined in init()
  totalDataRows : 0,

  changeData : null,

  init : function() {
    this.dataTable.setAttribute('aria-live', 'polite');
    this.changeDiv.setAttribute('aria-live', 'polite');

    // put in the input buttons
    this.insertButtons();

    fetch('js/issues-data.json', {
      method: 'GET',
      cache: 'no-cache',
    })
    .then(function(response) {
      return response.json();
    })
    .then(function(response) {

      wf.totalCols = wf.tableHeaders.length;
      wf.totalDataRows = response.issues.length;
      for (let i=0; i < wf.totalDataRows; i++) {
        let tr = wf.dataRowHolder.insertRow(-1);
        let priority = response.issues[i].priority.toLowerCase();
        tr.setAttribute('data-priority', priority);
        let td1 = document.createElement('td');
        td1.innerText = response.issues[i].id;
        let td2 = document.createElement('td');
        td2.innerText = response.issues[i].type;
        let td3 = document.createElement('td');
        let td3a = document.createElement('a');
        td3a.setAttribute('href', 'decc-'+response.issues[i].id+'.html') ;
        td3a.innerText = response.issues[i].desc;
        td3.appendChild(td3a);
        let td4 = document.createElement('td');
        td4.innerText = response.issues[i].priority;
        let td5 = document.createElement('td');
        td5.innerText = response.issues[i].assigned;
        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        tr.appendChild(td4);
        tr.appendChild(td5);
        
      }
    })
    .catch(function(error) { 
      console.log(error); 
    });
    fetch('js/recent-changes.json', {
      method: 'GET',
      cache: 'no-cache',
    })
    .then(function(response) {
      return response.json();
    })
    .then(function(response) {
      wf.changeData = response;
      wf.showRecentChanges();


    })
    .catch(function(error) { 
      console.log(error); 
    });


    // calculate cols/rows properties
    this.totalRows = this.dataTable.rows.length;


    // assign event handlers
    this.actionButtonsHldr.addEventListener('click', this.setDisplay, false);
    this.dataRowHolder.addEventListener('mouseover', this.rowHighlight, false);
    this.dataRowHolder.addEventListener('mouseout', this.rowHighlight, false);
    this.headerRowHolder.addEventListener('click', this.toggleArrow, false);

    // load previous settings
    this.loadStoredButtonSettings('hidePriority');
    this.loadStoredButtonSettings('allowHighlight');    
    this.loadStoredButtonSettings('hideColumn');
    this.loadStoredTableSettings();

  },
  
  setDisplay : function(evt) {

    const theInput = wf.findTarget(evt, 'input', this);

    if (!theInput) { return; }
  
    // use button value to determine what to do
    switch(theInput.value) {
    
      case 'Hide Low Priority': 
      
        wf.contentArea.className = 'hidePriority';
        theInput.value = 'Show Low Priority';
        localStorage.setItem('hidePriority', '1');
        break;
      
      case 'Show Low Priority': 
      
        wf.contentArea.className = '';
        theInput.value = 'Hide Low Priority';
        localStorage.setItem('hidePriority', '0');
        break;
      
      case 'Highlight Current Row': 
      
        wf.dataRowHolder.className = 'allowHighlight';
        theInput.value = 'Remove Row Highlight';
        localStorage.setItem('allowHighlight', '1');
        break;
    
      case 'Remove Row Highlight': 
      
        wf.dataRowHolder.className = '';
        theInput.value = 'Highlight Current Row';
        localStorage.setItem('allowHighlight', '0');
        break;
      
      case 'Hide Type Column': 
      
        wf.dataTable.className = 'hideColumn';
        theInput.value = 'Show Type Column';
        localStorage.setItem('hideColumn', '1');
        break;
      
      case 'Show Type Column':

        wf.dataTable.className = '';
        theInput.value = 'Hide Type Column';
        localStorage.setItem('hideColumn', '0');
        break;
      
    }
  
  },

  loadStoredButtonSettings : function(idVal) {

    const storedSetting = localStorage.getItem(idVal);

    if (storedSetting && storedSetting == '1') {
    
      const btnRef = document.getElementById(idVal);
      
      switch(idVal) {

        case 'hidePriority' :
          btnRef.value = 'Show Low Priority';
          wf.contentArea.className = 'hidePriority';
          break;

        case 'allowHighlight' :
          btnRef.value = 'Remove Row Highlight';
          wf.dataRowHolder.className = 'allowHighlight';
          break;

        case 'hideColumn' :
          btnRef.value = 'Show Type Column';
          wf.dataTable.className = 'hideColumn';
          break;

      }
    
    }

  },

  loadStoredTableSettings : function() {

    const storedSetting = localStorage.getItem('sortBy');

    if (storedSetting) {
    
      // split apart the value into an array
      // the first position is a number indicating column to sort on
      // the second position is the direction of the sort (up / down)
      const sortDetails = storedSetting.split('-');
       
      // convert string to number for the column index
      sortDetails[0] = parseInt(sortDetails[0]);
       
      // sort the table
      this.sortTable(sortDetails[0], sortDetails[1]);
       
      // display the correct arrow direction
      this.dataTable.rows[0].cells[sortDetails[0]].className = sortDetails[1] + 'Arrow';
    
    }
    
    else {
    
      this.sortTable(0, 'up');
      this.dataTable.rows[0].cells[0].className = 'upArrow';
      
    }

  },

  insertButtons : function() {

    wf.actionButtonsHldr.innerHTML = '<input type="button" name="actionButton" id="hidePriority" value="Hide Low Priority" /> <input type="button" name="actionButton" id="allowHighlight" value="Highlight Current Row" /> <input type="button" name="actionButton" id="hideColumn" value="Hide Type Column" />';

  },
  
  rowHighlight : function(evt) {

    const theRow = wf.findTarget(evt, 'tr', this);
    if (!theRow) { return; }
    theRow.id = (theRow.id) ? '' : 'currentRow';
  
  },

  toggleArrow : function(evt) {
  
    const theHdrCell = wf.findTarget(evt, 'th', this);
        
    // if no header cell found then stop processing
    if (!theHdrCell) { return; }

    evt.preventDefault();
    
    // set an id on the cell clicked so we can ignore it when we reset the arrow 
    // that is shown for another cell
    theHdrCell.id = 'sortBy';
    
    const theCellIndex = theHdrCell.cellIndex;
   
    switch (theHdrCell.className) {
    
      case '' :    
      case 'downArrow' :
      
        theHdrCell.className = 'upArrow';
        wf.sortTable(theCellIndex, 'up');
        localStorage.setItem('sortBy', theCellIndex + '-up');
        break;
    
      case 'upArrow' :
    
        theHdrCell.className = 'downArrow';
        wf.sortTable(theCellIndex, 'down');   
        localStorage.setItem('sortBy', theCellIndex + '-down');
    
    }
   
    // remove the up/down array from the other cell
    wf.removeOtherArrow();
    
    // remove the id from the cell you clicked
    theHdrCell.id = '';
    
  },
  
  sortTable : function(columnNumber, sortDirection) {
  
    const dataToSort = [], removedRows = [];
    
    // build the array of cell values and append their current row number to their value
    for (let i=0; i<wf.totalDataRows; i++) {
        
      // start from the 1 row rather than the 0 row
      const adjustedRowUp = i + 1;
      dataToSort[i] = wf.dataTable.rows[adjustedRowUp].cells[columnNumber].innerText + '.' + i;
    
    }
    
    // sort differently for numbers
    if (columnNumber === 0) {
    
      dataToSort.sort(function (num1,num2) {  
        return num1 - num2;
      });
    
    }
    
    else {
      
      // sort the array values in ascending order
      dataToSort.sort();
    
    }
    
    // flip sequence to descending order as necessary
    if (sortDirection === 'down') { 
        
      dataToSort.reverse(); 
    
    }
    
    // remove the rows in the table; populate an array with their values    
    for (let counter=0, allDataRows=dataToSort.length; counter<allDataRows; counter++) {        
        
      removedRows[counter] = wf.dataRowHolder.removeChild(wf.dataRows[0]);
    
    }
       
    // rebuild the table
    for (let x=0, allDataRows=dataToSort.length; x<allDataRows; x++) {
    
      // extract the original (pre-sort) row number
      const rowNum = dataToSort[x].substring(dataToSort[x].lastIndexOf(".") + 1);
        
      // pull out that row and insert it into the table
      wf.dataRowHolder.appendChild(removedRows[rowNum]);
        
    }
  
  },
  
  removeOtherArrow : function() {
    
    // loop through header cells to remove up/down arrows
    for (let i=0; i<wf.totalCols; i++) {
    
      const currentCell = this.dataTable.rows[0].cells[i];
      
      // skip the cell that was just clicked
      if (currentCell.id === 'sortBy') { continue; }
      
      if (currentCell.className === 'downArrow' || currentCell.className === 'upArrow') {      
         currentCell.className = '';      
      }

    }
    
  },

  showRecentChanges : function() {
    const totalChanges = wf.changeData.changes.length;


    let str = '<ul>';
 
    for (let i=0; i<totalChanges; i++) {
      
      str += '<li><a href="' + wf.changeData.changes[i].id + '.html" title="' + wf.changeData.changes[i].txt + '">';
      str += wf.changeData.changes[i].id + '</a>';
      str += ' (' + wf.changeData.changes[i].status + ')</li>';
    }
    
    str += '</ul>';

    // append the recent changes list
    document.getElementById('recentChanges').insertAdjacentHTML('beforeend', str);    

  },

  findTarget : function(evt, targetNode, container) {
    let currentNode = evt.target;
    while (currentNode && currentNode !== container) {  
      if (currentNode.nodeName.toLowerCase() === targetNode.toLowerCase()) { return currentNode; }
      else { currentNode = currentNode.parentNode; }
    }
    return false;
  }

  
}

wf.init();