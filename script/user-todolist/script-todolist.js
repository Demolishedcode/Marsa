$(document).ready(function() {
  $('.create-list-inner span').click(function(){
    // Close add new list window
    $('.create-list').slideUp(function(){
      $('.overlay').fadeOut();
    })
  });

  $('.add').click(function(){
    // Open add new list window
    $('.overlay').fadeIn(400, function() {
      $('.create-list').slideDown();
    })
  });

  // Add new item
  $('#new-item').click(function(){
    $('#new-item').parent().before("<tr class='create-list-item'><td><input type='text' name='item[]' placeholder='New Item' autocomplete='off'></td><td><img src='css/afbeeldingen/delete.png'></td></tr>");
  });

  // Animate delete
  $('.create-list table, .inner-table table').on('mouseenter', 'td img', function() {
      $(this).attr('src', 'css/afbeeldingen/delete-hover.png');
  }).on('mouseleave', 'td img', function() {
      $(this).attr('src', 'css/afbeeldingen/delete.png');
  });

  // Delete current Item
  $('.create-list table').on('click', 'td img', function() {
    $(this).closest('tr').remove();
  });

  // Submit clicked
  $('#submit').click(function(){
    var length = $('.create-list-item').length,
        title = $('form input:first-child').val(),
        prevent = false,
        error = '',
        allData = [];

    // Get data from items
    $(".create-list-item input[type='text']").each(function() {
        $val = $(this).val();

        if ($val != "") {
          allData.push($val);
        }
    });

    // Valide data form
    if (!title) {
      prevent = true;
      error += 'Please fill in a title<br>';
    }

    if (allData.length == 0) {
      prevent = true
      error += 'No items filled in';
    }

    if (prevent) {
      $('.create-list-inner p:last-of-type').html(error);
      return false;
    }
  });

  //Array for save
  var deleteArray = [],
      doneArray = [],
      undoneArray = [];

  $('section').on('click', "input[name='save']", function() {
    var dataString = "delete=" + deleteArray + "&mark=" + doneArray + "&unmark=" + undoneArray;

    $.ajax({
        type: "POST",
        url: "functions/todolist.php",
        data:dataString,
        cache: false
    });
  });

  // .inner-table table td img'
  $('.inner-table table').on('click', 'td img', function() {

    var item = $(this).closest('tr').attr('id'),
        dataString = "delete=true&itemId=" + item;

    deleteArray.push(item);
    $(this).closest('tr').css('display','none');
  });

  // Mark as done items
  $('section').on('change', "input[type='checkbox']", function() {
    var $this = $(this),
        isChecked = $this.is(':checked'),
        itemId = $this.closest('tr').attr('id');

    //Send ajax call
    if (isChecked) {
        $this.closest('tr').find("input[type='text']").css('text-decoration', 'line-through');

        var indexUnDone = $.inArray(itemId, undoneArray);
        if (indexUnDone > -1) {
            undoneArray.splice(indexUnDone, 1)
        }

        doneArray.push(itemId);
        changeDisable($this, 'disable_sp');
    } else {
        $this.closest('tr').find("input[type='text']").css('text-decoration', 'none');

        var indexDone = $.inArray(itemId, doneArray);
        if (indexDone > -1) {
            doneArray.splice(indexDone, 1);
        }

        undoneArray.push(itemId);

        changeDisable($this, 'enable_sp');
    }
  });

  $('section').on('click', '#delete', function() {
    //Message deleting
    var listId = $(this).closest("div.todo_list").attr('id');
    var dataString = "delete_list=true&listId=" + listId;

    $.ajax({
        type: "POST",
        url: "functions/todolist.php",
        data:dataString,
        cache: false,
        success: function(html){
            // succes message
            location.reload();
        }
    });
  });

  $('section').on('click', '#edit, #cancel', function() {
    var curState = $(this).closest('div').find('.inner-table').data('state'),
        curTable = $(this).closest('table'),
        obj = $(this);

    $(this).closest('tr').css('display','none');

    changeState(obj, curState, curTable);

    if ($(this).attr('id') == "cancel") {
        // Set readonly
        changeDisable($(this), 'disable');

        // Reset delete
        if (deleteArray.length > 0) {
          for (var i = 0; i < deleteArray.length; i++) {
            $('.inner-table #' + deleteArray[i] + '').css('display','block');
          }
        }

        if (doneArray.length > 0) {
          for (var a = 0; a < doneArray.length; a++) {
            $('.inner-table #' + doneArray[a] + '').find("input[type=text]").css('text-decoration','none');
            $('.inner-table #' + doneArray[a] + '').find("input[type='checkbox']").prop('checked', false);
          }
        }

        if (undoneArray.length > 0) {
          for (var b = 0; b < undoneArray.length; b++) {
            $('.inner-table #' + undoneArray[b] + '').find("input[type=text]").css('text-decoration','line-through');
            $('.inner-table #' + undoneArray[b] + '').find("input[type='checkbox']").prop('checked', true);
          }
        }
    } else {
      changeDisable($(this), 'enable');
    }

    deleteArray = [];
    doneArray = [];
    undoneArray = [];
  });

  function changeDisable (obj, action) {
    if (action == "enable") {
      obj.closest('table').find("input[type='text']").each(function(){
        if ($(this).attr('class') == 'notdone') {
          $(this).prop('disabled', false);
        }
      });
    } else if (action == "disable"){
      obj.closest('table').find("input[type='text']").each(function(){
        $(this).prop('disabled', true);
      });
    } else if (action == "enable_sp"){
      obj.closest('tr').find("input[type='text']").prop('disabled', false);
    } else if (action == "disable_sp") {
      obj.closest('tr').find("input[type='text']").prop('disabled', true);
    }
  }

  function changeState (obj, state, table) {
    if (state == "read") {
      // Goto edit state
      table.find("td img, input[type='checkbox']").css('display', 'block');
      obj.closest('table').find("tr[id='save-section']").css('display','block');
      obj.closest('div').find('.inner-table').data('state','edit')
    } else {
      // Goto read state
      table.find("td img, input[type='checkbox']").css('display', 'none');
      obj.closest('table').find("span[id='edit']").closest('tr').css('display','block');
      obj.closest('div').find('.inner-table').data('state','read')
    }
  }
});
