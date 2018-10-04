Drupal.behaviors.autocomplete_field =
  buildData: (term, page) ->
    search: term
    limit: 20
    offset: (page - 1) * 20
    api_key: Drupal.settings.autocomplete_field.apiKey

  processResults: (data, page) ->
    return
      results: data.values.map((o) ->
        id: o.key
        text: o.label
      )
      more: page * 20 < data.total_items
    
  defaultConfig: () ->
    minimumInputLength: 2
    ajax:
      url: Drupal.settings.autocomplete_field.endpoint
      data: this.buildData
      results: this.processResults
      dataType: 'json'
      quietMillis: 250

  attach: (context, settings) ->
    for html_id, element_config of settings.autocomplete_field.elements
      config = $.extend(true, {}, this.defaultConfig(), element_config)
      $("##{html_id}", context).select2(config)
    return
