Drupal.behaviors.autocomplete_field =
  buildData: (term, page) ->
    search: term
    limit: 20
    offset: (page - 1) * 20

  processResults: (data, page) ->
    return
      results: data.values.map((o) ->
        id: "#{o.unique_key}|#{o.key}|#{o.label}"
        text: o.label
      )
      more: page * 20 < data.total_items
    
  initSelection: (element, callback) ->
    v = element.val()
    p = v.split('|', 3)
    data =
      id: v
      text: if p.length == 3 then p[2] else v
    callback(data)
    
  defaultConfig: () ->
    minimumInputLength: 2
    initSelection: this.initSelection
    ajax:
      url: Drupal.settings.autocomplete_field.endpoint
      data: this.buildData
      results: this.processResults
      dataType: 'json'
      quietMillis: 250
      params:
        headers:
          Authorization: Drupal.settings.autocomplete_field.apiKey

  attach: (context, settings) ->
    for html_id, element_config of settings.autocomplete_field.elements
      config = $.extend(true, {}, this.defaultConfig(), element_config)
      $("##{html_id}", context).select2(config)
    return
