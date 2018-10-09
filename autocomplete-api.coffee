Drupal.behaviors.autocomplete_api =
  buildData: (term, page) ->
    search: term
    limit: 20
    offset: (page - 1) * 20

  processResults: (data, page) ->
    return
      results: data.values.map((o) ->
        id: JSON.stringify(o)
        text: o.label
      )
      more: page * 20 < data.total_items
    
  initSelection: (element, callback) ->
    v = element.val()
    data =
      id: v
      text: ''
    try
      p = JSON.parse(v)
      data.text = p.label
    catch e
      ## Nothing
    callback(data)
    
  defaultConfig: () ->
    minimumInputLength: 2
    initSelection: this.initSelection
    ajax:
      url: Drupal.settings.autocomplete_api.endpoint
      data: this.buildData
      results: this.processResults
      dataType: 'json'
      quietMillis: 250
      params:
        headers:
          Authorization: Drupal.settings.autocomplete_api.apiKey

  attach: (context, settings) ->
    for html_id, element_config of settings.autocomplete_api.elements
      config = $.extend(true, {}, this.defaultConfig(), element_config)
      $("##{html_id}", context).select2(config)
    return
