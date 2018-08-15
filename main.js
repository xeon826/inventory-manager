$(document).ready(function() {
  var lastElementClicked = '';
  var id = '';
  var data = {};
  ei_s_id = '';
  ei_p_id = '';
  $('.view-edit').on('click', function() {
    $('main').html($('.edit-wrapper').html());
  });
  $.ajax({
    type: 'POST',
    url: 'functions.php',
    data: ({
      action: 'get_current_inv',
      query: $('.query').val()
    }),
    success: function(response) {
      $('main').html(response);
    }
  });
  $('.view-edit').on('click', function() {
    $('main').html($('.edit-wrapper').html());
  });
  $('body').on('click', '.get', function() {
    lastElementClicked = $(this);
    data = {};
    $('main .form-group').children('input, select').each(function() {
      data[$(this).attr("name")] = $(this).val();
      console.log($(this).val());
    });
    id = $(this).data('id');
    ei_s_id = $(this).data('supplier');
    ei_p_id = $(this).data('product');
    $.ajax({
      type: 'POST',
      url: 'functions.php',
      data: ({
        action: $(this).data('action'),
        id: $(this).data('id'),
        ei_s_id: $(this).data('supplier'),
        ei_p_id: $(this).data('product'),
        data: data,
        table: $(this).data('table'),
        // mp_p_pn: $('.manage-table .product-name').val(),
        // mp_p_la: $('.manage-table .label').val(),
        // mp_p_si: $('.manage-table .starting-inventory').val(),
        // mp_p_mr: $('.manage-table .minimum-required').val(),
        purchase_date: $('.manage-table .purchase-date').val()
      }),
      success: function(response) {
        $('main').html(response);
      },
      complete: function() {
        // if last element clicked is to edit incoming, fill respective items/vendor dropdowns
        if ($(lastElementClicked).is('.single-incoming')) {
          fill_dropdown($('.item-dropdown'), 'items', ei_p_id);
          fill_dropdown($('.vendor-dropdown'), 'vendors', ei_s_id);
          // append respective add entry button to the manage table gui's
        } else if ($(lastElementClicked).data('action') == 'manage_products') {
          $('main').prepend('<button data-action="products" class="add_entry btn btn-primary">Add Entry</button>');
        } else if ($(lastElementClicked).data('action') == 'manage_incoming') {
          $('main').prepend('<button data-action="incoming" class="add_entry btn btn-primary">Add Entry</button>');
        } else if ($(lastElementClicked).data('action') == 'manage_outgoing') {
          $('main').prepend('<button data-action="outgoing" class="add_entry btn btn-primary">Add Entry</button>');
        } else if ($(lastElementClicked).data('action') == 'manage_vendors') {
          $('main').prepend('<button data-action="vendors" class="add_entry btn btn-primary">Add Entry</button>');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert(jqXHR.status);
      }
    });
  });
  $('body').on('click', '.add_entry', function() {
    switch ($(this).data('action')) {
      case 'products':
        // display add new product gui
        $('main').html($('.mp_h').html());
        break;
      case 'incoming':
        // display add new incoming and fill drop down with existing items/vendors
        $('main').html($('.mi_h').html());
        fill_dropdown($('main .item-dropdown'), 'items');
        fill_dropdown($('main .vendor-dropdown'), 'vendors');
        break;
    }
  });
  $('.current-inventory').addClass('active');
  $('.nav-bar').children().each(function(i, obj) {
    $(this).on('click', function() {
      $('.nav-bar *').removeClass('active');
      $(this).addClass('active');
    });
  });
  var container = $('.mod');
  $(document).mouseup(function(e) {
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      container.attr("style", "");
    }
  });

  function fill_dropdown(container = '', action = '', id = 'none') {
    // alert(id);
    $.ajax({
      type: 'POST',
      url: 'functions.php',
      data: ({
        action: action,
        ei_s_id: id,
        ei_p_id: id,
      }),
      success: function(response) {
        $(container).html(response);
      },
      error: function(xhr, textStatus, errorThrown) {
        alert(xhr.status);
      }
    });
  }
});
