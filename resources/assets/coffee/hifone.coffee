HifoneView = Backbone.View.extend
  el: "body"
  repliesPerPage: 50
  windowInActive: true

  initialize: ->
    @initComponents()

    if $('body').data('page') in ['forum']
      window._forumView = new ForumView({parentView: @})

    if $('body').data('page') in ['dashboard']
      window._dashboardView = new DashboardView({parentView: @})

    if $('body').data('page') in ['install']
      window._installView = new InstallView({parentView: @})

  initComponents: () ->
    #$("abbr.timeago").timeago()
    $(".alert").alert()
    $('.dropdown-toggle').dropdown()
    $('.bootstrap-select').remove()

    # 绑定评论框 Ctrl+Enter 提交事件
    $(".post-editor textarea").unbind "keydown"
    $(".post-editor textarea").bind "keydown", "ctrl+return", (el) ->
      if $(el.target).val().trim().length > 0
        $(el.target).parent().parent().submit()
      return false

    $(window).off "blur.inactive focus.inactive"
    $(window).on "blur.inactive focus.inactive", @updateWindowActiveState

  updateWindowActiveState: (e) ->
    prevType = $(this).data("prevType")

    if prevType != e.type
      switch (e.type)
        when "blur"
          @windowInActive = false
        when "focus"
          @windowInActive = true

    $(this).data("prevType", e.type)

window.Hifone =
  Config:
    locale: 'zh-CN'
    current_user_id: null
    token: ''
    emoj_cdn : ''
    notification_url: ''
    uploader_url: '/upload_image'
    asset_url : ''
    root_url : ''

  isLogined : ->
    Hifone.Config.current_user_id != null

  needLogined : ->
    if !Hifone.isLogined()
      location.href = "/auth/login"
      return false

  loading : () ->
    console.log "loading..."

  fixUrlDash : (url) ->
    url.replace(/\/\//g,"/").replace(/:\//,"://")

  # 警告信息显示, to 显示在那个dom前(可以用 css selector)
  alert : (msg, to) ->
    $(".alert").remove()
    $(to).before("<div class='alert alert-warning'><a class='close' href='#' data-dismiss='alert'>X</a>#{msg}</div>")

  # 成功信息显示, to 显示在那个dom前(可以用 css selector)
  notice : (msg, to) ->
    $(".alert").remove()
    $(to).before("<div class='alert alert-success'><a class='close' data-dismiss='alert' href='#'>X</a>#{msg}</div>")

  openUrl : (url) ->
    window.open(url)

  initTextareaAutoResize: ->
    $('textarea').autosize()
    return

  initAjax: ->
    # Ajax Setup
    $.ajaxPrefilter (options, originalOptions, jqXHR) ->
      token = null
      if !options.crossDomain
        token = $('meta[name="token"]').attr('content')
        if token
          jqXHR.setRequestHeader 'X-CSRF-Token', token
      jqXHR

    $.ajaxSetup beforeSend: (xhr) ->
      xhr.setRequestHeader 'Accept', 'application/json'
      # xhr.setRequestHeader('Content-Type', 'application/json; charset=utf-8');

    # Prevent double form submission
    $('form').submit ->
      $form = $(this)
      $form.find(':submit').prop 'disabled', true

  initDeleteForm: ->
    $('[data-method]').append(->
      $url = $(this).attr('data-url')
      $method = $(this).attr('data-method')
      '\n' + '<form action=\'' + $url + '\' method=\'POST\' style=\'display:none\'>\n' + '   <input type=\'hidden\' name=\'_method\' value=\'' + $method + '\'>\n' + '   <input type=\'hidden\' name=\'_token\' value=\'' + Hifone.Config.token + '\'>\n' + '</form>\n'
    ).attr('style', 'cursor:pointer;').removeAttr('href').click ->
      $form = $(this).find('form')
      $title = if $(this).attr('data-title') then $(this).attr('data-title') else 'Confirm your action'
      $text = if $(this).attr('data-text') then $(this).attr('data-text') else 'Are you sure you want to do this?'
      if $(this).hasClass('need-reason')
        swal {
          title: $title
          text: "请输入操作原因："
          type: "input"
          showCancelButton: true
          closeOnConfirm: false
          confirmButtonColor: '#FF6F6F'
          inputPlaceholder: "请输入6~200个字符"
        }, (inputValue)->
          if inputValue == false
            return false
          else if inputValue.trim() == ""
            return false
          else if inputValue.trim().length < 6 || inputValue.trim().length > 200
            swal.showInputError("请输入6~200个字符！");
            return false
          $form.attr('action', $form.attr('action') + '?reason=' + inputValue)
          $form.submit()
      else if $(this).hasClass('confirm-action')
        swal {
          type: 'warning'
          title: $title
          text: $text
          confirmButtonColor: '#FF6F6F'
          showCancelButton: true
        }, ->
          $form.submit()
      else
        $form.submit()

  initSelect2 : ->
    $('.selectpicker').select2
      theme: 'classic'

    $('.js-tag-tokenizer').select2
      tags: true
      tokenSeparators: [
        ','
        ' '
      ]

  initMessage : ->
    Messenger.options = {
      extraClasses: 'messenger-fixed messenger-on-top messenger-on-right',
      theme: 'air'
    }

$ ->
  window._hifoneView = new HifoneView()
