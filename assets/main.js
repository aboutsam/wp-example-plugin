jQuery(function ($) {

  var container = $('.sortable-container');
  setIndex()

  container.delegate('.wis-box', 'mouseenter mouseout', handleMouse);
  container.sortable({
    update: function () {
      setIndex()
    }
  })

  $(document).on('click', 'button.wis', function (e) {
    e.preventDefault()

    var btn = $(this)
    var val = btn.val()
    var item = btn.closest('.wis-box')
    if (val == 'up')
      moveUp(item)
    else if (val == 'down')
      moveDown(item)
    else if (val == 'create')
      create(item)
    else if (val == 'addImage')
      addImage($(this).data('file'))
    else
      remove(item)

  })

  function handleMouse(e) {
    if (e.type == "mouseenter") {
      $(this).addClass('highlight');
    } else if (e.type == "mouseout") {
      $(this).removeClass('highlight');
    }
  }

  function setIndex() {
    $('.wis-item').each(function () {
      var index = $(this).closest('.wis-box').index()
      var index = index + 1
      var name = $(this).attr('data-id')
      $(this).not('.btn').attr('id', name + '_' + index)
      $(this).not('.btn').attr('name', name + '_' + index)
      $(this).filter('.btn').attr('data-file', 'image_' + index)
      $(this).filter('.count').html(index)
    })

    var box_count = $('.wis-box').length
    $('#count').attr('value', box_count)
  }

  function moveUp(item) {
    var prev = item.prev();
    if (prev.length == 0)
      return
    prev.css('z-index', 999).css('position', 'relative').animate({ top: item.height() }, 250);
    item.css('z-index', 1000).css('position', 'relative').animate({ top: '-' + prev.height() }, 300, function () {
      prev.css('z-index', '').css('top', '').css('position', '');
      item.css('z-index', '').css('top', '').css('position', '');
      item.insertBefore(prev);
      setIndex()
    });

  }

  function moveDown(item) {
    var next = item.next();
    if (next.length == 0)
      return
    next.css('z-index', 999).css('position', 'relative').animate({ top: '-' + item.height() }, 250);
    item.css('z-index', 1000).css('position', 'relative').animate({ top: next.height() }, 300, function () {
      next.css('z-index', '').css('top', '').css('position', '');
      item.css('z-index', '').css('top', '').css('position', '');
      item.insertAfter(next);
      setIndex()
    });
  }

  function remove(item) {
    if (confirm('Möchtest du die Box wirklich löschen?')) {
      item.remove()
      setIndex()
    }
    return false
  }

  function create(item) {
    var newItem = item.clone().find('input:text').val('').end()
    newItem = newItem.find('input:hidden').val('').end()
    newItem = newItem.find('img').removeAttr('src').end()
    newItem = newItem.find('textarea').val('').end()


    newItem.insertAfter(item)
    setIndex()
  }

  function addImage(item) {
    wp.media.editor.send.attachment = function (props, attachment) {
      $('#' + item).attr('src', attachment.url)
      $('#file_' + item).attr('value', attachment.id)
      $('#file_' + item).attr('value', attachment.id)
    }
    wp.media.editor.open();
  }
})


