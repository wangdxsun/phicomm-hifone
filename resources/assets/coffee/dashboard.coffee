window.DashboardView = Backbone.View.extend
  el: "body"

  initialize: (opts) ->
    @parentView = opts.parentView

    @initComponents()

  initComponents : ->
    self = this

    Hifone.initAjax()
    Hifone.initTextareaAutoResize()
    Hifone.initDeleteForm()
    Hifone.initSelect2()

    self.initSortable()
    self.initSidebarToggle()
    self.initUploadImage()

  initSidebarToggle: ->
    $('.sidebar-toggler').click (e) ->
      e.preventDefault()
      $('.wrapper').toggleClass 'toggled'

  initSortable: ->
    self = this
    itemList = document.getElementById('item-list')
    if itemList
      item_name = $('#item-list').data('item-name')
      new Sortable(itemList,
        group: 'omega'
        handle: '.drag-handle'
        onUpdate: ->
          orderedItemIds = $.map($('#item-list .striped-list-item'), (elem) ->
            $(elem).data 'item-id'
          )
          $.ajax
            async: true
            url: '/dashboard/api/' + item_name + '/order'
            type: 'POST'
            data: ids: orderedItemIds
            success: ->
              $.notifier.notify 'Items order has been updated.', 'success'
            error: ->
              $.notifier.notify 'Items order could not be updated.', 'error'
      )

  initUploadImage: ->
    $('.btn-upload').click ->
      $('.input-file').click()
    $('.input-file').change ->
      $form = $('.create_form')
      formData = new FormData($form[0])
      imageUrl = $('#imageUrl')
      imagePreviewBox = $('.ImagePreviewBox')
      $.ajax {
        url: '/upload_image'
        type: 'POST'
        data: formData
        cache: false
        contentType: false
        processData: false
        beforeSend: ->
          $('.btn-upload').attr 'disabled', 'disabled'
        success: (result) ->
          imageUrl.val result.filename
          imagePreviewBox.attr('src', result.filename)
        error: (err) ->
          $.notifier.notify 'File upload failed', 'error'
        complete: ->
          $('.btn-upload').removeAttr 'disabled'
      }, 'json'
      false