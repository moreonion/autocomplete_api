do ($=jQuery) ->
  Drupal.behaviors.autocomplete_api =
    buildData: (params) ->
      search: params.term
      limit: 20
      offset: ((params.page || 1) - 1) * 20

    processResults: (data, page) ->
      return
        results: data.values.map((o) ->
          id: JSON.stringify(o)
          text: o.label
        )
        more: page * 20 < data.total_items

    defaultConfig: () ->
      minimumInputLength: 2
      ajax:
        url: Drupal.settings.autocomplete_api.endpoint
        data: this.buildData
        processResults: this.processResults
        dataType: 'json'
        delay: 250
        beforeSend: (xhr) ->
          xhr.setRequestHeader('Authorization', Drupal.settings.autocomplete_api.apiKey)
          return

    attach: (context, settings) ->
      for html_id, element_config of settings.autocomplete_api.elements
        config = $.extend(true, {}, this.defaultConfig(), element_config)
        $("##{html_id}", context).select2(config)
      return

  Drupal.webform = Drupal.webform || {}
  Drupal.webform.conditionalOperatorAutocompleteEqual = (element, existingValue, ruleValue) ->
    if $(element).closest('.webform-conditional-hidden').length > 0
      return false
  
    try
      data = JSON.parse($('input', element).select2('data').id)
      return data.key == ruleValue || data.label == ruleValue
    catch e
      return false

  Drupal.webform.conditionalOperatorAutocompleteNotEqual = (element, existingValue, ruleValue) ->
    !Drupal.webform.conditionalOperatorAutocompleteEqual(element, existingValue, ruleValue)

  return
